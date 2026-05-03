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
        Schema::create('submission_index_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('journal_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('submission_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_indexed')->nullable()->default(null);
            $table->string('last_check_status')->nullable(); // 'found', 'not_found', 'error'
            $table->timestamp('last_checked_at')->nullable();
            $table->text('scholar_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submission_index_stats');
    }
};
