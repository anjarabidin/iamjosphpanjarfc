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
        Schema::create('navigation_menus', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('journal_id')->nullable(); // null = site-wide menu
            $table->string('name'); // e.g., "Main Header", "User Topbar"
            $table->string('location'); // 'primary', 'user_top', 'footer', 'sidebar'
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Foreign key
            $table->foreign('journal_id')->references('id')->on('journals')->onDelete('cascade');

            // Unique constraint per journal + location
            $table->unique(['journal_id', 'location']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('navigation_menus');
    }
};
