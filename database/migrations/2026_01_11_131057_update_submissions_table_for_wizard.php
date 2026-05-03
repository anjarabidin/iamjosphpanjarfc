<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->string('subtitle')->nullable()->after('title');
            $table->string('submission_file_path')->nullable()->after('abstract'); // Primary file path
            $table->integer('stage_id')->default(1)->after('stage'); // 1=Submission
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropColumn(['subtitle', 'submission_file_path', 'stage_id']);
        });
    }
};
