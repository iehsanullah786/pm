<div class="row">
    <div class="col-md-6">
        <h2 class="mt-4">Upload File</h2>
        <form action="{{ route('projects.storeFiles', $project) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="tab" value="files">
            <div class="mb-3">
                <input type="file" name="files[]" multiple class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Upload Files</button>
        </form>
    </div>
    <div class="col-md-6">
        <h2 class="mt-4">Uploaded Files</h2>
        <ul class="list-group">
            @foreach ($project->files->sortByDesc('created_at') as $file)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <a href="{{ Storage::url($file->file_path) }}" target="_blank">{{ $file->file_name }}</a>
                    <small class="text-muted">
                        Uploaded by {{ $file->user->name ?? 'Unknown' }} at {{ $file->created_at->format('H:i d/m/y') }}
                    </small>
                    <form action="{{ route('projects.deleteFile', $file) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this file?');">Delete</button>
                    </form>
                </li>
            @endforeach
        </ul>
    </div>
</div>
