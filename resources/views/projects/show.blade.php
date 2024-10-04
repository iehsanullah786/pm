@extends('layouts.app')

@section('content')
<div class="container">
    @if (session()->has('success'))
        <div id="success-message" class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1> {{ $project->name }}</h1>
    </div>

    <div class="row">
        <div class="col-md-12">
            <!-- Tabs for Todos, Messages, Files, and Users -->
            <ul class="nav nav-tabs" id="projectTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ request('tab') === 'todos' ? 'active' : (request('tab') === null ? 'active' : '') }}" id="todos-tab" data-bs-toggle="tab" href="#todos" role="tab" aria-controls="todos" aria-selected="{{ request('tab') === 'todos' ? 'true' : (request('tab') === null ? 'true' : 'false') }}">Todos</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ request('tab') === 'messages' ? 'active' : '' }}" id="messages-tab" data-bs-toggle="tab" href="#messages" role="tab" aria-controls="messages" aria-selected="{{ request('tab') === 'messages' ? 'true' : 'false' }}">Messages</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ request('tab') === 'files' ? 'active' : '' }}" id="files-tab" data-bs-toggle="tab" href="#files" role="tab" aria-controls="files" aria-selected="{{ request('tab') === 'files' ? 'true' : 'false' }}">Files</a>
                </li>
                @role('super_admin')
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ request('tab') === 'users' ? 'active' : '' }}" id="users-tab" data-bs-toggle="tab" href="#users" role="tab" aria-controls="users" aria-selected="{{ request('tab') === 'users' ? 'true' : 'false' }}">Users</a>
                </li>
                @endrole
            </ul>

            <div class="tab-content" id="projectTabsContent">
                <!-- Todos Tab -->
                <div class="tab-pane fade {{ request('tab') === 'todos' ? 'show active' : (request('tab') === null ? 'show active' : '') }}" id="todos" role="tabpanel" aria-labelledby="todos-tab">
                    @include('projects.partials.todos', ['project' => $project])
                </div>
                
                <!-- Messages Tab -->
                <div class="tab-pane fade {{ request('tab') === 'messages' ? 'show active' : '' }}" id="messages" role="tabpanel" aria-labelledby="messages-tab">
                    @include('projects.partials.messages', ['project' => $project])
                </div>
                
                <!-- Files Tab -->
                <div class="tab-pane fade {{ request('tab') === 'files' ? 'show active' : '' }}" id="files" role="tabpanel" aria-labelledby="files-tab">
                    @include('projects.partials.files', ['project' => $project])
                </div>

                <!-- Users Tab (Super Admin Only) -->
                @role('super_admin')
                <div class="tab-pane fade {{ request('tab') === 'users' ? 'show active' : '' }}" id="users" role="tabpanel" aria-labelledby="users-tab">
                    @include('projects.partials.users', ['project' => $project])
                </div>
                @endrole
            </div>
        </div>
    </div>
</div>

<script>
    // Retain active tab after page refresh
    document.addEventListener('DOMContentLoaded', function () {
        var urlParams = new URLSearchParams(window.location.search);
        var activeTab = urlParams.get('tab');

        if (activeTab) {
            var tabElement = document.querySelector(`#${activeTab}-tab`);
            if (tabElement) {
                tabElement.click();
            }
        }
    });

    // Success message timeout
    setTimeout(function() {
        const successMessage = document.getElementById('success-message');
        if (successMessage) {
            successMessage.style.display = 'none';
        }
    }, 3000);
</script>
@endsection
