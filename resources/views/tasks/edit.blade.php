@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-center">Edit Task</h1>

    <div class="row justify-content-center">
        <div class="col-md-6"> <!-- Set the width of the form -->
            <form action="{{ route('tasks.update', $task) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="mb-3">
                    <label for="title" class="form-label">Task Title</label>
                    <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $task->title) }}" required>
                </div>
                <div class="mb-3">
                    <label for="due_date" class="form-label">Due Date (Optional)</label>
                    <input type="date" class="form-control" id="due_date" name="due_date" value="{{ old('due_date', $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('Y-m-d') : '') }}" min="{{ \Carbon\Carbon::today()->toDateString() }}">
                </div>
                <div class="mb-3">
                    <label for="due_time" class="form-label">Due Time (Optional)</label>
                    <input type="time" class="form-control" id="due_time" name="due_time" value="{{ old('due_time', $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('H:i') : '') }}">
                </div>
                <button type="submit" class="btn btn-primary">Update Task</button>
                <a href="{{ route('projects.show', $task->project) }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var today = new Date().toISOString().split('T')[0];
        document.getElementById('due_date').setAttribute('min', today);
    });
</script>
@endpush