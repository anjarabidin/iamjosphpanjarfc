<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Site Content Blocks - The "Page Builder" Engine
     * Allows Site Admin to configure, reorder, and toggle portal sections
     */
    public function up(): void
    {
        Schema::create('site_content_blocks', function (Blueprint $table) {
            $table->id();
            
            // Unique identifier for the block type
            $table->string('key')->unique();
            
            // Display name (can be customized)
            $table->string('title')->nullable();
            
            // Block description for admin UI
            $table->text('description')->nullable();
            
            // Configuration (JSON) - stores all block-specific settings
            // e.g., background_image, layout_style, colors, content, etc.
            $table->jsonb('config')->nullable()->default('{}');
            
            // Toggle visibility on/off
            $table->boolean('is_active')->default(true);
            
            // Vertical arrangement order
            $table->integer('sort_order')->default(0);
            
            // Block icon (for admin UI)
            $table->string('icon')->nullable()->default('fa-cube');
            
            // Block category for grouping in admin
            $table->string('category')->nullable()->default('content');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_content_blocks');
    }
};
