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
        // Drop old tables if they exist
        Schema::dropIfExists('navigation_items');

        // Modify navigation_menus table to add title and area_name
        Schema::table('navigation_menus', function (Blueprint $table) {
            // Rename 'name' to 'title' for OJS 3.3 compatibility
            $table->renameColumn('name', 'title');
            // Rename 'location' to 'area_name' for OJS 3.3 compatibility
            $table->renameColumn('location', 'area_name');
        });

        // Create navigation_menu_items table (global pool of menu items)
        Schema::create('navigation_menu_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('journal_id')->nullable();
            $table->string('title');
            $table->enum('type', ['custom', 'route', 'page'])->default('custom');
            $table->string('url')->nullable(); // For custom links
            $table->string('route_name')->nullable(); // For route-based links
            $table->uuid('related_id')->nullable(); // For page links
            $table->string('icon')->nullable(); // Font Awesome icon class
            $table->string('target')->default('_self'); // _self or _blank
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('journal_id')->references('id')->on('journals')->cascadeOnDelete();
        });

        // Create navigation_menu_item_assignments pivot table
        Schema::create('navigation_menu_item_assignments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('menu_id');
            $table->uuid('menu_item_id');
            $table->uuid('parent_id')->nullable(); // For dropdown/nested items (self-referencing, no FK)
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->foreign('menu_id')->references('id')->on('navigation_menus')->cascadeOnDelete();
            $table->foreign('menu_item_id')->references('id')->on('navigation_menu_items')->cascadeOnDelete();

            // Ensure unique assignment per menu
            $table->unique(['menu_id', 'menu_item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('navigation_menu_item_assignments');
        Schema::dropIfExists('navigation_menu_items');

        // Revert column renames
        Schema::table('navigation_menus', function (Blueprint $table) {
            $table->renameColumn('title', 'name');
            $table->renameColumn('area_name', 'location');
        });

        // Recreate old navigation_items table
        Schema::create('navigation_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('menu_id');
            $table->uuid('parent_id')->nullable();
            $table->string('label');
            $table->string('type')->default('custom');
            $table->string('url')->nullable();
            $table->string('route_name')->nullable();
            $table->json('route_params')->nullable();
            $table->string('icon')->nullable();
            $table->string('target')->default('_self');
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_divider')->default(false);
            $table->timestamps();

            $table->foreign('menu_id')->references('id')->on('navigation_menus')->cascadeOnDelete();
        });
    }
};
