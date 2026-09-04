<x-layouts.app title="Notifications - ProjectPilot">
    <div class="page-heading">
        <div class="page-heading__container">
            <h1 class="title">Notification Center</h1>
            <p class="caption">Stay updated with task assignments, status changes, and team discussions</p>
        </div>
        <div class="page-heading__container float-right d-none d-sm-block">
            @if(auth()->user()->unreadNotifications->count() > 0)
                <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="fa fa-check-circle margin-right-5"></i> Mark All as Read
                    </button>
                </form>
            @endif
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Notifications</li>
            </ol>
        </nav>
    </div>

    <div class="container-fluid">
        <!-- FILTER TABS -->
        <div class="nav nav-pills nav-fill mb-3 bg-white p-2 rounded border shadow-xs">
            <a class="nav-item nav-link {{ empty(request('filter')) ? 'active bg-primary text-white font-weight-bold' : 'text-dark' }}" href="{{ route('notifications.index') }}">
                <i class="fa fa-bell margin-right-5"></i> All Notifications
                <span class="badge badge-light ml-1">{{ auth()->user()->notifications()->count() }}</span>
            </a>
            <a class="nav-item nav-link {{ request('filter') === 'unread' ? 'active bg-primary text-white font-weight-bold' : 'text-dark' }}" href="{{ route('notifications.index', ['filter' => 'unread']) }}">
                <i class="fa fa-envelope-o text-danger margin-right-5"></i> Unread Only
                <span class="badge {{ auth()->user()->unreadNotifications->count() > 0 ? 'badge-danger' : 'badge-light' }} ml-1">{{ auth()->user()->unreadNotifications->count() }}</span>
            </a>
        </div>

        <!-- NOTIFICATIONS LIST -->
        <div class="card shadow-sm border">
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($notifications as $notification)
                        @php
                            $isUnread = $notification->unread();
                            $data = $notification->data;
                            $icon = $data['icon'] ?? 'fa-bell';
                            $iconColor = $data['icon_color'] ?? 'text-primary';
                        @endphp
                        <div class="list-group-item list-group-item-action p-3 {{ $isUnread ? 'bg-light border-left border-primary' : '' }}" style="{{ $isUnread ? 'border-left-width: 4px !important;' : '' }}">
                            <div class="d-flex align-items-center justify-content-between">
                                <a href="{{ route('notifications.read', $notification->id) }}" class="d-flex align-items-center flex-grow-1 text-decoration-none text-dark">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center mr-3 bg-white border shadow-xs" style="width: 46px; height: 46px; min-width: 46px;">
                                        <i class="fa {{ $icon }} {{ $iconColor }} fa-lg"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center">
                                            <h6 class="mb-0 {{ $isUnread ? 'font-weight-bold text-primary' : 'text-dark' }}">{{ $data['title'] ?? 'Notification' }}</h6>
                                            @if($isUnread)
                                                <span class="badge badge-pill badge-primary ml-2" style="font-size: 0.65rem;">NEW</span>
                                            @endif
                                        </div>
                                        <p class="mb-1 text-muted small" style="line-height: 1.4;">{{ $data['message'] ?? '' }}</p>
                                        <small class="text-muted">
                                            <i class="fa fa-clock-o mr-1"></i> {{ $notification->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                </a>
                                <div class="ml-3 d-flex align-items-center">
                                    @if($isUnread)
                                        <a href="{{ route('notifications.read', $notification->id) }}" class="btn btn-sm btn-light text-primary mr-1" title="Open and mark read">
                                            <i class="fa fa-external-link"></i>
                                        </a>
                                    @endif
                                    <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this notification?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-link text-danger p-0" title="Delete notification">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted">
                            <i class="fa fa-bell-slash-o fa-3x text-muted mb-3"></i>
                            <h5>No notifications found</h5>
                            <p class="small text-muted mb-0">
                                @if(request('filter') === 'unread')
                                    You're all caught up! No unread notifications right now.
                                @else
                                    When you receive task assignments or updates, they will appear here.
                                @endif
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>
            @if($notifications->hasPages())
                <div class="card-footer bg-white d-flex justify-content-center">
                    {{ $notifications->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
