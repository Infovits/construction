<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>File Management<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
.file-item { transition: all 0.2s ease; }
.file-item:hover { background-color: #f8fafc; }
.folder-tree { cursor: pointer; user-select: none; transition: all 0.2s ease; }
.folder-tree.active { background-color: #eef2ff; border-left: 3px solid #4f46e5; }
.folder-tree:hover { background-color: #f1f5f9; }
.expand-icon { transition: transform 0.2s; }
.expand-icon.expanded { transform: rotate(90deg); }
.sidebar-tree { max-height: 600px; overflow-y: auto; }
.sidebar-tree::-webkit-scrollbar { width: 6px; }
.sidebar-tree::-webkit-scrollbar-track { background: transparent; }
.sidebar-tree::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
.file-icon { width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 8px; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="min-h-screen bg-gray-50">
    <!-- Page Header -->
    <div class="bg-white border-b border-gray-200">
        <div class="px-6 py-6 sm:px-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900"><i class="fas fa-folder-open mr-3 text-indigo-600"></i>File Management</h1>
                    <p class="text-gray-500 mt-2 text-sm">Organize and manage your project documents</p>
                </div>
                <button type="button" class="inline-flex items-center px-6 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-all shadow-sm hover:shadow-md font-medium" onclick="document.getElementById('uploadModal').classList.remove('hidden')">
                    <i class="fas fa-cloud-upload-alt mr-2"></i> Upload Files
                </button>
            </div>
        </div>
    </div>

    <!-- Success Alert -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mx-6 mt-4 rounded">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700"><?= session()->getFlashdata('success') ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Main Content -->
    <div class="px-6 py-6 sm:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            
            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 sticky top-6">
                    <!-- Sidebar Header -->
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider flex items-center">
                            <i class="fas fa-layer-group mr-2 text-indigo-600"></i> Projects
                        </h3>
                    </div>
                    
                    <!-- Sidebar Navigation -->
                    <div class="sidebar-tree space-y-1 p-4">
                        <!-- All Files -->
                        <div class="folder-tree px-4 py-3 rounded-lg flex items-center text-sm" onclick="navigateToPath('all')" id="nav-all">
                            <i class="fas fa-home mr-3 text-indigo-600 text-lg"></i>
                            <span class="font-medium text-gray-700">All Files</span>
                        </div>

                        <!-- Projects -->
                        <?php foreach ($projects as $proj): ?>
                            <div class="mt-4">
                                <div class="folder-tree px-4 py-3 rounded-lg flex items-center text-sm hover:bg-gray-100 cursor-pointer" onclick="navigateToPath('project-<?= $proj['id'] ?>')" id="nav-project-<?= $proj['id'] ?>">
                                    <span class="folder-toggle mr-2 w-5 text-center">
                                        <i class="fas fa-chevron-right expand-icon text-gray-400 text-xs" id="toggle-<?= $proj['id'] ?>"></i>
                                    </span>
                                    <i class="fas fa-folder mr-3 text-yellow-500 text-lg"></i>
                                    <span class="font-medium text-gray-700"><?= $proj['name'] ?></span>
                                </div>
                                
                                <!-- Categories for this project -->
                                <div id="categories-<?= $proj['id'] ?>" class="hidden ml-2 mt-2 space-y-1">
                                    <?php foreach ($categories as $cat): ?>
                                        <div class="folder-tree px-4 py-2 rounded-lg flex items-center text-sm ml-4 hover:bg-gray-100" onclick="navigateToPath('category-<?= $cat['id'] ?>')" id="nav-cat-<?= $cat['id'] ?>">
                                            <i class="fas fa-tag mr-2 text-xs" style="color: <?= $cat['color_code'] ?? '#6366f1' ?>"></i>
                                            <span class="text-gray-600 text-xs"><?= $cat['name'] ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="lg:col-span-3 space-y-4">
                
                <!-- Breadcrumb -->
                <div class="bg-white rounded-xl border border-gray-200 px-6 py-4">
                    <nav class="flex items-center text-sm">
                        <i class="fas fa-home text-indigo-600 mr-2 cursor-pointer hover:text-indigo-700" onclick="navigateToPath('all')" title="Home"></i>
                        <a href="#" onclick="navigateToPath('all'); return false;" class="text-indigo-600 hover:text-indigo-700 font-medium">Files</a>
                        <span id="breadcrumbPath"></span>
                    </nav>
                </div>

                <!-- Toolbar -->
                <div class="bg-white rounded-xl border border-gray-200 px-6 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <!-- Search -->
                    <div class="flex-1 max-w-sm">
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                            <input type="text" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition" id="searchInput" placeholder="Search files...">
                        </div>
                    </div>
                    
                    <!-- View Toggle -->
                    <div class="flex items-center gap-2">
                        <div class="inline-flex rounded-lg border border-gray-300 bg-white p-1">
                            <button onclick="switchView('list')" id="listViewBtn" class="px-4 py-2 rounded-md bg-indigo-600 text-white transition-colors" title="List view">
                                <i class="fas fa-list"></i>
                            </button>
                            <button onclick="switchView('grid')" id="gridViewBtn" class="px-4 py-2 rounded-md text-gray-700 hover:text-gray-900 transition-colors" title="Grid view">
                                <i class="fas fa-th-large"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- List View -->
                <div id="listView" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Size</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Modified</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200" id="fileListBody">
                                <!-- Populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Grid View -->
                <div id="gridView" class="hidden grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4" id="fileGridBody">
                    <!-- Populated by JavaScript -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div class="fixed inset-0 z-50 hidden overflow-y-auto" id="uploadModal" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="uploadModalLabel">
                    <i class="fas fa-upload mr-2"></i>Upload Files
                </h3>
                <form id="uploadForm" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Project <span class="text-red-600">*</span></label>
                            <select name="project_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Select Project</option>
                                <?php if (!empty($projects)): ?>
                                    <?php foreach ($projects as $proj): ?>
                                        <option value="<?= $proj['id'] ?>" <?= (!empty($project) && $project['id'] == $proj['id']) ? 'selected' : '' ?>>
                                            <?= $proj['name'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Select Files <span class="text-red-600">*</span></label>
                            <input type="file" name="files[]" multiple required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                            <select name="category_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= $cat['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tags (comma-separated)</label>
                            <input type="text" name="tags" placeholder="e.g., blueprint, urgent, approved" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>

                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm">
                            Upload
                        </button>
                        <button type="button" onclick="document.getElementById('uploadModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let csrfName = '<?= csrf_token() ?>';
let csrfHash = '<?= csrf_hash() ?>';
let currentPath = 'all';

// File data organized by project
const filesData = {
    <?php foreach ($projects as $proj): ?>
    'project-<?= $proj['id'] ?>': <?= json_encode(array_filter($files, function($f) use ($proj) { return $f['project_id'] == $proj['id']; })) ?>,
    <?php endforeach; ?>
    'all': <?= json_encode($files) ?>
};

// Category data
const categoriesData = <?= json_encode($categories) ?>;

// Projects data
const projectsData = <?= json_encode($projects) ?>;

// Base URL for links
const baseUrl = '<?= base_url() ?>';

function navigateToPath(path) {
    currentPath = path;
    updateBreadcrumb();
    filterAndDisplayFiles();
    highlightCurrentPath();
}

function highlightCurrentPath() {
    // Remove highlight from all items
    document.querySelectorAll('.folder-tree').forEach(el => {
        el.classList.remove('bg-indigo-50', 'border-l-4', 'border-l-indigo-600');
    });
    
    // Highlight current item
    if (currentPath === 'all') {
        document.querySelector('[onclick*="navigateToPath(\'all\')"]').classList.add('bg-indigo-50', 'border-l-4', 'border-l-indigo-600');
    } else {
        const pathElements = currentPath.split('-');
        if (pathElements[0] === 'project') {
            document.querySelector('[onclick*="navigateToPath(\'project-' + pathElements[1] + '\')"]').classList.add('bg-indigo-50', 'border-l-4', 'border-l-indigo-600');
        } else if (pathElements[0] === 'category') {
            document.querySelector('[onclick*="navigateToPath(\'category-' + pathElements[1] + '\')"]').classList.add('bg-indigo-50', 'border-l-4', 'border-l-indigo-600');
        }
    }
}

function filterAndDisplayFiles() {
    const pathParts = currentPath.split('-');
    let filesToShow = [];
    let categoriesToShow = [];

    if (currentPath === 'all') {
        filesToShow = filesData['all'];
        categoriesToShow = categoriesData;
    } else if (pathParts[0] === 'project') {
        const projectId = pathParts[1];
        filesToShow = filesData['project-' + projectId] || [];
        // Only show categories that have files in this project
        categoriesToShow = categoriesData.filter(cat => 
            filesToShow.some(f => f.category_id == cat.id)
        );
    } else if (pathParts[0] === 'category') {
        const categoryId = pathParts[1];
        filesToShow = filesData['all'].filter(f => f.category_id == categoryId);
        categoriesToShow = categoriesData.filter(cat => cat.id == categoryId);
    }

    updateListView(filesToShow, categoriesToShow);
    updateGridView(filesToShow, categoriesToShow);
}

function updateListView(files, categories) {
    const tbody = document.getElementById('fileListBody');
    let html = '';

    // Add categories as folders
    categories.forEach(cat => {
        html += `
            <tr class="file-item hover:bg-gray-50 category-row" data-type="category" data-id="${cat.id}">
                <td class="px-6 py-4 text-sm text-gray-900 cursor-pointer" onclick="navigateToPath('category-${cat.id}')">
                    <i class="fas fa-folder-open mr-2" style="color: ${cat.color_code || '#6366f1'}"></i>
                    <span class="font-medium">${cat.name}</span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">Folder</td>
                <td class="px-6 py-4 text-sm text-gray-600">-</td>
                <td class="px-6 py-4 text-sm text-gray-600">-</td>
                <td class="px-6 py-4 text-sm space-x-2"></td>
            </tr>
        `;
    });

    // Add files
    if (files.length > 0) {
        files.forEach(file => {
            const ext = (file.file_type || '').toLowerCase();
            let iconClass = 'fa-file';
            let iconColor = 'text-gray-400';
            
            if (['pdf'].includes(ext)) {
                iconClass = 'fa-file-pdf';
                iconColor = 'text-red-500';
            } else if (['doc', 'docx'].includes(ext)) {
                iconClass = 'fa-file-word';
                iconColor = 'text-blue-600';
            } else if (['xls', 'xlsx'].includes(ext)) {
                iconClass = 'fa-file-excel';
                iconColor = 'text-green-600';
            } else if (['ppt', 'pptx'].includes(ext)) {
                iconClass = 'fa-file-powerpoint';
                iconColor = 'text-orange-600';
            } else if (['jpg', 'jpeg', 'png', 'gif', 'svg', 'bmp'].includes(ext)) {
                iconClass = 'fa-file-image';
                iconColor = 'text-purple-500';
            } else if (['zip', 'rar', '7z', 'tar', 'gz', 'bz2', 'iso', 'dmg'].includes(ext)) {
                iconClass = 'fa-file-archive';
                iconColor = 'text-amber-600';
            } else if (['mp4', 'avi', 'mkv', 'mov', 'wmv', 'flv', 'webm', 'm4v', 'mpg', 'mpeg'].includes(ext)) {
                iconClass = 'fa-file-video';
                iconColor = 'text-red-500';
            } else if (['mp3', 'wav', 'flac', 'aac', 'm4a', 'wma', 'ogg', 'aiff'].includes(ext)) {
                iconClass = 'fa-file-audio';
                iconColor = 'text-indigo-600';
            } else if (['php', 'php3', 'js', 'ts', 'jsx', 'tsx', 'py', 'java', 'cpp', 'c', 'cs', 'html', 'css', 'json', 'xml', 'sql', 'rb'].includes(ext)) {
                iconClass = 'fa-file-code';
                iconColor = 'text-slate-700';
            } else if (['txt', 'rtf', 'md', 'log', 'ini', 'conf'].includes(ext)) {
                iconClass = 'fa-file-alt';
                iconColor = 'text-gray-600';
            }

            const date = new Date(file.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
            const viewUrl = baseUrl + 'file-management/view/' + file.id;
            
            html += `
                <tr class="file-item hover:bg-gray-50 cursor-pointer select-none" data-type="file" data-id="${file.id}" data-search="${file.original_file_name.toLowerCase()}">
                    <td class="px-6 py-4 text-sm text-gray-900">
                        <i class="fas ${iconClass} ${iconColor} mr-2"></i>
                        <a href="${viewUrl}" class="text-indigo-600 hover:text-indigo-800 font-medium">${file.original_file_name}</a>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">${ext.toUpperCase()}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">${formatFileSize(file.file_size)}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">${date}</td>
                    <td class="px-6 py-4 text-sm space-x-3 flex">
                        <a href="${baseUrl}file-management/download/${file.id}" class="text-indigo-600 hover:text-indigo-800 cursor-pointer" title="Download">
                            <i class="fas fa-download"></i>
                        </a>
                        <a href="${viewUrl}" class="text-green-600 hover:text-green-800 cursor-pointer" title="View">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="javascript:void(0);" onclick="event.stopPropagation(); deleteFile(${file.id});" class="text-red-600 hover:text-red-800 cursor-pointer" title="Delete">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
            `;
        });
    } else if (categories.length === 0) {
        html += `
            <tr>
                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                    <i class="fas fa-folder-open text-4xl mb-2 block opacity-30"></i>
                    <p class="text-lg">Empty folder</p>
                    <button type="button" class="text-indigo-600 hover:text-indigo-800 font-semibold mt-2" onclick="document.getElementById('uploadModal').classList.remove('hidden')">Upload files here</button>
                </td>
            </tr>
        `;
    }

    tbody.innerHTML = html;
}

function updateGridView(files, categories) {
    const gridBody = document.getElementById('fileGridBody');
    let html = '';

    // Add category folders
    categories.forEach(cat => {
        html += `
            <div class="group cursor-pointer p-4 bg-white rounded-lg border border-gray-200 hover:shadow-lg hover:border-indigo-300 transition-all text-center category-card" data-type="category" data-id="${cat.id}" onclick="navigateToPath('category-${cat.id}')">
                <div class="mb-3">
                    <i class="fas fa-folder-open text-6xl" style="color: ${cat.color_code || '#6366f1'}"></i>
                </div>
                <p class="font-medium text-gray-900 text-sm mb-1 truncate">${cat.name}</p>
                <p class="text-xs text-gray-500">Folder</p>
            </div>
        `;
    });

    // Add files
    if (files.length > 0) {
        files.forEach(file => {
            const ext = (file.file_type || '').toLowerCase();
            let iconClass = 'fa-file';
            let iconColor = 'text-gray-400';
            
            if (['pdf'].includes(ext)) {
                iconClass = 'fa-file-pdf'; iconColor = 'text-red-600';
            } else if (['doc', 'docx', 'dot', 'dotx', 'docm'].includes(ext)) {
                iconClass = 'fa-file-word'; iconColor = 'text-blue-600';
            } else if (['xls', 'xlsx', 'xlsm', 'xltx', 'csv'].includes(ext)) {
                iconClass = 'fa-file-excel'; iconColor = 'text-green-600';
            } else if (['ppt', 'pptx', 'pptm', 'potx', 'odp'].includes(ext)) {
                iconClass = 'fa-file-powerpoint'; iconColor = 'text-orange-600';
            } else if (['jpg', 'jpeg', 'png', 'gif', 'svg', 'bmp', 'ico', 'tiff', 'webp', 'avif'].includes(ext)) {
                iconClass = 'fa-file-image'; iconColor = 'text-purple-500';
            } else if (['zip', 'rar', '7z', 'tar', 'gz', 'bz2', 'iso', 'dmg'].includes(ext)) {
                iconClass = 'fa-file-archive'; iconColor = 'text-amber-600';
            } else if (['mp4', 'avi', 'mkv', 'mov', 'wmv', 'flv', 'webm', 'm4v', 'mpg', 'mpeg'].includes(ext)) {
                iconClass = 'fa-file-video'; iconColor = 'text-red-500';
            } else if (['mp3', 'wav', 'flac', 'aac', 'm4a', 'wma', 'ogg', 'aiff'].includes(ext)) {
                iconClass = 'fa-file-audio'; iconColor = 'text-indigo-600';
            } else if (['php', 'php3', 'js', 'ts', 'jsx', 'tsx', 'py', 'java', 'cpp', 'c', 'cs', 'html', 'css', 'json', 'xml', 'sql', 'rb'].includes(ext)) {
                iconClass = 'fa-file-code'; iconColor = 'text-slate-700';
            } else if (['txt', 'rtf', 'md', 'log', 'ini', 'conf'].includes(ext)) {
                iconClass = 'fa-file-alt'; iconColor = 'text-gray-600';
            }

            html += `
                <div class="group p-4 bg-white rounded-lg border border-gray-200 hover:shadow-lg hover:border-indigo-300 transition-all text-center" data-type="file" data-id="${file.id}" data-search="${file.original_file_name.toLowerCase()}">
                    <div class="mb-3 relative">
                        <i class="fas ${iconClass} ${iconColor} text-5xl"></i>
                    </div>
                    <p class="font-medium text-gray-900 text-xs mb-1 truncate" title="${file.original_file_name}">
                        ${file.original_file_name}
                    </p>
                    <p class="text-xs text-gray-500 mb-3">${formatFileSize(file.file_size)}</p>
                    <div class="flex justify-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        <a href="${baseUrl}file-management/download/${file.id}" class="text-indigo-600 hover:text-indigo-800 cursor-pointer" title="Download">
                            <i class="fas fa-download"></i>
                        </a>
                        <a href="${baseUrl}file-management/view/${file.id}" class="text-green-600 hover:text-green-800 cursor-pointer" title="View">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="javascript:void(0);" onclick="deleteFile(${file.id});" class="text-red-600 hover:text-red-800 cursor-pointer" title="Delete">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </div>
            `;
        });
    } else if (categories.length === 0) {
        html += `
            <div class="col-span-full text-center py-12 text-gray-500">
                <i class="fas fa-folder-open text-6xl mb-4 block opacity-30"></i>
                <p class="text-lg">Empty folder</p>
                <button type="button" class="text-indigo-600 hover:text-indigo-800 font-semibold mt-2" onclick="document.getElementById('uploadModal').classList.remove('hidden')">Upload files here</button>
            </div>
        `;
    }

    gridBody.innerHTML = html;
}

function formatFileSize(bytes) {
    if (bytes >= 1073741824) return (bytes / 1073741824).toFixed(2) + ' GB';
    if (bytes >= 1048576) return (bytes / 1048576).toFixed(2) + ' MB';
    if (bytes >= 1024) return (bytes / 1024).toFixed(2) + ' KB';
    return bytes + ' B';
}

function updateBreadcrumb() {
    const breadcrumb = document.getElementById('breadcrumbPath');
    const parts = currentPath.split('-');
    
    if (parts[0] === 'project') {
        const projectId = parts[1];
        const proj = projectsData.find(p => p.id == projectId);
        breadcrumb.innerHTML = ' / <a href="#" onclick="navigateToPath(\'project-' + projectId + '\'); return false;" class="text-indigo-600 hover:text-indigo-700">' + (proj?.name || 'Project') + '</a>';
    } else if (parts[0] === 'category') {
        const categoryId = parts[1];
        const cat = categoriesData.find(c => c.id == categoryId);
        breadcrumb.innerHTML = ' / <a href="#" onclick="navigateToPath(\'category-' + categoryId + '\'); return false;" class="text-indigo-600 hover:text-indigo-700">' + (cat?.name || 'Category') + '</a>';
    } else {
        breadcrumb.innerHTML = '';
    }
}

document.getElementById('uploadForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('<?= base_url('file-management/upload') ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': csrfHash
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.csrfHash) {
            csrfHash = data.csrfHash;
            document.querySelectorAll('input[name="' + csrfName + '"]').forEach(el => el.value = csrfHash);
        }
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    });
});

function deleteFile(fileId) {
    if (confirm('Are you sure you want to delete this file?')) {
        fetch('<?= base_url('file-management/delete') ?>/' + fileId, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfHash
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.csrfHash) {
                csrfHash = data.csrfHash;
                document.querySelectorAll('input[name="' + csrfName + '"]').forEach(el => el.value = csrfHash);
            }
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        });
    }
}

