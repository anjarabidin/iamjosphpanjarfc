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
        Schema::create('editorial_assignments', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('submission_id')->index();
            $table->uuid('user_id')->index(); // The assigned editor
            $table->uuid('assigned_by')->nullable(); // Who assigned them

            $table->string('role')->default('editor'); // editor, section_editor, manager
            $table->boolean('is_active')->default(true);
            $table->boolean('can_edit')->default(true);
            $table->boolean('can_access_editorial_history')->default(true);

            $table->timestamp('date_assigned')->useCurrent();
            $table->timestamp('date_notified')->nullable();

            $table->timestamps();

            // Prevent duplicate assignments
            $table->unique(['submission_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('editorial_assignments');
    }
};
