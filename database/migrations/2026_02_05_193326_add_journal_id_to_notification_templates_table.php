<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
    //   add journal_id to notification_templates table
    Schema::table('notification_templates', function (Blueprint $table) {
        $table->uuid('journal_id')->nullable()->after('id');
        // delete unique constraint on event_key
        $table->dropUnique(['event_key']);
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // remove journal_id from notification_templates table
        Schema::table('notification_templates', function (Blueprint $table) {
            $table->dropColumn('journal_id');
        });
    }
};
