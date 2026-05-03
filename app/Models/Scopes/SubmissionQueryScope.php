<?php

namespace App\Models\Scopes;

use App\Models\Submission;
use Illuminate\Database\Eloquent\Builder;

/**
 * Centralized query scope for submissions with OJS 3.3-style filtering.
 * 
 * This class handles all submission filtering logic to avoid duplication
 * across controllers and services.
 */
class SubmissionQueryScope
{
    /**
     * Available filter types
     */
    public const FILTER_QUEUE = 'queue';
    public const FILTER_UNASSIGNED = 'unassigned';
    public const FILTER_ACTIVE = 'active';
    public const FILTER_ARCHIVES = 'archives';

    /**
     * Apply role-based filter based on user permissions
     * Authors see only their submissions, Editors see all.
     */
    public static function applyRoleBasedFilter(Builder $query, int $userId, bool $isEditor): Builder
    {
        if (!$isEditor) {
            // Author: only their own submissions
            return $query->where('user_id', $userId);
        }

        // Editor: no additional user restriction
        return $query;
    }

    /**
     * Apply status filter based on the selected tab
     */
    public static function applyStatusFilter(Builder $query, string $filter, bool $isEditor): Builder
    {
        $archivedStatuses = [Submission::STATUS_PUBLISHED, Submission::STATUS_REJECTED];

        if (!$isEditor) {
            // Author view
            if ($filter === self::FILTER_ARCHIVES) {
                return $query->whereIn('status', $archivedStatuses);
            }
            return $query->whereNotIn('status', $archivedStatuses);
        }

        // Editor view
        switch ($filter) {
            case self::FILTER_QUEUE:
                return $query->whereHas('editorialAssignments', function ($q) use ($userId) {
                    $q->where('user_id', $userId)->where('is_active', true);
                })->whereNotIn('status', $archivedStatuses);

            case self::FILTER_UNASSIGNED:
                return $query->where('status', Submission::STATUS_SUBMITTED)
                    ->whereDoesntHave('editorialAssignments', function ($q) {
                        $q->where('is_active', true);
                    });

            case self::FILTER_ACTIVE:
                return $query->whereNotIn('status', $archivedStatuses);

            case self::FILTER_ARCHIVES:
                return $query->whereIn('status', $archivedStatuses);

            default:
                return $query->whereHas('editorialAssignments', function ($q) use ($userId) {
                    $q->where('user_id', $userId)->where('is_active', true);
                })->whereNotIn('status', $archivedStatuses);
        }
    }

