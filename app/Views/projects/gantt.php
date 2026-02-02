<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Project Timeline - <?= esc($project['name']) ?><?= $this->endSection() ?>

<?= $this->section('head') ?>
<!-- Local DHTMLX Gantt files (download and place in public/assets/gantt/) -->
<link rel="stylesheet" href="<?= base_url('assets/gantt/dhtmlxgantt.css') ?>" type="text/css">
<script src="<?= base_url('assets/gantt/dhtmlxgantt.js') ?>"></script>

<style>
    #gantt_here {
        width: 100%;
        min-height: 700px;
        border-radius: 0 0 0.5rem 0.5rem;
        background: white;
    }

    .gantt-loading {
        position: absolute;
        inset: 0;
        background: rgba(255,255,255,0.95);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
        border-radius: 0.5rem;
    }

    .gantt-loading-content {
        text-align: center;
        padding: 2rem;
        background: white;
        border-radius: 1rem;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        border: 1px solid #e5e7eb;
    }

    .gantt-spinner {
        width: 48px;
        height: 48px;
        border: 5px solid #e5e7eb;
        border-top: 5px solid #6366f1;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto 1.25rem;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .gantt-empty {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 700px;
        background: #f8fafc;
        border: 2px dashed #d1d5db;
        border-radius: 0.5rem;
        color: #6b7280;
        text-align: center;
        padding: 3rem 2rem;
    }

    /* Better task styling */
    .gantt_task_line {
        border-radius: 6px !important;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1) !important;
    }

    .gantt_task_line:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
    }

    .gantt_task_progress {
        background: rgba(255,255,255,0.85) !important;
        border-radius: 3px !important;
    }

    .gantt_marker.today {
        background: #3b82f6 !important;
        border-color: #eff6ff !important;
    }

    @media (max-width: 768px) {
        #gantt_here {
            min-height: 500px;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 bg-white p-6 rounded-xl shadow border">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">
                Project Timeline
            </h1>
            <p class="text-gray-600 mt-1">
                <?= esc($project['name']) ?> · <?= esc($project['project_code']) ?>
            </p>
        </div>

        <div class="flex flex-wrap gap-3">
            <button 
                onclick="gantt.ext.fullscreen.toggle()"
                class="inline-flex items-center px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-800 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5 5m11-1v4m0 0h-4m4 0l-5 5"></path>
                </svg>
                Fullscreen
            </button>

            <a href="<?= base_url('admin/projects/' . $project['id'] . '/gantt/pdf') ?>" target="_blank"
               class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export PDF
            </a>

            <a href="<?= base_url('admin/projects/view/' . $project['id']) ?>"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Project
            </a>
        </div>
    </div>

    <!-- Controls -->
    <div class="bg-white p-5 rounded-xl shadow border">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-3">
                <h3 class="text-lg font-semibold text-gray-900">View Controls</h3>
                <div class="flex bg-gray-100 rounded-lg overflow-hidden">
                    <button onclick="gantt.ext.zoom.setLevel('day')" class="px-4 py-2 text-sm hover:bg-gray-200">Day</button>
                    <button onclick="gantt.ext.zoom.setLevel('week')" class="px-4 py-2 text-sm bg-indigo-600 text-white">Week</button>
                    <button onclick="gantt.ext.zoom.setLevel('month')" class="px-4 py-2 text-sm hover:bg-gray-200">Month</button>
                    <button onclick="gantt.ext.zoom.setLevel('quarter')" class="px-4 py-2 text-sm hover:bg-gray-200">Quarter</button>
                </div>
            </div>

            <div class="flex items-center gap-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="showCriticalPath" checked class="rounded text-indigo-600 focus:ring-indigo-500">
                    <span class="text-sm text-gray-700">Critical Path</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="showProgress" checked class="rounded text-indigo-600 focus:ring-indigo-500">
                    <span class="text-sm text-gray-700">Progress</span>
                </label>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-xl shadow border-l-4 border-l-indigo-500">
            <p class="text-sm font-medium text-indigo-600 uppercase">Total Tasks</p>
            <p class="text-3xl font-bold mt-2"><?= count($tasks) ?></p>
        </div>

        <div class="bg-white p-6 rounded-xl shadow border-l-4 border-l-green-500">
            <p class="text-sm font-medium text-green-600 uppercase">Milestones</p>
            <p class="text-3xl font-bold mt-2"><?= count($milestones) ?></p>
        </div>

        <div class="bg-white p-6 rounded-xl shadow border-l-4 border-l-blue-500">
            <p class="text-sm font-medium text-blue-600 uppercase">Duration</p>
            <p class="text-3xl font-bold mt-2">
                <?php
                if (!empty($project['start_date']) && !empty($project['planned_end_date'])) {
                    $start = new DateTime($project['start_date']);
                    $end   = new DateTime($project['planned_end_date']);
                    echo $start->diff($end)->days . ' days';
                } else {
                    echo 'N/A';
                }
                ?>
            </p>
        </div>

        <div class="bg-white p-6 rounded-xl shadow border-l-4 border-l-purple-500">
            <p class="text-sm font-medium text-purple-600 uppercase">Progress</p>
            <p class="text-3xl font-bold mt-2"><?= round($project['progress_percentage'] ?? 0, 1) ?>%</p>
        </div>
    </div>

    <!-- Gantt Chart -->
    <div class="bg-white rounded-xl shadow border overflow-hidden relative">
        <div id="gantt_loading" class="gantt-loading">
            <div class="gantt-loading-content">
                <div class="gantt-spinner"></div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Loading Project Timeline</h3>
                <p class="text-gray-600">Preparing tasks, milestones and dependencies...</p>
            </div>
        </div>

        <div id="gantt_here"></div>
    </div>

    <!-- Legend -->
    <div class="bg-white p-6 rounded-xl shadow border">
        <h3 class="text-lg font-semibold mb-4">Legend</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div class="flex items-center gap-3">
                <div class="w-5 h-2.5 bg-indigo-600 rounded"></div>
                <span class="text-sm text-gray-700">Regular Tasks</span>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-5 h-2.5 bg-amber-500 rounded"></div>
                <span class="text-sm text-gray-700">Critical Path</span>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-5 h-2.5 bg-green-500 rounded"></div>
                <span class="text-sm text-gray-700">Completed</span>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-3 h-3 bg-red-600 rounded-full"></div>
                <span class="text-sm text-gray-700">Milestones</span>
            </div>
        </div>
    </div>
</div>

<script>
// Gantt Initialization
document.addEventListener('DOMContentLoaded', function () {
    if (typeof gantt === 'undefined') {
        console.error('DHTMLX Gantt library not loaded');
        document.getElementById('gantt_loading').innerHTML = `
            <div class="text-center p-12 text-red-600">
                <h3 class="text-2xl font-bold mb-4">Gantt Library Failed to Load</h3>
                <p>Please check your internet connection or contact support.</p>
                <button onclick="location.reload()" class="mt-6 px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Refresh Page
                </button>
            </div>`;
        return;
    }

    const container = document.getElementById('gantt_here');
    if (!container) {
        console.error('Gantt container #gantt_here not found');
        return;
    }

    try {
        // Basic config
        gantt.config.date_format = "%Y-%m-%d";
        gantt.config.xml_date    = "%Y-%m-%d";

        gantt.config.columns = [
            {name: "text",     label: "Task / Milestone", width: "*", tree: true},
            {name: "start_date", label: "Start", width: 100, align: "center"},
            {name: "duration", label: "Duration", width: 80, align: "center"},
            {name: "progress", label: "%", width: 60, align: "center"},
            {name: "add",      label: "", width: 44}
        ];

        // Plugins
        gantt.plugins({
            tooltip: true,
            marker: true,
            fullscreen: true
        });

        // Zoom levels
        gantt.ext.zoom.init({
            levels: [
                { name: "day",    scale_height: 50, min_column_width: 30 },
                { name: "week",   scale_height: 50, min_column_width: 60 },
                { name: "month",  scale_height: 50, min_column_width: 100 },
                { name: "quarter", scale_height: 50, min_column_width: 120 }
            ]
        });

        gantt.ext.zoom.setLevel('week');

        // Data
        const ganttData = {
            data: [
                <?php foreach ($tasks as $t): ?>
                {
                    id: <?= $t['id'] ?>,
                    text: "<?= addslashes($t['title']) ?>",
                    start_date: "<?= $t['planned_start_date'] ?>",
                    duration: <?= max(1, (strtotime($t['planned_end_date']) - strtotime($t['planned_start_date'])) / 86400) ?>,
                    progress: <?= ($t['progress_percentage'] ?? 0) / 100 ?>,
                    type: "task"
                },
                <?php endforeach; ?>

                <?php foreach ($milestones as $m): ?>
                {
                    id: "m_<?= $m['id'] ?>",
                    text: "<?= addslashes($m['title']) ?>",
                    start_date: "<?= $m['planned_end_date'] ?>",
                    duration: 0,
                    type: "milestone"
                },
                <?php endforeach; ?>
            ],
            links: []
        };

        // Initialize
        gantt.init("gantt_here");
        gantt.parse(ganttData);

        // Hide loading
        document.getElementById('gantt_loading').style.display = 'none';

        // Today marker
        gantt.addMarker({
            start_date: new Date(),
            css: "today",
            text: "Today",
            title: "Today"
        });

        // Critical path & progress toggles
        document.getElementById('showCriticalPath')?.addEventListener('change', function() {
            gantt.config.highlight_critical_path = this.checked;
            gantt.refreshData();
        });

        document.getElementById('showProgress')?.addEventListener('change', function() {
            gantt.config.show_progress = this.checked;
            gantt.refreshData();
        });

    } catch (err) {
        console.error('Gantt init error:', err);
        document.getElementById('gantt_loading').innerHTML = `
            <div class="text-center p-12 text-red-600">
                <h3 class="text-2xl font-bold mb-4">Failed to Load Timeline</h3>
                <p>${err.message}</p>
                <button onclick="location.reload()" class="mt-6 px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    Try Again
                </button>
            </div>`;
    }
});
</script>

<?= $this->endSection() ?>