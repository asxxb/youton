<!DOCTYPE html>
<html>
<head>
    <title>Youton Dashboard</title>
    <style>
        body {
            font-family: Arial;
            margin: 0;
            background: #f3f4f6;
        }
        .header {
            background: #111827;
            padding: 20px;
            color: white;
            font-size: 28px;
            font-weight: bold;
        }
        .container {
            padding: 40px;
        }
        .card {
            background: white;
            padding: 25px;
            margin-bottom: 20px;
            border-radius: 12px;
            box-shadow: 0 3px 8px rgba(0,0,0,0.1);
        }
        .btn {
            display: inline-block;
            padding: 12px 20px;
            background: #2563eb;
            color: white;
            border-radius: 10px;
            text-decoration: none;
            font-size: 16px;
            transition: 0.2s;
        }
        .btn:hover {
            background: #1e40af;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit,minmax(280px,1fr));
            gap: 20px;
        }
        .grid .card h3 {
            margin-top: 0;
        }
    </style>
</head>
<body>

<div class="header">🔥 Youton Dashboard</div>

<div class="container">

    <div class="grid">

        <div class="card">
            <h3>🎬 Auto Shorts Generator</h3>
            <p>Create a full YouTube Shorts video using AI-generated image and your audio.</p>
            <a href="{{ route('auto.form') }}" class="btn">Go to Generator</a>
        </div>

        <div class="card">
            <h3>🖼️ AI Image Generator</h3>
            <p>Create stunning 1024×1024 images using ClipDrop text-to-image.</p>
            <a href="{{ route('auto.form') }}" class="btn">Generate Image</a>
        </div>

        <div class="card">
            <h3>🎥 Shorts (Manual)</h3>
            <p>Create a Shorts video manually by uploading your own image + audio.</p>
            <a href="/create-video" class="btn">Manual Shorts Maker</a>
        </div>



        <div class="card">
    <h3>📲 Reels Scraper (Advanced)</h3>
    <p>Search Instagram, TikTok, YouTube Shorts and Facebook Reels with filters.</p>
    <a href="{{ route('reels.form') }}" class="btn">Open Scraper</a>
</div>

        <div class="card">
            <h3>📁 Manage Videos</h3>
            <p>View and manage all your generated videos in one place.</p>
            <a href="#" class="btn">Coming Soon</a>
        </div>




        <div class="card">
            <h3>⚙️ Settings</h3>
            <p>Configure API keys, branding, templates, voice settings, etc.</p>
            <a href="#" class="btn">Coming Soon</a>
        </div>

    </div>

</div>

</body>
</html>
