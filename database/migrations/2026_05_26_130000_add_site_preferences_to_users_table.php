<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('preferred_country_code', 2)->nullable()->after('identity_country');
            $table->string('preferred_language', 20)->nullable()->after('preferred_country_code');
            $table->string('cookie_consent_mode', 20)->nullable()->after('preferred_language');
            $table->json('cookie_consent_preferences')->nullable()->after('cookie_consent_mode');
            $table->timestamp('cookie_consent_set_at')->nullable()->after('cookie_consent_preferences');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'preferred_country_code',
                'preferred_language',
                'cookie_consent_mode',
                'cookie_consent_preferences',
                'cookie_consent_set_at',
            ]);
        });
    }
};
