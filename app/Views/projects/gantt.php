<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Gantt Chart - <?= esc($project['name']) ?><?= $this->endSection() ?>

<?= $this->section('head') ?>
<!-- Primary DHTMLX Gantt CSS -->
<link rel="stylesheet" href="https://cdn.dhtmlx.com/gantt/7.1.13/dhtmlxgantt.css" type="text/css">

<!-- Fallback CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/dhtmlx-gantt@7.1.13/codebase/dhtmlxgantt.css" type="text/css">

<!-- Load DHTMLX Gantt JS in head to ensure it's available before we need it -->
<script src="https://cdn.dhtmlx.com/gantt/7.1.13/dhtmlxgantt.js"></script>

<!-- Additional styles for better container sizing -->
<style>
    #gantt_here {
        min-height: 600px !important;
        width: 100% !important;
        box-sizing: border-box !important;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- We will handle the scripts in the content section -->
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Project Timeline - <?= esc($project['name']) ?></h1>
            <p class="text-gray-600"><?= esc($project['project_code']) ?> | Gantt Chart View</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
            <button type="button" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors" onclick="gantt.ext.fullscreen.toggle()">
                <i data-lucide="maximize" class="w-4 h-4 mr-2"></i>
                Fullscreen
            </button>
            <button type="button" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors" onclick="exportToPDF()">
                <i data-lucide="file-text" class="w-4 h-4 mr-2"></i>
                Export PDF
            </button>
            <a href="<?= base_url('admin/projects/view/' . $project['id']) ?>" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to Project
            </a>
        </div>
    </div>

    <!-- Gantt Chart Controls -->
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="p-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex flex-wrap gap-2">
                    <button type="button" class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50 transition-colors" onclick="gantt.ext.zoom.setLevel('day')">
                        Day
                    </button>
                    <button type="button" class="px-3 py-1 text-sm bg-indigo-600 text-white border border-indigo-600 rounded-md hover:bg-indigo-700 transition-colors" onclick="gantt.ext.zoom.setLevel('week')">
                        Week
                    </button>
                    <button type="button" class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50 transition-colors" onclick="gantt.ext.zoom.setLevel('month')">
                        Month
                    </button>
                    <button type="button" class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50 transition-colors" onclick="gantt.ext.zoom.setLevel('quarter')">
                        Quarter
                    </button>
                </div>
                <div class="flex flex-wrap gap-4">
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" id="showCriticalPath" checked class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm text-gray-700">Show Critical Path</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" id="showProgress" checked class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm text-gray-700">Show Progress</span>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- Project Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-lg shadow-sm border border-l-4 border-l-indigo-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-indigo-600 uppercase tracking-wide">Total Tasks</p>
                    <p class="text-2xl font-bold text-gray-900"><?= count($tasks) ?></p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="check-square" class="w-6 h-6 text-indigo-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-sm border border-l-4 border-l-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-green-600 uppercase tracking-wide">Milestones</p>
                    <p class="text-2xl font-bold text-gray-900"><?= count($milestones) ?></p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="flag" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-sm border border-l-4 border-l-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-blue-600 uppercase tracking-wide">Duration</p>
                    <p class="text-2xl font-bold text-gray-900">
                        <?php
                        if (!empty($project['start_date']) && !empty($project['planned_end_date'])) {
                            $start = new DateTime($project['start_date']);
                            $end = new DateTime($project['planned_end_date']);
                            $duration = $start->diff($end)->days;
                            echo $duration . ' days';
                        } else {
                            echo 'N/A';
                        }
                        ?>
                    </p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="calendar" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-sm border border-l-4 border-l-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-yellow-600 uppercase tracking-wide">Progress</p>
                    <p class="text-2xl font-bold text-gray-900"><?= round($project['progress_percentage'] ?? 0, 1) ?>%</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="trending-up" class="w-6 h-6 text-yellow-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Gantt Chart Container -->
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="p-0 relative" style="min-height: 600px;">
            <div id="gantt_loading" class="gantt-loading absolute inset-0 z-10">
                <div class="text-center">
                    <svg class="animate-spin h-10 w-10 mb-4 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="text-lg font-semibold">Loading Gantt Chart...</p>
                    <p class="text-sm mt-2 text-gray-600">This may take a few moments...</p>
                </div>
            </div>
            <!-- Make sure the container is visible by default -->
            <div id="gantt_here" style="width: 100%; height: 600px; border-radius: 0.5rem;"></div>
        </div>
    </div>

    <!-- Legend -->
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="p-4 border-b">
            <h3 class="text-lg font-semibold text-gray-900">Legend</h3>
        </div>
        <div class="p-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="flex items-center space-x-3">
                    <div class="w-5 h-5 bg-indigo-500 rounded"></div>
                    <span class="text-sm text-gray-700">Regular Tasks</span>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="w-5 h-5 bg-yellow-500 rounded"></div>
                    <span class="text-sm text-gray-700">Critical Path</span>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="w-5 h-5 bg-green-500 rounded"></div>
                    <span class="text-sm text-gray-700">Completed Tasks</span>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="w-5 h-5 bg-red-500 rounded"></div>
                    <span class="text-sm text-gray-700">Milestones</span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.legend-color {
    width: 20px;
    height: 20px;
    border-radius: 3px;
}

