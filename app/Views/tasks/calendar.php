<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Task Calendar - Professional Project Management System
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
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Task Status</label>
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

                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Priority Level</label>
                            <select id="priorityFilter" class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 bg-white">
                                <option value="">All Priorities</option>
                                <option value="low">Low Priority</option>
                                <option value="medium">Medium Priority</option>
                                <option value="high">High Priority</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Team Member</label>
                            <select id="assigneeFilter" class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 bg-white">
                                <option value="">All Team Members</option>
                                <!-- Will be populated via JavaScript -->
                            </select>
                        </div>

                        <!-- Search Functionality -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Search Tasks</label>
                            <div class="relative">
                                <input type="text" id="taskSearch" placeholder="Search by task name, project..." class="w-full px-4 py-3 pl-12 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 bg-white">
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
                                    <h2 class="text-3xl font-bold text-gray-900" id="calendarTitle">Professional Calendar</h2>
                                    <div class="flex items-center space-x-4 mt-2">
                                        <div class="flex items-center space-x-2 text-sm text-gray-600">
                                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <span id="eventCount" class="font-semibold">0 tasks</span>
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

<!-- Enhanced Task Details Modal -->
<div id="taskModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-4 mx-auto p-4 w-full max-w-4xl">
        <div class="bg-white rounded-lg shadow-xl">
            <!-- Modal Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div id="modalStatusBadge" class="px-2 py-1 text-xs font-medium rounded-full"></div>
                    <h3 class="text-lg font-semibold text-gray-900" id="modalTitle">Task Details</h3>
                </div>
                <div class="flex items-center space-x-2">
                    <button id="editTaskBtn" class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </button>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 p-1 rounded-full hover:bg-gray-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="px-6 py-4 max-h-96 overflow-y-auto">
                <div id="modalContent" class="space-y-6">
                    <!-- Content will be loaded here -->
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-between px-6 py-4 border-t border-gray-200 bg-gray-50">
                <div class="flex items-center space-x-4 text-sm text-gray-500">
                    <span id="modalCreated">Created: --</span>
                    <span id="modalUpdated">Updated: --</span>
                </div>
                <div class="flex space-x-3">
                    <button onclick="closeModal()" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 text-sm font-medium">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions Menu -->
