<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Request;

class Setting extends Model
{

    protected $guarded = [
    ];

    public function callback(Request $request)
{
    $code = $request->code;

    $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
        'code' => $code,
        'client_id' => env('YOUTUBE_CLIENT_ID'),
        'client_secret' => env('YOUTUBE_CLIENT_SECRET'),
        'redirect_uri' => env('YOUTUBE_REDIRECT_URI'),
        'grant_type' => 'authorization_code'
    ]);

    $tokens = $response->json();

    $settings = Setting::first();

    $settings->update([
        'yt_access_token'  => $tokens['access_token'],
        'yt_refresh_token' => $tokens['refresh_token'] ?? null,
        'yt_expires_in'    => now()->addSeconds($tokens['expires_in']),
    ]);

    return redirect('/')->with('success', 'YouTube connected!');
}
}
