@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Messages for {{ $project->name }}</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="mb-4">
        @foreach ($messages as $message)
            <div class="card mb-2">
                <div class="card-body">
                    <h5 class="card-title">{{ $message->user->name }}</h5>
                    <p class="card-text">{{ $message->content }}</p>
                    <small class="text-muted">{{ $message->created_at->diffForHumans() }}</small>
                </div>
            </div>
        @endforeach
    </div>

    <form action="{{ route('messages.store', $project) }}" method="POST">
        @csrf
        <div class="mb-3">
            <textarea class="form-control" name="content" rows="3" required placeholder="Type your message here..."></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Send Message</button>
    </form>
</div>
@endsection