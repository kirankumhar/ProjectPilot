<div class="page-aside invert" id="page-aside">
    <div class="scroll" style="max-height: 100%">
        <div class="navigation" id="navigation-default">
            <div class="user user--bordered user--lg user--w-lineunder user--controls">
                <img src="{{ auth()->user() ? auth()->user()->avatar_url : asset('assets/img/users/user_1.jpg') }}" alt="User Profile" style="object-fit: cover; width: 60px; height: 60px; border-radius: 50%;">
                <div class="user__name">
                    <strong>{{ auth()->user()->name ?? 'User' }}</strong><br>
                    <span class="text-muted text-uppercase">{{ auth()->user()->role_display ?? 'Developer' }}</span>
                    <div class="user__controls">
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm btn-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="fa fa-cog"></span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">Profile Settings</a>
                                <div class="dropdown-divider"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger" style="cursor: pointer;">
                                        <i class="fa fa-power-off margin-right-5"></i> Log out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="user__lineunder">
                    <div class="text">ProjectPilot Admin</div>
                </div>
            </div>
            
            <ul>
                <li class="title">Main Menu</li>
                <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}">
                        <span class="icon li-home"></span> 
                        <span class="text">Dashboard</span>
                    </a>
                </li>
                
                <li class="openable {{ request()->routeIs('projects.*') ? 'open active' : '' }}">
                    <a href="#">
                        <span class="icon li-briefcase"></span> 
                        <span class="text">Projects</span>
                    </a>
                    <ul>
                        <li>
                            <a href="{{ route('projects.index') }}" class="no-icon">
                                <span class="text">All Projects</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('projects.create') }}" class="no-icon">
                                <span class="text">Create Project</span>
                            </a>
                        </li>
                    </ul>
                </li>
                
                <li class="openable {{ request()->routeIs('tasks.*') ? 'open active' : '' }}">
                    <a href="#">
                        <span class="icon li-clipboard"></span> 
                        <span class="text">Tasks</span>
                    </a>
                    <ul>
                        <li>
                            <a href="{{ route('tasks.index') }}" class="no-icon">
                                <span class="text">Task Board & List</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('tasks.create') }}" class="no-icon">
                                <span class="text">Add New Task</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="{{ request()->routeIs('chat.*') ? 'active' : '' }}">
                    <a href="{{ route('chat.index') }}">
                        <span class="icon li-bubble"></span> 
                        <span class="text">Team Messenger</span>
                    </a>
                </li>

                @if(auth()->check() && auth()->user()->isAdmin())
                <li class="openable {{ request()->routeIs('users.*') ? 'open active' : '' }}">
                    <a href="#">
                        <span class="icon li-users"></span> 
                        <span class="text">Team Members</span>
                    </a>
                    <ul>
                        <li>
                            <a href="{{ route('users.index') }}" class="no-icon">
                                <span class="text">All Users / Developers</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('users.create') }}" class="no-icon">
                                <span class="text">Add New Member</span>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                <li class="title">Account</li>
                <li class="{{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                    <a href="{{ route('profile.edit') }}">
                        <span class="icon li-user"></span> 
                        <span class="text">My Profile</span>
                    </a>
                </li>
                <li>
                    <a href="#" onclick="event.preventDefault(); document.getElementById('sidebar-logout-form').submit();">
                        <span class="icon li-power"></span> 
                        <span class="text text-danger">Logout</span>
                    </a>
                    <form id="sidebar-logout-form" method="POST" action="{{ route('logout') }}" style="display: none;">
                        @csrf
                    </form>
                </li>
            </ul>
        </div>
    </div>
</div>