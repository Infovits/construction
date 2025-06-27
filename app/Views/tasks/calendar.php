<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Task Calendar
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Task Calendar</h3>
                    <div class="btn-group">
                        <a href="<?= base_url('admin/tasks') ?>" class="btn btn-secondary">
                            <i class="fas fa-list"></i> List View
                        </a>
                        <a href="<?= base_url('admin/tasks/create') ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create Task
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Calendar will be rendered here -->
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Task Detail Modal -->
<div class="modal fade" id="taskModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Task Details</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="taskDetails">
                    <!-- Task details will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <a href="#" id="editTaskBtn" class="btn btn-primary">Edit Task</a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- FullCalendar CSS and JS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: function(fetchInfo, successCallback, failureCallback) {
            // Fetch tasks from server
            $.ajax({
                url: '<?= base_url('admin/tasks/api/calendar-events') ?>',
                method: 'GET',
                data: {
                    start: fetchInfo.startStr,
                    end: fetchInfo.endStr
                },
                success: function(data) {
                    var events = data.tasks.map(function(task) {
                        return {
                            id: task.id,
                            title: task.title,
                            start: task.due_date,
                            backgroundColor: getStatusColor(task.status),
                            borderColor: getPriorityColor(task.priority),
                            extendedProps: {
                                status: task.status,
                                priority: task.priority,
                                project: task.project_name,
                                assignedTo: task.assigned_name,
                                progress: task.progress_percentage
                            }
                        };
                    });
                    successCallback(events);
                },
                error: function() {
                    failureCallback();
                }
            });
        },
        eventClick: function(info) {
            showTaskDetails(info.event.id);
        },
        dateClick: function(info) {
            // Redirect to create task with pre-filled date
            window.location.href = '<?= base_url('admin/tasks/create') ?>?due_date=' + info.dateStr;
        }
    });
    
    calendar.render();
});

function getStatusColor(status) {
    var colors = {
        'pending': '#ffc107',
        'in_progress': '#007bff',
        'review': '#17a2b8',
        'completed': '#28a745',
        'cancelled': '#dc3545'
    };
    return colors[status] || '#6c757d';
}

function getPriorityColor(priority) {
    var colors = {
        'low': '#28a745',
        'medium': '#ffc107',
        'high': '#fd7e14',
        'urgent': '#dc3545'
    };
    return colors[priority] || '#6c757d';
}

function showTaskDetails(taskId) {
    $('#taskDetails').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');
    $('#editTaskBtn').attr('href', '<?= base_url('admin/tasks/edit') ?>/' + taskId);
    $('#taskModal').modal('show');
    
    $.ajax({
        url: '<?= base_url('admin/tasks/api/details') ?>/' + taskId,
        method: 'GET',
        success: function(data) {
            var task = data.task;
            var html = `
                <div class="row">
                    <div class="col-md-8">
                        <h5>${task.title}</h5>
                        <p class="text-muted">${task.description || 'No description'}</p>
                        <p><strong>Project:</strong> ${task.project_name}</p>
                        <p><strong>Assigned To:</strong> ${task.assigned_name || 'Unassigned'}</p>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body">
                                <p><strong>Status:</strong> <span class="badge badge-${getStatusBadgeClass(task.status)}">${formatStatus(task.status)}</span></p>
                                <p><strong>Priority:</strong> <span class="badge badge-${getPriorityBadgeClass(task.priority)}">${task.priority}</span></p>
                                <p><strong>Due Date:</strong> ${task.due_date ? new Date(task.due_date).toLocaleDateString() : 'Not set'}</p>
                                <div class="progress mb-2">
                                    <div class="progress-bar" role="progressbar" style="width: ${task.progress_percentage}%">
                                        ${task.progress_percentage}%
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            $('#taskDetails').html(html);
        },
        error: function() {
            $('#taskDetails').html('<div class="alert alert-danger">Error loading task details</div>');
        }
    });
}

function getStatusBadgeClass(status) {
    var classes = {
        'pending': 'warning',
        'in_progress': 'primary',
        'review': 'info',
        'completed': 'success',
        'cancelled': 'danger'
    };
    return classes[status] || 'secondary';
}

function getPriorityBadgeClass(priority) {
    var classes = {
        'low': 'success',
        'medium': 'warning',
        'high': 'danger',
        'urgent': 'dark'
    };
    return classes[priority] || 'secondary';
}

function formatStatus(status) {
    return status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
}
</script>
<?= $this->endSection() ?>
