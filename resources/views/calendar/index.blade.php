<x-layouts.app title="Interactive Calendar - ProjectPilot">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.css">

    <style>
        .fc {
            background: #ffffff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.05);
        }
        .fc-toolbar h2 {
            font-size: 1.35rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
            line-height: 1.5;
        }
        .fc-button {
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            color: #334155;
            text-shadow: none;
            box-shadow: none;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 6px;
            text-transform: capitalize;
            transition: all 0.15s ease;
        }
        .fc-button:hover {
            background-color: #e2e8f0;
            color: #0f172a;
        }
        .fc-state-active, .fc-button.fc-state-active {
            background-color: #0284c7 !important;
            border-color: #0284c7 !important;
            color: #ffffff !important;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.15) !important;
        }
        .fc-today {
            background-color: #f0f9ff !important;
        }
        .fc-day-header {
            padding: 10px 0 !important;
            font-weight: 700;
            color: #475569;
            background: #f8fafc;
            border-color: #e2e8f0 !important;
        }
        .fc-day {
            border-color: #f1f5f9 !important;
        }
        .fc-event {
            border-radius: 5px !important;
            padding: 3px 6px !important;
            margin: 2px 4px !important;
            font-size: 0.82rem !important;
            font-weight: 500 !important;
            cursor: pointer !important;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1) !important;
            border-width: 0 !important;
            border-left: 4px solid rgba(0,0,0,0.25) !important;
            transition: transform 0.15s ease, box-shadow 0.15s ease !important;
        }
        .fc-event:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 10px rgba(0,0,0,0.2) !important;
        }
        .legend-dot {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 6px;
        }
    </style>

    <div class="page-heading">
        <div class="page-heading__container">
            <h1 class="title">Project & Task Calendar</h1>
            <p class="caption">Visual timeline of upcoming deadlines, deliverables, and scheduled milestones</p>
        </div>
        <div class="page-heading__container float-right d-none d-sm-block">
            <a href="{{ route('tasks.create') }}" class="btn btn-primary margin-right-5">
                <i class="fa fa-plus-circle margin-right-5"></i> New Task
            </a>
            <a href="{{ route('projects.create') }}" class="btn btn-outline-secondary">
                <i class="fa fa-folder-open margin-right-5"></i> New Project
            </a>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Calendar</li>
            </ol>
        </nav>
    </div>

    <div class="container-fluid">
        <!-- FILTER BAR & LEGEND -->
        <div class="card margin-bottom-20 shadow-sm border">
            <div class="card-body">
                <div class="form-row align-items-center">
                    <div class="col-12 col-md-3 margin-bottom-10">
                        <label class="small font-weight-bold text-muted mb-1">Item Category</label>
                        <select id="filter-type" class="form-control">
                            <option value="all">All Items (Projects & Tasks)</option>
                            <option value="tasks">Tasks Only</option>
                            <option value="projects">Projects Only</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-3 margin-bottom-10">
                        <label class="small font-weight-bold text-muted mb-1">Project</label>
                        <select id="filter-project" class="form-control">
                            <option value="">All Projects</option>
                            @foreach($projects as $p)
                                <option value="{{ $p->id }}">{{ Str::limit($p->name, 25) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-6 col-md-2 margin-bottom-10">
                        <label class="small font-weight-bold text-muted mb-1">Priority</label>
                        <select id="filter-priority" class="form-control">
                            <option value="">All Priorities</option>
                            <option value="high">High Priority</option>
                            <option value="medium">Medium Priority</option>
                            <option value="low">Low Priority</option>
                        </select>
                    </div>

                    <div class="col-6 col-md-2 margin-bottom-10">
                        <label class="small font-weight-bold text-muted mb-1">Status</label>
                        <select id="filter-status" class="form-control">
                            <option value="">All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-2 margin-bottom-10 d-flex align-items-end" style="height: 100%;">
                        <button type="button" id="btn-reset-filters" class="btn btn-light btn-block mt-auto" title="Reset Filters">
                            <i class="fa fa-refresh margin-right-5"></i> Reset
                        </button>
                    </div>
                </div>

                <!-- COLOR CODED LEGEND -->
                <div class="d-flex flex-wrap align-items-center gap-3 pt-3 border-top mt-2 small text-muted">
                    <span class="font-weight-bold mr-2"><i class="fa fa-info-circle mr-1"></i> Legend:</span>
                    <span class="mr-3"><span class="legend-dot" style="background-color: #6366f1;"></span> Project Deadline</span>
                    <span class="mr-3"><span class="legend-dot" style="background-color: #ef4444;"></span> High Priority Task</span>
                    <span class="mr-3"><span class="legend-dot" style="background-color: #0284c7;"></span> Medium Priority Task</span>
                    <span class="mr-3"><span class="legend-dot" style="background-color: #64748b;"></span> Low Priority Task</span>
                    <span class="mr-3"><span class="legend-dot" style="background-color: #10b981;"></span> Completed Task</span>
                </div>
            </div>
        </div>

        <!-- CALENDAR CONTAINER -->
        <div class="card shadow-sm border margin-bottom-25">
            <div class="card-body p-3">
                <div id="calendar"></div>
            </div>
        </div>
    </div>

    <!-- QUICK VIEW MODAL -->
    <div class="modal fade" id="eventModal" tabindex="-1" role="dialog" aria-labelledby="eventModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-light">
                    <div class="d-flex align-items-center">
                        <span id="modal-type-badge" class="badge mr-2">Task</span>
                        <h5 class="modal-title font-weight-bold text-dark" id="eventModalTitle">Item Title</h5>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3 d-flex flex-wrap gap-2">
                        <span id="modal-status-badge" class="badge badge-primary mr-1">In Progress</span>
                        <span id="modal-priority-badge" class="badge badge-info mr-1">Medium Priority</span>
                        <span id="modal-overdue-badge" class="badge badge-danger d-none">Overdue</span>
                    </div>

                    <div class="p-3 bg-light rounded border mb-3">
                        <div class="row">
                            <div class="col-6 mb-2">
                                <span class="text-muted small">Start Date:</span><br>
                                <strong id="modal-start-date" class="text-dark">-</strong>
                            </div>
                            <div class="col-6 mb-2">
                                <span class="text-muted small">Due Date:</span><br>
                                <strong id="modal-due-date" class="text-dark">-</strong>
                            </div>
                            <div class="col-12 mt-1">
                                <span class="text-muted small">Project:</span><br>
                                <a href="#" id="modal-project-link" class="text-primary font-weight-bold">-</a>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center mb-2">
                        <img src="" id="modal-assignee-avatar" class="rounded-circle mr-2 border" style="width: 38px; height: 38px; object-fit: cover;">
                        <div>
                            <span class="text-muted small" id="modal-assignee-label">Assigned to:</span><br>
                            <strong id="modal-assignee-name" class="text-dark">-</strong>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white d-flex justify-content-between">
                    <a href="#" id="modal-edit-btn" class="btn btn-outline-secondary btn-sm">
                        <i class="fa fa-pencil mr-1"></i> Edit Item
                    </a>
                    <div>
                        <button type="button" class="btn btn-light btn-sm mr-1" data-dismiss="modal">Close</button>
                        <a href="#" id="modal-view-btn" class="btn btn-primary btn-sm">
                            <i class="fa fa-external-link mr-1"></i> Open Details & Discussion
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FULLCALENDAR SCRIPT -->
    <script type="text/javascript" src="{{ asset('js/vendors/fullcalendar/fullcalendar.js') }}"></script>

    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = $('#calendar');

            calendarEl.fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                buttonText: {
                    today: 'Today',
                    month: 'Month',
                    week: 'Week',
                    day: 'Day'
                },
                buttonIcons: {
                    prev: 'fa fa-angle-left',
                    next: 'fa fa-angle-right'
                },
                editable: false,
                eventLimit: true,
                views: {
                    month: { eventLimit: 4 }
                },
                events: function(start, end, timezone, callback) {
                    $.ajax({
                        url: "{{ route('calendar.events') }}",
                        dataType: 'json',
                        data: {
                            start: start.format('YYYY-MM-DD'),
                            end: end.format('YYYY-MM-DD'),
                            type: $('#filter-type').val(),
                            project_id: $('#filter-project').val(),
                            priority: $('#filter-priority').val(),
                            status: $('#filter-status').val()
                        },
                        success: function(events) {
                            callback(events);
                        },
                        error: function(err) {
                            console.error('Error fetching calendar events:', err);
                        }
                    });
                },
                eventClick: function(calEvent, jsEvent, view) {
                    jsEvent.preventDefault();
                    var props = calEvent.extendedProps;
                    if (!props) return;

                    // Populate modal
                    $('#eventModalTitle').text(props.title);
                    $('#modal-type-badge').text(props.type === 'project' ? 'Project' : 'Task')
                        .removeClass('badge-primary badge-info')
                        .addClass(props.type === 'project' ? 'badge-info' : 'badge-primary');

                    $('#modal-status-badge').text(props.status);
                    $('#modal-priority-badge').text(props.priority + ' Priority');

                    if (props.is_overdue) {
                        $('#modal-overdue-badge').removeClass('d-none');
                    } else {
                        $('#modal-overdue-badge').addClass('d-none');
                    }

                    $('#modal-start-date').text(props.start_date);
                    $('#modal-due-date').text(props.due_date);
                    $('#modal-project-link').text(props.project_name).attr('href', props.project_url);

                    $('#modal-assignee-label').text(props.type === 'project' ? 'Project Owner:' : 'Assigned to:');
                    $('#modal-assignee-name').text(props.assignee_name);
                    $('#modal-assignee-avatar').attr('src', props.assignee_avatar);

                    $('#modal-view-btn').attr('href', props.view_url);
                    $('#modal-edit-btn').attr('href', props.edit_url);

                    $('#eventModal').modal('show');
                }
            });

            // Filter changes trigger event reload
            $('#filter-type, #filter-project, #filter-priority, #filter-status').on('change', function() {
                calendarEl.fullCalendar('refetchEvents');
            });

            // Reset filters
            $('#btn-reset-filters').on('click', function() {
                $('#filter-type').val('all');
                $('#filter-project').val('');
                $('#filter-priority').val('');
                $('#filter-status').val('');
                calendarEl.fullCalendar('refetchEvents');
            });
        });
    </script>
</x-layouts.app>
