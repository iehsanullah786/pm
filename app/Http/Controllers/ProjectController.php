<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class ProjectController extends Controller
{
    // Show the list of projects
    public function index()
    {
        $user = auth()->user();
        $projects = Project::with('users')->get(); // Eager load users

        if ($user->hasRole('super_admin')) {
            $projects = Project::all();
        } else {
            $projects = $user->projects;
        }

        // Fetch super admin and non-super-admin users
        $superAdmins = User::role('super_admin')->get();
        $nonSuperAdminUsers = User::role('staff')->get();
        \Log::info('Super Admins:', $superAdmins->toArray());
        return view('projects.index', compact('projects', 'nonSuperAdminUsers', 'superAdmins'));
    }

    // Show the form for creating a new project
    public function create()
    {
        return view('projects.create');
    }

    // Store a newly created project
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'files.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048', // Adjust as needed
        ]);

        $project = new Project();
        $project->name = $request->name;
        $project->user_id = Auth::id(); // Associate project with the authenticated user
        $project->save();

        // Handle file uploads
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('public/project_files');
                $fileName = basename($path);

                $projectFile = new ProjectFile();
                $projectFile->project_id = $project->id;
                $projectFile->file_name = $fileName;
                $projectFile->file_path = $path;
                $projectFile->save();
            }
        }

        return redirect()->route('projects.index')->with('success', 'Project created successfully.');
    }

    // Show a specific project
    public function show(Project $project, Request $request)
    {
        $user = auth()->user();
        
        if ($user->hasRole('staff')) {
            // Check if the user is assigned to the project
            if (!$project->users->contains($user)) {
                abort(403, 'Unauthorized action.');
            }
            
        }

        // Eager load tasks, messages, and files with user relationship
        $project->load(['tasks' => function ($query) {
            $query->orderBy('completed'); // Incomplete tasks first
        }, 'messages.user', 'files']);
        
        // Fetch non-super-admin users who are not part of the project
        $nonSuperAdminUsers = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', 'super_admin');
        })->whereDoesntHave('projects', function ($query) use ($project) {
            $query->where('project_id', $project->id);
        })->get();
        
        // Pass the project and nonSuperAdminUsers to the view
        return view('projects.show', compact('project', 'nonSuperAdminUsers'));
    }

    // Show the form for editing a project
    public function edit(Project $project)
    {
        $user = auth()->user();
        
        if ($user->hasRole('staff')) {
            // Check if the user is assigned to the project
            if (!$project->users->contains($user)) {
                abort(403, 'Unauthorized action.');
            }
        }
        
        return view('projects.edit', compact('project'));
    }

    // Update a specific project
    public function update(Request $request, Project $project)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'files.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048', // Adjust as needed
        ]);

        $user = auth()->user();
        
        if ($user->hasRole('staff')) {
            // Check if the user is assigned to the project
            if (!$project->users->contains($user)) {
                abort(403, 'Unauthorized action.');
            }
        }

        $project->name = $request->name;
        $project->save();
        
        // Handle file uploads
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('public/project_files');
                $fileName = basename($path);
                
                $projectFile = new ProjectFile();
                $projectFile->project_id = $project->id;
                $projectFile->file_name = $fileName;
                $projectFile->file_path = $path;
                $projectFile->user_id = $user->id;
                $projectFile->save();
            }
        }

        return redirect()->route('projects.show', $project->id)->with('success', 'Project updated successfully.');
    }

    public function storeFiles(Request $request, Project $project)
    {
        $request->validate([
            'files.*' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:20480',
        ]);

        foreach ($request->file('files') as $file) {
            $originalName = $file->getClientOriginalName();
            $path = $file->store('files', 'public');

            $project->files()->create([
                'file_name' => $originalName,
                'file_path' => $path,
                'user_id' => auth()->id(),
            ]);
        }

        return redirect()->route('projects.show', ['project' => $project->id, 'tab' => 'files'])
            ->with('success', 'Files uploaded successfully!');
    }

    public function addUser(Request $request, Project $project)
    {
        $userId = $request->input('user_id');
        $user = User::find($userId);

        if ($user) {
            $project->users()->attach($user);
            return redirect()->route('projects.show', ['project' => $project->id, 'tab' => 'users'])
                         ->with('success', 'User added successfully.');
        }

            return redirect()->route('projects.show', ['project' => $project->id, 'tab' => 'users'])
                ->with('error', 'User could not be added.');   
        }

        public function removeUser(Project $project, User $user)
        {
            // Detach the user from the project
            $project->users()->detach($user);

            // Redirect to the project show page with 'users' tab selected and success message
            return redirect()->route('projects.show', ['project' => $project->id, 'tab' => 'users'])
                            ->with('success', 'User removed successfully.');
        }

        // Delete a specific project
        public function destroy(Project $project)
        {
            // Check if the user has access to delete this project
            $user = auth()->user();
            
            if ($user->hasRole('staff')) {
                if (!$project->users->contains($user)) {
                    abort(403, 'Unauthorized action.');
                }
            }

            // Delete associated files
            foreach ($project->files as $file) {
                Storage::delete($file->file_path);
                $file->delete();
            }

            $project->delete();
            return redirect()->route('projects.index')
                ->with('success', 'Project deleted successfully!');
        }

        public function deleteFile(ProjectFile $file)
        {
            // Check if the user has access to delete this file
            $project = $file->project;
            $user = auth()->user();
            
            if ($user->hasRole('staff')) {
                if (!$project->users->contains($user)) {
                    abort(403, 'Unauthorized action.');
                }
            }

            Storage::delete($file->file_path);
            $file->delete();

            return redirect()->route('projects.show', ['project' => $project->id, 'tab' => 'files'])
                ->with('success', 'File deleted successfully!');
        }
}
