<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('review_assignments', function (Blueprint $table) {
            $table->uuid('review_round_id')->nullable()->index()->after('submission_id');
            $table->string('review_method')->default('double_blind')->after('round');
            // review_method: double_blind, blind, open
            $table->timestamp('response_due_date')->nullable()->after('due_date');
        });
    }

    public function down(): void
    {
        Schema::table('review_assignments', function (Blueprint $table) {
            $table->dropColumn(['review_round_id', 'review_method', 'response_due_date']);
        });
    }
};
