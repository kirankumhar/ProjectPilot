@props(['title' => null])
<!DOCTYPE html>
<html lang="en">

<head>
    <title>{{ $title ?? config('app.name', 'ProjectPilot') }}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/styles2c70.css?v=1.0.3') }}">
</head>

<body>
    <div class="page page--w-header">
        <!-- PAGE HEADER -->
        <header class="page__header">
            <div class="logo-holder">
                <a href="{{ route('dashboard') }}" class="logo-text d-none d-lg-block">
                    <strong class="text-primary">#</strong> PROJECT <strong>PILOT</strong>
                </a>
                <a href="{{ route('dashboard') }}" class="logo-text d-lg-none">
                    <strong class="text-primary">#</strong><strong>PP</strong>
                </a>
                <div class="rw-btn rw-btn--nav" data-action="aside-hide"><span></span></div>
            </div>
            <div class="box">
                <form class="page-header-search" id="header_search" action="{{ route('projects.index') }}" method="GET">
                    <input type="text" name="search" class="form-control" placeholder="Search projects..." value="{{ request('search') }}">
                    <div class="page-header-search__icon"></div>
                </form>
            </div>
            <div class="box-fluid"></div>
            <div class="box d-flex align-items-center">
                <!-- NOTIFICATIONS BELL DROPDOWN -->
                @auth
                    @php
                        $unreadNotificationsCount = auth()->user()->unreadNotifications->count();
                        $recentNotifications = auth()->user()->notifications()->take(5)->get();
                    @endphp
                    <div class="dropdown float-left mr-2">
                        <button class="btn btn-light btn-icon position-relative" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Notifications" style="width: 36px; height: 36px; padding: 0;">
                            <i class="fa fa-bell-o text-dark" style="font-size: 16px; margin: 0; line-height: 1;"></i>
                            @if($unreadNotificationsCount > 0)
                                <span class="badge badge-danger position-absolute" style="top: -2px; right: -2px; font-size: 9px; padding: 2px 5px; border-radius: 10px;">
                                    {{ $unreadNotificationsCount > 99 ? '99+' : $unreadNotificationsCount }}
                                </span>
                            @endif
                        </button>
                        <div class="dropdown-menu dropdown-menu-right shadow-lg p-0" style="width: 330px; max-width: 90vw;">
                            <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
                                <strong class="text-dark"><i class="fa fa-bell text-primary mr-1"></i> Notifications</strong>
                                @if($unreadNotificationsCount > 0)
                                    <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit" class="btn btn-link btn-sm text-primary p-0" style="font-size: 0.75rem;">Mark all read</button>
                                    </form>
                                @endif
                            </div>
                            <div class="list-group list-group-flush" style="max-height: 300px; overflow-y: auto;">
                                @forelse($recentNotifications as $notif)
                                    @php
                                        $isUnread = $notif->unread();
                                        $data = $notif->data;
                                    @endphp
                                    <a href="{{ route('notifications.read', $notif->id) }}" class="list-group-item list-group-item-action p-2 {{ $isUnread ? 'bg-light' : '' }}">
                                        <div class="d-flex align-items-start">
                                            <div class="mr-2 mt-1">
                                                <i class="fa {{ $data['icon'] ?? 'fa-bell' }} {{ $data['icon_color'] ?? 'text-primary' }}"></i>
                                            </div>
                                            <div class="flex-grow-1 small" style="min-width: 0;">
                                                <div class="{{ $isUnread ? 'text-primary font-weight-bold' : 'text-dark' }} text-truncate">{{ $data['title'] ?? 'Notification' }}</div>
                                                <div class="text-muted" style="font-size: 0.75rem; line-height: 1.3;">{{ Str::limit($data['message'] ?? '', 55) }}</div>
                                                <div class="text-muted" style="font-size: 0.7rem;"><i class="fa fa-clock-o mr-1"></i>{{ $notif->created_at->diffForHumans() }}</div>
                                            </div>
                                            @if($isUnread)
                                                <span class="badge badge-primary badge-pill ml-1" style="font-size: 8px;">•</span>
                                            @endif
                                        </div>
                                    </a>
                                @empty
                                    <div class="p-3 text-center text-muted small">
                                        <i class="fa fa-bell-slash-o mb-1 d-block"></i> No notifications yet
                                    </div>
                                @endforelse
                            </div>
                            <div class="p-2 text-center border-top bg-white">
                                <a href="{{ route('notifications.index') }}" class="small text-primary font-weight-bold">
                                    View All Notifications <i class="fa fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endauth

                <div class="dropdown float-left">
                    <button class="btn btn-light btn-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="li-clipboard-alert"></span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <div class="page-heading">
                            <div class="page-heading__container">
                                <h1 class="title">Quick Actions</h1>
                                <p class="caption">Project Management Shortcuts</p>
                            </div>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item padding-left-15 border-top-0">
                                <a href="{{ route('projects.create') }}" class="text-dark">
                                    <i class="fa fa-plus-circle text-primary margin-right-10"></i> Create New Project
                                </a>
                            </li>
                            <li class="list-group-item padding-left-15">
                                <a href="{{ route('tasks.create') }}" class="text-dark">
                                    <i class="fa fa-tasks text-success margin-right-10"></i> Create New Task
                                </a>
                            </li>
                            <li class="list-group-item padding-left-15">
                                <a href="{{ route('projects.index') }}" class="text-dark">
                                    <i class="fa fa-folder-open text-info margin-right-10"></i> View All Projects
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </header>

        <!-- PAGE CONTENT WRAPPER -->
        <div class="page__content page__content--w-aside-fixed" id="page-content">
            <!-- ASIDE NAVIGATION -->
            <x-layouts.appmenu />

            <!-- MAIN CONTENT CONTAINER -->
            <div class="content d-flex flex-column" id="content" style="min-height: calc(100vh - 65px);">
                <div class="flex-grow-1">
                    @if (session('success'))
                        <div class="container-fluid padding-top-15">
                            <div class="alert alert-success alert-dismissible fade show margin-bottom-10" role="alert">
                                <strong><i class="fa fa-check-circle margin-right-5"></i> Success!</strong> {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="container-fluid padding-top-15">
                            <div class="alert alert-danger alert-dismissible fade show margin-bottom-10" role="alert">
                                <strong><i class="fa fa-exclamation-circle margin-right-5"></i> Error!</strong> {{ session('error') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        </div>
                    @endif

                    @if (isset($errors) && $errors->any())
                        <div class="container-fluid padding-top-15">
                            <div class="alert alert-danger alert-dismissible fade show margin-bottom-10" role="alert">
                                <strong><i class="fa fa-exclamation-circle margin-right-5"></i> Please fix the errors below:</strong>
                                <ul class="margin-bottom-0 margin-top-5 padding-left-20">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        </div>
                    @endif

                    {{ $slot }}
                </div>

                <!-- PAGE FOOTER -->
                <footer class="footer bg-white border-top py-3 mt-auto">
                    <div class="container-fluid text-center">
                        <span class="text-muted small" style="font-size: 0.9rem;">
                            &copy; {{ date('Y') }} <strong>ProjectPilot</strong> &bull; Developed by <strong class="text-primary">Kiran</strong>
                        </span>
                    </div>
                </footer>
            </div>
        </div>
    </div>

    <!-- IMPORTANT SCRIPTS -->
    <script type="text/javascript" src="{{ asset('js/vendors/jquery/jquery.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/vendors/jquery/jquery-migrate.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/vendors/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/vendors/mcustomscrollbar/jquery.mCustomScrollbar.concat.min.js') }}"></script>
    <!-- THIS PAGE SCRIPTS ONLY -->
    <script type="text/javascript" src="{{ asset('js/vendors/moment/moment-with-locales.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/vendors/select2/select2.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/vendors/raty/jquery.raty.js') }}"></script>
    <!-- TEMPLATE SCRIPTS -->
    <script type="text/javascript" src="{{ asset('js/app.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/plugins.js') }}"></script>
</body>

</html>