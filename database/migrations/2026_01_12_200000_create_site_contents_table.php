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
        Schema::create('site_contents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('key')->unique();
            $table->longText('value')->nullable();
            $table->string('group')->default('general'); // hero, footer, social, etc.
            $table->string('type')->default('text'); // text, textarea, json, image
            $table->string('label')->nullable(); // Human-readable label for admin UI
            $table->timestamps();

            $table->index('group');
        });
    }

    /**
     * Revert the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_contents');
    }
};
