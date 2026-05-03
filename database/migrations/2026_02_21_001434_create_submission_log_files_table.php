<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('submission_log_files', function (Blueprint $table) {
            $table->foreignUuid('submission_log_id')->constrained('submission_logs')->cascadeOnDelete();
            $table->foreignUuid('submission_file_id')->constrained('submission_files')->cascadeOnDelete();
            $table->timestamps();
            
            $table->primary(['submission_log_id', 'submission_file_id'], 'sub_log_file_primary');
        });

        Schema::table('submission_logs', function (Blueprint $table) {
            if (Schema::hasColumn('submission_logs', 'file_id')) {
                if (\Illuminate\Support\Facades\DB::getDriverName() !== 'sqlite') {
                    try {
                        $table->dropForeign(['file_id']);
                    } catch (\Throwable $e) {
                        // Ignore if foreign key doesn't exist
                    }
                    $table->dropColumn('file_id');
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submission_log_files');
        
        Schema::table('submission_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('submission_logs', 'file_id')) {
                $table->foreignUuid('file_id')->nullable()->constrained('submission_files')->nullOnDelete();
            }
        });
    }
};
