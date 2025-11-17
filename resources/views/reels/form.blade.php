<!DOCTYPE html>
<html>
<head>
    <title>Reels Scraper</title>
    <style>
        body { font-family: Arial; margin: 30px; }
        input, select { width: 300px; padding: 10px; margin-bottom: 10px; }
        button { padding: 10px 20px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px; margin-top: 30px; }
        .card {
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 3px 8px rgba(0,0,0,0.1);
        }
        .card img { width: 100%; border-radius: 10px; }
        .source { background: #111; color: white; padding: 3px 8px; font-size: 12px; border-radius: 5px; }
        .title { font-weight: bold; margin: 10px 0; }
    </style>
</head>
<body>

<h1>🔥 Advanced Reels Scraper</h1>

<form action="{{ route('reels.search') }}" method="POST">
    @csrf

    <label>Search Query</label><br>
    <input type="text" name="query" placeholder="funny cat" required><br>

    <label>Page</label><br>
    <input type="number" name="page" value="1"><br>

    <label>Time Filter</label><br>
    <select name="time">
        <option value="any">Any</option>
        <option value="day">Last 24 Hours</option>
        <option value="week">Last Week</option>
        <option value="month">Last Month</option>
        <option value="year">Last Year</option>
    </select><br>

    <label>Posted Date From</label><br>
    <input type="date" name="posted_date_from"><br>

    <label>Posted Date To</label><br>
    <input type="date" name="posted_date_to"><br>

    <label>
        <input type="checkbox" name="high_quality" value="1"> High Quality Only
    </label><br>

    <label>
        <input type="checkbox" name="closed_captioned" value="1"> Closed Caption Only
    </label><br><br>


    <label>Platform</label><br>
<select name="platform">
    <option value="">Any</option>
    <option value="YouTube">YouTube</option>
    <option value="Instagram">Instagram</option>
</select><br>

<label>
    <input type="checkbox" name="shorts_only" value="1"> YouTube Shorts Only
</label><br>

<label>
    <input type="checkbox" name="youtube_normal" value="1"> YouTube Normal Videos
</label><br>


    <button type="submit">Search</button>
</form>

@if(isset($data))
    <h2>Results:</h2>

    <div class="grid">
        @foreach($data as $item)
            <div class="card">
    <img src="{{ $item['image_url'] }}" alt="">
    <div class="title">{{ $item['title'] }}</div>
    <div class="source">{{ $item['source'] ?? 'Unknown' }}</div>

    <a href="{{ $item['video_url'] }}" target="_blank">Open Reel</a>
    <br><br>

  <a href="{{ route('reels.download', ['video_url' => $item['video_url']]) }}"
   style="display:inline-block;padding:8px 12px;background:#28a745;color:white;border-radius:6px;text-decoration:none;">
    Download
</a>
</div>
        @endforeach
    </div>
@endif

</body>
</html>
