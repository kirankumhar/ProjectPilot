import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import { 
    FolderKanban, 
    CheckSquare, 
    Users, 
    TrendingUp, 
    Clock, 
    ArrowUpRight, 
    Activity, 
    Calendar,
    CheckCircle2,
    AlertCircle,
    Layers
} from 'lucide-react';

export default function Dashboard({
    totalProjects = 0,
    activeProjects = 0,
    completedProjects = 0,
    totalTasks = 0,
    pendingTasks = 0,
    completedTasks = 0,
    totalMembers = 0,
    recentProjects = [],
    recentTasks = [],
    recentActivities = []
}) {
    const projectCompletionRate = totalProjects > 0 
        ? Math.round((completedProjects / totalProjects) * 100) 
        : 0;

    const taskCompletionRate = totalTasks > 0
        ? Math.round((completedTasks / totalTasks) * 100)
        : 0;

    const getStatusBadge = (status) => {
        const styles = {
            in_progress: 'bg-blue-50 text-blue-700 border-blue-200',
            completed: 'bg-emerald-50 text-emerald-700 border-emerald-200',
            pending: 'bg-amber-50 text-amber-700 border-amber-200',
            on_hold: 'bg-rose-50 text-rose-700 border-rose-200',
        };
        const labels = {
            in_progress: 'In Progress',
            completed: 'Completed',
            pending: 'Pending',
            on_hold: 'On Hold',
        };
        return (
            <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border ${styles[status] || 'bg-gray-100 text-gray-700 border-gray-200'}`}>
                {labels[status] || status || 'Active'}
            </span>
        );
    };

    const getPriorityBadge = (priority) => {
        const styles = {
            high: 'text-rose-600 bg-rose-50',
            medium: 'text-amber-600 bg-amber-50',
            low: 'text-slate-600 bg-slate-100',
        };
        return (
            <span className={`text-xs px-2 py-0.5 rounded font-medium capitalize ${styles[priority] || 'text-gray-600 bg-gray-50'}`}>
                {priority || 'normal'}
            </span>
        );
    };

    return (
        <AuthenticatedLayout
            header={
                <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h2 className="text-2xl font-bold text-gray-900 tracking-tight">
                            Project Dashboard
                        </h2>
                        <p className="text-sm text-gray-500 mt-0.5">
                            Real-time overview of your team projects, tasks and overall progress.
                        </p>
                    </div>
                    <div className="flex items-center gap-2.5">
                        <Link
                            href={route('projects.index')}
                            className="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition"
                        >
                            <FolderKanban className="w-4 h-4" />
                            View Projects
                        </Link>
                        <Link
                            href={route('tasks.index')}
                            className="inline-flex items-center gap-1.5 px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-semibold rounded-lg shadow-sm transition"
                        >
                            <CheckSquare className="w-4 h-4 text-gray-500" />
                            Tasks Board
                        </Link>
                    </div>
                </div>
            }
        >
            <Head title="Dashboard" />

            <div className="py-8">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
                    
                    {/* Top Stats Cards */}
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                        {/* Stat 1: Total Projects */}
                        <div className="bg-white rounded-xl border border-gray-200/80 p-5 shadow-xs hover:shadow-md transition">
                            <div className="flex items-center justify-between">
                                <span className="text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Total Projects
                                </span>
                                <div className="p-2.5 bg-indigo-50 text-indigo-600 rounded-lg">
                                    <FolderKanban className="w-5 h-5" />
                                </div>
                            </div>
                            <div className="mt-3 flex items-baseline gap-2">
                                <span className="text-3xl font-extrabold text-gray-900">
                                    {totalProjects}
                                </span>
                                <span className="text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded">
                                    {activeProjects} Active
                                </span>
                            </div>
                            <div className="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
                                <span>Completed</span>
                                <span className="font-semibold text-gray-700">{completedProjects}</span>
                            </div>
                        </div>

                        {/* Stat 2: Total Tasks */}
                        <div className="bg-white rounded-xl border border-gray-200/80 p-5 shadow-xs hover:shadow-md transition">
                            <div className="flex items-center justify-between">
                                <span className="text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Tasks Pipeline
                                </span>
                                <div className="p-2.5 bg-emerald-50 text-emerald-600 rounded-lg">
                                    <CheckSquare className="w-5 h-5" />
                                </div>
                            </div>
                            <div className="mt-3 flex items-baseline gap-2">
                                <span className="text-3xl font-extrabold text-gray-900">
                                    {totalTasks}
                                </span>
                                <span className="text-xs font-medium text-amber-700 bg-amber-50 px-2 py-0.5 rounded">
                                    {pendingTasks} Pending
                                </span>
                            </div>
                            <div className="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
                                <span>Task Completion</span>
                                <span className="font-semibold text-emerald-600">{taskCompletionRate}%</span>
                            </div>
                        </div>

                        {/* Stat 3: Team Members */}
                        <div className="bg-white rounded-xl border border-gray-200/80 p-5 shadow-xs hover:shadow-md transition">
                            <div className="flex items-center justify-between">
                                <span className="text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Team Members
                                </span>
                                <div className="p-2.5 bg-sky-50 text-sky-600 rounded-lg">
                                    <Users className="w-5 h-5" />
                                </div>
                            </div>
                            <div className="mt-3 flex items-baseline gap-2">
                                <span className="text-3xl font-extrabold text-gray-900">
                                    {totalMembers}
                                </span>
                                <span className="text-xs text-gray-500 font-medium">collaborators</span>
                            </div>
                            <div className="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
                                <span>Workspace status</span>
                                <span className="font-medium text-emerald-600">Active</span>
                            </div>
                        </div>

                        {/* Stat 4: Project Success Rate */}
                        <div className="bg-white rounded-xl border border-gray-200/80 p-5 shadow-xs hover:shadow-md transition">
                            <div className="flex items-center justify-between">
                                <span className="text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Delivery Rate
                                </span>
                                <div className="p-2.5 bg-purple-50 text-purple-600 rounded-lg">
                                    <TrendingUp className="w-5 h-5" />
                                </div>
                            </div>
                            <div className="mt-3 flex items-baseline gap-2">
                                <span className="text-3xl font-extrabold text-gray-900">
                                    {projectCompletionRate}%
                                </span>
                                <span className="text-xs text-gray-500 font-medium">overall</span>
                            </div>
                            <div className="mt-3 pt-3 border-t border-gray-100">
                                <div className="w-full bg-gray-100 rounded-full h-1.5">
                                    <div 
                                        className="bg-purple-600 h-1.5 rounded-full transition-all duration-500" 
                                        style={{ width: `${projectCompletionRate}%` }}
                                    ></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Main Grid: Projects & Tasks + Activity Sidebar */}
                    <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        
                        {/* Left 2 Columns: Projects and Tasks */}
                        <div className="lg:col-span-2 space-y-8">
                            
                            {/* Recent Projects Card */}
                            <div className="bg-white rounded-xl border border-gray-200/80 shadow-xs overflow-hidden">
                                <div className="p-5 border-b border-gray-100 flex items-center justify-between">
                                    <div className="flex items-center gap-2">
                                        <Layers className="w-5 h-5 text-indigo-600" />
                                        <h3 className="text-base font-semibold text-gray-900">Recent Projects</h3>
                                    </div>
                                    <Link 
                                        href={route('projects.index')}
                                        className="text-xs font-semibold text-indigo-600 hover:text-indigo-800 flex items-center gap-1"
                                    >
                                        View all <ArrowUpRight className="w-3.5 h-3.5" />
                                    </Link>
                                </div>

                                <div className="divide-y divide-gray-100">
                                    {recentProjects.length > 0 ? (
                                        recentProjects.map((project) => (
                                            <div key={project.id} className="p-4 sm:p-5 hover:bg-gray-50/80 transition flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                                <div className="space-y-1">
                                                    <div className="flex items-center gap-2">
                                                        <Link 
                                                            href={route('projects.show', project.id)}
                                                            className="font-semibold text-gray-900 hover:text-indigo-600 text-sm"
                                                        >
                                                            {project.name}
                                                        </Link>
                                                        {getStatusBadge(project.status)}
                                                    </div>
                                                    <p className="text-xs text-gray-500 line-clamp-1 max-w-md">
                                                        {project.description || 'No description provided.'}
                                                    </p>
                                                    <div className="flex items-center gap-3 text-xs text-gray-400 pt-1">
                                                        {project.owner && (
                                                            <span>Lead: <span className="text-gray-600 font-medium">{project.owner.name}</span></span>
                                                        )}
                                                        {project.tasks && (
                                                            <span>• {project.tasks.length} tasks</span>
                                                        )}
                                                    </div>
                                                </div>

                                                <div className="flex items-center gap-3 self-end sm:self-center">
                                                    {getPriorityBadge(project.priority)}
                                                    <Link 
                                                        href={route('projects.show', project.id)}
                                                        className="p-1.5 rounded-lg border border-gray-200 text-gray-400 hover:text-gray-700 hover:bg-white transition"
                                                    >
                                                        <ArrowUpRight className="w-4 h-4" />
                                                    </Link>
                                                </div>
                                            </div>
                                        ))
                                    ) : (
                                        <div className="p-8 text-center text-gray-500 text-sm">
                                            No projects created yet. Start by creating your first project!
                                        </div>
                                    )}
                                </div>
                            </div>

                            {/* Recent Tasks Card */}
                            <div className="bg-white rounded-xl border border-gray-200/80 shadow-xs overflow-hidden">
                                <div className="p-5 border-b border-gray-100 flex items-center justify-between">
                                    <div className="flex items-center gap-2">
                                        <CheckSquare className="w-5 h-5 text-emerald-600" />
                                        <h3 className="text-base font-semibold text-gray-900">Latest Tasks</h3>
                                    </div>
                                    <Link 
                                        href={route('tasks.index')}
                                        className="text-xs font-semibold text-indigo-600 hover:text-indigo-800 flex items-center gap-1"
                                    >
                                        Tasks board <ArrowUpRight className="w-3.5 h-3.5" />
                                    </Link>
                                </div>

                                <div className="divide-y divide-gray-100">
                                    {recentTasks.length > 0 ? (
                                        recentTasks.map((task) => (
                                            <div key={task.id} className="p-4 hover:bg-gray-50/80 transition flex items-center justify-between gap-4">
                                                <div className="space-y-1">
                                                    <div className="flex items-center gap-2">
                                                        <span className="text-sm font-medium text-gray-900">
                                                            {task.title}
                                                        </span>
                                                        {getPriorityBadge(task.priority)}
                                                    </div>
                                                    <div className="flex items-center gap-3 text-xs text-gray-500">
                                                        {task.project && (
                                                            <span className="text-indigo-600 font-medium">{task.project.name}</span>
                                                        )}
                                                        {task.due_date && (
                                                            <span className="flex items-center gap-1 text-gray-400">
                                                                <Clock className="w-3 h-3" />
                                                                {new Date(task.due_date).toLocaleDateString()}
                                                            </span>
                                                        )}
                                                    </div>
                                                </div>
                                                <div>
                                                    {getStatusBadge(task.status)}
                                                </div>
                                            </div>
                                        ))
                                    ) : (
                                        <div className="p-8 text-center text-gray-500 text-sm">
                                            No tasks found. Create a task inside a project to get started.
                                        </div>
                                    )}
                                </div>
                            </div>
                        </div>

                        {/* Right 1 Column: Activity Feed & Quick Links */}
                        <div className="space-y-8">
                            
                            {/* Activity Feed */}
                            <div className="bg-white rounded-xl border border-gray-200/80 shadow-xs p-5">
                                <div className="flex items-center gap-2 mb-4 pb-3 border-b border-gray-100">
                                    <Activity className="w-5 h-5 text-indigo-600" />
                                    <h3 className="text-base font-semibold text-gray-900">Recent Activity</h3>
                                </div>

                                <div className="space-y-4">
                                    {recentActivities.length > 0 ? (
                                        recentActivities.map((act) => (
                                            <div key={act.id} className="flex items-start gap-3 text-xs">
                                                <div className="w-2 h-2 rounded-full bg-indigo-500 mt-1.5 shrink-0"></div>
                                                <div className="flex-1">
                                                    <p className="text-gray-800">
                                                        <span className="font-semibold text-gray-900">
                                                            {act.user?.name || 'A team member'}
                                                        </span>{' '}
                                                        {act.description || act.action || 'performed an action'}
                                                    </p>
                                                    <span className="text-gray-400 text-[11px]">
                                                        {new Date(act.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
                                                    </span>
                                                </div>
                                            </div>
                                        ))
                                    ) : (
                                        <p className="text-xs text-gray-400 italic py-2">
                                            No recent activities recorded.
                                        </p>
                                    )}
                                </div>
                            </div>

                            {/* Quick Shortcuts */}
                            <div className="bg-gradient-to-br from-indigo-900 to-slate-900 rounded-xl p-5 text-white shadow-sm">
                                <h4 className="font-bold text-sm text-white mb-1">ProjectPilot Workspace</h4>
                                <p className="text-xs text-indigo-200 mb-4">Quickly jump to workspace tools</p>
                                <div className="grid grid-cols-2 gap-2 text-xs">
                                    <Link 
                                        href={route('projects.index')} 
                                        className="p-2.5 bg-white/10 hover:bg-white/20 rounded-lg text-indigo-100 font-medium transition flex items-center gap-2"
                                    >
                                        <FolderKanban className="w-4 h-4" />
                                        Projects
                                    </Link>
                                    <Link 
                                        href={route('tasks.index')} 
                                        className="p-2.5 bg-white/10 hover:bg-white/20 rounded-lg text-indigo-100 font-medium transition flex items-center gap-2"
                                    >
                                        <CheckSquare className="w-4 h-4" />
                                        Tasks
                                    </Link>
                                    <Link 
                                        href={route('calendar.index')} 
                                        className="p-2.5 bg-white/10 hover:bg-white/20 rounded-lg text-indigo-100 font-medium transition flex items-center gap-2"
                                    >
                                        <Calendar className="w-4 h-4" />
                                        Calendar
                                    </Link>
                                    <Link 
                                        href={route('timesheets.index')} 
                                        className="p-2.5 bg-white/10 hover:bg-white/20 rounded-lg text-indigo-100 font-medium transition flex items-center gap-2"
                                    >
                                        <Clock className="w-4 h-4" />
                                        Timesheets
                                    </Link>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </AuthenticatedLayout>
    );
}
