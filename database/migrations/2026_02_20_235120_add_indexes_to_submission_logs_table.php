<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add composite indexes to submission_logs for high-volume performance.
     *
     * Queries accelerated:
     *  - submission_logs WHERE submission_id = ? ORDER BY created_at DESC  (history tab)
     *  - submission_logs WHERE submission_id = ? AND event_type = ?         (filtered queries)
     *  - submission_logs WHERE event_type = ? (global audit queries)
     */
    public function up(): void
    {
        Schema::table('submission_logs', function (Blueprint $table) {
            // Primary query pattern: fetch all logs for a submission, newest first
            $table->index(['submission_id', 'created_at'], 'idx_submission_logs_sub_created');

            // Filter by event type within a submission (e.g. only file_uploaded events)
            $table->index(['submission_id', 'event_type'], 'idx_submission_logs_sub_event');

            // Global audit filter by event type (admin-level queries)
            $table->index(['event_type'], 'idx_submission_logs_event_type');

            // Index on file_id to speed up JOIN-based file lookups
            // (Only if not already a FK index — safe to add explicitly)
            $table->index(['file_id'], 'idx_submission_logs_file_id');
        });
    }

    public function down(): void
    {
        Schema::table('submission_logs', function (Blueprint $table) {
            $table->dropIndex('idx_submission_logs_sub_created');
            $table->dropIndex('idx_submission_logs_sub_event');
            $table->dropIndex('idx_submission_logs_event_type');
            $table->dropIndex('idx_submission_logs_file_id');
        });
    }
};
