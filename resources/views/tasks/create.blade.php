@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-center">Create Task</h1>

    <div class="row justify-content-center">
        <div class="col-md-6"> <!-- Set the width of the form -->
            <form action="{{ route('tasks.store', $project) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="title" class="form-label">Task Title</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>
                <div class="mb-3">
                    <label for="due_date" class="form-label">Due Date (Optional)</label>
                    <input type="date" class="form-control" id="due_date" name="due_date" min="{{ \Carbon\Carbon::today()->toDateString() }}">
                </div>
                <div class="mb-3">
                    <label for="due_time" class="form-label">Due Time (Optional)</label>
                    <input type="time" class="form-control" id="due_time" name="due_time">
                </div>
                <button type="submit" class="btn btn-primary">Create Task</button>
                <a href="{{ route('projects.show', $project) }}" class="btn btn-secondary">Cancel</a>
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