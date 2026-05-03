<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Journal;
use App\Models\Submission;
use App\Models\SubmissionLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CorrespondenceController extends Controller
{
    /**
     * Download the Correspondence Proof PDF.
     */
    public function download(Journal $journal, Submission $submission)
    {
        // 1. Security Check
        if ($submission->journal_id !== $journal->id) {
            abort(404);
        }

        $user = auth()->user();
        
        // Permissions: Author or Internal Staff
        $isAuthor = $submission->user_id === $user->id;
        // Check for Editor/Manager/SectionEditor/Assistant/GuestEditor permissions
        $isStaff = $user->hasJournalPermission([1, 2, 3, 4, 16], $journal->id);

        if (!$isAuthor && !$isStaff) {
            abort(403);
        }

        // 2. Load Data with Relationships
        $submission->load([
            'authors',
            'journal',
            'issue',
            'section',
            'currentPublication',
            'logs.user' // Eager load user for logs if needed
        ]);

        // 3. Map Activity Logs
        // Create a chronological list of significant events
        $logs = $submission->logs
            ->sortBy('created_at')
            ->map(function ($log) {
                return [
                    'date' => $log->created_at->translatedFormat('d F Y'),
                    'time' => $log->created_at->format('H:i'),
                    'description' => $this->mapLogEvent($log),
                    'status' => $this->mapLogStatus($log),
                    'user' => $log->user ? $log->user->name : 'System',
                ];
            })
            ->values(); // Reset keys for array

        // 4. Generate PDF
        $data = [
            'journal' => $journal,
            'submission' => $submission,
            'publication' => $submission->currentPublication,
            'logs' => $logs,
            'branding' => [
                'logo_path' => $journal->logo_path ? public_path('storage/' . $journal->logo_path) : null,
            ]
        ];

        $pdf = Pdf::loadView('pdfs.correspondence', $data);
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('Correspondence_' . $submission->submission_code . '.pdf');
    }

    /**
     * Map log events to formal Indonesian description with English translation.
     */
    private function mapLogEvent(SubmissionLog $log): string
    {
        $type = $log->event_type;
        $title = $log->title;
        // $desc = $log->description;

        return match ($type) {
            SubmissionLog::EVENT_SUBMITTED => 'Naskah diserahkan (Article Submitted)',
            SubmissionLog::EVENT_EDITOR_ASSIGNED => 'Editor ditugaskan (Editor Assigned)',
            SubmissionLog::EVENT_EDITOR_UNASSIGNED => 'Penugasan Editor dibatalkan (Editor Unassigned)',
            SubmissionLog::EVENT_REVIEWER_ASSIGNED => 'Reviewer ditugaskan (Reviewer Assigned)',
            SubmissionLog::EVENT_REVIEW_SUBMITTED => 'Hasil review diterima (Review Result Received)',
            SubmissionLog::EVENT_DECISION_MADE => 'Keputusan Editor (Editor Decision): ' . strip_tags($title),
            SubmissionLog::EVENT_STAGE_CHANGED => 'Tahap proses naskah berubah (Stage Changed)',
            SubmissionLog::EVENT_DISCUSSION_CREATED => 'Diskusi editorial baru (New Editorial Discussion)',
            SubmissionLog::EVENT_FILE_UPLOADED => 'File revisi/pendukung diunggah (File Uploaded)',
            SubmissionLog::EVENT_PUBLISHED => 'Artikel diterbitkan (Article Published)',
            default => $title ?? 'Aktivitas Sistem (System Activity)',
        };
    }

    /**
     * Map log status for display.
     */
    private function mapLogStatus(SubmissionLog $log): string
    {
        return match ($log->event_type) {
            SubmissionLog::EVENT_SUBMITTED => 'Submitted',
            SubmissionLog::EVENT_DECISION_MADE => 'Decision',
            SubmissionLog::EVENT_PUBLISHED => 'Published',
            default => 'Process',
        };
    }
}
