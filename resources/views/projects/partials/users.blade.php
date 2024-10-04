<!-- Users Tab -->
@role('super_admin')
<div class="tab-pane fade {{ request('tab') === 'users' ? 'show active' : '' }}" id="users" role="tabpanel" aria-labelledby="users-tab">
    <h2 class="mt-4">Users</h2>

    <!-- Add User Form -->
    <form action="{{ route('projects.addUser', $project) }}" method="POST" class="mb-3">
        @csrf
        <div class="form-group">
            <label for="user_id">Add User to Project</label>
            <select name="user_id" id="user_id" class="form-control" required>
                <option value="" disabled selected>Select User</option>
                @foreach($nonSuperAdminUsers as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Add User</button>
    </form>

    <!-- List of Users in Project -->
    <h3 class="mt-4">Project Users</h3>
    <ul class="list-group">
        @foreach ($project->users as $user)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                {{ $user->name }}
                <form action="{{ route('projects.removeUser', [$project, $user]) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to remove this user from the project?');">Remove</button>
                </form>
            </li>
        @endforeach
    </ul>
</div>
@endrole
