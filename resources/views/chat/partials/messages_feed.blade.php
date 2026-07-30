@forelse($messages as $message)
    @include('chat.partials.message_single', ['message' => $message])
@empty
    <div class="text-center py-5 text-muted">
        <i class="fa fa-comments-o fa-3x mb-3 text-secondary d-block"></i>
        <h5>No messages yet!</h5>
        <p class="text-sm mb-0">Send a message below to start conversation.</p>
    </div>
@endforelse
