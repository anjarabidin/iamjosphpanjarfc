<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This table stores the participants for each discussion.
     * Only participants in this table will receive notifications when new messages are added.
     */
    public function up(): void
    {
        Schema::create('discussion_participants', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('discussion_id');
            $table->uuid('user_id');

            $table->foreign('discussion_id')
                ->references('id')
                ->on('discussions')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->timestamps();

            // Prevent duplicate entries
            $table->unique(['discussion_id', 'user_id']);
        });

        // Add closed_at column to discussions table for tracking when discussion was closed
        Schema::table('discussions', function (Blueprint $table) {
            $table->timestamp('closed_at')->nullable()->after('is_open');
            $table->uuid('closed_by')->nullable()->after('closed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('discussions', function (Blueprint $table) {
            $table->dropColumn(['closed_at', 'closed_by']);
        });

        Schema::dropIfExists('discussion_participants');
    }
};
