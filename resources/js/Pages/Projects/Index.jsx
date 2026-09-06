import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router, useForm } from '@inertiajs/react';
import { useState } from 'react';
import { 
    FolderKanban, 
    Plus, 
    Search, 
    Filter, 
    ExternalLink, 
    Calendar, 
    CheckSquare, 
    Users, 
    MoreVertical, 
    Trash2, 
    Edit, 
    X,
    Clock,
    Sparkles
} from 'lucide-react';

export default function Index({
    projects = { data: [], links: [] },
    stats = { total: 0, new_dev: 0, maintenance: 0 },
    filters = {},
    allUsers = []
}) {
    const [isCreateModalOpen, setIsCreateModalOpen] = useState(false);
    const [search, setSearch] = useState(filters.search || '');
    const [statusFilter, setStatusFilter] = useState(filters.status || '');
    const [typeFilter, setTypeFilter] = useState(filters.type || '');
    const [priorityFilter, setPriorityFilter] = useState(filters.priority || '');

    // Form handling for creating new project
    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        type: 'new_development',
        url: '',
        description: '',
        status: 'pending',
        priority: 'medium',
        start_date: '',
        due_date: '',
        members: []
    });

    const handleSearch = (e) => {
        e.preventDefault();
        router.get(route('projects.index'), {
            search,
            status: statusFilter,
            type: typeFilter,
            priority: priorityFilter
        }, { preserveState: true });
    };

    const handleFilterChange = (key, value) => {
        const query = {
            search,
            status: statusFilter,
            type: typeFilter,
            priority: priorityFilter,
            [key]: value
        };
        if (key === 'status') setStatusFilter(value);
        if (key === 'type') setTypeFilter(value);
        if (key === 'priority') setPriorityFilter(value);

        router.get(route('projects.index'), query, { preserveState: true });
    };

    const submitCreate = (e) => {
        e.preventDefault();
        post(route('projects.store'), {
            onSuccess: () => {
                setIsCreateModalOpen(false);
                reset();
            }
        });
    };

    const handleDelete = (id, name) => {
        if (confirm(`Are you sure you want to delete "${name}"? All associated tasks will be removed.`)) {
            router.delete(route('projects.destroy', id));
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
            <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border ${styles[status] || 'bg-gray-50 text-gray-700 border-gray-200'}`}>
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
            <span className={`text-[11px] px-2 py-0.5 rounded-md font-medium border capitalize ${styles[priority] || 'text-gray-600 bg-gray-50'}`}>
                {priority}
            </span>
        );
    };

    return (
        <AuthenticatedLayout
            header={
                <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h2 className="text-2xl font-bold text-gray-900 tracking-tight">
                            Projects
                        </h2>
                        <p className="text-sm text-gray-500 mt-0.5">
                            Manage your workspace projects, track roadmaps, and monitor progress.
                        </p>
                    </div>
                    <button
                        onClick={() => setIsCreateModalOpen(true)}
                        className="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition"
                    >
                        <Plus className="w-4 h-4" />
                        Create Project
                    </button>
                </div>
            }
        >
            <Head title="Projects" />

            <div className="py-8">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

                    {/* Quick Stats Pill Header */}
                    <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div className="bg-white rounded-xl p-4 border border-gray-200 flex items-center justify-between">
                            <div>
                                <p className="text-xs text-gray-500 font-medium uppercase tracking-wider">Total Projects</p>
                                <p className="text-2xl font-bold text-gray-900 mt-0.5">{stats.total || 0}</p>
                            </div>
                            <div className="p-2.5 rounded-lg bg-indigo-50 text-indigo-600">
                                <FolderKanban className="w-5 h-5" />
                            </div>
                        </div>
                        <div className="bg-white rounded-xl p-4 border border-gray-200 flex items-center justify-between">
                            <div>
                                <p className="text-xs text-gray-500 font-medium uppercase tracking-wider">New Development</p>
                                <p className="text-2xl font-bold text-gray-900 mt-0.5">{stats.new_dev || 0}</p>
                            </div>
                            <div className="p-2.5 rounded-lg bg-emerald-50 text-emerald-600">
                                <Sparkles className="w-5 h-5" />
                            </div>
                        </div>
                        <div className="bg-white rounded-xl p-4 border border-gray-200 flex items-center justify-between">
                            <div>
                                <p className="text-xs text-gray-500 font-medium uppercase tracking-wider">Maintenance</p>
                                <p className="text-2xl font-bold text-gray-900 mt-0.5">{stats.maintenance || 0}</p>
                            </div>
                            <div className="p-2.5 rounded-lg bg-amber-50 text-amber-600">
                                <Clock className="w-5 h-5" />
                            </div>
                        </div>
                    </div>

                    {/* Filter & Search Bar */}
                    <div className="bg-white rounded-xl border border-gray-200 p-4 shadow-2xs space-y-3">
                        <div className="flex flex-col md:flex-row gap-3">
                            {/* Search Form */}
                            <form onSubmit={handleSearch} className="flex-1 relative">
                                <Search className="w-4 h-4 text-gray-400 absolute left-3.5 top-3" />
                                <input
                                    type="text"
                                    placeholder="Search projects by name or description..."
                                    value={search}
                                    onChange={(e) => setSearch(e.target.value)}
                                    className="w-full pl-9 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"
                                />
                            </form>

                            {/* Filters */}
                            <div className="flex flex-wrap items-center gap-2">
                                <select
                                    value={statusFilter}
                                    onChange={(e) => handleFilterChange('status', e.target.value)}
                                    className="py-2 pl-3 pr-8 bg-gray-50 border border-gray-200 rounded-lg text-xs font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                >
                                    <option value="">All Statuses</option>
                                    <option value="pending">Pending</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                    <option value="on_hold">On Hold</option>
                                </select>

                                <select
                                    value={typeFilter}
                                    onChange={(e) => handleFilterChange('type', e.target.value)}
                                    className="py-2 pl-3 pr-8 bg-gray-50 border border-gray-200 rounded-lg text-xs font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                >
                                    <option value="">All Types</option>
                                    <option value="new_development">New Development</option>
                                    <option value="maintenance">Maintenance</option>
                                </select>

                                <select
                                    value={priorityFilter}
                                    onChange={(e) => handleFilterChange('priority', e.target.value)}
                                    className="py-2 pl-3 pr-8 bg-gray-50 border border-gray-200 rounded-lg text-xs font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                >
                                    <option value="">All Priorities</option>
                                    <option value="high">High Priority</option>
                                    <option value="medium">Medium Priority</option>
                                    <option value="low">Low Priority</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {/* Projects Grid */}
                    {projects.data && projects.data.length > 0 ? (
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            {projects.data.map((project) => {
                                const totalProjectTasks = project.tasks ? project.tasks.length : 0;
                                const completedTasks = project.tasks 
                                    ? project.tasks.filter(t => t.status === 'completed').length 
                                    : 0;
                                const progress = totalProjectTasks > 0 
                                    ? Math.round((completedTasks / totalProjectTasks) * 100) 
                                    : 0;

                                return (
                                    <div 
                                        key={project.id}
                                        className="bg-white rounded-xl border border-gray-200/90 shadow-2xs hover:shadow-md transition flex flex-col justify-between overflow-hidden"
                                    >
                                        <div className="p-5 space-y-3">
                                            <div className="flex items-start justify-between gap-2">
                                                <div className="space-y-1">
                                                    <span className="text-[10px] font-bold tracking-wider uppercase text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded">
                                                        {project.type === 'new_development' ? 'New Dev' : 'Maintenance'}
                                                    </span>
                                                    <h3 className="text-base font-bold text-gray-900 line-clamp-1 hover:text-indigo-600 transition">
                                                        <Link href={route('projects.show', project.id)}>
                                                            {project.name}
                                                        </Link>
                                                    </h3>
                                                </div>
                                                {getStatusBadge(project.status)}
                                            </div>

                                            <p className="text-xs text-gray-500 line-clamp-2 min-h-[32px]">
                                                {project.description || 'No description provided.'}
                                            </p>

                                            {project.url && (
                                                <a 
                                                    href={project.url} 
                                                    target="_blank" 
                                                    rel="noopener noreferrer" 
                                                    className="inline-flex items-center gap-1 text-xs text-indigo-600 hover:text-indigo-800 font-medium"
                                                >
                                                    <ExternalLink className="w-3 h-3" />
                                                    {project.url.replace(/^https?:\/\//, '')}
                                                </a>
                                            )}

                                            {/* Progress Bar */}
                                            <div className="space-y-1.5 pt-2">
                                                <div className="flex justify-between text-xs text-gray-500">
                                                    <span>Tasks: {completedTasks}/{totalProjectTasks}</span>
                                                    <span className="font-semibold text-gray-700">{progress}%</span>
                                                </div>
                                                <div className="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                                    <div 
                                                        className={`h-full rounded-full transition-all duration-300 ${
                                                            progress === 100 ? 'bg-emerald-500' : 'bg-indigo-600'
                                                        }`}
                                                        style={{ width: `${progress}%` }}
                                                    ></div>
                                                </div>
                                            </div>
                                        </div>

                                        {/* Card Footer */}
                                        <div className="px-5 py-3 bg-gray-50/80 border-t border-gray-100 flex items-center justify-between text-xs">
                                            {/* Assigned Team Avatars */}
                                            <div className="flex -space-x-1.5 overflow-hidden">
                                                {project.members && project.members.length > 0 ? (
                                                    project.members.slice(0, 4).map((member) => (
                                                        <div 
                                                            key={member.id} 
                                                            title={member.name}
                                                            className="inline-block h-6 w-6 rounded-full ring-2 ring-white bg-indigo-100 text-indigo-700 font-bold text-[10px] flex items-center justify-center uppercase"
                                                        >
                                                            {member.name.substring(0, 2)}
                                                        </div>
                                                    ))
                                                ) : (
                                                    <span className="text-gray-400 text-[11px]">No members</span>
                                                )}
                                                {project.members && project.members.length > 4 && (
                                                    <span className="inline-block h-6 w-6 rounded-full ring-2 ring-white bg-gray-200 text-gray-600 font-bold text-[10px] flex items-center justify-center">
                                                        +{project.members.length - 4}
                                                    </span>
                                                )}
                                            </div>

                                            {/* Priority & Details Action */}
                                            <div className="flex items-center gap-2">
                                                {getPriorityBadge(project.priority)}
                                                <Link
                                                    href={route('projects.show', project.id)}
                                                    className="p-1 text-gray-400 hover:text-indigo-600 transition"
                                                    title="View Details"
                                                >
                                                    <ExternalLink className="w-4 h-4" />
                                                </Link>
                                                <button
                                                    onClick={() => handleDelete(project.id, project.name)}
                                                    className="p-1 text-gray-400 hover:text-rose-600 transition"
                                                    title="Delete Project"
                                                >
                                                    <Trash2 className="w-4 h-4" />
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                );
                            })}
                        </div>
                    ) : (
                        <div className="bg-white rounded-xl border border-gray-200 p-12 text-center">
                            <FolderKanban className="w-12 h-12 text-gray-300 mx-auto mb-3" />
                            <h3 className="text-base font-semibold text-gray-900">No projects found</h3>
                            <p className="text-xs text-gray-500 max-w-sm mx-auto mt-1 mb-4">
                                {search || statusFilter || typeFilter ? 'Try clearing your search or filters to see more results.' : 'Get started by creating your first project now.'}
                            </p>
                            <button
                                onClick={() => setIsCreateModalOpen(true)}
                                className="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm"
                            >
                                <Plus className="w-4 h-4" />
                                Create New Project
                            </button>
                        </div>
                    )}

                    {/* Pagination */}
                    {projects.links && projects.links.length > 3 && (
                        <div className="flex justify-center items-center gap-1.5 pt-4">
                            {projects.links.map((link, idx) => (
                                <Link
                                    key={idx}
                                    href={link.url || '#'}
                                    preserveState
                                    className={`px-3 py-1.5 rounded-lg text-xs font-medium transition ${
                                        link.active 
                                            ? 'bg-indigo-600 text-white font-semibold' 
                                            : link.url 
                                                ? 'bg-white border border-gray-200 text-gray-700 hover:bg-gray-50' 
                                                : 'text-gray-300 pointer-events-none'
                                    }`}
                                    dangerouslySetInnerHTML={{ __html: link.label }}
                                />
                            ))}
                        </div>
                    )}

                </div>
            </div>

            {/* Create Project Modal */}
            {isCreateModalOpen && (
                <div className="fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-xs flex items-center justify-center p-4">
                    <div className="bg-white rounded-2xl max-w-xl w-full p-6 shadow-2xl border border-gray-100 relative">
                        <div className="flex items-center justify-between pb-4 border-b border-gray-100">
                            <div>
                                <h3 className="text-lg font-bold text-gray-900">Create New Project</h3>
                                <p className="text-xs text-gray-500">Fill in the details to launch a new workspace project.</p>
                            </div>
                            <button 
                                onClick={() => setIsCreateModalOpen(false)}
                                className="text-gray-400 hover:text-gray-600 p-1"
                            >
                                <X className="w-5 h-5" />
                            </button>
                        </div>

                        <form onSubmit={submitCreate} className="space-y-4 pt-4">
                            <div>
                                <label className="block text-xs font-semibold text-gray-700 mb-1">
                                    Project Name <span className="text-rose-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    required
                                    placeholder="e.g. CRM Redesign 2026"
                                    value={data.name}
                                    onChange={(e) => setData('name', e.target.value)}
                                    className="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                                />
                                {errors.name && <p className="text-xs text-rose-500 mt-1">{errors.name}</p>}
                            </div>

                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-xs font-semibold text-gray-700 mb-1">Project Type</label>
                                    <select
                                        value={data.type}
                                        onChange={(e) => setData('type', e.target.value)}
                                        className="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                                    >
                                        <option value="new_development">New Development</option>
                                        <option value="maintenance">Maintenance</option>
                                    </select>
                                </div>
                                <div>
                                    <label className="block text-xs font-semibold text-gray-700 mb-1">Priority</label>
                                    <select
                                        value={data.priority}
                                        onChange={(e) => setData('priority', e.target.value)}
                                        className="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                                    >
                                        <option value="low">Low</option>
                                        <option value="medium">Medium</option>
                                        <option value="high">High</option>
                                    </select>
                                </div>
                            </div>

                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-xs font-semibold text-gray-700 mb-1">Status</label>
                                    <select
                                        value={data.status}
                                        onChange={(e) => setData('status', e.target.value)}
                                        className="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                                    >
                                        <option value="pending">Pending</option>
                                        <option value="in_progress">In Progress</option>
                                        <option value="completed">Completed</option>
                                        <option value="on_hold">On Hold</option>
                                    </select>
                                </div>
                                <div>
                                    <label className="block text-xs font-semibold text-gray-700 mb-1">Repository / Website URL</label>
                                    <input
                                        type="url"
                                        placeholder="https://example.com"
                                        value={data.url}
                                        onChange={(e) => setData('url', e.target.value)}
                                        className="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                                    />
                                </div>
                            </div>

                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-xs font-semibold text-gray-700 mb-1">Start Date</label>
                                    <input
                                        type="date"
                                        value={data.start_date}
                                        onChange={(e) => setData('start_date', e.target.value)}
                                        className="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                                    />
                                </div>
                                <div>
                                    <label className="block text-xs font-semibold text-gray-700 mb-1">Due Date</label>
                                    <input
                                        type="date"
                                        value={data.due_date}
                                        onChange={(e) => setData('due_date', e.target.value)}
                                        className="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                                    />
                                </div>
                            </div>

                            <div>
                                <label className="block text-xs font-semibold text-gray-700 mb-1">Description</label>
                                <textarea
                                    rows={3}
                                    placeholder="Brief overview of goals, scope and deliverables..."
                                    value={data.description}
                                    onChange={(e) => setData('description', e.target.value)}
                                    className="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                                ></textarea>
                            </div>

                            {/* Assign Members */}
                            {allUsers.length > 0 && (
                                <div>
                                    <label className="block text-xs font-semibold text-gray-700 mb-1.5">Assign Team Members</label>
                                    <div className="grid grid-cols-2 sm:grid-cols-3 gap-2 max-h-32 overflow-y-auto p-2 border border-gray-200 rounded-lg bg-gray-50/50">
                                        {allUsers.map((user) => {
                                            const isSelected = data.members.includes(user.id);
                                            return (
                                                <label 
                                                    key={user.id} 
                                                    className={`flex items-center gap-2 p-1.5 rounded cursor-pointer text-xs transition ${
                                                        isSelected ? 'bg-indigo-100/70 text-indigo-900 font-medium' : 'text-gray-700 hover:bg-gray-100'
                                                    }`}
                                                >
                                                    <input
                                                        type="checkbox"
                                                        value={user.id}
                                                        checked={isSelected}
                                                        onChange={(e) => {
                                                            if (e.target.checked) {
                                                                setData('members', [...data.members, user.id]);
                                                            } else {
                                                                setData('members', data.members.filter(id => id !== user.id));
                                                            }
                                                        }}
                                                        className="rounded text-indigo-600 focus:ring-indigo-500 h-3.5 w-3.5"
                                                    />
                                                    <span className="truncate">{user.name}</span>
                                                </label>
                                            );
                                        })}
                                    </div>
                                </div>
                            )}

                            <div className="flex items-center justify-end gap-2.5 pt-4 border-t border-gray-100">
                                <button
                                    type="button"
                                    onClick={() => setIsCreateModalOpen(false)}
                                    className="px-4 py-2 border border-gray-300 text-gray-700 text-xs font-semibold rounded-lg hover:bg-gray-50 transition"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition disabled:opacity-50"
                                >
                                    {processing ? 'Creating...' : 'Save Project'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}
