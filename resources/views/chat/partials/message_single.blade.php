@php
    $isMe = $message->sender_id === auth()->id();
    $canDelete = $isMe || (auth()->check() && auth()->user()->isAdmin());
    $senderAvatar = $message->sender ? $message->sender->avatar_url : asset('assets/img/users/user_' . (($message->sender_id % 8) + 1) . '.jpg');
@endphp

<div class="timeline__item {{ $isMe ? 'timeline--right' : '' }} margin-bottom-20" id="msg-{{ $message->id }}">
    <div class="user">
        <img src="{{ $senderAvatar }}" alt="{{ $message->sender->name ?? 'User' }}" style="object-fit: cover; width: 42px; height: 42px; border-radius: 50%;">
    </div>
    <div class="content {{ $isMe ? 'bg-light border-primary' : '' }} position-relative">
        <div class="title d-flex justify-content-between align-items-center mb-1">
            <div>
                <a href="#" class="{{ $isMe ? 'text-primary font-weight-bold' : 'text-info font-weight-bold' }}">
                    {{ $isMe ? 'You' : ($message->sender->name ?? 'User') }}
                </a>
                @if(!$isMe && $message->sender)
                    <span class="badge badge-secondary ml-1">{{ $message->sender->role_display }}</span>
                @endif
            </div>

            @if($canDelete)
                <button type="button" 
                        onclick="deleteChatMessage({{ $message->id }})" 
                        class="btn btn-link text-danger p-0 ml-2 border-0 opacity-75 hover-opacity-100" 
                        style="font-size: 0.85rem; line-height: 1;" 
                        title="Delete Message">
                    <i class="fa fa-trash"></i>
                </button>
            @endif
        </div>

        <p class="margin-bottom-10" style="font-size: 1.05rem; line-height: 1.5; white-space: pre-wrap;">{{ $message->message }}</p>

        <div class="clearfix">
            <span class="pull-right text-muted text-sm">
                <i class="fa fa-clock-o margin-right-5"></i> {{ $message->created_at->diffForHumans() }}
                @if($isMe)
                    <i class="fa {{ $message->is_read ? 'fa-check-circle text-success' : 'fa-check text-muted' }} margin-left-5" title="{{ $message->is_read ? 'Read' : 'Delivered' }}"></i>
                @endif
            </span>
        </div>
    </div>
</div>
