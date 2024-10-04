@extends('layouts.app')

@section('content')
    <h1>{{ $project->name }}</h1>
    <a href="{{ route('tasks.create', $project) }}" class="btn btn-primary">Create Task</a>
    <ul class="list-group mt-3">
        @foreach ($project->tasks as $task)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                {{ $task->title }}
                @if (!$task->completed)
                    <form action="{{ route('tasks.complete', $task) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm">Complete</button>
                    </form>
                @endif
            </li>
        @endforeach
    </ul>
@endsection