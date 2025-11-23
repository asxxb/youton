@extends('layouts.app')

@section('title', 'Settings | Youton')

@section('content')

<style>
    .settings-wrapper {
        max-width: 650px;
        margin: auto;
    }
    .settings-title {
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 20px;
        color: #111827;
    }
    .input-label {
        font-weight: bold;
        margin-bottom: 6px;
        display: block;
        color: #374151;
    }
    .settings-input {
        width: 100%;
        padding: 12px;
        margin-bottom: 15px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        background: #f9fafb;
    }
    .btn-secondary {
        background: #6b7280;
        padding: 10px 16px;
        color: white;
        border-radius: 8px;
        text-decoration: none;
        font-size: 14px;
        margin-bottom: 20px;
        display: inline-block;
    }
    .btn-secondary:hover {
        background: #4b5563;
    }
    .kv-row {
        display: flex;
        gap: 10px;
        margin-bottom: 10px;
    }
    .kv-row input {
        padding: 10px;
        flex: 1;
        border-radius: 8px;
        border: 1px solid #d1d5db;
    }
    .remove-btn {
        background: #dc2626;
        color: white;
        padding: 8px 12px;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        font-size: 13px;
    }
    .remove-btn:hover {
        background: #b91c1c;
    }
    .add-btn {
        background: #2563eb;
        color: white;
        padding: 10px 14px;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        margin-top: 10px;
    }
    .add-btn:hover {
        background: #1e40af;
    }
</style>

<div class="settings-wrapper">

    <a href="{{ route('dashboard') }}" class="btn-secondary">⬅ Back</a>

    <div class="card">
        <h2 class="settings-title">⚙️ API Settings</h2>

        @if(session('success'))
            <p style="color:green;">{{ session('success') }}</p>
        @endif

        <form action="{{ route('settings.update') }}" method="POST">
            @csrf

            {{-- Fixed API Keys --}}
            <label class="input-label">ClipDrop API Key</label>
            <input type="text" name="clipdrop_api"
                class="settings-input"
                value="{{ $settings['clipdrop_api'] ?? '' }}">

            <label class="input-label">Murf API Key</label>
            <input type="text" name="murf_api"
                class="settings-input"
                value="{{ $settings['murf_api'] ?? '' }}">

            <label class="input-label">RapidAPI Key</label>
            <input type="text" name="rapidapi_key"
                class="settings-input"
                value="{{ $settings['rapidapi_key'] ?? '' }}">

            <hr style="margin: 25px 0;">

            {{-- KEY-VALUE PAIRS --}}
            <h3 style="margin-bottom:10px;">➕ Custom API Keys</h3>

            <div id="kv-container">
                @if(!empty($kv))
                    @foreach($kv as $pair)
                        <div class="kv-row">
                            <input type="text" name="keys[]" placeholder="Key" value="{{ $pair['key'] }}">
                            <input type="text" name="values[]" placeholder="Value" value="{{ $pair['value'] }}">
                            <button type="button" class="remove-btn" onclick="this.parentElement.remove()">X</button>
                        </div>
                    @endforeach
                @endif
            </div>

            <button type="button" class="add-btn" onclick="addKV()">+ Add New Field</button>

            <button class="btn" style="margin-top:20px;">Save Settings</button>
        </form>
    </div>

</div>

<script>
function addKV() {
    const container = document.getElementById('kv-container');
    const row = document.createElement('div');
    row.classList.add('kv-row');

    row.innerHTML = `
        <input type="text" name="keys[]" placeholder="Key">
        <input type="text" name="values[]" placeholder="Value">
        <button type="button" class="remove-btn" onclick="this.parentElement.remove()">X</button>
    `;

    container.appendChild(row);
}
</script>

@endsection
