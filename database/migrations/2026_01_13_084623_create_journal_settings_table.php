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
        Schema::create('journal_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('journal_id')->constrained()->onDelete('cascade');
            $table->string('setting_name');
            $table->longText('setting_value')->nullable();
            $table->string('setting_type')->default('string'); // string, boolean, json, file
            $table->string('group')->default('general'); // homepage, footer, appearance, content
            $table->timestamps();

            // Unique constraint to prevent duplicate settings per journal
            $table->unique(['journal_id', 'setting_name']);
            $table->index(['journal_id', 'group']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_settings');
    }
};
