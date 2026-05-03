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
        // 1. Add workflow settings columns to journals table
        Schema::table('journals', function (Blueprint $table) {
            // Submission Settings
            $table->text('author_guidelines')->nullable()->after('settings');
            $table->json('submission_metadata_settings')->nullable()->after('author_guidelines');

            // Review Settings
            $table->string('review_mode')->default('double_blind')->after('submission_metadata_settings');
            $table->integer('review_response_weeks')->default(2)->after('review_mode');
            $table->integer('review_completion_weeks')->default(4)->after('review_response_weeks');
            $table->text('reviewer_guidelines')->nullable()->after('review_completion_weeks');
            $table->boolean('require_competing_interests')->default(true)->after('reviewer_guidelines');

            // Email Settings
            $table->text('email_signature')->nullable()->after('require_competing_interests');
            $table->string('email_bounce_address')->nullable()->after('email_signature');
            $table->string('email_reply_to')->nullable()->after('email_bounce_address');
        });

        // 2. Create submission_checklists table
        Schema::create('submission_checklists', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('journal_id')->index();
            $table->text('content');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_required')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // 3. Create review_forms table
        Schema::create('review_forms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('journal_id')->index();
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('elements')->nullable(); // Form fields/questions
            $table->boolean('is_active')->default(true);
            $table->integer('response_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        // 4. Create library_files table (Publisher Library)
        Schema::create('library_files', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('journal_id')->index();
            $table->string('name');
            $table->string('original_name');
            $table->string('file_path');
            $table->string('file_type'); // pdf, doc, docx
            $table->string('category')->default('general'); // marketing, permissions, contracts, general
            $table->integer('file_size')->default(0); // in bytes
            $table->uuid('uploaded_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 5. Create email_templates table
        Schema::create('email_templates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('journal_id')->index();
            $table->string('key')->index(); // e.g., SUBMISSION_ACK, REVIEW_REQUEST
            $table->string('name'); // Human-readable name
            $table->string('subject');
            $table->text('body');
            $table->text('description')->nullable();
            $table->string('from_name')->nullable();
            $table->string('from_email')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->boolean('is_custom')->default(false); // If journal has customized this
            $table->timestamps();

            $table->unique(['journal_id', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop tables
        Schema::dropIfExists('email_templates');
        Schema::dropIfExists('library_files');
        Schema::dropIfExists('review_forms');
        Schema::dropIfExists('submission_checklists');

        // Remove columns from journals
        Schema::table('journals', function (Blueprint $table) {
            $table->dropColumn([
                'author_guidelines',
                'submission_metadata_settings',
                'review_mode',
                'review_response_weeks',
                'review_completion_weeks',
                'reviewer_guidelines',
                'require_competing_interests',
                'email_signature',
                'email_bounce_address',
                'email_reply_to',
            ]);
        });
    }
};