.gantt_task_line.critical_path {
    background-color: #f6c23e !important;
}

.gantt_task_line.completed {
    background-color: #1cc88a !important;
}

.gantt_task_line.milestone {
    background-color: #e74a3b !important;
}

.gantt_grid_scale .gantt_grid_head_cell,
.gantt_grid_scale .gantt_grid_head_cell {
    background-color: #f8f9fc;
    border-color: #e3e6f0;
}

.gantt_layout_root {
    border: 1px solid #e3e6f0;
    border-radius: 0.35rem;
}

/* Placeholder message for when gantt isn't loaded yet */
.gantt-loading {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 600px;
    width: 100%;
    background-color: rgba(248, 249, 252, 0.9);
    color: #6366f1;
    z-index: 50;
}

/* Element to display when no tasks are available */
.gantt-empty {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 600px;
    width: 100%;
    background-color: #f8f9fc;
    color: #64748b;
    text-align: center;
    padding: 2rem;
}

/* Ensure gantt container has proper sizing */
#gantt_here {
    min-height: 600px;
    position: relative;
}

/* Custom canvas styles to ensure proper rendering */
#gantt_here canvas {
    max-width: 100%;
}
</style>

<script>
// Check if DHTMLX Gantt script is loaded
function loadGanttScript() {
    return new Promise((resolve, reject) => {
        // Library should be already loaded in head
        if (typeof gantt !== 'undefined') {
            console.log('DHTMLX Gantt library already loaded');
            return resolve();
        } else {
            console.error('DHTMLX Gantt library not found');
            
            // As a last resort, try loading from CDN
            const script = document.createElement('script');
            script.src = "https://cdn.jsdelivr.net/npm/dhtmlx-gantt@7.1.13/codebase/dhtmlxgantt.js";
            script.onload = () => {
                console.log('DHTMLX Gantt library loaded successfully from fallback CDN');
                resolve();
            };
            script.onerror = () => {
                console.error('Failed to load DHTMLX Gantt library from all sources');
                reject(new Error('Failed to load DHTMLX Gantt library'));
            };
            document.body.appendChild(script);
        }
    });
}