function switchView(view) {
    const gridView = document.getElementById('gridView');
    const listView = document.getElementById('listView');
    const gridBtn = document.getElementById('gridViewBtn');
    const listBtn = document.getElementById('listViewBtn');

    if (view === 'grid') {
        gridView.classList.remove('hidden');
        listView.classList.add('hidden');
        gridBtn.classList.remove('bg-white', 'border-gray-300');
        gridBtn.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
        listBtn.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
        listBtn.classList.add('bg-white', 'border-gray-300');
        localStorage.setItem('fileViewMode', 'grid');
    } else {
        gridView.classList.add('hidden');
        listView.classList.remove('hidden');
        listBtn.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
        listBtn.classList.remove('bg-white', 'border-gray-300');
        gridBtn.classList.add('bg-white', 'border-gray-300');
        gridBtn.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
        localStorage.setItem('fileViewMode', 'list');
    }
}

// Expand/collapse project folders
document.querySelectorAll('[id^="toggle-"]').forEach(toggle => {
    toggle.parentElement.addEventListener('click', function(e) {
        e.stopPropagation();
        const projectId = this.id.replace('toggle-', '');
        const categories = document.getElementById('categories-' + projectId);
        const icon = document.getElementById('toggle-' + projectId);
        
        if (categories.classList.contains('hidden')) {
            categories.classList.remove('hidden');
            icon.classList.add('expanded');
        } else {
            categories.classList.add('hidden');
            icon.classList.remove('expanded');
        }
    });
});

// Search functionality
document.getElementById('searchInput').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    document.querySelectorAll('[data-search]').forEach(el => {
        el.style.display = !searchTerm || el.getAttribute('data-search').includes(searchTerm) ? '' : 'none';
    });
});

// Load saved view preference
document.addEventListener('DOMContentLoaded', function() {
    const savedView = localStorage.getItem('fileViewMode') || 'list';
    switchView(savedView);
    navigateToPath('all');
});
</script>

<?= $this->endSection() ?>

