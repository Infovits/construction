<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Milestone Calendar | Project Management
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Loading Overlay -->
<div id="loadingOverlay" class="fixed inset-0 bg-white bg-opacity-90 flex items-center justify-center z-50 hidden">
    <div class="text-center">
        <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-indigo-600 mx-auto mb-4"></div>
        <p class="text-gray-600 font-medium">Loading calendar...</p>
    </div>
</div>

<!-- Main Application Container -->
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">

<!-- Main Content Area - Larger and More Professional -->
<div class="max-w-full mx-auto px-6 lg:px-8 py-12 bg-gray-50 min-h-screen">
    <div class="grid grid-cols-1 xl:grid-cols-5 gap-8 h-full">
        <!-- Enhanced Sidebar - With Filters -->
        <div class="xl:col-span-1 space-y-8">
            <!-- Quick Navigation Card -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
                    <h3 class="text-lg font-bold text-white flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Quick Navigation
                    </h3>
                </div>
                <div class="p-6">
                    <div id="mini-calendar" class="text-sm bg-gray-50 rounded-lg p-4"></div>
                </div>
            </div>

            <!-- Advanced Filters Card -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-green-500 to-teal-600 px-6 py-4">
                    <h3 class="text-lg font-bold text-white flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        Advanced Filters
                    </h3>
                </div>
                <div class="p-6 space-y-6">
                    <!-- Project Filter -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">Project</label>
                        <select id="projectFilter" class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 bg-white">
                            <option value="">All Projects</option>
                            <?php foreach($projects as $project): ?>
                                <option value="<?= $project['id'] ?>" <?= ($project_id ?? '') == $project['id'] ? 'selected' : '' ?>>
                                    <?= esc($project['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">Status</label>
                        <select id="statusFilter" class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 bg-white">
                            <option value="">All Statuses</option>
                            <option value="not_started">Not Started</option>
                            <option value="in_progress">In Progress</option>
                            <option value="review">Under Review</option>
                            <option value="completed">Completed</option>
                            <option value="on_hold">On Hold</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>

                    <!-- Priority Filter -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">Priority</label>
                        <select id="priorityFilter" class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 bg-white">
                            <option value="">All Priorities</option>
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="critical">Critical</option>
                        </select>
                    </div>

                    <!-- Search Functionality -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">Search Milestones</label>
                        <div class="relative">
                            <input type="text" id="milestoneSearch" placeholder="Search by milestone name..." class="w-full px-4 py-3 pl-12 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 bg-white">
                            <svg class="absolute left-4 top-3.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>

                    <div class="flex space-x-2">
                        <button id="applyFiltersBtn" class="flex-1 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold py-3 px-4 rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 transform hover:scale-105 shadow-lg">
                            Apply Filters
                        </button>
                        <button id="clearFiltersBtn" class="px-4 py-3 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition-all duration-200">
                            Clear
                        </button>
                    </div>

                    <!-- Export Options -->
                    <div class="pt-4 border-t border-gray-200">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Export Calendar</label>
                        <div class="grid grid-cols-2 gap-2">
                            <button id="exportPDFBtn" class="flex items-center justify-center px-3 py-2 bg-red-500 text-white text-sm font-medium rounded-lg hover:bg-red-600 transition-all duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                PDF
                            </button>
                            <button id="exportExcelBtn" class="flex items-center justify-center px-3 py-2 bg-green-500 text-white text-sm font-medium rounded-lg hover:bg-green-600 transition-all duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Excel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Calendar - Much Larger and More Professional -->
        <div class="xl:col-span-4">
            <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden">
                <!-- Enhanced Calendar Header -->
                <div class="bg-gradient-to-r from-white via-gray-50 to-white border-b border-gray-200 px-8 py-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-6">
                            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl p-3">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3a1 1 0 011-1h6a1 1 0 011 1v4m-6 4v10a2 2 0 002 2h4a2 2 0 002-2V11M9 11h6m-6 4h6m-6 4h6"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-3xl font-bold text-gray-900" id="calendarTitle">Professional Milestone Calendar</h2>
                                <div class="flex items-center space-x-4 mt-2">
                                    <div class="flex items-center space-x-2 text-sm text-gray-600">
                                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <span id="eventCount" class="font-semibold">0 milestones</span>
                                    </div>
                                    <div class="flex items-center space-x-2 text-sm text-gray-600">
                                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span id="completionRate" class="font-semibold">0% completed</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Simplified Controls -->
                        <div class="flex items-center justify-center">
                            <button id="todayBtn" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 transform hover:scale-105 shadow-lg">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                                Today
                            </button>
                        </div>
                    </div>

                    <!-- Large Calendar Container -->
                    <div class="p-8">
                        <div id="calendar" class="rounded-xl border-2 border-gray-100 bg-gradient-to-br from-white to-gray-50" style="height: 900px;"></div>
                    </div>

                </div>
            </div>


        </div>
    </div>
</div>

<!-- Milestone Details Modal -->
<div id="milestoneModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modalTitle">Milestone Details</h3>
                            <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <div class="mt-2">
                            <div id="modalContent" class="text-gray-700">
                                <!-- Content loaded via AJAX -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" onclick="closeModal()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors duration-200">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
<style>
/* Force calendar styles with maximum specificity */
#calendar {
    height: 700px;
}

/* PREMIUM PROFESSIONAL DATE STYLING */
div#calendar .fc-daygrid-day-number,
#calendar .fc-daygrid-day-number,
.fc-view-harness .fc-daygrid-day-number {
    position: relative !important;
    text-align: center !important;
    font-weight: 800 !important;
    font-size: 24px !important;
    color: #0f172a !important;
    margin: 0 !important;
    padding: 14px 8px 12px 8px !important;
    display: block !important;
    width: 100% !important;
    line-height: 1.1 !important;
    background: transparent !important;
    border: none !important;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif !important;
    letter-spacing: -0.05em !important;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1) !important;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
    z-index: 10 !important;
}

