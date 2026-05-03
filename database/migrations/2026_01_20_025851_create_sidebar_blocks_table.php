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
        Schema::create('sidebar_blocks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('journal_id');
            $table->string('type')->default('custom'); // 'system', 'custom'
            $table->string('title');
            $table->text('content')->nullable(); // HTML content for custom blocks
            $table->string('component_name')->nullable(); // Blade component for system blocks (e.g., 'sidebar.login-block')
            $table->string('icon')->nullable(); // Optional icon
            $table->json('settings')->nullable(); // Additional settings as JSON
            $table->boolean('is_active')->default(true);
            $table->string('position')->default('right'); // 'left', 'right' sidebar
            $table->integer('order')->default(0);
            $table->timestamps();

            // Foreign key
            $table->foreign('journal_id')->references('id')->on('journals')->onDelete('cascade');

            // Index for ordering
            $table->index(['journal_id', 'position', 'is_active', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sidebar_blocks');
    }
};
