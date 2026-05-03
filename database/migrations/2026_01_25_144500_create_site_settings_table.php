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
        Schema::create('site_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            // General Settings
            $table->string('site_title')->nullable();
            $table->text('site_intro')->nullable();
            $table->integer('min_password_length')->default(8);
            $table->boolean('redirect_to_journal')->default(false);
            
            // WhatsApp Gateway Configuration
            $table->string('wa_api_url')->nullable()->comment('The endpoint link (e.g., https://api.whatsapp.com/send)');
            $table->string('wa_sender_number')->nullable()->comment('The registered WhatsApp number');
            $table->string('wa_device_id')->nullable()->comment('The Device ID or API Token');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
