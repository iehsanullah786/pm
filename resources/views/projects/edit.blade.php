@extends('layouts.app')

@section('content')
    <div class="container col-md-8">
        <h1>Edit Project</h1>

        <form action="{{ route('projects.update', $project) }}" method="POST">
            @csrf
            @method('PATCH')

            <div class="form-group mb-3">
                <label for="name">Name</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ $project->name }}" required>
            </div>

            <button type="submit" class="btn btn-primary">Update Project</button>
        </form>
    </div>
@endsection