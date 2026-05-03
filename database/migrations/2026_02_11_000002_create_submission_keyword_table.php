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
        Schema::create('submission_keyword', function (Blueprint $table) {
            $table->uuid('submission_id');
            $table->uuid('keyword_id');
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('submission_id')
                ->references('id')
                ->on('submissions')
                ->onDelete('cascade');
                
            $table->foreign('keyword_id')
                ->references('id')
                ->on('keywords')
                ->onDelete('cascade');
            
            // Composite unique index to prevent duplicates
            $table->unique(['submission_id', 'keyword_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submission_keyword');
    }
};
