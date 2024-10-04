<div class="d-flex justify-content-between align-items-center mb-2 mt-2">
    <h3>Todos</h3>
    <a href="{{ route('tasks.create', $project) }}" class="btn btn-primary btn-sm">Add Todo</a>
</div><br>
<ul class="list-group">
    @foreach ($project->tasks->where('completed', false) as $task)
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center flex-grow-1">
                <form action="{{ route('tasks.complete', $task) }}" method="POST" class="me-2">
                    @csrf
                    <input type="hidden" name="completed" value="1">
                    <input type="hidden" name="tab" value="todos">
                    <input type="checkbox" class="form-check-input" id="task-{{ $task->id }}" onchange="this.form.submit()">
                </form>
                <span id="task-title-{{ $task->id }}">{{ $task->title }}</span>
            </div>
            <div>
                @if($task->due_date)
                    <small class="text-muted">
                        Due: {{ \Carbon\Carbon::parse($task->due_date)->format('d/m/y H:i') }}
                    </small>
                @endif
                <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-info">Edit</a>
                <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this task?');">Delete</button>
                </form>
            </div>
        </li>
    @endforeach
</ul>
<h3 class="mt-4">Completed</h3>
<ul class="list-group">
    @foreach ($project->tasks->where('completed', true) as $task)
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center flex-grow-1">
                <form action="{{ route('tasks.complete', $task) }}" method="POST" class="me-2">
                    @csrf
                    <input type="hidden" name="completed" value="0">
                    <input type="checkbox" class="form-check-input" id="task-{{ $task->id }}" checked onchange="this.form.submit()">
                </form>
                <span id="task-title-{{ $task->id }}" class="text-decoration-line-through">{{ $task->title }}</span>
            </div>
            <div>
                @if($task->due_date)
                    <small class="text-muted">
                        Due: {{ \Carbon\Carbon::parse($task->due_date)->format('d/m/y H:i') }}
                    </small>
                @endif
                <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-info">Edit</a>
                <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this task?');">Delete</button>
                </form>
            </div>
        </li>
    @endforeach
</ul>
