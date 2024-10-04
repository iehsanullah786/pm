@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Projects</h1>
    @if(auth()->user()->hasRole('super_admin'))
        <a href="{{ route('projects.create') }}" class="btn btn-primary mb-3">Create New Project</a>
    @endif

    <div class="row">
        @foreach ($projects->sortByDesc('created_at') as $project)
            <div class="col-md-4">
                <div class="card">
                    <h4 class="card-header">{{ $project->name }}</h4>
                    <div class="card-body">
                        <a href="{{ route('projects.show', $project->id) }}" class="btn btn-info">View</a>
                        @if(auth()->user()->hasRole('super_admin'))
                            <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-warning">Edit</a>
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $project->id }}">Delete</button>
                            <!-- Delete Modal -->
                            <div class="modal fade" id="deleteModal-{{ $project->id }}" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteModalLabel">Delete Project</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Are you sure you want to delete project: <b>{{ $project->name }}</b>?</p>
                                            <p>Please enter the project name to confirm:</p>
                                            <form>
                                                <input type="text" id="project-name-{{ $project->id }}" class="form-control">
                                            </form>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <form action="{{ route('projects.destroy', $project->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" onclick="return confirmDelete('{{ $project->name }}', '{{ $project->id }}')">Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="card-footer mt-3">
                            <h5>Project Members</h5>
                            <div class="d-flex flex-wrap justify-content-start">
                                @php
                                    // Initialize members array
                                    $members = [];

                                    // Add project users to the members array
                                    foreach ($project->users as $user) {
                                        $profilePicture = $user->profile_picture ? asset('storage/' . $user->profile_picture) : asset('img/default-profile-picture.jpg');
                                        $members[$user->name] = $profilePicture;
                                    }

                                    // Add super admins to the members array, excluding the current user if already included
                                    foreach ($superAdmins as $superAdmin) {
                                        if ($superAdmin->id !== auth()->user()->id) {
                                            $profilePicture = $superAdmin->profile_picture ? asset('storage/' . $superAdmin->profile_picture) : asset('img/default-profile-picture.jpg');
                                            $members[$superAdmin->name] = $profilePicture;
                                        }
                                    }

                                    // Add the current user, avoiding duplicates
                                    if (!isset($members[auth()->user()->name])) {
                                        $members[auth()->user()->name] = auth()->user()->profile_picture ? asset('storage/' . auth()->user()->profile_picture) : asset('img/default-profile-picture.jpg');
                                    }
                                @endphp

                                @foreach ($members as $name => $profilePicture)
                                    <div>
                                        <img
                                            src="{{ $profilePicture ?: asset('img/default-profile-picture.jpg') }}" 
                                            alt="{{ $name }}'s Profile Picture" 
                                            class="img-fluid rounded-circle data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Tooltip on top"" 
                                            width="40" 
                                            height="40"
                                            title="{{ $name }}" 
                                            data-bs-toggle="tooltip"
                                        >
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach           
    </div>
</div>

<script>
    function confirmDelete(projectName, projectId) {
        var input = document.getElementById('project-name-' + projectId).value;
        if (input === projectName) {
            return true;
        } else {
            alert('Project name does not match. Deletion cancelled.');
            return false;
        }
    }
</script>
@endsection
