<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Archived Files<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
.file-item { transition: all 0.2s ease; }
.file-item:hover { background-color: #f8fafc; }
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
                    <h1 class="text-3xl font-bold text-gray-900"><i class="fas fa-archive mr-3 text-gray-600"></i>Archived Files</h1>
                    <p class="text-gray-500 mt-2 text-sm">View and manage archived documents</p>
                </div>
                <a href="<?= base_url('file-management') ?>" class="inline-flex items-center px-6 py-2.5 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-all shadow-sm hover:shadow-md font-medium">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Files
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="px-6 py-6 sm:px-8">
        <!-- Toolbar -->
        <div class="bg-white rounded-xl border border-gray-200 px-6 py-4 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-4">
            <!-- Search -->
            <div class="flex-1 max-w-sm">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    <input type="text" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition" id="searchInput" placeholder="Search archived files...">
                </div>
            </div>

            <!-- Category Filter -->
            <div class="min-w-[220px]">
                <div class="relative">
                    <i class="fas fa-filter absolute left-3 top-3 text-gray-400"></i>
                    <select id="categoryFilter" class="w-full pl-10 pr-8 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= $cat['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Extension Filter -->
            <div class="min-w-[220px]">
                <div class="relative">
                    <i class="fas fa-file-code absolute left-3 top-3 text-gray-400"></i>
                    <select id="extensionFilter" class="w-full pl-10 pr-8 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                        <option value="">All Types</option>
                        <option value="pdf">PDF</option>
                        <option value="doc,docx">Word</option>
                        <option value="xls,xlsx">Excel</option>
                        <option value="ppt,pptx">PowerPoint</option>
                        <option value="jpg,jpeg,png,gif,svg,bmp">Images</option>
                        <option value="zip,rar,7z,tar,gz">Archives</option>
                        <option value="mp4,avi,mkv,mov,webm">Videos</option>
                        <option value="mp3,wav,flac,aac">Audio</option>
                    </select>
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
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Archived</th>
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
        <div id="fileGridBody" class="hidden">
            <!-- Populated by JavaScript -->
        </div>
    </div>
</div>

<script>
const csrfName = '<?= csrf_token() ?>';
let csrfHash = '<?= csrf_hash() ?>';

const filesData = <?= json_encode($files) ?>;
const categoriesData = <?= json_encode($categories) ?>;
const baseUrl = '<?= base_url() ?>';

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    filterFiles();
    
    // Restore view preference
    const savedView = localStorage.getItem('fileView') || 'list';
    switchView(savedView);
    
    // Event listeners
    document.getElementById('searchInput').addEventListener('input', filterFiles);
    document.getElementById('categoryFilter').addEventListener('change', filterFiles);
    document.getElementById('extensionFilter').addEventListener('change', filterFiles);
});

function filterFiles() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const categoryFilter = document.getElementById('categoryFilter').value;
    const extensionFilter = document.getElementById('extensionFilter').value;

    const filtered = filesData.filter(file => {
        const matchesSearch = !searchTerm || file.original_file_name.toLowerCase().includes(searchTerm);
        const matchesCategory = !categoryFilter || String(file.category_id) === categoryFilter;
        const matchesExtension = !extensionFilter || extensionFilter.split(',').includes(file.file_type.toLowerCase());
        
        return matchesSearch && matchesCategory && matchesExtension;
    });

    updateListView(filtered);
    updateGridView(filtered);
}

function updateListView(files) {
    const tbody = document.getElementById('fileListBody');
    if (files.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">No archived files found</td></tr>';
        return;
    }

    tbody.innerHTML = files.map(file => {
        const icon = getFileIcon(file.file_type);
        const size = formatFileSize(file.file_size);
        const date = new Date(file.created_at).toLocaleDateString();

        return `
            <tr class="file-item hover:bg-gray-50 cursor-pointer" onclick="window.location.href='${baseUrl}/file-management/view/${file.id}'" data-id="${file.id}" data-search="${file.original_file_name.toLowerCase()}" data-category="${file.category_id || ''}" data-extension="${file.file_type.toLowerCase()}">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        ${icon}
                        <span class="font-medium text-gray-900">${file.original_file_name}</span>
                    </div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600 uppercase">${file.file_type}</td>
                <td class="px-6 py-4 text-sm text-gray-600">${size}</td>
                <td class="px-6 py-4 text-sm text-gray-600">${date}</td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-2">
                        <button onclick="restoreFile(${file.id}); event.stopPropagation();" class="px-3 py-1 rounded text-emerald-600 hover:bg-emerald-50 hover:text-emerald-800 text-xs font-medium transition" title="Restore">
                            <i class="fas fa-undo"></i> Restore
                        </button>
                        <a href="${baseUrl}/file-management/download/${file.id}" onclick="event.stopPropagation();" class="px-3 py-1 rounded text-indigo-600 hover:bg-indigo-50 hover:text-indigo-800 text-xs font-medium transition" title="Download">
                            <i class="fas fa-download"></i> Download
                        </a>
                        <button onclick="deleteFile(${file.id}); event.stopPropagation();" class="px-3 py-1 rounded text-red-600 hover:bg-red-50 hover:text-red-800 text-xs font-medium transition" title="Delete">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
}

function updateGridView(files) {
    const gridBody = document.getElementById('fileGridBody');
    if (files.length === 0) {
        gridBody.innerHTML = '<div class="col-span-full text-center py-12 text-gray-500 bg-white rounded-xl border border-gray-200">No archived files found</div>';
        return;
    }

    gridBody.innerHTML = '<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">' +
        files.map(file => {
            const icon = getFileIcon(file.file_type);
            const size = formatFileSize(file.file_size);

            return `
                <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-lg transition-shadow cursor-pointer group relative" onclick="window.location.href='${baseUrl}/file-management/view/${file.id}'" data-id="${file.id}" data-search="${file.original_file_name.toLowerCase()}" data-category="${file.category_id || ''}" data-extension="${file.file_type.toLowerCase()}">
                    <div class="flex flex-col items-center text-center">
                        ${icon}
                        <h3 class="mt-3 font-medium text-gray-900 text-sm truncate w-full" title="${file.original_file_name}">${file.original_file_name}</h3>
                        <p class="text-xs text-gray-500 mt-1">${size}</p>
                    </div>
                    <div class="mt-3 flex justify-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button onclick="restoreFile(${file.id}); event.stopPropagation();" class="px-2 py-1 rounded text-xs font-medium text-emerald-600 hover:bg-emerald-50 hover:text-emerald-800 transition" title="Restore">
                            <i class="fas fa-undo"></i>
                        </button>
                        <a href="${baseUrl}/file-management/download/${file.id}" onclick="event.stopPropagation();" class="px-2 py-1 rounded text-xs font-medium text-indigo-600 hover:bg-indigo-50 hover:text-indigo-800 transition" title="Download">
                            <i class="fas fa-download"></i>
                        </a>
                        <button onclick="deleteFile(${file.id}); event.stopPropagation();" class="px-2 py-1 rounded text-xs font-medium text-red-600 hover:bg-red-50 hover:text-red-800 transition" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
        }).join('') + '</div>';
}