function initGanttChart() {
    console.log('Starting Gantt chart initialization');
    
    // Check if gantt is defined in global scope
    if (typeof gantt === 'undefined') {
        console.error('DHTMLX Gantt library not properly loaded in global scope');
        document.getElementById('gantt_loading').innerHTML = `
            <div class="text-center text-red-600">
                <p class="text-xl mb-2">Error: Gantt library not available</p>
                <p>Please try refreshing the page or check your internet connection.</p>
            </div>
        `;
        return;
    }
    
    // Ensure the container exists before proceeding
    const ganttContainer = document.getElementById('gantt_here');
    if (!ganttContainer) {
        console.error('Gantt chart container not found in DOM');
        document.getElementById('gantt_loading').innerHTML = `
            <div class="text-center text-red-600">
                <p class="text-xl mb-2">Error: Gantt chart container not found</p>
                <p>Please try refreshing the page.</p>
            </div>
        `;
        return;
    }
    
    // Show the gantt container but keep loading visible until initialized
    ganttContainer.style.display = 'block';
    
    // Configure Gantt
    gantt.config.date_format = "%Y-%m-%d";
    gantt.config.columns = [
        {name: "text", label: "Task Name", width: "*", tree: true},
        {name: "start_date", label: "Start Date", width: 80, align: "center"},
        {name: "duration", label: "Duration", width: 60, align: "center"},
        {name: "add", label: "", width: 44}
    ];

    // Configure plugins
    gantt.plugins({
        tooltip: true,
        marker: true,
        fullscreen: true,
        export_api: true
    });

    // Configure zoom levels
    gantt.ext.zoom.init({
        levels: [
            {
                name: "day",
                scale_height: 60,
                min_column_width: 30,
                scales: [
                    {unit: "month", step: 1, format: "%F %Y"},
                    {unit: "day", step: 1, format: "%j"}
                ]
            },
            {
                name: "week",
                scale_height: 60,
                min_column_width: 50,
                scales: [
                    {unit: "month", step: 1, format: "%F %Y"},
                    {unit: "week", step: 1, format: "Week %W"}
                ]
            },
            {
                name: "month",
                scale_height: 60,
                min_column_width: 120,
                scales: [
                    {unit: "year", step: 1, format: "%Y"},
                    {unit: "month", step: 1, format: "%F"}
                ]
            },
            {
                name: "quarter",
                scale_height: 60,
                min_column_width: 90,
                scales: [
                    {unit: "year", step: 1, format: "%Y"},
                    {unit: "quarter", step: 1, format: "Q%q"}
                ]
            }
        ]
    });

    // Set default zoom
    gantt.ext.zoom.setLevel("week");

    // Configure tooltips
    gantt.templates.tooltip_text = function(start, end, task) {
        return "<b>Task:</b> " + task.text + "<br/>" +
               "<b>Start:</b> " + gantt.templates.tooltip_date_format(start) + "<br/>" +
               "<b>End:</b> " + gantt.templates.tooltip_date_format(end) + "<br/>" +
               "<b>Progress:</b> " + Math.round(task.progress * 100) + "%";
    };

    // Configure task colors based on status
    gantt.templates.task_class = function(start, end, task) {
        let css = [];
        
        if (task.critical_path) {
            css.push("critical_path");
        }
        
        if (task.progress >= 1) {
            css.push("completed");
        }
        
        if (task.type === "milestone") {
            css.push("milestone");
        }
        
        return css.join(" ");
    };

    // Add today marker
    gantt.addMarker({
        start_date: new Date(),
        css: "today",
        text: "Today",
        title: "Today: " + new Date().toDateString()
    });

    // Log data for debugging
    console.log("Tasks available:", <?= count($tasks) ?>);
    console.log("Milestones available:", <?= count($milestones) ?>);
    
    // Create a variable with a different name to avoid conflict
    const ganttTasks = {
        data: [
            <?php if (!empty($tasks)) : ?>
            <?php foreach ($tasks as $index => $task): ?>
            {
                id: "task_<?= $task['id'] ?>",
                text: "<?= addslashes($task['title'] ?? 'Unnamed Task') ?>",
                start_date: "<?= $task['planned_start_date'] ?>",
                duration: <?= max(1, (strtotime($task['planned_end_date']) - strtotime($task['planned_start_date'])) / 86400) ?>,
                progress: <?= ($task['progress_percentage'] ?? 0) / 100 ?>,
                type: "task",
                critical_path: <?= $task['is_critical_path'] ? 'true' : 'false' ?>,
                assigned_to: "<?= addslashes($task['assigned_name'] ?? '') ?>",
                status: "<?= $task['status'] ?? 'not_started' ?>",
                priority: "<?= $task['priority'] ?? 'medium' ?>"
            }<?= $index < count($tasks) - 1 || !empty($milestones) ? ',' : '' ?>
            <?php endforeach; ?>
            <?php endif; ?>
            
            <?php if (!empty($milestones)) : ?>
            <?php foreach ($milestones as $index => $milestone): ?>
            {
                id: "milestone_<?= $milestone['id'] ?>",
                text: "<?= addslashes($milestone['title'] ?? 'Unnamed Milestone') ?>",
                start_date: "<?= $milestone['planned_end_date'] ?>",
                duration: 0,
                progress: <?= ($milestone['progress_percentage'] ?? 0) / 100 ?>,
                type: "milestone",
                critical_path: false,
                status: "<?= $milestone['status'] ?? 'not_started' ?>",
                priority: "<?= $milestone['priority'] ?? 'medium' ?>"
            }<?= $index < count($milestones) - 1 ? ',' : '' ?>
            <?php endforeach; ?>
            <?php endif; ?>
        ],
        links: []
    };

    console.log("Gantt data prepared:", ganttTasks);
    
    // Provide a clean dataset to parse
    const tasks = ganttTasks;

    // Add dependencies
    <?php if (!empty($tasks)) : ?>
    <?php foreach ($tasks as $task): ?>
    <?php if (!empty($task['depends_on'])): ?>
    <?php $dependencies = explode(',', $task['depends_on']); ?>
    <?php foreach ($dependencies as $depId): ?>
    {
        const depId = "<?= trim($depId) ?>";
        tasks.links.push({
            id: "link_<?= $task['id'] ?>_<?= trim($depId) ?>",
            source: "task_" + depId,
            target: "task_<?= $task['id'] ?>",
            type: "0" // finish_to_start
        });
        console.log("Added dependency: Task <?= $task['id'] ?> depends on Task " + depId);
    }
    <?php endforeach; ?>
    <?php endif; ?>
    <?php endforeach; ?>
    <?php endif; ?>

    try {
        if (typeof gantt === 'undefined') {
            throw new Error('DHTMLX Gantt library not loaded properly');
        }
        
        // Initialize Gantt by explicitly getting the container element
        const ganttContainer = document.getElementById('gantt_here');
        console.log("Initializing gantt chart in container:", ganttContainer);
        
        if (!ganttContainer) {
            throw new Error('Gantt container not found in DOM');
        }
        
        // Make sure the canvas is visible in DOM before initialization
        if (window.getComputedStyle(ganttContainer).display === 'none') {
            ganttContainer.style.display = 'block';
            console.log("Made gantt container visible");
        }
        
        // Use a small delay to ensure DOM is fully ready
        setTimeout(() => {
            try {
                // Initialize the gantt chart with container element
                gantt.init("gantt_here"); // Use ID string instead of element
                
                // Check if we have any tasks or milestones
                if (tasks.data && tasks.data.length > 0) {
                    console.log("Parsing tasks into gantt chart:", tasks);
                    gantt.parse(tasks);
                } else {
                    console.log("No tasks to display");
                    ganttContainer.innerHTML = 
                        '<div class="flex items-center justify-center h-full">' +
                            '<div class="text-gray-500 text-center p-8">' +
                                '<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mx-auto mb-4"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>' +
                                '<p>No tasks or milestones with valid dates found for this project.</p>' +
                                '<p class="mt-2">Add tasks with start and end dates to see the Gantt chart.</p>' +
                            '</div>' +
                        '</div>';
                }
                
                // Hide loading indicator only after successful initialization
                document.getElementById('gantt_loading').style.display = 'none';
                
            } catch (innerError) {
                console.error("Inner error initializing gantt chart:", innerError);
                document.getElementById('gantt_loading').style.display = 'none';
                ganttContainer.style.display = 'block';
                ganttContainer.innerHTML = 
                    '<div class="flex items-center justify-center h-full">' +
                        '<div class="text-red-600 text-center p-8">' +
                            '<p class="text-xl mb-4">Error initializing Gantt chart:</p>' +
                            '<p>' + innerError.message + '</p>' +
                            '<p class="mt-2 text-sm">Additional details: ' + (innerError.stack ? innerError.stack.split('\n')[1] : 'No details') + '</p>' +
                        '</div>' +
                    '</div>';
            }
        }, 100);
        
    } catch (error) {
        // Handle any errors that occur during initialization
        console.error("Error before gantt initialization:", error);
        const ganttContainer = document.getElementById('gantt_here');
        if (ganttContainer) ganttContainer.style.display = 'none';
        
        const loadingDiv = document.getElementById("gantt_loading");
        if (loadingDiv) {
            loadingDiv.innerHTML = 
                '<div class="text-red-600 text-center p-8">' +
                    '<p class="text-xl mb-4">Error initializing Gantt chart:</p>' +
                    '<p>' + error.message + '</p>' +
                    '<p class="mt-4 text-sm">Please check browser console for more details.</p>' +
                '</div>';
        }
    }

    // Event handlers
    gantt.attachEvent("onTaskClick", function(id, e) {
        if (id.startsWith("task_")) {
            const taskId = id.replace("task_", "");
            window.open("/admin/tasks/" + taskId, "_blank");
        } else if (id.startsWith("milestone_")) {
            const milestoneId = id.replace("milestone_", "");
            window.open("/admin/milestones/" + milestoneId, "_blank");
        }
        return true;
    });

    // Toggle controls
    document.getElementById('showCriticalPath').addEventListener('change', function() {
        gantt.refreshData();
    });

    document.getElementById('showProgress').addEventListener('change', function() {
        gantt.config.show_progress = this.checked;
        gantt.refreshData();
    });
}

