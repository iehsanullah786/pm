<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function create(Project $project)
    {
        return view('tasks.create', compact('project'));
    }

    public function store(Request $request, Project $project)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'due_date' => 'nullable|date',
            'due_time' => 'nullable|date_format:H:i',
        ]);

        $task = new Task();
        $task->title = $request->title;

        // Set due_date only if both due_date and due_time are provided
        if ($request->due_date && $request->due_time) {
            $task->due_date = $request->due_date . ' ' . $request->due_time;
        } else {
            $task->due_date = null; // Or you can leave it unset
        }

        $task->project_id = $project->id;
        $task->save();

        return redirect()->route('projects.show', $project)->with('success', 'Task created successfully.');
    }

    public function update(Request $request, Task $task)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'due_date' => 'nullable|date',
            'due_time' => 'nullable|date_format:H:i',
        ]);

        $task->title = $request->title;

        // Set due_date only if either due_date or both due_date and due_time are provided
        if ($request->due_date) {
            if ($request->due_time) {
                // Combine date and time if both are provided
                $task->due_date = $request->due_date . ' ' . $request->due_time;
            } else {
                // Set only the due date if time is not provided
                $task->due_date = $request->due_date;
            }
        } else {
            // If no due_date is provided, set it to null
            $task->due_date = null;
        }

        $task->save();

        return redirect()->route('projects.show', $task->project)->with('success', 'Task updated successfully.');
    }

    
    public function edit(Task $task)
    {
        // Return the edit view with the task data
        return view('tasks.edit', compact('task'));
    }

    public function complete(Request $request, Task $task)
    {
        // Validate the request
        $request->validate([
            'completed' => 'required|boolean',
        ]);

        // Update the task completion status
        $task->completed = $request->completed;
        $task->save();

        // Redirect back to the project view with a success message
        return redirect()->route('projects.show', $task->project)->with('success', 'Task status updated successfully.');
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->back()->with('success', 'Task deleted successfully.');
    }
}