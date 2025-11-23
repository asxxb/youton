<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class YouTubeController extends Controller
{
    /**
     * Step 1: Redirect user to Google OAuth
     */
    public function redirect()
    {
        $clientId = env('YOUTUBE_CLIENT_ID');
        $redirectUri = urlencode(env('YOUTUBE_REDIRECT_URI'));

        $scope = urlencode("https://www.googleapis.com/auth/youtube.upload https://www.googleapis.com/auth/youtube");

        return redirect(
            "https://accounts.google.com/o/oauth2/auth?"
            . "client_id={$clientId}"
            . "&redirect_uri={$redirectUri}"
            . "&scope={$scope}"
            . "&response_type=code"
            . "&access_type=offline"
            . "&prompt=consent"
        );
    }



    /**
     * Step 2: Callback – Save tokens to user table
     */
    public function callback(Request $request)
    {
        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'code' => $request->code,
            'client_id' => env('YOUTUBE_CLIENT_ID'),
            'client_secret' => env('YOUTUBE_CLIENT_SECRET'),
            'redirect_uri' => env('YOUTUBE_REDIRECT_URI'),
            'grant_type' => 'authorization_code'
        ]);

        $tokens = $response->json();

        $user = Auth::user();

        $user->update([
            'yt_access_token'  => $tokens['access_token'] ?? null,
            'yt_refresh_token' => $tokens['refresh_token'] ?? null,
            'yt_expires_in'    => isset($tokens['expires_in']) 
                                    ? now()->addSeconds($tokens['expires_in'])
                                    : null,
        ]);

        return redirect('/')->with('success', 'YouTube connected!');
    }



    /**
     * Step 3: Refresh user’s access token
     */
    public function refreshToken()
    {
        $user = Auth::user();

        if (!$user->yt_refresh_token) {
            return null;
        }

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'client_id' => env('YOUTUBE_CLIENT_ID'),
            'client_secret' => env('YOUTUBE_CLIENT_SECRET'),
            'refresh_token' => $user->yt_refresh_token,
            'grant_type' => 'refresh_token',
        ]);

        $tokens = $response->json();

        if (!isset($tokens['access_token'])) {
            return null;
        }

        $user->update([
            'yt_access_token' => $tokens['access_token'],
            'yt_expires_in'   => now()->addSeconds($tokens['expires_in'] ?? 3600),
        ]);

        return $tokens['access_token'];
    }



    /**
     * Step 4: Upload video (Multipart upload)
     */
    public function upload(Request $request)
    {
        $request->validate([
            'video_path' => 'required|string',
            'title'      => 'required|string',
            'description'=> 'nullable|string',
        ]);

        $user = Auth::user();

        if (!$user->yt_access_token) {
            return back()->withErrors(['YouTube not connected']);
        }

        // Refresh if expired
        if (now()->greaterThan($user->yt_expires_in)) {
            $accessToken = $this->refreshToken();
        } else {
            $accessToken = $user->yt_access_token;
        }

        $videoPath = public_path($request->video_path);

        if (!file_exists($videoPath)) {
            return back()->withErrors(['Video not found']);
        }

        $metadata = [
            "snippet" => [
                "title" => $request->title,
                "description" => $request->description ?? "",
                "tags" => ["AI", "Youton Generated"]
            ],
            "status" => [
                "privacyStatus" => "public"
            ]
        ];

        $uploadResponse = Http::withToken($accessToken)
            ->timeout(600)
            ->attach('metadata', json_encode($metadata), 'metadata.json')
            ->attach('video', file_get_contents($videoPath), 'video.mp4')
            ->post("https://www.googleapis.com/upload/youtube/v3/videos?uploadType=multipart&part=snippet,status");

        if (!$uploadResponse->ok()) {
            return back()->withErrors([
                "Upload failed",
                $uploadResponse->json()
            ]);
        }

        return back()->with('success', "Uploaded Successfully!");
    }




    /**
     * Step 5: Resumable Upload
     */
    public function uploadResumable(Request $request)
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '1024M');

        $request->validate([
            'video_path' => 'required|string',
            'title'      => 'required|string',
        ]);

        $user = Auth::user();

        // Refresh token if expired
        if (now()->greaterThan($user->yt_expires_in)) {
            $accessToken = $this->refreshToken();
        } else {
            $accessToken = $user->yt_access_token;
        }

        $videoPath = public_path($request->video_path);
        $fileSize = filesize($videoPath);

        $metadata = [
            "snippet" => [
                "title" => $request->title,
                "description" => $request->description ?? "",
            ],
            "status" => ["privacyStatus" => "public"]
        ];

        // Step 1: Start session
        $init = Http::withToken($accessToken)
            ->withHeaders([
                "X-Upload-Content-Type" => "video/mp4",
                "X-Upload-Content-Length" => $fileSize
            ])
            ->post("https://www.googleapis.com/upload/youtube/v3/videos?uploadType=resumable&part=snippet,status", $metadata);

        $uploadUrl = $init->header("Location");

        // Step 2: Upload chunks
        $chunkSize = 8 * 1024 * 1024;
        $offset = 0;
        $handle = fopen($videoPath, "rb");

        while (!feof($handle)) {
            $chunk = fread($handle, $chunkSize);
            $chunkLen = strlen($chunk);
            $end = $offset + $chunkLen - 1;

            $response = Http::withToken($accessToken)
                ->withHeaders([
                    "Content-Length" => $chunkLen,
                    "Content-Range" => "bytes {$offset}-{$end}/{$fileSize}"
                ])
                ->timeout(0)
                ->send("PUT", $uploadUrl, ["body" => $chunk]);

            if ($response->status() == 308) {
                $range = $response->header("Range");
                $offset = intval(explode("-", $range)[1]) + 1;
            } else {
                fclose($handle);
                return back()->with("success", "Resumable upload complete!");
            }
        }

        fclose($handle);
    }




    /**
     * Step 6: Channel Details (from user tokens)
     */
    public function channelInfo()
    {
        $user = Auth::user();




        if (!$user->yt_access_token) {
            return ["connected" => false];
        }

        if (now()->greaterThan($user->yt_expires_in)) {
            $accessToken = $this->refreshToken();
        } else {
            $accessToken = $user->yt_access_token;
        }

        $res = Http::withToken($accessToken)->get(
            "https://www.googleapis.com/youtube/v3/channels",
            ["part" => "snippet,statistics", "mine" => true]
        );

        if (!$res->ok()) return ["connected" => false];

        $data = $res->json()['items'][0];

        return [
            "connected"   => true,
            "title"       => $data["snippet"]["title"],
            "icon"        => $data["snippet"]["thumbnails"]["default"]["url"],
            "subscribers" => $data["statistics"]["subscriberCount"],
            "views"       => $data["statistics"]["viewCount"],
            "videos"      => $data["statistics"]["videoCount"],
        ];
    }
}
