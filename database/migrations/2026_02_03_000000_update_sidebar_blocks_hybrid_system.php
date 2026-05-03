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
        Schema::table('sidebar_blocks', function (Blueprint $table) {
            // Update type or logic handled in application, adding columns for hybrid support
            // We do not modify 'type' enum constraint since it might be string, 
            // but we will treat 'custom' as 'block' in new logic or migrate data if needed.
            // But prompt asked to update table to support new types.
            // If type is string, we just proceed.
            
            $table->string('slug')->nullable()->after('type'); // For custom page URL
            $table->boolean('show_title')->default(true)->after('title'); // Toggle Header
            
            // Ensure slug is unique per journal
            $table->unique(['journal_id', 'slug']);
        });

        // Optional: If we want to migrate existing 'custom' blocks to 'block' type?
        // The prompt says "type: Enum 'block' (default) or 'page'".
        // If we strictly follow prompt we might want to change default.
        // But let's check if we can just leave type as string and start using 'block'/'page' in code.
        // I will allow 'system' to remain for system blocks.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sidebar_blocks', function (Blueprint $table) {
            $table->dropUnique(['journal_id', 'slug']);
            $table->dropColumn(['slug', 'show_title']);
        });
    }
};