/* Style for task count badges */
.task-count-badge {
    position: absolute !important;
    top: 2px !important;
    right: 2px !important;
    background: linear-gradient(135deg, #ef4444, #dc2626) !important;
    color: white !important;
    border-radius: 50% !important;
    width: 20px !important;
    height: 20px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 11px !important;
    font-weight: 800 !important;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2) !important;
    border: 2px solid white !important;
    z-index: 1000 !important;
}

.task-count-badge.low {
    background: linear-gradient(135deg, #10b981, #059669) !important;
}

.task-count-badge.medium {
    background: linear-gradient(135deg, #f59e0b, #d97706) !important;
}

.task-count-badge.high {
    background: linear-gradient(135deg, #ef4444, #dc2626) !important;
}

.task-count-badge.urgent {
    background: linear-gradient(135deg, #7c3aed, #6d28d9) !important;
    animation: pulse 2s infinite !important;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

/* Calendar day styling */
.fc-daygrid-day,
.fc .fc-daygrid-day {
    position: relative !important;
    min-height: 80px !important;
}

.fc-daygrid-day:hover,
.fc .fc-daygrid-day:hover {
    background-color: #f8fafc !important;
}

/* PREMIUM TODAY STYLING - Enhanced Professional Look */
.fc-day-today,
.fc .fc-day-today {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 50%, #fcd34d 100%) !important;
    border: 4px solid #f59e0b !important;
    box-shadow: inset 0 4px 8px rgba(245, 158, 11, 0.2), 0 0 0 2px rgba(245, 158, 11, 0.1) !important;
    position: relative !important;
    border-radius: 12px !important;
    margin: 2px !important;
    animation: todayGlow 3s ease-in-out infinite alternate !important;
    z-index: 5 !important;
}

@keyframes todayGlow {
    0% {
        box-shadow: inset 0 4px 8px rgba(245, 158, 11, 0.2), 0 0 0 2px rgba(245, 158, 11, 0.1), 0 0 20px rgba(245, 158, 11, 0.1);
        transform: scale(1);
    }
    100% {
        box-shadow: inset 0 4px 8px rgba(245, 158, 11, 0.3), 0 0 0 3px rgba(245, 158, 11, 0.2), 0 0 40px rgba(245, 158, 11, 0.2);
        transform: scale(1.02);
    }
}

.fc-day-today .fc-daygrid-day-number,
.fc .fc-day-today .fc-daygrid-day-number {
    color: #92400e !important;
    font-weight: 900 !important;
    font-size: 26px !important;
    text-shadow: 0 2px 4px rgba(146, 64, 14, 0.3) !important;
    background: rgba(255, 255, 255, 0.9) !important;
    border-radius: 12px !important;
    margin: 6px 8px !important;
    padding: 16px 12px 14px 12px !important;
    box-shadow: 0 4px 8px rgba(245, 158, 11, 0.3) !important;
    border: 2px solid rgba(245, 158, 11, 0.5) !important;
    position: relative !important;
    z-index: 10 !important;
    backdrop-filter: blur(10px) !important;
}

/* Event styling - Display as clickable dots */
.fc-event,
.fc .fc-event,
div#calendar .fc-event {
    cursor: pointer !important;
    border-radius: 50% !important;
    border: none !important;
    width: 8px !important;
    height: 8px !important;
    padding: 0 !important;
    margin: 1px !important;
    display: inline-block !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2) !important;
    transition: all 0.2s ease !important;
}

.fc-event:hover,
.fc .fc-event:hover {
    opacity: 0.9 !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2) !important;
}

.fc-event.pending,
.fc-event.not_started,
.fc .fc-event.pending,
.fc .fc-event.not_started {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8) !important;
    color: white !important;
}

.fc-event.in_progress,
.fc .fc-event.in_progress {
    background: linear-gradient(135deg, #eab308, #ca8a04) !important;
    color: white !important;
}

.fc-event.review,
.fc .fc-event.review {
    background: linear-gradient(135deg, #8b5cf6, #7c3aed) !important;
    color: white !important;
}

.fc-event.completed,
.fc .fc-event.completed {
    background: linear-gradient(135deg, #22c55e, #16a34a) !important;
    color: white !important;
}

.fc-event.cancelled,
.fc .fc-event.cancelled {
    background: linear-gradient(135deg, #ef4444, #dc2626) !important;
    color: white !important;
}

.fc-event.on_hold,
.fc .fc-event.on_hold {
    background: linear-gradient(135deg, #6b7280, #4b5563) !important;
    color: white !important;
}

/* Toolbar styling with maximum specificity */
.fc-toolbar-title,
.fc .fc-toolbar-title,
div#calendar .fc-toolbar-title {
    font-size: 1.5rem !important;
    font-weight: 700 !important;
    color: #1f2937 !important;
    letter-spacing: -0.025em !important;
    text-align: center !important;
}

.fc-button,
.fc .fc-button {
    background-color: #f8fafc !important;
    border: 1px solid #e2e8f0 !important;
    color: #475569 !important;
    border-radius: 6px !important;
    font-weight: 500 !important;
    transition: all 0.2s ease !important;
    text-transform: capitalize !important;
    font-size: 14px !important;
    padding: 8px 12px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
}

.fc-button:not(:empty),
.fc .fc-button:not(:empty) {
    min-width: 36px !important;
}

/* Ensure FullCalendar button icons are visible */
.fc-button .fc-icon,
.fc .fc-button .fc-icon,
.fc-icon-chevron-left,
.fc-icon-chevron-right,
.fc-icon {
    font-size: 16px !important;
    margin: 0 !important;
    display: inline-block !important;
    width: 16px !important;
    height: 16px !important;
    vertical-align: middle !important;
    line-height: 1 !important;
    font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace !important;
    font-weight: normal !important;
    color: inherit !important;
    text-rendering: auto !important;
    -webkit-font-smoothing: antialiased !important;
    -moz-osx-font-smoothing: grayscale !important;
}

/* Display text labels instead of icons */
.fc-icon-chevron-left:before,
.fc-icon-chevron-right:before {
    content: '' !important;
    display: none !important;
}

/* Add text labels directly to buttons */
.fc-prev-button::after {
    content: 'Previous' !important;
    display: inline !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    color: inherit !important;
    margin-left: 8px !important;
    position: relative !important;
    z-index: 2 !important;
}

.fc-next-button::after {
    content: 'Next' !important;
    display: inline !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    color: inherit !important;
    margin-left: 8px !important;
    position: relative !important;
    z-index: 2 !important;
}

/* Force button content visibility */
.fc-button {
    position: relative !important;
}

.fc-button:before,
.fc-button:after {
    z-index: 1 !important;
}

.fc-button:hover,
.fc .fc-button:hover {
    background-color: #f1f5f9 !important;
    border-color: #cbd5e1 !important;
    color: #334155 !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
}

.fc-button-active,
.fc .fc-button-active {
    background: linear-gradient(135deg, #4f46e5, #3730a3) !important;
    border-color: #4f46e5 !important;
    color: white !important;
    box-shadow: 0 2px 4px rgba(79, 70, 229, 0.3) !important;
}

.fc-button-active:hover,
.fc .fc-button-active:hover {
    background: linear-gradient(135deg, #4338ca, #312e81) !important;
    box-shadow: 0 4px 8px rgba(79, 70, 229, 0.4) !important;
}

/* Header styling */
.fc-header-toolbar,
.fc .fc-header-toolbar {
    margin-bottom: 1.5rem !important;
    flex-wrap: wrap !important;
    gap: 0.5rem !important;
}

/* Week/Day view enhancements */
.fc-timegrid-slot,
.fc .fc-timegrid-slot {
    height: 40px !important;
}

.fc-timegrid-axis,
.fc .fc-timegrid-axis {
    width: 60px !important;
}

/* Ensure calendar container has proper styling */
#calendar .fc-view-harness {
    background: white !important;
}

#calendar .fc-daygrid-body {
    background: white !important;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    #calendar {
        height: 500px;
    }

    .fc-toolbar-title,
    .fc .fc-toolbar-title {
        font-size: 1.25rem !important;
    }

    .fc-daygrid-day-number,
    #calendar .fc-daygrid-day-number {
        font-size: 16px !important;
        padding: 6px 0 !important;
    }

    .task-count-badge {
        width: 18px !important;
        height: 18px !important;
        font-size: 10px !important;
    }
}

/* Force override all FullCalendar default styles */
.fc-daygrid-day-number,
.fc .fc-daygrid-day-number,
#calendar .fc-daygrid-day-number,
div#calendar .fc-daygrid-day-number {
    text-align: center !important;
    display: block !important;
    width: 100% !important;
    margin: 0 !important;
    padding: 8px 0 !important;
    font-weight: 700 !important;
    font-size: 18px !important;
    color: #1f2937 !important;
    background: transparent !important;
    border: none !important;
    line-height: 1.2 !important;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    let allMilestones = []; // Store all milestones for badge counting

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next',
            center: 'title',
            right: 'today'
        },
        events: function(fetchInfo, successCallback, failureCallback) {
            // Fetch events via AJAX
            const projectFilter = document.getElementById('projectFilter').value;
            const statusFilter = document.getElementById('statusFilter').value;
            const priorityFilter = document.getElementById('priorityFilter').value;

            let url = `<?= base_url('admin/milestones/api/calendar-events') ?>?start=${fetchInfo.startStr}&end=${fetchInfo.endStr}`;
            if (projectFilter) url += `&project=${projectFilter}`;
            if (statusFilter) url += `&status=${statusFilter}`;
            if (priorityFilter) url += `&priority=${priorityFilter}`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    allMilestones = data.milestones || []; // Store for dot indicators

                    // Don't create events - we'll handle dots via CSS/JavaScript
                    updateStatistics(allMilestones);
                    addDotIndicators();

                    successCallback([]);
                })
                .catch(error => {
                    console.error('Error fetching calendar events:', error);
                    failureCallback(error);
                });
        },
        eventClick: function(info) {
            showMilestoneDetails(info.event.id);
        },
        eventDidMount: function(info) {
            // Add custom tooltip
            const milestone = info.event.extendedProps;
            if (milestone) {
                const tooltip = `Milestone: ${milestone.title}\nProject: ${milestone.project_name}\nStatus: ${milestone.status}\nPriority: ${milestone.priority}`;
                info.el.setAttribute('title', tooltip.replace(/\n/g, '\n'));
            }
        },
        datesSet: function(dateInfo) {
            // Add dot indicators when calendar view changes
            setTimeout(() => {
                addDotIndicators();
            }, 100);
        },
        viewDidMount: function() {
            // Add dots after view is rendered
            setTimeout(() => {
                addDotIndicators();
            }, 100);
        },
        dayMaxEvents: 3,
        moreLinkClick: 'popover',
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            meridiem: false
        }
    });

    calendar.render();

    // Apply custom styles after calendar is rendered
    setTimeout(() => {
        applyCustomCalendarStyles();
        addDotIndicators();
        addButtonText();
    }, 200);

    // Function to add text labels to navigation buttons
    function addButtonText() {
        // Add text to Previous button
        const prevButton = document.querySelector('.fc-prev-button');
        if (prevButton && !prevButton.querySelector('.button-text')) {
            const textSpan = document.createElement('span');
            textSpan.className = 'button-text';
            textSpan.textContent = 'Previous';
            textSpan.style.marginLeft = '8px';
            textSpan.style.fontSize = '12px';
            textSpan.style.fontWeight = '600';
            prevButton.appendChild(textSpan);
        }

        // Add text to Next button
        const nextButton = document.querySelector('.fc-next-button');
        if (nextButton && !nextButton.querySelector('.button-text')) {
            const textSpan = document.createElement('span');
            textSpan.className = 'button-text';
            textSpan.textContent = 'Next';
            textSpan.style.marginLeft = '8px';
            textSpan.style.fontSize = '12px';
            textSpan.style.fontWeight = '600';
            nextButton.appendChild(textSpan);
        }
    }

    // Function to apply custom calendar styles
    function applyCustomCalendarStyles() {
        const style = document.createElement('style');
        style.textContent = `
            /* ===== PROFESSIONAL MILESTONE CALENDAR STYLING ===== */

            /* Calendar container - premium styling */
            #calendar {
                border-radius: 12px !important;
                overflow: hidden !important;
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
                border: 1px solid #e5e7eb !important;
            }

            /* Dot indicators for milestones */
            .fc-daygrid-day.has-milestone::after {
                content: '' !important;
                position: absolute !important;
                bottom: 6px !important;
                left: 50% !important;
                transform: translateX(-50%) !important;
                width: 8px !important;
                height: 8px !important;
                border-radius: 50% !important;
                background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%) !important;
                border: 2px solid white !important;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3) !important;
                z-index: 20 !important;
                cursor: pointer !important;
                opacity: 0.9 !important;
                transition: all 0.3s ease !important;
            }

            .fc-daygrid-day.has-milestone:hover::after {
                transform: translateX(-50%) scale(1.3) !important;
                box-shadow: 0 3px 6px rgba(0, 0, 0, 0.4) !important;
                opacity: 1 !important;
            }

            .fc-daygrid-day.multiple-milestones::after {
                background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
                animation: gentlePulse 2s ease-in-out infinite !important;
            }

            @keyframes gentlePulse {
                0%, 100% { transform: translateX(-50%) scale(1); }
                50% { transform: translateX(-50%) scale(1.1); }
            }
        `;
        document.head.appendChild(style);
    }

    // Add dot indicators to calendar days
    function addDotIndicators() {
        // Remove existing classes
        document.querySelectorAll('.fc-daygrid-day').forEach(day => {
            day.classList.remove('has-milestone', 'multiple-milestones');
        });

        // Add classes for days with milestones
        allMilestones.forEach(milestone => {
            const dateStr = milestone.planned_end_date;
            const dayElement = document.querySelector(`.fc-daygrid-day[data-date="${dateStr}"]`);
            if (dayElement) {
                // Check if there are multiple milestones on this date
                const milestonesOnDate = allMilestones.filter(m => m.planned_end_date === dateStr);
                if (milestonesOnDate.length > 1) {
                    dayElement.classList.add('multiple-milestones');
                } else {
                    dayElement.classList.add('has-milestone');
                }

                // Make the day clickable
                dayElement.style.cursor = 'pointer';
                dayElement.onclick = () => {
                    if (milestonesOnDate.length === 1) {
                        showMilestoneDetails(milestonesOnDate[0].id);
                    } else {
                        showMilestoneList(milestonesOnDate);
                    }
                };
            }
        });
    }

    // Update statistics display
    function updateStatistics(milestones) {
        const total = milestones.length;
        const completed = milestones.filter(m => m.status === 'completed').length;
        const completionRate = total > 0 ? Math.round((completed / total) * 100) : 0;

        const eventCountEl = document.getElementById('eventCount');
        const completionRateEl = document.getElementById('completionRate');

        if (eventCountEl) eventCountEl.textContent = `${total} milestones`;
        if (completionRateEl) completionRateEl.textContent = `${completionRate}% completed`;
    }

    // Today button functionality
    document.getElementById('todayBtn').addEventListener('click', function() {
        calendar.today();
    });

    // Navigation buttons functionality - restored for month navigation
    document.addEventListener('click', function(e) {
        if (e.target.closest('.fc-prev-button')) {
            calendar.prev();
            setTimeout(() => addDotIndicators(), 100);
        }
        if (e.target.closest('.fc-next-button')) {
            calendar.next();
            setTimeout(() => addDotIndicators(), 100);
        }
    });

    // Update calendar title
    calendar.on('datesSet', function() {
        const titleEl = document.getElementById('calendarTitle');
        if (titleEl) {
            titleEl.textContent = calendar.view.title;
        }
    });

    // Filter functionality
    document.getElementById('applyFiltersBtn').addEventListener('click', function() {
        calendar.refetchEvents();
    });

    document.getElementById('clearFiltersBtn').addEventListener('click', function() {
        document.getElementById('projectFilter').value = '';
        document.getElementById('statusFilter').value = '';
        document.getElementById('priorityFilter').value = '';
        calendar.refetchEvents();
    });
});

