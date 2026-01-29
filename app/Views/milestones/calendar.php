<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Milestone Calendar<?= $this->endSection() ?>

<?= $this->section('content') ?>

<style>
    :root {
        --fc-border-color: #e2e8f0;
    }

    #calendar {
        max-width: 1400px;
        margin: 0 auto 2rem;
        background: white;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 20px rgba(0,0,0,0.07);
        min-height: 650px;           /* Ensures visibility */
        height: auto !important;
    }

    .fc .fc-scrollgrid {
        min-height: 600px !important;
    }

    /* Calendar dots only */
    .fc .fc-daygrid-event {
        border: none !important;
        background: transparent !important;
        width: 10px !important;
        height: 10px !important;
        margin: 3px auto !important;
        padding: 0 !important;
    }
    .fc .fc-daygrid-event-dot {
        width: 10px !important;
        height: 10px !important;
        border: none !important;
    }

    /* Status colors for calendar dots */
    .fc-event.not_started   .fc-daygrid-event-dot { background-color: #6b7280; }
    .fc-event.in_progress   .fc-daygrid-event-dot { background-color: #3b82f6; }
    .fc-event.review        .fc-daygrid-event-dot { background-color: #8b5cf6; }
    .fc-event.completed     .fc-daygrid-event-dot { background-color: #10b981; }
    .fc-event.cancelled     .fc-daygrid-event-dot { background-color: #ef4444; }
    .fc-event.on_hold       .fc-daygrid-event-dot { background-color: #f59e0b; }

    /* Priority badge on calendar days */
    .milestone-count-badge {
        position: absolute;
        top: 8px;
        right: 8px;
        min-width: 24px;
        height: 24px;
        border-radius: 999px;
        color: white;
        font-size: 0.8rem;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid white;
        box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        z-index: 10;
    }
    .milestone-count-badge.critical { background: #c026d3; }
    .milestone-count-badge.high    { background: #ef4444; }
    .milestone-count-badge.medium  { background: #f59e0b; }
    .milestone-count-badge.low     { background: #10b981; }

    .fc .fc-daygrid-day-number {
        width: 2.1rem;
        height: 2.1rem;
        font-size: 1.15rem;
        font-weight: 600;
        background: #f1f5f9;
        border-radius: 999px;
        margin: 6px auto 4px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .fc .fc-day-today .fc-daygrid-day-number {
        background: #3b82f6;
        color: white;
    }

    .status-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 1.25rem;
        justify-content: center;
        padding: 1rem 0;
        font-size: 0.95rem;
        color: #4b5563;
    }
    .status-item { display: flex; align-items: center; gap: 0.5rem; }
    .status-dot { width: 12px; height: 12px; border-radius: 50%; }

    /* Status pill in modal */
    .status-pill {
        font-size: 0.75rem;
        font-weight: 500;
        padding: 0.25rem 0.65rem;
        border-radius: 9999px;
        white-space: nowrap;
    }

    @media (max-width: 768px) {
        .fc .fc-daygrid-event-dot { width: 8px !important; height: 8px !important; }
        .milestone-count-badge { width: 22px; height: 22px; font-size: 0.75rem; }
        #calendar { min-height: 500px !important; }
    }
</style>

<div class="min-h-screen bg-gray-50/60 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">

        <!-- Stats + Controls -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="lg:col-span-2 bg-white rounded-xl shadow border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Performance Overview</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="bg-blue-50 p-4 rounded-lg text-center">
                        <p class="text-sm text-blue-700">Total Milestones</p>
                        <p class="text-2xl font-bold text-blue-900" id="totalMilestones">0</p>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg text-center">
                        <p class="text-sm text-green-700">Completed</p>
                        <p class="text-2xl font-bold text-green-900" id="completedMilestones">0</p>
                    </div>
                    <div class="bg-amber-50 p-4 rounded-lg text-center">
                        <p class="text-sm text-amber-700">Active</p>
                        <p class="text-2xl font-bold text-amber-900" id="activeMilestones">0</p>
                    </div>
                    <div class="bg-red-50 p-4 rounded-lg text-center">
                        <p class="text-sm text-red-700">Overdue</p>
                        <p class="text-2xl font-bold text-red-900" id="overdueMilestones">0</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow border border-gray-200 p-6 flex flex-col justify-center">
                <div class="flex flex-wrap gap-3 justify-center lg:justify-end">
                    <button id="todayBtn" class="px-5 py-2 bg-white border border-gray-300 rounded-lg font-medium hover:bg-gray-50">
                        Today
                    </button>
                    <div class="flex border border-gray-300 rounded-lg overflow-hidden">
                        <button class="fc-prev-button px-5 py-2 hover:bg-gray-50">‹ Prev</button>
                        <div class="border-l border-gray-300"></div>
                        <button class="fc-next-button px-5 py-2 hover:bg-gray-50">Next ›</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendar Card -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-5 sm:px-8 sm:py-6 border-b border-gray-200 bg-gray-50">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900" id="calendarTitle">Milestone Calendar</h1>
                        <p class="mt-1 text-gray-600" id="eventCount">0 milestones loaded</p>
                    </div>
                </div>

                <div class="status-legend mt-5">
                    <div class="status-item"><div class="status-dot bg-gray-500"></div><span>Not Started</span></div>
                    <div class="status-item"><div class="status-dot bg-blue-500"></div><span>In Progress</span></div>
                    <div class="status-item"><div class="status-dot bg-purple-500"></div><span>Review</span></div>
                    <div class="status-item"><div class="status-dot bg-green-500"></div><span>Completed</span></div>
                    <div class="status-item"><div class="status-dot bg-red-500"></div><span>Cancelled</span></div>
                    <div class="status-item"><div class="status-dot bg-amber-500"></div><span>On Hold</span></div>
                </div>
            </div>

            <div class="p-4 sm:p-6 lg:p-8">
                <div id="calendar"></div>
            </div>
        </div>

    </div>
</div>

<!-- Day Milestones Modal -->
<div id="dayMilestonesModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full mx-4 max-h-[85vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-center mb-5">
                <h3 class="text-xl font-bold text-gray-900" id="modalDateTitle">Milestones on …</h3>
                <button id="closeDayModal" class="text-2xl text-gray-500 hover:text-gray-700">×</button>
            </div>
            <div id="dayMilestonesList" class="space-y-3"></div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">

<script>
document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    let allMilestones = [];

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        height: 'auto',
        contentHeight: 'auto',
        aspectRatio: 1.45,
        headerToolbar: false,
        events: function(fetchInfo, successCallback) {
            fetch(`<?= base_url('admin/milestones/api/calendar-events') ?>?start=${fetchInfo.startStr}&end=${fetchInfo.endStr}`)
                .then(r => r.json())
                .then(data => {
                    allMilestones = data.milestones || [];
                    updateStats(allMilestones);
                    addBadges(allMilestones);

                    document.getElementById('calendarTitle').textContent = calendar.view.title;
                    document.getElementById('eventCount').textContent = `${allMilestones.length} milestones`;

                    const events = allMilestones.map(m => ({
                        id: m.id,
                        title: m.title,
                        start: m.planned_end_date,
                        allDay: true,
                        classNames: [getStatusClass(m.status)],
                        extendedProps: { priority: m.priority || 'low' }
                    }));
                    successCallback(events);
                })
                .catch(err => console.error('Calendar fetch error:', err));
        },

        dateClick: function(info) {
            const dateStr = info.dateStr;
            const dayMilestones = allMilestones.filter(m => m.planned_end_date?.startsWith(dateStr));

            if (dayMilestones.length === 0) return;

            document.getElementById('modalDateTitle').textContent = `Milestones on ${dateStr}`;
            const container = document.getElementById('dayMilestonesList');
            container.innerHTML = '';

            dayMilestones.forEach(milestone => {
                const div = document.createElement('div');
                div.className = 'p-4 bg-gray-50 hover:bg-gray-100 rounded-lg cursor-pointer border border-gray-200 transition flex items-start gap-3';

                const projectName   = milestone.project_name   || milestone.project_title   || milestone.project   || '—';
                const assignedTo    = milestone.assigned_to_name || milestone.assignee_name || milestone.assigned_to || milestone.user_name || 'Unassigned';

                div.innerHTML = `
                    <div class="flex-shrink-0 mt-0.5">
                        <span class="status-pill ${getStatusPillClass(milestone.status)}">
                            ${formatStatus(milestone.status)}
                        </span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <a href="<?= base_url('admin/milestones/') ?>${milestone.id}" class="font-semibold text-gray-900 hover:text-indigo-600 truncate block">${milestone.title}</a>
                        
                        <div class="text-sm mt-2 text-gray-700 space-y-1">
                            <div><span class="font-medium text-gray-600">Project:</span> ${projectName}</div>
                            <div><span class="font-medium text-gray-600">Assigned to:</span> ${assignedTo}</div>
                            <div><span class="font-medium text-gray-600">Priority:</span> ${milestone.priority || '—'}</div>
                        </div>
                    </div>
                `;

                div.addEventListener('click', () => showMilestoneDetails(milestone.id));
                container.appendChild(div);
            });

            document.getElementById('dayMilestonesModal').classList.remove('hidden');
        },

        eventClick: info => {
            info.jsEvent.preventDefault();
            showMilestoneDetails(info.event.id);
        },

        datesSet: () => setTimeout(() => addBadges(allMilestones), 80)
    });

    calendar.render();

    // Navigation
    document.querySelector('.fc-prev-button')?.addEventListener('click', () => calendar.prev());
    document.querySelector('.fc-next-button')?.addEventListener('click', () => calendar.next());
    document.getElementById('todayBtn')?.addEventListener('click', () => calendar.today());

    // Close modal
    document.getElementById('closeDayModal').onclick = () => {
        document.getElementById('dayMilestonesModal').classList.add('hidden');
    };
    document.getElementById('dayMilestonesModal').onclick = e => {
        if (e.target === document.getElementById('dayMilestonesModal')) {
            document.getElementById('dayMilestonesModal').classList.add('hidden');
        }
    };

    // ──────────────────────────────────────────────
    // Helper functions (same as task calendar)
    // ──────────────────────────────────────────────

    function getStatusClass(status) {
        const map = {
            not_started: 'not_started',
            pending: 'not_started',
            in_progress: 'in_progress',
            review: 'review',
            completed: 'completed',
            cancelled: 'cancelled',
            on_hold: 'on_hold'
        };
        return map[status?.toLowerCase()] || 'not_started';
    }

    function getStatusPillClass(status) {
        const s = (status || '').toLowerCase();
        if (['not_started', 'pending'].includes(s)) return 'bg-gray-100 text-gray-800 border border-gray-300';
        if (s === 'in_progress')               return 'bg-blue-100 text-blue-800 border border-blue-300';
        if (s === 'review')                     return 'bg-purple-100 text-purple-800 border border-purple-300';
        if (s === 'completed')                  return 'bg-green-100 text-green-800 border border-green-300';
        if (s === 'cancelled')                  return 'bg-red-100 text-red-800 border border-red-300';
        if (s === 'on_hold')                    return 'bg-amber-100 text-amber-800 border border-amber-300';
        return 'bg-gray-100 text-gray-800 border border-gray-300';
    }

    function formatStatus(status) {
        if (!status) return 'Unknown';
        return status
            .replace(/_/g, ' ')
            .split(' ')
            .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
            .join(' ');
    }

    function updateStats(milestones) {
        const total = milestones.length;
        const completed = milestones.filter(m => m.status === 'completed').length;
        const active = milestones.filter(m => ['not_started','in_progress','review'].includes(m.status)).length;
        const overdue = milestones.filter(m =>
            m.planned_end_date &&
            new Date(m.planned_end_date) < new Date() &&
            m.status !== 'completed'
        ).length;

        document.getElementById('totalMilestones').textContent     = total;
        document.getElementById('completedMilestones').textContent = completed;
        document.getElementById('activeMilestones').textContent    = active;
        document.getElementById('overdueMilestones').textContent   = overdue;
    }

    function addBadges(milestones) {
        document.querySelectorAll('.milestone-count-badge').forEach(el => el.remove());

        document.querySelectorAll('.fc-daygrid-day').forEach(day => {
            const date = day.dataset.date;
            const dayMilestones = milestones.filter(m => m.planned_end_date?.startsWith(date));
            if (dayMilestones.length === 0) return;

            let priority = 'low';
            if (dayMilestones.some(m => m.priority === 'critical')) priority = 'critical';
            else if (dayMilestones.some(m => m.priority === 'high')) priority = 'high';
            else if (dayMilestones.some(m => m.priority === 'medium')) priority = 'medium';

            const badge = document.createElement('div');
            badge.className = `milestone-count-badge ${priority}`;
            badge.textContent = dayMilestones.length > 9 ? '9+' : dayMilestones.length;
            day.querySelector('.fc-daygrid-day-top')?.appendChild(badge);
        });
    }

    function showMilestoneDetails(id) {
        fetch(`<?= base_url('admin/milestones/') ?>${id}`)
            .then(r => r.text())
            .then(html => {
                document.getElementById('modalContent').innerHTML = html;
                document.getElementById('milestoneModal').classList.remove('hidden');
            })
            .catch(err => console.error(err));
    }
});
</script>
<?= $this->endSection() ?>
