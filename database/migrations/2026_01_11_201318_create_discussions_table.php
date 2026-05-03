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
        Schema::create('discussions', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('submission_id')->index();
            $table->uuid('user_id')->index(); // Creator

            $table->string('subject');
            $table->integer('stage_id')->default(1); // 1 = Submission stage
            $table->boolean('is_open')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discussions');
    }
};
