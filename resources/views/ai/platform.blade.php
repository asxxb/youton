@extends('layouts.app')

@section('title', 'Video Merge | Youton')

@section('content')
<div class="max-w-4xl mx-auto py-10">

    <h2 class="text-3xl font-bold mb-6 text-gray-100">🎬 Video Merge Tool</h2>

    <!-- Upload Box -->
    <div class="border border-gray-700 bg-gray-800 rounded-xl p-6 mb-8">
        <form id="uploadForm" enctype="multipart/form-data">
            @csrf

            <label class="block text-gray-300 mb-2">Upload Multiple Videos</label>

            <input 
                type="file" 
                name="videos[]" 
                id="videosInput"
                class="w-full bg-gray-900 border border-gray-700 text-gray-300 rounded p-3"
                multiple 
                accept="video/*"
            />

            <button 
                type="submit" 
                class="mt-4 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                Upload Videos
            </button>
        </form>
    </div>

    <!-- Video List -->
    <div class="mb-10">
        <h3 class="text-xl font-semibold text-gray-200 mb-3">Uploaded Videos (Drag to Reorder)</h3>

        <div id="videoList" class="space-y-4">
            <!-- dynamically added -->
        </div>
    </div>

    <!-- Merge Button -->
    <div class="text-center">
        <button 
            id="mergeBtn" 
            class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 text-lg rounded-lg font-semibold">
            🔗 Merge Videos
        </button>
    </div>

    <!-- Progress Status -->
    <div id="statusBox" class="mt-6 hidden">
        <p id="statusText" class="text-lg text-gray-300"></p>
    </div>

    <!-- Output -->
    <div id="outputBox" class="mt-8 hidden p-6 bg-gray-800 border border-gray-700 rounded-xl">
        <h3 class="text-xl text-gray-100 mb-3">✅ Merged Video</h3>
        <a id="downloadLink" 
           href="#" 
           target="_blank"
           class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg inline-block">
           Download Merged Video
        </a>
    </div>

</div>

@endsection

@section('scripts')
<!-- SortableJS for drag-and-drop -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>
let uploadedFiles = [];

// Enable sortable drag and drop
new Sortable(document.getElementById('videoList'), {
    animation: 150,
    onEnd: function () {
        reorderFiles();
    }
});

// Reorder file paths after drag
function reorderFiles() {
    let items = document.querySelectorAll('.video-item');
    let newOrder = [];
    items.forEach(i => newOrder.push(i.dataset.path));
    uploadedFiles = newOrder;
}

function renderUploadedVideos() {
    let container = $('#videoList');
    container.html('');

    uploadedFiles.forEach(file => {
        let html = `
            <div class="video-item flex items-center justify-between p-4 bg-gray-900 border border-gray-700 rounded-lg" data-path="${file}">
                <div class="flex items-center space-x-4">
                    <span class="text-white text-xl">🎥</span>
                    <p class="text-gray-300 text-sm">${file}</p>
                </div>
                <span class="cursor-move text-gray-400">☰</span>
            </div>
        `;
        container.append(html);
    });
}

// Merge Button
$('#mergeBtn').on('click', function () {

    if (uploadedFiles.length < 2) {
        alert("Please upload at least 2 videos");
        return;
    }

    $('#statusBox').removeClass('hidden');
    $('#statusText').text("Merging videos... please wait");

    $.ajax({
        // url: "{{ url('/merge-videos') }}",
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            videos: uploadedFiles
        },
        success: (res) => {
            if (res.success) {
                $('#statusText').text("Merge completed!");
                $('#outputBox').removeClass('hidden');
                $('#downloadLink').attr('href', res.download_url);
            } else {
                $('#statusText').text("❌ Failed to merge videos");
            }
        }
    });
});
</script>
@endsection
