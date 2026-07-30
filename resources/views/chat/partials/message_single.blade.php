@php
    $isMe = $message->sender_id === auth()->id();
    $avatarIndex = (($message->sender_id % 8) + 1);
@endphp

<div class="timeline__item {{ $isMe ? 'timeline--right' : '' }} margin-bottom-20" id="msg-{{ $message->id }}">
    <div class="user">
        <img src="{{ asset('assets/img/users/user_' . $avatarIndex . '.jpg') }}" alt="{{ $message->sender->name }}">
    </div>
    <div class="content {{ $isMe ? 'bg-light border-primary' : '' }}">
        <div class="title">
            <a href="#" class="{{ $isMe ? 'text-primary font-weight-bold' : 'text-info font-weight-bold' }}">
                {{ $isMe ? 'You' : $message->sender->name }}
            </a>
            @if(!$isMe)
                <span class="badge badge-secondary ml-1">{{ $message->sender->role_display }}</span>
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
