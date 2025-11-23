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
        Schema::table('users', function (Blueprint $table) {
            $table->longText('yt_access_token')->nullable()->after('remember_token');
            $table->longText('yt_refresh_token')->nullable()->after('yt_access_token');
            $table->timestamp('yt_expires_in')->nullable()->after('yt_refresh_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
             $table->dropColumn([
                'yt_access_token',
                'yt_refresh_token',
                'yt_expires_in'
            ]);
        });
    }
};
