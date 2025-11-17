<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str; 
class VideoController extends Controller
{
  public function form()
    {
        return view('video.form');
    }

    public function generate(Request $request)
    {
        $request->validate([
            'image' => 'required|image',
            'audio' => 'required|mimes:mp3,wav',
            'title' => 'required|string',
            'caption' => 'required|string',
        ]);

        // Store uploaded files
        $image = $request->file('image')->store('uploads', 'public');
        $audio = $request->file('audio')->store('uploads', 'public');

        $imagePath = public_path("storage/$image");
        $audioPath = public_path("storage/$audio");

        // Create output dir if not exists
        if (!is_dir(public_path('videos'))) {
            mkdir(public_path('videos'), 0755, true);
        }

        $outputFile = "videos/" . Str::random(20) . ".mp4";
        $outputPath = public_path($outputFile);

        $title = $request->title;
        $caption = $request->caption;

        // Fix text escaping
        $safeTitle = addslashes($title);
        $safeCaption = addslashes($caption);

        // FFmpeg Command (Image + Audio + Text Overlay)
      $cmd = "
ffmpeg -y -i \"$imagePath\" -i \"$audioPath\" \
-filter_complex \"\
[0:v]scale=1080:1920:force_original_aspect_ratio=increase,crop=1080:1920,zoompan=z='min(zoom+0.0015,1.5)':d=300:s=1080x1920:fps=30[fg]; \
[0:v]scale=1080:1920:force_original_aspect_ratio=increase,boxblur=20:10[bg]; \
[bg][fg]overlay=(W-w)/2:(H-h)/2, \
drawtext=text='$safeTitle':fontcolor=white:fontsize=70:x=50:y=100, \
drawtext=text='$safeCaption':fontcolor=white:fontsize=50:x=50:y=h-200 \
\" \
-t 10 -c:v libx264 -preset ultrafast -c:a aac -b:a 192k -pix_fmt yuv420p -shortest \"$outputPath\"
";


        shell_exec($cmd);

        return back()->with('video', $outputFile);
    }
}
