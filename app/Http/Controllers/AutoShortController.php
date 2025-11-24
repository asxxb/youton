<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AutoShortController extends Controller
{
    public function form()
    {
        return view('auto.form');
    }

  public function generate(Request $request)
{
    $request->validate([
        'prompt'  => 'required|string|max:1000',
        'title'   => 'required|string',
        'caption' => 'required|string',
        'tts_voice' => 'nullable|string',
        'tts_text' => 'nullable|string',
        'audio_mode' => 'required|string|in:upload,tts',
    ]);

    // -----------------------------------------------------
    // AUDIO LOGIC → Only ONE path will be used
    // -----------------------------------------------------
    if ($request->audio_mode === "tts") {

        if (!$request->tts_text) {
            return back()->withErrors(['error' => 'Please enter text for TTS audio']);
        }

        $audioPath = $this->generateTTS($request->tts_text, $request->tts_voice);

        if (!$audioPath) {
            return back()->withErrors(['error' => 'TTS Audio Generation Failed']);
        }

    } else {

        // User uploading audio
        $request->validate([
            'audio' => 'required|mimes:mp3,wav'
        ]);

        $audio = $request->file('audio')->store('uploads', 'public');
        $audioPath = public_path("storage/$audio");
    }

    // -----------------------------------------------------
    // IMAGE GENERATION FROM PROMPT
    // -----------------------------------------------------
    $prompt  = $request->prompt;
    $title   = addslashes($request->title);
    $caption = addslashes($request->caption);

    $apiKey = '56093a2543dc24355e6e86e266b67ab8c710d0be4fc5e07f9ad4a873f375948e80d991a081ddd390f39a9a55178dc915';

    $response = Http::withHeaders([
        'x-api-key' => $apiKey,
    ])->asMultipart()->post('https://clipdrop-api.co/text-to-image/v1', [
        'prompt' => $prompt,
    ]);

    if (!$response->ok()) {
        return back()->withErrors([
            'error' => $response->json()['error'] ?? 'Image generation failed'
        ]);
    }

    // Save Image
    $imageName = Str::random(20) . '.png';
    $imagePath = public_path("generated/$imageName");

    if (!is_dir(public_path('generated'))) {
        mkdir(public_path('generated'), 0755, true);
    }

    file_put_contents($imagePath, $response->body());

    // -----------------------------------------------------
    // GENERATE FINAL VIDEO
    // -----------------------------------------------------
    if (!is_dir(public_path('videos'))) {
        mkdir(public_path('videos'), 0755, true);
    }

    $videoName = Str::random(20) . '.mp4';
    $videoPath = public_path("videos/$videoName");

    $cmd = "
    ffmpeg -y -i \"$imagePath\" -i \"$audioPath\" \
    -filter_complex \"\
    [0:v]scale=1080:1920:force_original_aspect_ratio=increase,crop=1080:1920,zoompan=z='min(zoom+0.0015,1.3)':d=300:s=1080x1920:fps=30[fg]; \
    [0:v]scale=1080:1920:force_original_aspect_ratio=increase,boxblur=20:10[bg]; \
    [bg][fg]overlay=(W-w)/2:(H-h)/2, \
    drawtext=text='$title':fontcolor=white:fontsize=70:x=50:y=100, \
    drawtext=text='$caption':fontcolor=white:fontsize=50:x=50:y=h-200 \
    \" \
    -t 10 -c:v libx264 -preset ultrafast -c:a aac -pix_fmt yuv420p -shortest \"$videoPath\"
    ";

    shell_exec($cmd);

    return back()
        ->with('image', "generated/$imageName")
        ->with('video', "videos/$videoName");
}


    private function generateTTS($text, $voice)
{
    $apiKey = 'ap2_93afaabd-17d1-4316-b1d4-68ec355982ff';

    $response = Http::withHeaders([
        "api-key" => $apiKey,
        "Content-Type" => "application/json"
    ])->post("https://global.api.murf.ai/v1/speech/stream", [
        "text" => $text,
        "voiceId" => $voice,
        "multiNativeLocale" => "en-US",
        "model" => "FALCON"
    ]);

    if (!$response->ok()) {
        return null;
    }

    // Save TTS audio
    $fileName = "tts_" . time() . ".mp3";
    $path = public_path("storage/uploads/$fileName");

    if (!is_dir(public_path("storage/uploads"))) {
        mkdir(public_path("storage/uploads"), 0755, true);
    }

    file_put_contents($path, $response->body());

    return $path;
}







//////////////////////////////////////////////////////////////



public function multiForm()
    {
        return view('auto.multi');
    }

    // 🔥 NEW: Multi-image cinematic generator
    public function multiGenerate(Request $request)
    {
        $request->validate([
            'prompt'           => 'required|string|max:2000',
            'image_count'      => 'nullable|integer|min:2|max:20',
            'seconds_per_image'=> 'nullable|integer|min:1|max:10',
            'tts_voice'        => 'nullable|string',
        ]);

        $prompt          = $request->prompt;
        $imageCount      = $request->input('image_count', 6);
        $secondsPerImage = $request->input('seconds_per_image', 3);
        $ttsVoice        = $request->input('tts_voice', 'Matthew');

        // -----------------------------------------------------
        // 1) GENERATE TTS AUDIO FROM PROMPT
        // -----------------------------------------------------
        $audioPath = $this->generateTTSs($prompt, $ttsVoice);

        if (!$audioPath) {
            return back()->withErrors(['error' => 'TTS Audio Generation Failed']);
        }

        // -----------------------------------------------------
        // 2) GENERATE MULTIPLE IMAGES FROM THE SAME PROMPT
        // -----------------------------------------------------
        $apiKey = env('CLIPDROP_API_KEY'); // move yours to .env

        $sessionId = Str::uuid()->toString();

        $imageDir = public_path("generated_multi/{$sessionId}");
        if (!is_dir($imageDir)) {
            mkdir($imageDir, 0755, true);
        }

        $imagesRelative = []; // For returning to Blade
        $imagesFull     = []; // For ffmpeg

        for ($i = 1; $i <= $imageCount; $i++) {
            $response = Http::withHeaders([
                'x-api-key' => $apiKey,
            ])->asMultipart()->post('https://clipdrop-api.co/text-to-image/v1', [
                'prompt' => $prompt,
            ]);

            if (!$response->ok()) {
                // If any call fails, you can choose to stop or skip
                return back()->withErrors([
                    'error' => 'Image generation failed on image #' . $i,
                ]);
            }

            $imageName = "img_{$i}.png";
            $imagePath = $imageDir . DIRECTORY_SEPARATOR . $imageName;

            file_put_contents($imagePath, $response->body());

            $relativePath = "generated_multi/{$sessionId}/{$imageName}";
            $imagesRelative[] = $relativePath;
            $imagesFull[]     = $imagePath;
        }

        if (empty($imagesFull)) {
            return back()->withErrors(['error' => 'No images generated']);
        }

        // -----------------------------------------------------
        // 3) CREATE KEN BURNS VIDEO SEGMENTS FOR EACH IMAGE
        // -----------------------------------------------------
        $segmentDir = public_path("segments/{$sessionId}");
        if (!is_dir($segmentDir)) {
            mkdir($segmentDir, 0755, true);
        }

        $fps     = 30;
        $frames  = $secondsPerImage * $fps;
        $segments = [];

        foreach ($imagesFull as $index => $imagePath) {
            $segName = "seg_" . ($index + 1) . ".mp4";
            $segPath = $segmentDir . DIRECTORY_SEPARATOR . $segName;

            // Ken Burns style: scale + crop + slow zoom
            $cmd = sprintf(
                'ffmpeg -y -loop 1 -i %s -vf "scale=1080:1920:force_original_aspect_ratio=increase,crop=1080:1920,zoompan=z=\'min(zoom+0.0015,1.2)\':d=%d:s=1080x1920" -t %d -r %d -c:v libx264 -pix_fmt yuv420p %s',
                escapeshellarg($imagePath),
                $frames,
                $secondsPerImage,
                $fps,
                escapeshellarg($segPath)
            );

            shell_exec($cmd);
            $segments[] = $segPath;
        }

        if (empty($segments)) {
            return back()->withErrors(['error' => 'Failed to build video segments']);
        }

        // -----------------------------------------------------
        // 4) CONCAT ALL SEGMENTS INTO ONE VIDEO
        // -----------------------------------------------------
        $listFile = $segmentDir . DIRECTORY_SEPARATOR . 'list.txt';
        $listContent = '';

        foreach ($segments as $segPath) {
            // concat demuxer requires this format
            $listContent .= "file '" . str_replace("'", "'\\''", $segPath) . "'\n";
        }

        file_put_contents($listFile, $listContent);

        $finalDir = public_path('videos_multi');
        if (!is_dir($finalDir)) {
            mkdir($finalDir, 0755, true);
        }

        $videoName = Str::random(20) . '.mp4';
        $finalVideoPath = $finalDir . DIRECTORY_SEPARATOR . $videoName;

        // Combine video + audio, -shortest so it cuts to audio length
        $cmdFinal = sprintf(
            'ffmpeg -y -f concat -safe 0 -i %s -i %s -c:v libx264 -c:a aac -shortest %s',
            escapeshellarg($listFile),
            escapeshellarg($audioPath),
            escapeshellarg($finalVideoPath)
        );

        shell_exec($cmdFinal);

        $relativeVideoPath = "videos_multi/{$videoName}";

        return back()
            ->with('images', $imagesRelative)
            ->with('video', $relativeVideoPath);
    }

    // 🔁 Your existing generate() + generateTTS() can stay as-is below
    // ...

    private function generateTTSs($text, $voice)
{
    $apiKey = 'ap2_93afaabd-17d1-4316-b1d4-68ec355982ff';

    $response = Http::withHeaders([
        "api-key" => $apiKey,
        "Content-Type" => "application/json"
    ])->post("https://global.api.murf.ai/v1/speech/stream", [
        "text" => $text,
        "voiceId" => $voice,
        "multiNativeLocale" => "en-US",
        "model" => "FALCON"
    ]);

    if (!$response->ok()) {
        return null;
    }

    // Save TTS audio
    $fileName = "tts_" . time() . ".mp3";
    $path = public_path("storage/uploads/$fileName");

    if (!is_dir(public_path("storage/uploads"))) {
        mkdir(public_path("storage/uploads"), 0755, true);
    }

    file_put_contents($path, $response->body());

    return $path;
}

}
