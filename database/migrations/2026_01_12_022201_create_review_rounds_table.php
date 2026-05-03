<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('review_rounds', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('submission_id')->index();
            $table->integer('round')->default(1);
            $table->string('status')->default('pending')->index();
            // Status: pending, revisions_requested, resubmit_for_review, approved, declined
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_rounds');
    }
};
