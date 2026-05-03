<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('submission_logs', function (Blueprint $table) {
            // Link to a submission file (for file upload events)
            $table->foreignUuid('file_id')
                ->nullable()
                ->after('metadata')
                ->constrained('submission_files')
                ->nullOnDelete();

            // Workflow stage at time of event: submission, review, copyediting, production
            $table->string('stage', 50)
                ->nullable()
                ->after('file_id');

            $table->index('file_id');
        });
    }

    public function down(): void
    {
        Schema::table('submission_logs', function (Blueprint $table) {
            $table->dropForeign(['file_id']);
            $table->dropIndex(['file_id']);
            $table->dropColumn(['file_id', 'stage']);
        });
    }
};
