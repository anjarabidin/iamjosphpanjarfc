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
        Schema::create('crossref_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('journal_id');
            $table->uuid('submission_id')->nullable();
            $table->string('status')->default('PENDING'); // Pending, Success, Failed
            $table->string('crossref_batch_id')->nullable();
            $table->text('message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crossref_logs');
    }
};
