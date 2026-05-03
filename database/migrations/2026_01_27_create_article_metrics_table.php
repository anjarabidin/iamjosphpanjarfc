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
        Schema::create('article_metrics', function (Blueprint $table) {
            $table->id();
            $table->uuid('submission_id');
            $table->enum('type', ['view', 'download']);
            $table->string('ip_address', 45); // Support IPv6
            $table->string('country_code', 2)->nullable();
            $table->string('city')->nullable();
            $table->date('date'); // Separate date column for faster grouping
            $table->timestamps();
            
            // Foreign key
            $table->foreign('submission_id')
                ->references('id')
                ->on('submissions')
                ->onDelete('cascade');
            
            // Speed up chart queries
            $table->index(['submission_id', 'type', 'date']);
            $table->index('country_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_metrics');
    }
};