function getFileIcon(ext) {
    ext = ext.toLowerCase();
    let icon = 'fa-file';
    let color = 'text-gray-400';

    if (['pdf'].includes(ext)) { icon = 'fa-file-pdf'; color = 'text-red-600'; }
    else if (['doc', 'docx', 'dot', 'dotx'].includes(ext)) { icon = 'fa-file-word'; color = 'text-blue-600'; }
    else if (['xls', 'xlsx', 'csv'].includes(ext)) { icon = 'fa-file-excel'; color = 'text-green-600'; }
    else if (['ppt', 'pptx'].includes(ext)) { icon = 'fa-file-powerpoint'; color = 'text-orange-600'; }
    else if (['jpg', 'jpeg', 'png', 'gif', 'svg', 'bmp', 'webp', 'avif'].includes(ext)) { icon = 'fa-file-image'; color = 'text-purple-600'; }
    else if (['zip', 'rar', '7z', 'tar', 'gz'].includes(ext)) { icon = 'fa-file-archive'; color = 'text-amber-600'; }
    else if (['mp4', 'avi', 'mkv', 'mov', 'webm'].includes(ext)) { icon = 'fa-file-video'; color = 'text-red-500'; }
    else if (['mp3', 'wav', 'flac', 'aac'].includes(ext)) { icon = 'fa-file-audio'; color = 'text-indigo-600'; }
    else if (['txt', 'md'].includes(ext)) { icon = 'fa-file-alt'; color = 'text-gray-600'; }

    return `<div class="file-icon bg-gray-100"><i class="fas ${icon} ${color} text-2xl"></i></div>`;
}

function formatFileSize(bytes) {
    if (!bytes) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + ' ' + sizes[i];
}

function switchView(view) {
    const listView = document.getElementById('listView');
    const gridView = document.getElementById('fileGridBody');
    const listBtn = document.getElementById('listViewBtn');
    const gridBtn = document.getElementById('gridViewBtn');

    if (view === 'grid') {
        listView.classList.add('hidden');
        gridView.classList.remove('hidden');
        listBtn.classList.remove('bg-indigo-600', 'text-white');
        listBtn.classList.add('text-gray-700', 'hover:text-gray-900');
        gridBtn.classList.add('bg-indigo-600', 'text-white');
        gridBtn.classList.remove('text-gray-700', 'hover:text-gray-900');
    } else {
        listView.classList.remove('hidden');
        gridView.classList.add('hidden');
        listBtn.classList.add('bg-indigo-600', 'text-white');
        listBtn.classList.remove('text-gray-700', 'hover:text-gray-900');
        gridBtn.classList.remove('bg-indigo-600', 'text-white');
        gridBtn.classList.add('text-gray-700', 'hover:text-gray-900');
    }

    localStorage.setItem('fileView', view);
}

// Handle file restore
function restoreFile(fileId) {
    if (confirm('Are you sure you want to restore this file? It will be moved back to active files.')) {
        const csrfInput = document.querySelector('input[name="<?= csrf_token() ?>"]');
        const formData = new FormData();
        formData.append('<?= csrf_token() ?>', csrfInput?.value || '');
        
        fetch('<?= base_url('file-management/restore') ?>/' + fileId, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.csrfHash && csrfInput) {
                csrfInput.value = data.csrfHash;
            }
            if (data.success) {
                alert(data.message);
                window.location.href = '<?= base_url('file-management/archived') ?>';
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Restore error:', error);
            alert('Error: ' + error.message);
        });
    }
}

// Handle file deletion
function deleteFile(fileId) {
    if (confirm('Are you sure you want to permanently delete this file? This action cannot be undone.')) {
        const csrfInput = document.querySelector('input[name="<?= csrf_token() ?>"]');
        const formData = new FormData();
        formData.append('<?= csrf_token() ?>', csrfInput?.value || '');
        
        fetch('<?= base_url('file-management/delete') ?>/' + fileId, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.csrfHash && csrfInput) {
                csrfInput.value = data.csrfHash;
            }
            if (data.success) {
                alert(data.message);
                window.location.href = '<?= base_url('file-management/archived') ?>';
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Delete error:', error);
            alert('Error: ' + error.message);
        });
    }
}
</script>

<?= $this->endSection() ?>
