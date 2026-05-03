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
        Schema::table('submission_logs', function (Blueprint $table) {
            $table->string('email_subject')->nullable()->after('stage');
            $table->longText('email_body')->nullable()->after('email_subject');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submission_logs', function (Blueprint $table) {
            $table->dropColumn(['email_subject', 'email_body']);
        });
    }
};
