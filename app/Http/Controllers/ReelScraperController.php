<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ReelScraperController extends Controller
{
    public function form()
    {
        return view('reels.form');
    }

    public function search(Request $request)
    {
 $request->validate([
        'query' => 'required|string|max:100',
        'time' => 'nullable|string|in:any,day,week,month,year',
        'posted_date_from' => 'nullable|date',
        'posted_date_to' => 'nullable|date',
        'high_quality' => 'nullable|boolean',
        'closed_captioned' => 'nullable|boolean',
    ]);

    $params = [
        'query' => $request->input('query'),   // FIXED
        'page' => $request->input('page', 1),
        'time' => $request->input('time', 'any'),
    ];

    if ($request->posted_date_from)
        $params['posted_date_from'] = $request->posted_date_from;

    if ($request->posted_date_to)
        $params['posted_date_to'] = $request->posted_date_to;

    if ($request->boolean('high_quality'))
        $params['high_quality'] = 'true';

    if ($request->boolean('closed_captioned'))
        $params['closed_captioned'] = 'true';

    $queryString = http_build_query($params);

    $response = Http::withHeaders([
        "x-rapidapi-host" => "real-time-shorts-search.p.rapidapi.com",
        "x-rapidapi-key"  => '57497050c5msh8f0d9d4a6abbb4dp13d5d7jsnbf75cb6a0c3f'
    ])->get("https://real-time-shorts-search.p.rapidapi.com/search?$queryString");



    if (!$response->ok()) {
        return back()->withErrors(['error' => $response->json()['error'] ?? 'API error']);
    }

    // $data = $response->json()['data'] ?? [];

    $responseData = $response->json()['data'] ?? [];

$data = collect($responseData)

    // 1️⃣ Keep only YouTube or Instagram
    ->when($request->platform, function ($items) use ($request) {
        return $items->filter(function ($item) use ($request) {
            return strtolower($item['source'] ?? '') === strtolower($request->platform);
        });
    })

    // 2️⃣ Optional: Only YouTube Shorts
    ->when($request->boolean('shorts_only'), function ($items) {
        return $items->filter(function ($item) {
            return isset($item['video_url']) &&
                   str_contains($item['video_url'], 'youtube.com/shorts');
        });
    })

    // 3️⃣ Optional: Only YouTube normal
    ->when($request->boolean('youtube_normal'), function ($items) {
        return $items->filter(function ($item) {
            return isset($item['video_url']) &&
                   str_contains($item['video_url'], 'youtube.com/watch');
        });
    })

    // 4️⃣ Normalize keys
    ->map(function ($item) {
        return [
            'title'       => $item['title'] ?? 'No title',
            'image_url'   => $item['image_url'] ?? $item['thumbnail'] ?? '/default.jpg',
            'video_url'   => $item['video_url'] ?? '',
            'source'      => $item['source'] ?? 'Unknown',
            'channel'     => $item['channel_name'] ?? 'Unknown Channel',
        ];
    })

    ->values()
    ->toArray();

return view('reels.form', compact('data'));


    // return view('reels.form', compact('data'));
    }



public function download(Request $request)
{
    $videoUrl = $request->video_url;

    if (!$videoUrl) return "No video URL";

    // Call the unified downloader API
    $response = Http::withHeaders([
        "Content-Type" => "application/json",
        "x-rapidapi-host" => "all-social-media-video-downloader2.p.rapidapi.com",
        "x-rapidapi-key"  => "57497050c5msh8f0d9d4a6abbb4dp13d5d7jsnbf75cb6a0c3f"
    ])->post("https://all-social-media-video-downloader2.p.rapidapi.com/download", [
        "url" => $videoUrl
    ]);

    info(    $response->json() );

    if (!$response->ok()) {
        return "Download API error";
    }

    $data = $response->json();

    // Extract main video (ALWAYS inside medias[0])
    $media = $data['medias'][0] ?? null;

    if (!$media || empty($media['url'])) {
        return "No downloadable video found.";
    }

    $downloadUrl = $media['url'];
    $extension   = $media['extension'] ?? 'mp4';

    // Save File
    $filename = "video_" . time() . "." . $extension;
    $path = storage_path("app/public/reels/" . $filename);

    file_put_contents($path, file_get_contents($downloadUrl));

    return response()->download($path);
}



}



