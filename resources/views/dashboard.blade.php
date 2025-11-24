@extends('layouts.app')

@section('title', 'Dashboard | Youton')

@section('content')

<style>
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 25px;
        margin-bottom: 40px;
    }
    .section-title {
        font-size: 22px;
        font-weight: bold;
        margin-bottom: 15px;
        color: #111827;
    }
    .card h3 {
        margin-top: 0;
        margin-bottom: 8px;
    }
    .card p {
        margin-top: 0;
        color: #555;
        font-size: 15px;
    }
    .profile-img {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 10px;
    }
    .status-pill {
        padding: 5px 10px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: bold;
    }
    .status-ok {
        background: #d1fae5;
        color: #065f46;
    }
    .status-bad {
        background: #fee2e2;
        color: #991b1b;
    }
</style>

{{-- =====================
     FEATURE SHORTCUTS
====================== --}}
<h2 class="section-title">🚀 Quick Actions</h2>
<div class="dashboard-grid">

    <div class="card">
        <h3>🎬 Auto Shorts Generator</h3>
        <p>Create a full YouTube Shorts video with AI visuals + audio.</p>
        <a href="{{ route('auto.form') }}" class="btn">Start Creating</a>
    </div>

    <div class="card">
        <h3>🖼️ AI Image Generator</h3>
        <p>Create stunning images using ClipDrop AI.</p>
        <a href="{{ route('auto.form') }}" class="btn">Generate Image</a>
    </div>

    <div class="card">
        <h3>🎥 Manual Shorts Maker</h3>
        <p>Upload your own photos & audio to create Shorts quickly.</p>
        <a href="/create-video" class="btn">Open Builder</a>
    </div>

    <div class="card">
        <h3>📲 Reels Scraper (Advanced)</h3>
        <p>Fetch Instagram, TikTok, YouTube Shorts & FB reels.</p>
        <a href="{{ route('reels.form') }}" class="btn">Start Scraping</a>
    </div>

    <div class="card">
        <h3>📁 Manage Videos</h3>
        <p>See all your generated videos in one place.</p>
        <a href="#" class="btn">Coming Soon</a>
    </div>

    <div class="card">
        <h3>⚙️ Settings</h3>
        <p>Configure API keys, templates and more.</p>
        <a href="{{ route('settings.index') }}" class="btn">Open Settings</a>
    </div>


       <div class="card">
        <h3>ai video platform</h3>
        <p>Create a full YouTube Shorts video with AI visuals + audio.</p>
        <a href="{{ route('ai.platform') }}" class="btn">Start Creating</a>
    </div>
</div>



{{-- =====================
       USER PROFILE
====================== --}}
<h2 class="section-title">👤 My Profile</h2>
<div class="card">

    <div style="display:flex; align-items:center; gap:20px;">
        @if($user->profile_photo ?? false)
            <img src="{{ asset('storage/'.$user->profile_photo) }}" class="profile-img">
        @else
            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=111827&color=fff" 
                 class="profile-img">
        @endif

        <div>
            <p><strong>Name:</strong> {{ $user->name }}</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
        </div>
    </div>

    <hr style="margin:20px 0;">

    <p><strong>YouTube Status:</strong> 
        @if($user->yt_access_token ?? false)
            <span class="status-pill status-ok">Connected</span>
        @else
            <span class="status-pill status-bad">Not Connected</span>
        @endif
    </p>

    <p><strong>Instagram:</strong> 
        <span class="status-pill" style="background:#e5e7eb;color:#374151;">Coming Soon</span>
    </p>
</div>



{{-- =====================
     YOUTUBE CHANNEL INFO
====================== --}}
<h2 class="section-title">📺 YouTube Channel</h2>

@if($youtube['connected'])
<div class="card">

    <div style="display:flex; align-items:center; gap:20px;">
        <img src="{{ $youtube['icon'] }}" style="width:70px;border-radius:50%;">
        <div>
            <h3 style="margin:0;">{{ $youtube['title'] }}</h3>
            {{-- <p style="margin:0; color:#555;">{{ $youtube['description'] }}</p> --}}
        </div>
    </div>

    <hr style="margin:20px 0;">

    <p><strong>Subscribers:</strong> {{ number_format($youtube['subscribers']) }}</p>
    <p><strong>Total Videos:</strong> {{ number_format($youtube['videos']) }}</p>
    <p><strong>Total Views:</strong> {{ number_format($youtube['views']) }}</p>

    <a href="https://www.youtube.com/channel/UCiZm-_ZMG-xoawCrR0CwG6g" target="_blank" class="btn">View Channel</a>
</div>

@else
<div class="card">
    <h3>⚠️ YouTube Not Connected</h3>
    <p>Connect your YouTube account to enable uploading features.</p>
    <a href="{{ route('youtube.redirect') }}" class="btn">Connect YouTube</a>
</div>
@endif


{{-- =====================
     LOGOUT
====================== --}}
<form action="{{ route('logout') }}" method="POST" style="margin-top:20px;">
    @csrf
    <button class="btn" style="background:#DC2626;width:200px;">Logout</button>
</form>

@endsection
