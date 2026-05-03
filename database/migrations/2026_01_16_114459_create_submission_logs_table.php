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
        Schema::create('submission_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('submission_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('event_type', 50); // submission_created, editor_assigned, review_assigned, etc.
            $table->string('title'); // Short title for display
            $table->text('description')->nullable(); // Detailed description
            $table->json('metadata')->nullable(); // Additional context data
            $table->timestamps();

            $table->index(['submission_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submission_logs');
    }
};
