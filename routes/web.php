<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PasswordController;
use Illuminate\Support\Facades\Route;

// Route for the homepage
Route::get('/', function () {
    return view('auth.login');
});

// Authentication routes
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Routes for authenticated users
Route::middleware('auth')->group(function () {

    // User management routes for super admins
    Route::middleware(['role:super_admin'])->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
            // Route for removing a user from a project
        Route::delete('/projects/{project}/users/{user}', [ProjectController::class, 'removeUser'])
            ->name('projects.removeUser')
            ->middleware('auth', 'role:super_admin');
    });

    // Profile picture routes
    Route::get('/profile/edit', [UserController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/picture', [UserController::class, 'updateProfilePicture'])->name('profile.picture.update');


    // Project-related routes
    Route::resource('projects', ProjectController::class);
    Route::get('/projects/create', [ProjectController::class, 'create'])
        ->name('projects.create')
        ->middleware('auth', 'role:super_admin');

    //Project Files
    Route::post('/projects/{project}/files', [ProjectController::class, 'storeFiles'])->name('projects.storeFiles');
    Route::delete('/projects/files/{file}', [ProjectController::class, 'deleteFile'])->name('projects.deleteFile');
    Route::post('/projects/{project}/add-user', [ProjectController::class, 'addUser'])->name('projects.addUser');

    // Task routes within a project
    Route::prefix('projects/{project}')->group(function () {
        Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
        Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
        Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    });

    Route::prefix('tasks/{task}')->group(function () {
        Route::get('/', [TaskController::class, 'show'])->name('tasks.show');
        Route::get('/edit', [TaskController::class, 'edit'])->name('tasks.edit');
        Route::patch('/', [TaskController::class, 'update'])->name('tasks.update');
        Route::delete('/', [TaskController::class, 'destroy'])->name('tasks.destroy');
        Route::post('/complete', [TaskController::class, 'complete'])->name('tasks.complete');
    });

    // Messaging routes within a project
    Route::prefix('projects/{project}')->group(function () {
        Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
        Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
        Route::post('/messages/{message}/reply', [MessageController::class, 'reply'])
            ->name('messages.reply');
    });

    // Profile management routes
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/delete', [ProfileController::class, 'destroy'])->name('profile.destroy');



    // Redirect to projects index after login
    Route::get('/home', [ProjectController::class, 'index'])->name('home'); 

    // Staff-only routes (if any)
    Route::middleware(['role:staff'])->group(function () {
        // Additional staff routes can be added here
    });

    // Guest-only routes (if any)
    Route::middleware(['role:guest'])->group(function () {
        // Define guest routes here
    });
});
