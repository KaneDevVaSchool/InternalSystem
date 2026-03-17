<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('point')->default(0)->after('remember_token');
            $table->integer('contribution_point')->default(0)->after('point');
            $table->boolean('check_first_login')->default(false)->after('contribution_point');
            $table->timestamp('first_login_at')->nullable()->after('check_first_login');
            $table->timestamp('last_login_at')->nullable()->after('first_login_at');
            $table->timestamp('last_logout_at')->nullable()->after('last_login_at');
            $table->string('level')->nullable()->after('last_logout_at');

            // Google OAuth fields
            $table->string('google_id')->nullable()->index()->after('level');
            $table->text('google_access_token')->nullable()->after('google_id');
            $table->text('google_refresh_token')->nullable()->after('google_access_token');
            $table->timestamp('google_token_expires_at')->nullable()->after('google_refresh_token');
            $table->json('google_scopes')->nullable()->after('google_token_expires_at');
            $table->string('google_event_id')->nullable()->after('google_scopes');
            $table->string('google_calendar_id')->nullable()->after('google_event_id');

            $table->boolean('strava_reconnect_suggested')->default(false)->after('google_calendar_id');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'point',
                'contribution_point',
                'check_first_login',
                'first_login_at',
                'last_login_at',
                'last_logout_at',
                'level',
                'google_id',
                'google_access_token',
                'google_refresh_token',
                'google_token_expires_at',
                'google_scopes',
                'google_event_id',
                'google_calendar_id',
                'strava_reconnect_suggested',
            ]);
            $table->dropSoftDeletes();
        });
    }
};