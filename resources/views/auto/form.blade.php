<!DOCTYPE html>
<html>
<head>
    <title>Auto YouTube Shorts Generator</title>
    <style>
        body { font-family: Arial; margin: 30px; }
        input, textarea, select { width: 400px; padding: 10px; margin-bottom: 15px; }
        button { padding: 10px 20px; }
        img, video { margin-top: 20px; max-width: 300px; border-radius: 10px; }

        .hidden { display: none; }
    </style>

    <script>
        function toggleAudioSource() {
            const mode = document.querySelector('input[name="audio_mode"]:checked').value;
            document.getElementById("upload_audio_box").style.display = (mode === "upload") ? "block" : "none";
            document.getElementById("tts_box").style.display = (mode === "tts") ? "block" : "none";
        }
    </script>
</head>
<body>

<h1>🔥 Auto YouTube Shorts Generator</h1>

@if($errors->any())
    <div style="color:red;">
        Error: {{ $errors->first() }}
    </div>
@endif

<form action="{{ route('auto.generate') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <!-- PROMPT -->
    <label><strong>Prompt (Generate Image)</strong></label><br>
    <textarea name="prompt" rows="3" required></textarea><br>

    <!-- TITLE -->
    <label><strong>Title</strong></label><br>
    <input type="text" name="title" required><br>

    <!-- CAPTION -->
    <label><strong>Caption</strong></label><br>
    <textarea name="caption" rows="2" required></textarea><br>

    <!-- AUDIO MODE SWITCH -->
    <label><strong>Choose Audio Source</strong></label><br>

    <label>
        <input type="radio" name="audio_mode" value="upload" checked onclick="toggleAudioSource()"> Upload Audio
    </label>
    &nbsp;&nbsp;
    <label>
        <input type="radio" name="audio_mode" value="tts" onclick="toggleAudioSource()"> Use Text-to-Speech (Murf)
    </label>

    <br><br>

    <!-- UPLOAD AUDIO -->
    <div id="upload_audio_box">
        <label><strong>Upload Audio (mp3/wav)</strong></label><br>
        <input type="file" name="audio" accept="audio/mp3,audio/wav"><br><br>
    </div>

    <!-- TTS AUDIO BOX -->
    <div id="tts_box" class="hidden">
        <label><strong>Text for TTS</strong></label><br>
        <textarea name="tts_text" rows="3" placeholder="Enter text to generate AI voice..."></textarea><br>

        <label><strong>Select Voice</strong></label><br>
        <select name="tts_voice">
            <option value="Matthew">Matthew (Male)</option>
            <option value="Olivia">Olivia (Female)</option>
            <option value="William">William (Male)</option>
            <option value="Ella">Ella (Female)</option>
        </select><br><br>
    </div>

    <button type="submit">Generate Shorts</button>
</form>

<hr>

<!-- OUTPUT -->
@if(session('image'))
    <h2>Generated Image</h2>
    <img src="{{ asset(session('image')) }}">
@endif

@if(session('video'))
    <h2>Your YouTube Shorts Video</h2>
    <video controls>
        <source src="{{ asset(session('video')) }}" type="video/mp4">
    </video>
@endif

</body>
</html>
