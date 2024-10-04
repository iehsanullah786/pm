<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    // Display the user's profile data
    public function show()
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You need to login first');
        }

        $user = Auth::user();

        // Log user data for debugging
        Log::info('User data for profile:', ['user' => $user]);

        // Return a simple view with user data
        return view('profile', ['user' => $user]);
    }

    // Show the form for editing the user's profile
    public function edit()
    {
        $user = Auth::user(); // Get the authenticated user
        return view('profile.edit', compact('user')); // Pass the user data to the view
    }

    // Update the user's profile
    public function update(Request $request)
    {

        $user = Auth::user(); // Get the authenticated user

        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Update the user profile
        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = bcrypt($request->input('password'));
        }
        
        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully.');
    }

    // Delete the user's profile
    public function destroy()
    {
        $user = Auth::user();
        $user->delete(); // Delete the user

        return redirect('/')->with('success', 'Profile deleted successfully.');
    }
}
