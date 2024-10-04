<h2 class="mt-4">Messages</h2>
<form action="{{ route('messages.store', $project) }}" method="POST" class="mb-3">
    @csrf
    <input type="hidden" name="tab" value="messages">
    <div class="mb-3">
        <textarea class="form-control" name="content" rows="3" required placeholder="Type your message here..."></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Send Message</button>
</form>
<div class="row">
    <!-- Messages List -->
    <div class="col-md-7">
        <h3>Messages</h3>
        <div class="list-group" style="max-height: 400px; overflow-y: auto;">
            @foreach ($project->messages->whereNull('parent_id')->sortByDesc('created_at') as $message)
                <a href="#message-{{ $message->id }}" class="list-group-item list-group-item-action" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="message-{{ $message->id }}" data-message-id="{{ $message->id }}">
                    <div class="d-flex w-100 justify-content-start">
                        <img src="{{ $message->user->profile_picture ? asset('storage/' . $message->user->profile_picture) : asset('path/to/default-image.jpg') }}" alt="Profile Picture" class="rounded-circle me-2" style="width: 40px; height: 40px;">
                        <div>
                            <strong>{{ $message->user->name }}</strong>
                            <small class="text-muted d-block">{{ $message->created_at->format('d/m/y H:i') }}</small>
                        </div>
                    </div>
                    <p class="mb-1">{{ Str::limit($message->content, 100) }}</p>
                </a>
            @endforeach
        </div>
    </div>
    <!-- Threads and Replies -->
    <div class="col-md-5">
        <h3>Threads</h3>
        <div id="threads" style="max-height: 400px; overflow-y: auto;">
            @foreach ($project->messages->whereNull('parent_id')->sortByDesc('created_at') as $message)
                <div class="collapse" id="message-{{ $message->id }}">
                    <div class="card mb-2">
                        <div class="card-body">
                            <div class="d-flex">
                                <img src="{{ $message->user->profile_picture ? asset('storage/' . $message->user->profile_picture) : asset('path/to/default-image.jpg') }}" alt="Profile Picture" class="rounded-circle me-2" style="width: 40px; height: 40px;">
                                <div>
                                    <h5 class="card-title">{{ $message->user->name }}</h5>
                                    <p class="card-text">{{ $message->content }}</p>
                                    <small class="text-muted">{{ $message->created_at->format('d/m/y H:i') }}</small>
                                </div>
                            </div>
                            <div class="mt-3">
                                @foreach ($message->replies->sortBy('created_at') as $reply)
                                    <div class="card mb-2 ms-4">
                                        <div class="card-body">
                                            <div class="d-flex">
                                                <img src="{{ $reply->user->profile_picture ? asset('storage/' . $reply->user->profile_picture) : asset('path/to/default-image.jpg') }}" alt="Profile Picture" class="rounded-circle me-2" style="width: 30px; height: 30px;">
                                                <div>
                                                    <h6 class="card-subtitle mb-2 text-muted">{{ $reply->user->name }}</h6>
                                                    <p class="card-text">{{ $reply->content }}</p>
                                                    <small class="text-muted">{{ $reply->created_at->format('d/m/y H:i') }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                <form action="{{ route('messages.reply', ['project' => $project->id, 'message' => $message->id]) }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <textarea class="form-control" name="reply_content" rows="2" placeholder="Type your reply here..."></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-sm">Reply</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
<script>
    function showMessage(messageId) {
        // Hide all threads
        document.querySelectorAll('#threads .collapse').forEach(function(thread) {
            thread.classList.remove('show');
        });

        // Show the selected thread
        document.getElementById('message-' + messageId).classList.add('show');
    }

    function handleReply(event, messageId) {
        event.preventDefault();

        const form = event.target;
        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update the threads section with the new HTML
                document.getElementById('threads').innerHTML = data.updatedThreadsHtml;
                // Scroll to the latest message
                document.querySelector(`#message-${messageId}`).scrollIntoView({ behavior: 'smooth' });
            }
        })
        .catch(error => console.error('Error:', error));
    }

        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const selectedMessageId = urlParams.get('selectedMessageId');

            if (selectedMessageId) {
                // Automatically collapse other threads
                document.querySelectorAll('.collapse').forEach(collapse => {
                    if (collapse.id !== `message-${selectedMessageId}`) {
                        collapse.classList.remove('show');
                    }
                });

                // Scroll to the selected message
                const selectedCollapse = document.getElementById(`message-${selectedMessageId}`);
                if (selectedCollapse) {
                    selectedCollapse.classList.add('show');
                    selectedCollapse.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        });
</script>
