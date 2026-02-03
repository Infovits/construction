<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Start a Conversation</h1>
        <p class="text-gray-600">Select a colleague and send a message</p>
    </div>

    <?php if (session('error')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <?= esc(session('error')) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= base_url('admin/messages/start') ?>" enctype="multipart/form-data" class="bg-white rounded-lg shadow-sm border p-6 space-y-4">
        <?= csrf_field() ?>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Recipients</label>
            <div class="relative" style="z-index: 100;">
                <button type="button" id="dropdownToggle" class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-left hover:bg-gray-50 flex items-center justify-between">
                    <span id="selectedLabel" class="text-gray-700">Select recipients...</span>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <input type="hidden" name="recipient_ids" id="recipientIds" required>
                <div id="selectedUsers" class="mt-2 flex flex-wrap gap-2 hidden"></div>
                <ul id="userDropdown" class="hidden absolute top-full left-0 right-0 border border-gray-300 bg-white rounded-lg shadow-lg mt-1 max-h-64 overflow-y-auto" style="z-index: 101;">
                    <?php foreach ($users as $user): ?>
                        <li class="user-option p-3 hover:bg-indigo-50 cursor-pointer border-b last:border-b-0 flex items-center" 
                            data-id="<?= $user['id'] ?>" 
                            data-name="<?= esc($user['first_name'] . ' ' . $user['last_name']) ?>">
                            <input type="checkbox" class="user-checkbox mr-3 rounded" data-id="<?= $user['id'] ?>">
                            <div>
                                <div class="font-medium text-gray-900"><?= esc($user['first_name'] . ' ' . $user['last_name']) ?></div>
                                <div class="text-sm text-gray-500"><?= esc($user['email']) ?></div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Message</label>
            <textarea name="message" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"></textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Attachment (optional)</label>
            <input type="file" name="attachment" id="attachmentInput" 
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            <p class="text-xs text-gray-500 mt-1">Allowed: images, PDF, Word, Excel, PowerPoint. Max 2MB.</p>
            <p id="fileError" class="text-xs text-red-600 mt-1 hidden"></p>
        </div>
        <div class="flex justify-end">
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Send</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('recipientSearch');
    const recipientIds = document.getElementById('recipientIds');
    const dropdown = document.getElementById('userDropdown');
    const dropdownToggle = document.getElementById('dropdownToggle');
    const options = document.querySelectorAll('.user-option');
    const checkboxes = document.querySelectorAll('.user-checkbox');
    const selectedUsersDiv = document.getElementById('selectedUsers');
    const attachmentInput = document.getElementById('attachmentInput');
    const fileError = document.getElementById('fileError');
    const form = document.querySelector('form');
    
    let selectedUsers = [];

    // File validation
    const MAX_FILE_SIZE = 2 * 1024 * 1024; // 2MB
    const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];
    
    attachmentInput.addEventListener('change', function(e) {
        fileError.classList.add('hidden');
        fileError.textContent = '';

        if (!this.files || this.files.length === 0) {
            return;
        }

        const file = this.files[0];
        const extension = file.name.split('.').pop().toLowerCase();

        if (file.size > MAX_FILE_SIZE) {
            fileError.textContent = 'File size exceeds 2MB limit. Please choose a smaller file.';
            fileError.classList.remove('hidden');
            attachmentInput.value = '';
            return;
        }

        if (!ALLOWED_EXTENSIONS.includes(extension)) {
            fileError.textContent = 'File type not allowed. Please use images, PDF, or Office documents.';
            fileError.classList.remove('hidden');
            attachmentInput.value = '';
            return;
        }
    });

    // Form submission validation
    form.addEventListener('submit', function(e) {
        if (selectedUsers.length === 0) {
            e.preventDefault();
            alert('Please select at least one recipient');
            return false;
        }
        // Update hidden field with selected user IDs
        recipientIds.value = selectedUsers.join(',');
    });

    // Toggle dropdown with arrow button
    dropdownToggle.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        dropdown.classList.toggle('hidden');
        if (!dropdown.classList.contains('hidden')) {
            options.forEach(option => option.style.display = 'block');
        }
    });

    // Show dropdown on focus
    searchInput.addEventListener('focus', function(e) {
        e.stopPropagation();
        dropdown.classList.remove('hidden');
        if (!this.value.trim()) {
            options.forEach(option => option.style.display = 'block');
        }
    });
    
    searchInput.addEventListener('click', function(e) {
        e.stopPropagation();
        e.stopImmediatePropagation();
    });

    // Filter users as they type
    searchInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase().trim();
        let hasVisibleOptions = false;

        options.forEach(option => {
            const searchText = option.dataset.search;
            if (!searchTerm || searchText.includes(searchTerm)) {
                option.style.display = 'block';
                hasVisibleOptions = true;
            } else {
                option.style.display = 'none';
            }
        });

        dropdown.classList.remove('hidden');
    });

    // Update selected users display
    function updateSelectedDisplay() {
        if (selectedUsers.length === 0) {
            selectedUsersDiv.classList.add('hidden');
            searchInput.placeholder = 'Search or select users (click to select multiple)...';
            return;
        }

        selectedUsersDiv.classList.remove('hidden');
        selectedUsersDiv.innerHTML = '';
        
        selectedUsers.forEach(userId => {
            const option = document.querySelector(`.user-option[data-id="${userId}"]`);
            if (!option) return;
            
            const userName = option.dataset.name;
            const badge = document.createElement('span');
            badge.className = 'inline-flex items-center gap-1 px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-sm';
            badge.innerHTML = `
                ${userName}
                <button type="button" class="remove-user hover:text-indigo-600" data-id="${userId}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            `;
            
            selectedUsersDiv.appendChild(badge);
        });

        // Add remove handlers
        document.querySelectorAll('.remove-user').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const userId = this.dataset.id;
                selectedUsers = selectedUsers.filter(id => id !== userId);
                const checkbox = document.querySelector(`.user-checkbox[data-id="${userId}"]`);
                if (checkbox) checkbox.checked = false;
                updateSelectedDisplay();
            });
        });

        searchInput.placeholder = `${selectedUsers.length} user(s) selected`;
    }

    // Handle checkbox changes
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function(e) {
            e.stopPropagation();
            const userId = this.dataset.id;
            
            if (this.checked) {
                if (!selectedUsers.includes(userId)) {
                    selectedUsers.push(userId);
                }
            } else {
                selectedUsers = selectedUsers.filter(id => id !== userId);
            }
            
            updateSelectedDisplay();
        });
    });

    // Handle option click (toggle checkbox)
    options.forEach(option => {
        option.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const userId = this.dataset.id;
            const checkbox = this.querySelector('.user-checkbox');
            
            if (checkbox) {
                checkbox.checked = !checkbox.checked;
                checkbox.dispatchEvent(new Event('change'));
            }
        });
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#recipientSearch') && 
            !e.target.closest('#dropdownToggle') && 
            !e.target.closest('#userDropdown')) {
            dropdown.classList.add('hidden');
        }
    });

    dropdown.addEventListener('click', function(e) {
        e.stopPropagation();
    });
});
</script>
<?= $this->endSection() ?>
