<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Creates a pivot table for journal-specific user role assignments.
     * This follows OJS 3.3 logic where users can have different roles
     * in different journals (e.g., Author in Journal A, Reviewer in Journal B).
     */
    public function up(): void
    {
        Schema::create('journal_user_roles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('journal_id');
            $table->uuid('user_id');
            $table->uuid('role_id');
            $table->timestamps();

            // Foreign keys
            $table->foreign('journal_id')
                ->references('id')
                ->on('journals')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('role_id')
                ->references('id')
                ->on('roles')
                ->onDelete('cascade');

            // Unique constraint: A user can only have a specific role once per journal
            $table->unique(['journal_id', 'user_id', 'role_id'], 'journal_user_role_unique');

            // Indexes for faster lookups
            $table->index(['journal_id', 'user_id']);
            $table->index(['user_id', 'role_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_user_roles');
    }
};