function getEventStatusClass(status) {
    const classes = {
        'not_started': 'pending',
        'pending': 'pending',
        'in_progress': 'in_progress',
        'review': 'review',
        'completed': 'completed',
        'cancelled': 'cancelled',
        'on_hold': 'on_hold'
    };
    return classes[status] || 'pending';
}

function showMilestoneDetails(milestoneId) {
    // Load milestone details via AJAX
    fetch(`<?= base_url('admin/milestones/') ?>${milestoneId}`)
        .then(response => response.text())
        .then(html => {
            // Extract the content from the HTML response
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const content = doc.querySelector('.bg-white.rounded-lg.shadow-sm.border') || doc.body;

            if (content) {
                document.getElementById('modalContent').innerHTML = content.innerHTML;
                document.getElementById('milestoneModal').classList.remove('hidden');
            }
        })
        .catch(error => {
            console.error('Error loading milestone details:', error);
            alert('Error loading milestone details');
        });
}

function showMilestoneList(milestones) {
    const milestoneList = milestones.map(m => `
        <div class="p-3 border-b border-gray-200 hover:bg-gray-50 cursor-pointer" onclick="showMilestoneDetails(${m.id})">
            <div class="font-medium text-gray-900">${m.title}</div>
            <div class="text-sm text-gray-600">Project: ${m.project_name}</div>
            <div class="text-xs text-gray-500">Status: ${m.status}</div>
        </div>
    `).join('');

    document.getElementById('modalTitle').textContent = `Milestones on ${new Date(milestones[0].planned_end_date).toLocaleDateString()}`;
    document.getElementById('modalContent').innerHTML = milestoneList;
    document.getElementById('milestoneModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('milestoneModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('milestoneModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>
<?= $this->endSection() ?>