    /**
     * Apply advanced OJS 3.3 style filters (section, stage, issue, etc.)
     */
    public static function applyAdvancedFilters(Builder $query, array $filters): Builder
    {
        // Filter by sections
        if (!empty($filters['sections'])) {
            $query->whereIn('section_id', (array) $filters['sections']);
        }

        // Filter by stages
        if (!empty($filters['stages'])) {
            $query->whereIn('stage', (array) $filters['stages']);
        }

        // Filter by issue IDs (typically for archives)
        if (!empty($filters['issue_ids'])) {
            $query->whereIn('issue_id', (array) $filters['issue_ids']);
        }

        // Filter by status
        if (!empty($filters['statuses'])) {
            $query->whereIn('status', (array) $filters['statuses']);
        }

        // Filter by date range
        if (!empty($filters['date_from'])) {
            $query->where('submitted_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->where('submitted_at', '<=', $filters['date_to']);
        }

        // Full-text search on title and abstract
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'ilike', "%{$search}%")
                    ->orWhere('abstract', 'ilike', "%{$search}%")
                    ->orWhere('submission_code', 'ilike', "%{$search}%");
            });
        }

        return $query;
    }

    /**
     * Get submission counts using a single optimized query with FILTER clauses
     */
    public static function getCounts(Builder $base, int $userId, bool $isEditor): array
    {
        $archivedStatuses = implode(',', [
            "'" . Submission::STATUS_PUBLISHED . "'",
            "'" . Submission::STATUS_REJECTED . "'"
        ]);

        $baseQuery = (clone $base)->getQuery();

        if (!$isEditor) {
            // Author: simple counts
            return [
                'active' => (clone $base)
                    ->where('user_id', $userId)
                    ->whereNotIn('status', [Submission::STATUS_PUBLISHED, Submission::STATUS_REJECTED])
                    ->count(),
                'archives' => (clone $base)
                    ->where('user_id', $userId)
                    ->whereIn('status', [Submission::STATUS_PUBLISHED, Submission::STATUS_REJECTED])
                    ->count(),
            ];
        }

        // Editor: full OJS 3.3 counts using PostgreSQL FILTER clause
        // Note: Using multiple COUNTs in single query for efficiency
        $counts = DB::selectOne("
            SELECT 
                COUNT(*) FILTER (WHERE 
                    EXISTS (SELECT 1 FROM editorial_assignments ea 
                            WHERE ea.submission_id = submissions.id 
                            AND ea.user_id = ? AND ea.is_active = true)
                    AND status NOT IN ({$archivedStatuses})
                ) as queue_count,
                
                COUNT(*) FILTER (WHERE 
                    status = ? 
                    AND NOT EXISTS (SELECT 1 FROM editorial_assignments ea 
                                   WHERE ea.submission_id = submissions.id 
                                   AND ea.is_active = true)
                ) as unassigned_count,
                
                COUNT(*) FILTER (WHERE 
                    status NOT IN ({$archivedStatuses})
                ) as active_count,
                
                COUNT(*) FILTER (WHERE 
                    status IN ({$archivedStatuses})
                ) as archives_count
            FROM submissions
            WHERE {$baseQuery->wheres[0]['sql'] ?? 'journal_id = ?'}
        ", [$userId, Submission::STATUS_SUBMITTED, $baseQuery->wheres[0]['values'] ?? []]);

        return [
            'queue' => (int) ($counts->queue_count ?? 0),
            'unassigned' => (int) ($counts->unassigned_count ?? 0),
            'active' => (int) ($counts->active_count ?? 0),
            'archives' => (int) ($counts->archives_count ?? 0),
        ];
    }

    /**
     * Simpler alternative for getCounts that works with all databases
     * Uses Laravel's query builder with subqueries
     */
    public static function getCountsPortable(Builder $base, int $userId, bool $isEditor): array
    {
        $archivedStatuses = [Submission::STATUS_PUBLISHED, Submission::STATUS_REJECTED];

        if (!$isEditor) {
            return [
                'active' => (clone $base)
                    ->where('user_id', $userId)
                    ->whereNotIn('status', $archivedStatuses)
                    ->count(),
                'archives' => (clone $base)
                    ->where('user_id', $userId)
                    ->whereIn('status', $archivedStatuses)
                    ->count(),
            ];
        }

        // Use a single query with conditional counting
        $result = (clone $base)
            ->selectRaw("
                COUNT(*) as total,
                COUNT(*) FILTER (WHERE 
                    EXISTS (SELECT 1 FROM editorial_assignments ea 
                            WHERE ea.submission_id = submissions.id 
                            AND ea.user_id = ? AND ea.is_active = true)
                    AND status NOT IN (?, ?)
                ) as queue_count,
                COUNT(*) FILTER (WHERE 
                    status = ?
                    AND NOT EXISTS (SELECT 1 FROM editorial_assignments ea 
                                   WHERE ea.submission_id = submissions.id 
                                   AND ea.is_active = true)
                ) as unassigned_count,
                COUNT(*) FILTER (WHERE status NOT IN (?, ?)) as active_count,
                COUNT(*) FILTER (WHERE status IN (?, ?)) as archives_count
            ", array_merge(
                [$userId],
                $archivedStatuses,
                [Submission::STATUS_SUBMITTED],
                $archivedStatuses,
                $archivedStatuses
            ))
            ->first();

        return [
            'queue' => (int) ($result->queue_count ?? 0),
            'unassigned' => (int) ($result->unassigned_count ?? 0),
            'active' => (int) ($result->active_count ?? 0),
            'archives' => (int) ($result->archives_count ?? 0),
        ];
    }
}
