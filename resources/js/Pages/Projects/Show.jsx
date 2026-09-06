import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router } from '@inertiajs/react';
import { useState } from 'react';
import { 
    FolderKanban, 
    ArrowLeft, 
    ExternalLink, 
    Calendar, 
    Clock, 
    CheckSquare, 
    Users, 
    Activity, 
    Plus, 
    Edit, 
    Trash2,
    CheckCircle2,
    AlertCircle,
    FileText,
    Tag
} from 'lucide-react';

export default function Show({
    project,
    allUsers = [],
    taskTypesCount = {}
}) {
    const [activeTab, setActiveTab] = useState('tasks'); // 'tasks' | 'team' | 'activities'

    const tasks = project.tasks || [];
    const members = project.members || [];
    const activities = project.activities || [];

    const completedTasks = tasks.filter(t => t.status === 'completed').length;
    const progress = tasks.length > 0 ? Math.round((completedTasks / tasks.length) * 100) : 0;

    const handleDelete = () => {
        if (confirm(`Are you sure you want to delete project "${project.name}"?`)) {
            router.delete(route('projects.destroy', project.id));
        }
    };

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
            <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border ${styles[status] || 'bg-gray-50 text-gray-700 border-gray-200'}`}>
                {labels[status] || status}
            </span>
        );
    };

    const getPriorityBadge = (priority) => {
        const styles = {
            high: 'text-rose-600 bg-rose-50 border-rose-200',
            medium: 'text-amber-600 bg-amber-50 border-amber-200',
            low: 'text-slate-600 bg-slate-50 border-slate-200',
        };
        return (
            <span className={`text-xs px-2.5 py-0.5 rounded-md font-medium border capitalize ${styles[priority] || 'text-gray-600 bg-gray-50'}`}>
                {priority} Priority
            </span>
        );
    };

    return (
        <AuthenticatedLayout
            header={
                <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div className="flex items-center gap-3">
                        <Link
                            href={route('projects.index')}
                            className="p-2 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-600 transition"
                            title="Back to Projects"
                        >
                            <ArrowLeft className="w-4 h-4" />
                        </Link>
                        <div>
                            <div className="flex items-center gap-2.5">
                                <h2 className="text-2xl font-bold text-gray-900 tracking-tight">
                                    {project.name}
                                </h2>
                                {getStatusBadge(project.status)}
                            </div>
                            <p className="text-xs text-gray-500 mt-0.5 flex items-center gap-2">
                                <span>Owner: <strong className="text-gray-700">{project.owner?.name || 'Workspace'}</strong></span>
                                <span>•</span>
                                <span>Type: <strong className="capitalize text-gray-700">{project.type?.replace('_', ' ')}</strong></span>
                            </p>
                        </div>
                    </div>

                    <div className="flex items-center gap-2">
                        <Link
                            href={route('tasks.create', { project_id: project.id })}
                            className="inline-flex items-center gap-1.5 px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition"
                        >
                            <Plus className="w-4 h-4" />
                            Add Task
                        </Link>
                        <button
                            onClick={handleDelete}
                            className="p-2 border border-gray-200 text-gray-400 hover:text-rose-600 rounded-lg bg-white transition"
                            title="Delete Project"
                        >
                            <Trash2 className="w-4 h-4" />
                        </button>
                    </div>
                </div>
            }
        >
            <Head title={project.name} />

            <div className="py-8">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

                    {/* Overview Header Cards */}
                    <div className="grid grid-cols-1 lg:grid-cols-4 gap-4">
                        {/* Progress */}
                        <div className="bg-white rounded-xl p-5 border border-gray-200 shadow-2xs space-y-2">
                            <div className="flex justify-between items-center text-xs text-gray-500">
                                <span className="font-semibold uppercase tracking-wider">Overall Progress</span>
                                <span className="font-bold text-indigo-600">{progress}%</span>
                            </div>
                            <div className="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                                <div 
                                    className="bg-indigo-600 h-full rounded-full transition-all duration-500" 
                                    style={{ width: `${progress}%` }}
                                ></div>
                            </div>
                            <p className="text-[11px] text-gray-400">
                                {completedTasks} of {tasks.length} tasks completed
                            </p>
                        </div>

                        {/* Dates */}
                        <div className="bg-white rounded-xl p-5 border border-gray-200 shadow-2xs space-y-1">
                            <span className="text-xs font-semibold uppercase tracking-wider text-gray-500">Timeline</span>
                            <div className="text-sm font-semibold text-gray-800 flex items-center gap-1.5 pt-1">
                                <Calendar className="w-4 h-4 text-gray-400" />
                                {project.due_date ? new Date(project.due_date).toLocaleDateString() : 'No deadline'}
                            </div>
                            <p className="text-[11px] text-gray-400">
                                Started: {project.start_date ? new Date(project.start_date).toLocaleDateString() : 'Not set'}
                            </p>
                        </div>

                        {/* Priority & URL */}
                        <div className="bg-white rounded-xl p-5 border border-gray-200 shadow-2xs space-y-1">
                            <span className="text-xs font-semibold uppercase tracking-wider text-gray-500">Priority & Repo</span>
                            <div className="pt-1 flex items-center gap-2">
                                {getPriorityBadge(project.priority)}
                            </div>
                            {project.url ? (
                                <a 
                                    href={project.url} 
                                    target="_blank" 
                                    rel="noreferrer" 
                                    className="inline-flex items-center gap-1 text-xs text-indigo-600 hover:underline pt-1 font-medium truncate max-w-full"
                                >
                                    <ExternalLink className="w-3 h-3 shrink-0" />
                                    <span className="truncate">{project.url.replace(/^https?:\/\//, '')}</span>
                                </a>
                            ) : (
                                <span className="text-xs text-gray-400">No URL attached</span>
                            )}
                        </div>

                        {/* Team Size */}
                        <div className="bg-white rounded-xl p-5 border border-gray-200 shadow-2xs space-y-1">
                            <span className="text-xs font-semibold uppercase tracking-wider text-gray-500">Team Assigned</span>
                            <div className="text-xl font-bold text-gray-900 pt-0.5">
                                {members.length} <span className="text-xs font-normal text-gray-500">members</span>
                            </div>
                            <div className="flex -space-x-1 pt-1">
                                {members.slice(0, 5).map(m => (
                                    <div 
                                        key={m.id} 
                                        title={m.name}
                                        className="h-6 w-6 rounded-full bg-indigo-100 text-indigo-700 font-bold text-[10px] flex items-center justify-center ring-2 ring-white uppercase"
                                    >
                                        {m.name.substring(0, 2)}
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>

                    {/* Description Box if any */}
                    {project.description && (
                        <div className="bg-white rounded-xl p-5 border border-gray-200 shadow-2xs">
                            <h4 className="text-xs font-bold uppercase tracking-wider text-gray-500 mb-1.5">Project Scope & Description</h4>
                            <p className="text-sm text-gray-700 leading-relaxed whitespace-pre-line">
                                {project.description}
                            </p>
                        </div>
                    )}

                    {/* Task Types Summary Bar */}
                    {tasks.length > 0 && (
                        <div className="bg-white rounded-xl p-4 border border-gray-200 shadow-2xs flex flex-wrap items-center gap-4 text-xs">
                            <span className="font-semibold text-gray-500 uppercase tracking-wider text-[11px]">Task Breakdown:</span>
                            <span className="px-2.5 py-1 bg-purple-50 text-purple-700 rounded-md font-medium">
                                Features: {taskTypesCount.feature || 0}
                            </span>
                            <span className="px-2.5 py-1 bg-rose-50 text-rose-700 rounded-md font-medium">
                                Bug Fixes: {taskTypesCount.bug_fix || 0}
                            </span>
                            <span className="px-2.5 py-1 bg-amber-50 text-amber-700 rounded-md font-medium">
                                Maintenance: {taskTypesCount.maintenance || 0}
                            </span>
                            <span className="px-2.5 py-1 bg-blue-50 text-blue-700 rounded-md font-medium">
                                Support: {taskTypesCount.support || 0}
                            </span>
                        </div>
                    )}

                    {/* Navigation Tabs */}
                    <div className="border-b border-gray-200">
                        <nav className="flex space-x-8">
                            <button
                                onClick={() => setActiveTab('tasks')}
                                className={`py-3 px-1 border-b-2 font-semibold text-sm transition flex items-center gap-2 ${
                                    activeTab === 'tasks'
                                        ? 'border-indigo-600 text-indigo-600'
                                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                }`}
                            >
                                <CheckSquare className="w-4 h-4" />
                                Tasks ({tasks.length})
                            </button>

                            <button
                                onClick={() => setActiveTab('team')}
                                className={`py-3 px-1 border-b-2 font-semibold text-sm transition flex items-center gap-2 ${
                                    activeTab === 'team'
                                        ? 'border-indigo-600 text-indigo-600'
                                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                }`}
                            >
                                <Users className="w-4 h-4" />
                                Team Members ({members.length})
                            </button>

                            <button
                                onClick={() => setActiveTab('activities')}
                                className={`py-3 px-1 border-b-2 font-semibold text-sm transition flex items-center gap-2 ${
                                    activeTab === 'activities'
                                        ? 'border-indigo-600 text-indigo-600'
                                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                }`}
                            >
                                <Activity className="w-4 h-4" />
                                History & Logs ({activities.length})
                            </button>
                        </nav>
                    </div>

                    {/* Tab 1: Tasks */}
                    {activeTab === 'tasks' && (
                        <div className="bg-white rounded-xl border border-gray-200 shadow-2xs overflow-hidden">
                            {tasks.length > 0 ? (
                                <div className="divide-y divide-gray-100">
                                    {tasks.map((task) => (
                                        <div key={task.id} className="p-4 hover:bg-gray-50/80 transition flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                            <div className="space-y-1">
                                                <div className="flex items-center gap-2">
                                                    <Link 
                                                        href={route('tasks.show', task.id)}
                                                        className="font-semibold text-sm text-gray-900 hover:text-indigo-600"
                                                    >
                                                        {task.title}
                                                    </Link>
                                                    {task.priority && (
                                                        <span className="text-[10px] uppercase font-bold text-gray-500 bg-gray-100 px-2 py-0.5 rounded">
                                                            {task.priority}
                                                        </span>
                                                    )}
                                                </div>
                                                <div className="flex items-center gap-3 text-xs text-gray-400">
                                                    <span>Assignee: <strong className="text-gray-600">{task.assignee?.name || 'Unassigned'}</strong></span>
                                                    {task.due_date && (
                                                        <span>• Due: {new Date(task.due_date).toLocaleDateString()}</span>
                                                    )}
                                                </div>
                                            </div>

                                            <div className="flex items-center gap-3 self-end sm:self-center">
                                                {getStatusBadge(task.status)}
                                                <Link
                                                    href={route('tasks.show', task.id)}
                                                    className="text-xs font-semibold text-indigo-600 hover:text-indigo-800"
                                                >
                                                    View &rarr;
                                                </Link>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <div className="p-12 text-center text-gray-500">
                                    <CheckSquare className="w-10 h-10 text-gray-300 mx-auto mb-2" />
                                    <p className="text-sm font-semibold text-gray-800">No tasks in this project yet</p>
                                    <p className="text-xs text-gray-400 mt-1 mb-4">Break down project deliverables by adding tasks.</p>
                                    <Link
                                        href={route('tasks.create', { project_id: project.id })}
                                        className="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm"
                                    >
                                        <Plus className="w-4 h-4" />
                                        Add First Task
                                    </Link>
                                </div>
                            )}
                        </div>
                    )}

                    {/* Tab 2: Team */}
                    {activeTab === 'team' && (
                        <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                            {members.length > 0 ? (
                                members.map(m => (
                                    <div key={m.id} className="bg-white rounded-xl p-4 border border-gray-200 shadow-2xs flex items-center gap-3">
                                        <div className="h-10 w-10 rounded-full bg-indigo-100 text-indigo-700 font-bold text-sm flex items-center justify-center uppercase">
                                            {m.name.substring(0, 2)}
                                        </div>
                                        <div>
                                            <h4 className="text-sm font-semibold text-gray-900">{m.name}</h4>
                                            <p className="text-xs text-gray-500">{m.email}</p>
                                        </div>
                                    </div>
                                ))
                            ) : (
                                <div className="col-span-3 bg-white p-8 rounded-xl border border-gray-200 text-center text-gray-500 text-xs">
                                    No members assigned to this project.
                                </div>
                            )}
                        </div>
                    )}

                    {/* Tab 3: Activities */}
                    {activeTab === 'activities' && (
                        <div className="bg-white rounded-xl border border-gray-200 p-5 shadow-2xs space-y-4">
                            {activities.length > 0 ? (
                                activities.map(act => (
                                    <div key={act.id} className="flex items-start gap-3 text-xs border-b border-gray-50 pb-3 last:border-none">
                                        <div className="w-2 h-2 rounded-full bg-indigo-600 mt-1.5"></div>
                                        <div>
                                            <p className="text-gray-800">
                                                <strong className="text-gray-900">{act.user?.name || 'A team member'}</strong> {act.description}
                                            </p>
                                            <span className="text-[11px] text-gray-400">
                                                {new Date(act.created_at).toLocaleString()}
                                            </span>
                                        </div>
                                    </div>
                                ))
                            ) : (
                                <p className="text-xs text-gray-400 italic">No activity recorded for this project.</p>
                            )}
                        </div>
                    )}

                </div>
            </div>
        </AuthenticatedLayout>
    );
}
