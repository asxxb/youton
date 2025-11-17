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

}
