@extends('layouts.app')

@section('content')
    <h1>Your Projects</h1>
    <a href="{{ route('projects.create') }}" class="btn btn-primary">Create Project</a>
    <ul class="list-group mt-3">
        @foreach ($projects as $project)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                {{ $project->name }}
                <a href="{{ route('projects.show', $project) }}" class="btn btn-secondary btn-sm">View</a>
            </li>
        @endforeach
    </ul>
@endsection