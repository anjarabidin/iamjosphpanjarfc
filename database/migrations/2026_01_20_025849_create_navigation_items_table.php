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
        Schema::create('navigation_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('menu_id');
            $table->uuid('parent_id')->nullable(); // For nested dropdowns
            $table->string('label'); // Display text
            $table->string('url')->nullable(); // URL or route name
            $table->string('type')->default('custom'); // 'custom', 'page', 'route', 'divider'
            $table->string('route_name')->nullable(); // Laravel route name if type=route
            $table->json('route_params')->nullable(); // Route parameters as JSON
            $table->string('icon')->nullable(); // Font Awesome icon class
            $table->string('target')->default('_self'); // '_self', '_blank'
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Foreign key to menus
            $table->foreign('menu_id')->references('id')->on('navigation_menus')->onDelete('cascade');

            // Index for ordering
            $table->index(['menu_id', 'parent_id', 'order']);
        });

        // Add self-referencing foreign key after table creation
        Schema::table('navigation_items', function (Blueprint $table) {
            $table->foreign('parent_id')->references('id')->on('navigation_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('navigation_items');
    }
};
