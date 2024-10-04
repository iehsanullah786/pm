<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index()
    {
        // Fetch all users from the database
        $users = User::paginate(10);
        return view('users.index', compact('users'));
    }

    // Show the form to create a new user
    public function create()
    {
        // Fetch roles to pass to the view
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    // Store a newly created user
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|exists:roles,name',
        ]);

        if ($validator->fails()) {
            return redirect()->route('users.create')
                             ->withErrors($validator)
                             ->withInput();
        }

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign the role to the user
        $user->assignRole($request->role);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();

        return view('users.edit', compact('user', 'roles'));
    }

    // Update the specified user
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|confirmed|min:8',
            'role' => 'required|string|exists:roles,name',
        ]);
    
        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->email = $request->email;
    
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
    
        $user->save();
    
        // Remove all roles and assign the new role
        $user->syncRoles($request->role);
    
        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }    

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }

    // Update the user's profile picture
    public function updateProfilePicture(Request $request)
    {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = auth()->user();
        $oldProfilePicture = $user->profile_picture;

        // Delete the old profile picture if it exists
        if ($oldProfilePicture && Storage::disk('public')->exists($oldProfilePicture)) {
            Storage::disk('public')->delete($oldProfilePicture);
        } else {
            \Log::warning("Old profile picture not found or already deleted: " . $oldProfilePicture);
        }

        // Store the new profile picture
        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            $path = $file->store('profile_pictures', 'public');
     
            $user->profile_picture = $path; // Save only the relative path
            \Log::info("Uploaded new profile picture: " . $path);
        }

        $user->save();
        return redirect()->route('profile.edit')->with('status', 'Profile picture updated successfully.');
    }

}
