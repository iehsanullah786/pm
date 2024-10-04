@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h1 class="mb-4">Edit Profile</h1>

                <!-- Display Flash Message -->
                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif

                <!-- Profile Picture -->
                <div class="text-center mb-4">
                    @if (auth()->user()->profile_picture && Storage::disk('public')->exists(auth()->user()->profile_picture))
                        <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}" class="img-fluid rounded-circle mx-auto d-block" width="200" />
                    @else
                        <img src="{{ asset('img/default-profile-picture.jpg') }}" alt="Default Profile Picture" class="img-fluid rounded-circle mx-auto d-block" width="200">
                    @endif

                    <br>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#uploadPictureModal">
                        <i class="fas fa-edit"></i> Upload New Image
                    </button>
   
                    <!-- Upload Picture Modal -->
                    <div class="modal fade" id="uploadPictureModal" tabindex="-1" aria-labelledby="uploadPictureModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="uploadPictureModalLabel">Upload Profile Picture</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ route('profile.picture.update') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <input type="file" name="profile_picture" id="profile_picture" class="form-control">
                                        <button type="submit" class="btn btn-primary mt-3">Upload</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Update Profile Form -->
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Name Field -->
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email Field -->
                    <div class="form-group mt-3">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div class="form-group mt-3">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" class="form-control">
                        @error('password')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Confirm Password Field -->
                    <div class="form-group mt-3">
                        <label for="password_confirmation">Confirm Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                    </div>

                    <!-- Update Button -->
                    <button type="submit" class="btn btn-primary mt-3">Update Profile</button>
                </form>
            </div>
        </div>
    </div>
@endsection
