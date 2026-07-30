<x-layouts.app title="Team Chat - ProjectPilot">
    <div class="page-heading">
        <div class="page-heading__container">
            <h1 class="title">Team Messenger & Chat</h1>
            <p class="caption">Real-time team messaging and developer collaboration</p>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <!-- LEFT PANEL: LOGGED IN USER & TEAM CONTACTS -->
            <div class="col-12 col-lg-4 col-xl-3">
                <!-- LOGGED IN USER CARD -->
                <div class="card margin-bottom-20">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="user user--bordered user--xlg margin-bottom-15">
                                    <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" style="object-fit: cover; width: 60px; height: 60px; border-radius: 50%;">
                                    <div class="user__name">
                                        <strong>{{ auth()->user()->name }}</strong><br>
                                        <span class="badge badge-primary margin-top-5">{{ auth()->user()->role_display }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class="text-muted text-sm margin-bottom-10"><i class="fa fa-envelope-o margin-right-5"></i> {{ auth()->user()->email }}</p>
                    </div>
                </div>

                <!-- TEAM CONTACTS LIST -->
                <div class="card margin-bottom-20">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Team Contacts</h5>
                        <span class="badge badge-secondary">{{ $users->count() }} Members</span>
                    </div>
                    <div class="card-body p-0 scroll" style="max-height: 480px; overflow-y: auto;">
                        <div class="list-group list-group-flush">
                            @forelse($users as $u)
                                @php
                                    $isActive = $activeUser && $activeUser->id === $u->id;
                                @endphp
                                <a href="{{ route('chat.index', ['user_id' => $u->id]) }}" class="list-group-item list-group-item-action {{ $isActive ? 'active bg-light border-left border-primary' : '' }} p-3">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="user user--bordered user--lg m-0">
                                            <img src="{{ $u->avatar_url }}" alt="{{ $u->name }}" style="object-fit: cover; width: 45px; height: 45px; border-radius: 50%;">
                                            <div class="user__name">
                                                <strong class="{{ $isActive ? 'text-primary' : 'text-dark' }}">{{ $u->name }}</strong><br>
                                                <small class="text-muted">{{ $u->role_display }}</small>
                                            </div>
                                        </div>
                                        @if(isset($u->unread_count) && $u->unread_count > 0)
                                            <span class="badge badge-pill badge-danger font-weight-bold">{{ $u->unread_count }}</span>
                                        @endif
                                    </div>
                                </a>
                            @empty
                                <div class="p-3 text-muted text-center">No other team members available.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT PANEL: CHAT FEED & MESSAGE INPUT -->
            <div class="col-12 col-lg-8 col-xl-9">
                <div class="card">
                    @if($activeUser)
                        <!-- ACTIVE CHAT HEADER -->
                        <div class="card-header d-flex align-items-center justify-content-between bg-white border-bottom py-3">
                            <div class="user user--bordered user--lg m-0">
                                <img src="{{ $activeUser->avatar_url }}" alt="{{ $activeUser->name }}" style="object-fit: cover; width: 45px; height: 45px; border-radius: 50%;">
                                <div class="user__name">
                                    <h5 class="mb-0 text-bold">{{ $activeUser->name }}</h5>
                                    <span class="text-muted text-sm">{{ $activeUser->email }} &bull; <strong class="text-info">{{ $activeUser->role_display }}</strong></span>
                                </div>
                            </div>
                            <div>
                                <span class="badge badge-success px-2 py-1"><i class="fa fa-circle margin-right-5"></i> Active Now</span>
                            </div>
                        </div>

                        <!-- MESSAGES TIMELINE BOX -->
                        <div class="card-body bg-light position-relative scroll" id="chat-messages-container" style="height: 460px; overflow-y: auto;">
                            <div class="timeline margin-top-10" id="chat-messages-feed">
                                @include('chat.partials.messages_feed', ['messages' => $messages])
                            </div>
                        </div>

                        <!-- CHAT INPUT FORM -->
                        <div class="card-footer bg-white p-3 border-top">
                            <form id="chat-form" action="{{ route('chat.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="receiver_id" id="receiver_id" value="{{ $activeUser->id }}">
                                
                                <div class="input-group">
                                    <input type="text" name="message" id="message-input" class="form-control form-control-lg" placeholder="Type your message to {{ $activeUser->name }}..." autocomplete="off" required>
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary btn-lg" id="btn-send">
                                            <i class="fa fa-paper-plane margin-right-5"></i> Send
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="card-body py-5 text-center text-muted">
                            <i class="fa fa-comments fa-4x mb-3 text-secondary"></i>
                            <h4>Select a contact to start chatting</h4>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($activeUser)
    <script>
        // Global Message Deletion Handler
        window.deleteChatMessage = function(messageId) {
            if (!confirm("Are you sure you want to delete this message?")) {
                return;
            }

            fetch(`/chat/${messageId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const msgElem = document.getElementById(`msg-${messageId}`);
                    if (msgElem) {
                        msgElem.style.transition = "all 0.3s ease";
                        msgElem.style.opacity = "0";
                        msgElem.style.transform = "scale(0.9)";
                        setTimeout(() => msgElem.remove(), 300);
                    }
                } else {
                    alert(data.message || "Failed to delete message.");
                }
            })
            .catch(err => {
                console.error("Delete Error:", err);
                alert("Error deleting message.");
            });
        };

        document.addEventListener("DOMContentLoaded", function () {
            const chatContainer = document.getElementById('chat-messages-container');
            const chatFeed = document.getElementById('chat-messages-feed');
            const chatForm = document.getElementById('chat-form');
            const messageInput = document.getElementById('message-input');
            const btnSend = document.getElementById('btn-send');
            const activeUserId = "{{ $activeUser->id }}";

            // Scroll to bottom of chat
            function scrollToBottom() {
                if (chatContainer) {
                    chatContainer.scrollTop = chatContainer.scrollHeight;
                }
            }
            scrollToBottom();

            // Handle AJAX Message Submit
            chatForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const messageText = messageInput.value.trim();
                if (!messageText) return;

                btnSend.disabled = true;

                fetch("{{ route('chat.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        receiver_id: activeUserId,
                        message: messageText
                    })
                })
                .then(response => response.json())
                .then(data => {
                    btnSend.disabled = false;
                    if (data.status === 'success') {
                        messageInput.value = '';
                        // Append message
                        const emptyNotice = chatFeed.querySelector('.py-5');
                        if (emptyNotice) {
                            emptyNotice.remove();
                        }
                        chatFeed.insertAdjacentHTML('beforeend', data.html);
                        scrollToBottom();
                    }
                })
                .catch(err => {
                    btnSend.disabled = false;
                    console.error("Chat Error:", err);
                });
            });

            // Poll for new messages every 3 seconds
            setInterval(function () {
                fetch(`/chat/fetch/${activeUserId}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        chatFeed.innerHTML = data.html;
                    }
                })
                .catch(err => console.error("Poll Error:", err));
            }, 3000);
        });
    </script>
    @endif
</x-layouts.app>
