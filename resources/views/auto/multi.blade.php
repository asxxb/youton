<!DOCTYPE html>
<html>
<head>
    <title>Multi-Image Cinematic Shorts Generator</title>
    <style>
        body { font-family: Arial; margin: 30px; }
        input, textarea, select { width: 400px; padding: 10px; margin-bottom: 15px; }
        button { padding: 10px 20px; }
        img, video { margin-top: 20px; max-width: 300px; border-radius: 10px; }
        .thumbs img { max-width: 150px; margin-right: 10px; }
    </style>
</head>
<body>

<h1>🎬 Multi-Image Cinematic Shorts (Ken Burns)</h1>

@if($errors->any())
    <div style="color:red; margin-bottom:15px;">
        Error: {{ $errors->first() }}
    </div>
@endif

<form action="{{ route('auto.multi.generate') }}" method="POST">
    @csrf

    <!-- SINGLE PROMPT (for both image gen + TTS) -->
    <label><strong>Prompt (used for images + voice)</strong></label><br>
    <textarea name="prompt" rows="4" required placeholder="Example: A lone samurai walking through neon-lit rain..."></textarea><br>

    <!-- OPTIONAL: Number of images -->
    <label><strong>Number of Images</strong></label><br>
    <select name="image_count">
        <option value="4">4</option>
        <option value="6" selected>6</option>
        <option value="8">8</option>
        <option value="10">10</option>
    </select><br>

    <!-- OPTIONAL: Seconds per image -->
    <label><strong>Seconds per Image</strong></label><br>
    <select name="seconds_per_image">
        <option value="2">2 sec</option>
        <option value="3" selected>3 sec</option>
        <option value="4">4 sec</option>
        <option value="5">5 sec</option>
    </select><br>

    <!-- TTS Voice selection -->
    <label><strong>TTS Voice (Murf)</strong></label><br>
    <select name="tts_voice">
        <option value="Matthew">Matthew (Male)</option>
        <option value="Olivia">Olivia (Female)</option>
        <option value="William">William (Male)</option>
        <option value="Ella">Ella (Female)</option>
    </select><br><br>

    <button type="submit">Generate Cinematic Short</button>
</form>

<hr>

{{-- Show generated images --}}
@if(session('images') && is_array(session('images')))
    <h2>Generated Frames</h2>
    <div class="thumbs">
        @foreach(session('images') as $img)
            <img src="{{ asset($img) }}" alt="Frame">
        @endforeach
    </div>
@endif

{{-- Show final video --}}
@if(session('video'))
    <h2>Your Cinematic Short</h2>
    <video width="300" controls>
        <source src="{{ asset(session('video')) }}" type="video/mp4">
    </video>
@endif

</body>
</html>
