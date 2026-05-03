<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discussion_files', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('discussion_message_id')->nullable()->index(); // Nullable for initial upload
            $table->uuid('user_id')->index();
            $table->string('file_path');
            $table->string('original_name');
            $table->string('file_type')->default('document');
            $table->unsignedBigInteger('file_size');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discussion_files');
    }
};
