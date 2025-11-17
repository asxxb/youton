<!DOCTYPE html>
<html>
<head>
    <title>Create Shorts</title>
    <style>
        body { font-family: Arial; margin: 40px; }
        input, textarea { width: 300px; padding: 8px; margin-bottom: 10px; }
        label { font-weight: bold; }
        button { padding: 10px 20px; }
    </style>
</head>
<body>

<h1>Create a YouTube Shorts (30 sec)</h1>

<form action="{{ route('video.generate') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <label>Title</label><br>
    <input type="text" name="title" required><br>

    <label>Caption</label><br>
    <textarea name="caption" required></textarea><br>

    <label>Upload Image</label><br>
    <input type="file" name="image" required><br>

    <label>Upload Audio</label><br>
    <input type="file" name="audio" required><br><br>

    <button type="submit">Generate Video</button>
</form>

@if(session('video'))
    <h2>Your Video:</h2>
    <video width="300" controls>
        <source src="{{ asset(session('video')) }}" type="video/mp4">
    </video>

    <p><a href="{{ asset(session('video')) }}" download>Download Video</a></p>
@endif

</body>
</html>
