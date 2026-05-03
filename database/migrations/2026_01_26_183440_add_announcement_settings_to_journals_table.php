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
        Schema::table('journals', function (Blueprint $table) {
            $table->boolean('enable_announcements')->default(false)->after('info_librarians');
            $table->longText('announcements_introduction')->nullable()->after('enable_announcements');
            $table->boolean('show_announcements_on_homepage')->default(false)->after('announcements_introduction');
            $table->integer('num_announcements_homepage')->nullable()->after('show_announcements_on_homepage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('journals', function (Blueprint $table) {
            $table->dropColumn([
                'enable_announcements',
                'announcements_introduction',
                'show_announcements_on_homepage',
                'num_announcements_homepage',
            ]);
        });
    }
};