<div id="quickActionsMenu" class="fixed z-40 hidden">
    <div class="bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5">
        <div class="py-1">
            <button class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900" onclick="quickViewTask()">
                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                Quick View
            </button>
            <button class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900" onclick="editTask()">
                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit Task
            </button>
            <button class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900" onclick="duplicateTask()">
                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
                Duplicate
            </button>
            <div class="border-t border-gray-100"></div>
            <button class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 hover:text-red-700" onclick="deleteTask()">
                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Delete Task
            </button>
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
    let allTasks = []; // Store all tasks for badge counting

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next',
            center: 'title',
            right: 'today'
        },
        events: function(fetchInfo, successCallback, failureCallback) {
            // Fetch events via AJAX
            fetch(`<?= base_url('admin/tasks/api/calendar-events') ?>?start=${fetchInfo.startStr}&end=${fetchInfo.endStr}`)
                .then(response => response.json())
                .then(data => {
                    allTasks = data.tasks || []; // Store for badge counting

                    const events = allTasks.map(task => ({
                        id: task.id,
                        title: task.title,
                        start: task.planned_start_date,
                        end: task.planned_end_date,
                        className: getTaskStatusClass(task.status),
                        extendedProps: {
                            task: task,
                            project_name: task.project_name,
                            status: task.status,
                            priority: task.priority,
                            assigned_name: task.assigned_name,
                            progress_percentage: task.progress_percentage
                        }
                    }));

                    // Update statistics
                    updateStatistics(allTasks);

                    successCallback(events);
                })
                .catch(error => {
                    console.error('Error fetching calendar events:', error);
                    failureCallback(error);
                });
        },
        eventClick: function(info) {
            showTaskDetails(info.event.id);
        },
        eventDidMount: function(info) {
            // Add custom tooltip
            const task = info.event.extendedProps.task;
            if (task) {
                const tooltip = `
                    <strong>${task.title}</strong><br>
                    Project: ${task.project_name}<br>
                    Status: ${task.status.replace('_', ' ')}<br>
                    Priority: ${task.priority}<br>
                    Progress: ${task.progress_percentage}%<br>
                    ${task.assigned_name ? 'Assigned to: ' + task.assigned_name : 'Unassigned'}
                `;
                info.el.setAttribute('title', tooltip.replace(/<br>/g, '\n'));
            }
        },
        datesSet: function(dateInfo) {
            // Add task count badges when calendar view changes
            setTimeout(() => {
                addTaskCountBadges();
            }, 100);
        },
        viewDidMount: function() {
            // Add badges after view is rendered
            setTimeout(() => {
                addTaskCountBadges();
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
        addTaskCountBadges();
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
            /* ===== PROFESSIONAL CALENDAR STYLING ===== */

            /* Calendar container - premium styling */
            #calendar {
                border-radius: 12px !important;
                overflow: hidden !important;
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
                border: 1px solid #e5e7eb !important;
            }

            /* Professional date styling */
            .fc-daygrid-day-number {
                text-align: center !important;
                font-weight: 700 !important;
                font-size: 20px !important;
                color: #1f2937 !important;
                padding: 12px 8px 8px 8px !important;
                margin: 0 !important;
                display: block !important;
                width: 100% !important;
                line-height: 1.3 !important;
                background: transparent !important;
                border: none !important;
                position: relative !important;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
                font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif !important;
                letter-spacing: -0.025em !important;
            }

            /* Calendar day cells - premium design */
            .fc-daygrid-day {
                min-height: 100px !important;
                position: relative !important;
                border-right: 1px solid #f3f4f6 !important;
                border-bottom: 1px solid #f3f4f6 !important;
                background: linear-gradient(135deg, #ffffff 0%, #fafbfc 100%) !important;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            }

            .fc-daygrid-day:hover {
                background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%) !important;
                transform: translateY(-2px) !important;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
            }

            /* Today styling - premium highlight */
            .fc-day-today {
                background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%) !important;
                border: 3px solid #f59e0b !important;
                box-shadow: inset 0 2px 4px rgba(245, 158, 11, 0.1) !important;
                position: relative !important;
            }

            .fc-day-today::before {
                content: '' !important;
                position: absolute !important;
                top: -3px !important;
                left: -3px !important;
                right: -3px !important;
                bottom: -3px !important;
                background: linear-gradient(135deg, #f59e0b, #d97706) !important;
                border-radius: inherit !important;
                z-index: -1 !important;
                opacity: 0.3 !important;
            }

            .fc-day-today .fc-daygrid-day-number {
                color: #92400e !important;
                font-weight: 800 !important;
                text-shadow: 0 1px 2px rgba(146, 64, 14, 0.1) !important;
                background: rgba(255, 255, 255, 0.8) !important;
                border-radius: 8px !important;
                margin: 4px 8px !important;
                box-shadow: 0 2px 4px rgba(245, 158, 11, 0.2) !important;
            }

            /* Event styling - premium design */
            .fc-event {
                border-radius: 8px !important;
                font-weight: 600 !important;
                padding: 4px 10px !important;
                margin-bottom: 3px !important;
                border: none !important;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
                backdrop-filter: blur(10px) !important;
                border-left: 4px solid rgba(255, 255, 255, 0.9) !important;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
                font-size: 13px !important;
                letter-spacing: -0.025em !important;
                position: relative !important;
                overflow: hidden !important;
            }

            .fc-event::before {
                content: '' !important;
                position: absolute !important;
                top: 0 !important;
                left: 0 !important;
                right: 0 !important;
                bottom: 0 !important;
                background: inherit !important;
                opacity: 0.9 !important;
                z-index: -1 !important;
            }

            .fc-event:hover {
                transform: translateY(-2px) scale(1.02) !important;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
                z-index: 10 !important;
            }

            /* Premium status colors with enhanced gradients */
            .fc-event.pending,
            .fc-event.not_started {
                background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 50%, #1e40af 100%) !important;
                color: white !important;
                box-shadow: 0 4px 6px rgba(59, 130, 246, 0.3) !important;
            }

            .fc-event.in_progress {
                background: linear-gradient(135deg, #eab308 0%, #ca8a04 50%, #a16207 100%) !important;
                color: white !important;
                box-shadow: 0 4px 6px rgba(234, 179, 8, 0.3) !important;
            }

            .fc-event.review {
                background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 50%, #6d28d9 100%) !important;
                color: white !important;
                box-shadow: 0 4px 6px rgba(139, 92, 246, 0.3) !important;
            }

            .fc-event.completed {
                background: linear-gradient(135deg, #22c55e 0%, #16a34a 50%, #15803d 100%) !important;
                color: white !important;
                box-shadow: 0 4px 6px rgba(34, 197, 94, 0.3) !important;
            }

            .fc-event.cancelled {
                background: linear-gradient(135deg, #ef4444 0%, #dc2626 50%, #b91c1c 100%) !important;
                color: white !important;
                box-shadow: 0 4px 6px rgba(239, 68, 68, 0.3) !important;
            }

            .fc-event.on_hold {
                background: linear-gradient(135deg, #6b7280 0%, #4b5563 50%, #374151 100%) !important;
                color: white !important;
                box-shadow: 0 4px 6px rgba(107, 114, 128, 0.3) !important;
            }

            /* Premium toolbar styling */
            .fc-toolbar-title {
                font-size: 1.75rem !important;
                font-weight: 800 !important;
                color: #1f2937 !important;
                letter-spacing: -0.05em !important;
                text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1) !important;
                background: linear-gradient(135deg, #1f2937, #374151) !important;
                -webkit-background-clip: text !important;
                -webkit-text-fill-color: transparent !important;
                background-clip: text !important;
            }

            .fc-button {
                border-radius: 8px !important;
                font-weight: 600 !important;
                font-size: 14px !important;
                padding: 10px 16px !important;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
                border: 2px solid #e5e7eb !important;
                background: linear-gradient(135deg, #ffffff, #f9fafb) !important;
                color: #374151 !important;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1) !important;
                text-transform: capitalize !important;
                font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif !important;
            }

            .fc-button:hover {
                transform: translateY(-2px) !important;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.12) !important;
                border-color: #d1d5db !important;
                background: linear-gradient(135deg, #f9fafb, #f3f4f6) !important;
            }

            .fc-button-active {
                background: linear-gradient(135deg, #4f46e5 0%, #3730a3 50%, #312e81 100%) !important;
                color: white !important;
                border-color: #4f46e5 !important;
                box-shadow: 0 4px 8px rgba(79, 70, 229, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.1) !important;
                transform: translateY(-1px) !important;
            }

            .fc-button-active:hover {
                background: linear-gradient(135deg, #4338ca 0%, #3730a3 50%, #312e81 100%) !important;
                box-shadow: 0 6px 12px rgba(79, 70, 229, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.1) !important;
            }

            /* Premium task count badges */
            .task-count-badge {
                position: absolute !important;
                top: 6px !important;
                right: 6px !important;
                width: 24px !important;
                height: 24px !important;
                border-radius: 50% !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                font-size: 12px !important;
                font-weight: 900 !important;
                color: white !important;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3), 0 2px 4px rgba(0, 0, 0, 0.2) !important;
                border: 3px solid white !important;
                z-index: 1000 !important;
                animation: gentlePulse 3s ease-in-out infinite !important;
                backdrop-filter: blur(10px) !important;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            }

            .task-count-badge:hover {
                transform: scale(1.1) !important;
                box-shadow: 0 6px 12px rgba(0, 0, 0, 0.4), 0 3px 6px rgba(0, 0, 0, 0.3) !important;
            }

            .task-count-badge.low {
                background: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%) !important;
                box-shadow: 0 4px 8px rgba(16, 185, 129, 0.4), 0 2px 4px rgba(16, 185, 129, 0.3) !important;
            }

            .task-count-badge.medium {
                background: linear-gradient(135deg, #f59e0b 0%, #d97706 50%, #b45309 100%) !important;
                box-shadow: 0 4px 8px rgba(245, 158, 11, 0.4), 0 2px 4px rgba(245, 158, 11, 0.3) !important;
            }

            .task-count-badge.high {
                background: linear-gradient(135deg, #ef4444 0%, #dc2626 50%, #b91c1c 100%) !important;
                box-shadow: 0 4px 8px rgba(239, 68, 68, 0.4), 0 2px 4px rgba(239, 68, 68, 0.3) !important;
            }

            .task-count-badge.urgent {
                background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 50%, #5b21b6 100%) !important;
                box-shadow: 0 4px 8px rgba(124, 58, 237, 0.4), 0 2px 4px rgba(124, 58, 237, 0.3) !important;
                animation: urgentPulse 2s ease-in-out infinite !important;
            }

            @keyframes gentlePulse {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(1.05); }
            }

            @keyframes urgentPulse {
                0%, 100% { transform: scale(1); box-shadow: 0 4px 8px rgba(124, 58, 237, 0.4), 0 2px 4px rgba(124, 58, 237, 0.3); }
                50% { transform: scale(1.15); box-shadow: 0 6px 12px rgba(124, 58, 237, 0.6), 0 3px 6px rgba(124, 58, 237, 0.4); }
            }

            /* Week/Day headers - premium styling */
            .fc-col-header {
                background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%) !important;
                border-bottom: 2px solid #cbd5e1 !important;
            }

            .fc-col-header-cell {
                font-weight: 700 !important;
                font-size: 14px !important;
                color: #374151 !important;
                text-transform: uppercase !important;
                letter-spacing: 0.05em !important;
                padding: 16px 8px !important;
            }

            /* Enhanced header toolbar layout */
            .fc-header-toolbar {
                margin-bottom: 2rem !important;
                padding: 1rem !important;
                background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%) !important;
                border-radius: 12px 12px 0 0 !important;
                border-bottom: 1px solid #e5e7eb !important;
            }

            /* Responsive enhancements */
            @media (max-width: 768px) {
                #calendar {
                    border-radius: 8px !important;
                }

                .fc-toolbar-title {
                    font-size: 1.5rem !important;
                }

                .fc-daygrid-day-number {
                    font-size: 18px !important;
                    padding: 10px 6px 6px 6px !important;
                }

                .task-count-badge {
                    width: 22px !important;
                    height: 22px !important;
                    font-size: 11px !important;
                }

                .fc-daygrid-day {
                    min-height: 80px !important;
                }
            }

            /* Loading state styling */
            .fc-view-harness {
                background: white !important;
                border-radius: 0 0 12px 12px !important;
            }

            /* Subtle animations */
            .fc-daygrid-day,
            .fc-event,
            .fc-button,
            .task-count-badge {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            }
        `;
        document.head.appendChild(style);
    }

    // Add task count badges to calendar days
    function addTaskCountBadges() {
        // Remove existing badges first
        document.querySelectorAll('.task-count-badge').forEach(badge => badge.remove());

        // Get all calendar day elements
        const dayElements = document.querySelectorAll('.fc-daygrid-day');

        dayElements.forEach(dayEl => {
            const dateStr = dayEl.getAttribute('data-date');
            if (!dateStr) return;

            // Count tasks for this date
            const tasksForDate = allTasks.filter(task => {
                if (!task.planned_start_date) return false;
                const taskDate = new Date(task.planned_start_date).toISOString().split('T')[0];
                return taskDate === dateStr;
            });

            if (tasksForDate.length > 0) {
                // Determine badge color based on highest priority task
                let badgeClass = 'low';
                const priorities = tasksForDate.map(t => t.priority);
                if (priorities.includes('urgent')) badgeClass = 'urgent';
                else if (priorities.includes('high')) badgeClass = 'high';
                else if (priorities.includes('medium')) badgeClass = 'medium';

                // Create badge element
                const badge = document.createElement('div');
                badge.className = `task-count-badge ${badgeClass}`;
                badge.textContent = tasksForDate.length > 9 ? '9+' : tasksForDate.length;

                // Add to day element
                const dayTop = dayEl.querySelector('.fc-daygrid-day-top');
                if (dayTop) {
                    dayTop.appendChild(badge);
                }
            }
        });
    }

    // Update statistics display
    function updateStatistics(tasks) {
        const totalTasksEl = document.getElementById('totalTasks');
        const activeTasksEl = document.getElementById('activeTasks');
        const completedTasksEl = document.getElementById('completedTasks');
        const overdueTasksEl = document.getElementById('overdueTasks');
        const eventCountEl = document.getElementById('eventCount');

        const total = tasks.length;
        const active = tasks.filter(t => ['not_started', 'in_progress', 'review'].includes(t.status)).length;
        const completed = tasks.filter(t => t.status === 'completed').length;
        const overdue = tasks.filter(t => {
            if (!t.planned_end_date) return false;
            return new Date(t.planned_end_date) < new Date() && t.status !== 'completed';
        }).length;

        if (totalTasksEl) totalTasksEl.textContent = total;
        if (activeTasksEl) activeTasksEl.textContent = active;
        if (completedTasksEl) completedTasksEl.textContent = completed;
        if (overdueTasksEl) overdueTasksEl.textContent = overdue;
        if (eventCountEl) eventCountEl.textContent = `${total} tasks`;
    }

    // Today button functionality
    document.getElementById('todayBtn').addEventListener('click', function() {
        calendar.today();
    });

    // Navigation buttons functionality - restored for month navigation
    document.addEventListener('click', function(e) {
        if (e.target.closest('.fc-prev-button')) {
            calendar.prev();
        }
        if (e.target.closest('.fc-next-button')) {
            calendar.next();
        }
    });

    // Update calendar title
    calendar.on('datesSet', function() {
        const titleEl = document.getElementById('calendarTitle');
        if (titleEl) {
            titleEl.textContent = calendar.view.title;
        }
    });
});

function getTaskStatusClass(status) {
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

function showTaskDetails(taskId) {
    // Load task details via AJAX
    fetch(`<?= base_url('admin/tasks/') ?>${taskId}`)
        .then(response => response.text())
        .then(html => {
            // Extract the content from the HTML response
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const content = doc.querySelector('.bg-white.rounded-lg.shadow-sm.border');

            if (content) {
                document.getElementById('modalContent').innerHTML = content.innerHTML;
                document.getElementById('taskModal').classList.remove('hidden');
            }
        })
        .catch(error => {
            console.error('Error loading task details:', error);
            alert('Error loading task details');
        });
}

function closeModal() {
    document.getElementById('taskModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('taskModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

// Placeholder functions for quick actions menu
function quickViewTask() {
    closeModal();
}

function editTask() {
    closeModal();
}

function duplicateTask() {
    closeModal();
}

function deleteTask() {
    closeModal();
}
</script>
<?= $this->endSection() ?>
