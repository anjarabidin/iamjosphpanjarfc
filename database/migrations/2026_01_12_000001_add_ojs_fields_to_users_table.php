<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds OJS 3.3 compatible fields to users table
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Username for OJS compatibility (unique identifier)
            $table->string('username', 50)->unique()->nullable()->after('name');

            // Given name and family name (OJS uses these separately)
            $table->string('given_name', 255)->nullable()->after('username');
            $table->string('family_name', 255)->nullable()->after('given_name');

            // ORCID iD for academic identification
            $table->string('orcid_id', 50)->nullable()->after('country');

            // Mailing address
            $table->text('mailing_address')->nullable()->after('orcid_id');

            // User signature (for correspondence)
            $table->text('signature')->nullable()->after('mailing_address');

            // Preferred language
            $table->string('locale', 10)->default('en')->after('signature');

            // Date last login
            $table->timestamp('date_last_login')->nullable()->after('locale');

            // Registration date (if you want separate from created_at)
            $table->timestamp('date_registered')->nullable()->after('date_last_login');

            // Must change password flag
            $table->boolean('must_change_password')->default(false)->after('date_registered');

            // Disabled flag (account status)
            $table->boolean('disabled')->default(false)->after('must_change_password');

            // Reason for disabling
            $table->text('disabled_reason')->nullable()->after('disabled');

            // Inline help preference
            $table->boolean('inline_help')->default(true)->after('disabled_reason');

            // Email notification preferences
            $table->boolean('email_notifications')->default(true)->after('inline_help');

            // Privacy consent date
            $table->timestamp('privacy_consented_at')->nullable()->after('email_notifications');

            // Index for faster lookups
            $table->index(['username']);
            $table->index(['given_name', 'family_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['username']);
            $table->dropIndex(['given_name', 'family_name']);

            $table->dropColumn([
                'username',
                'given_name',
                'family_name',
                'orcid_id',
                'mailing_address',
                'signature',
                'locale',
                'date_last_login',
                'date_registered',
                'must_change_password',
                'disabled',
                'disabled_reason',
                'inline_help',
                'email_notifications',
                'privacy_consented_at',
            ]);
        });
    }
};