// Function to export the gantt chart to PDF
function exportToPDF() {
    try {
        // First check if gantt is defined
        if (typeof gantt === 'undefined') {
            alert('Gantt chart library is not loaded. Please try refreshing the page.');
            return;
        }
        
        // Then check if exportToPDF function exists
        if (!gantt.exportToPDF) {
            console.error('exportToPDF function not available in the Gantt library');
            alert('PDF export functionality is not available. Please ensure you have the Enterprise version of DHTMLX Gantt.');
            return;
        }
        
        // Check if the chart is properly initialized
        const container = document.getElementById('gantt_here');
        if (!container || container.style.display === 'none' || container.children.length === 0) {
            alert('Gantt chart is not fully loaded or initialized. Please wait and try again.');
            return;
        }

        // Execute the export
        gantt.exportToPDF({
            name: "project_gantt_<?= $project['project_code'] ?>_<?= date('Y-m-d') ?>.pdf",
            header: "<h1><?= addslashes($project['name']) ?> - Project Timeline</h1>",
            footer: "<div style='text-align:center'>Generated on <?= date('F j, Y') ?></div>"
        });
    } catch (error) {
        console.error('Error during PDF export:', error);
        alert('An error occurred while generating the PDF. Please try again later.');
    }
}

// Wait for both DOM and window load to ensure everything is ready
window.addEventListener('load', function() {
    console.log('Window fully loaded - starting Gantt initialization');
    
    // Get references to DOM elements
    const ganttLoading = document.getElementById('gantt_loading');
    const ganttContainer = document.getElementById('gantt_here');
    
    if (!ganttLoading || !ganttContainer) {
        console.error('Critical DOM elements missing:', {
            'gantt_loading': !!ganttLoading,
            'gantt_here': !!ganttContainer
        });
        
        if (ganttLoading) {
            ganttLoading.innerHTML = `
                <div class="text-center text-red-600">
                    <p class="text-xl mb-2">Critical error: DOM elements missing</p>
                    <p>Please contact support.</p>
                </div>
            `;
        }
        return;
    }
    
    // Make sure gantt container is visible before initialization
    ganttContainer.style.display = 'block';
    
    // Verify the Gantt library is loaded and initialize
    loadGanttScript()
        .then(() => {
            console.log('Gantt script verified, now initializing chart');
            // Give a bit more time for everything to settle
            setTimeout(initGanttChart, 500);
        })
        .catch(error => {
            // Show error if script verification fails
            if (ganttLoading) {
                ganttLoading.innerHTML = `
                    <div class="text-center text-red-600">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mx-auto mb-4"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                        <p class="text-xl mb-2">Failed to load Gantt chart library</p>
                        <p>Please try refreshing the page or check your network connection.</p>
                        <p class="mt-4 text-sm text-gray-600">Error details: ${error.message}</p>
                    </div>
                `;
            }
            console.error('Error initializing Gantt chart:', error);
        });
});
</script>

<?= $this->endSection() ?>
