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
            <label class="block text-sm font-medium text-gray-700 mb-2">Recipients <span class="text-red-600">*</span></label>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <button type="button" id="openRecipientModal" 
                            class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-left transition-colors">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-700" id="selectedUsersText">Select recipients...</span>
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                    </button>
                </div>
                <div id="selectedUsersContainer" class="flex flex-wrap gap-2"></div>
                <input type="hidden" name="recipient_ids" id="recipientIds" required>
                <p class="text-xs text-gray-500">Click the button above to select recipients</p>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Message</label>
            <textarea name="message" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" placeholder="Type your message here..."></textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Attachment (optional)</label>
            <input type="file" name="attachment" id="attachmentInput" 
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            <p class="text-xs text-gray-500 mt-1">Allowed: images, PDF, Word, Excel, PowerPoint. Max 2MB.</p>
            <p id="fileError" class="text-xs text-red-600 mt-1 hidden"></p>
        </div>
        <div class="flex justify-end">
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                </svg>
                Send Message
            </button>
        </div>
    </form>
</div>

<!-- Recipient Selection Modal -->
<div id="recipientModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4 max-h-[80vh] overflow-hidden">
        <div class="flex items-center justify-between p-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Select Recipients</h3>
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-500" id="selectedCount">0 selected</span>
                <button type="button" id="closeModalBtn" class="p-1 hover:bg-gray-100 rounded-full">
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        <div class="p-4 border-b border-gray-200">
            <div class="flex items-center space-x-2">
                <input type="text" id="userSearch" placeholder="Search users..." 
                       class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <button type="button" id="selectAllBtn" class="px-4 py-2 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                    Select All
                </button>
                <button type="button" id="clearAllBtn" class="px-4 py-2 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                    Clear All
                </button>
            </div>
        </div>
        <div class="p-4 overflow-y-auto max-h-[40vh]">
            <div id="userList" class="space-y-2">
                <?php if (empty($users)): ?>
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                        No users available
                    </div>
                <?php else: ?>
                    <?php foreach ($users as $user): ?>
                        <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer user-item" 
                                data-name="<?= esc($user['first_name'] . ' ' . $user['last_name']) ?>"
                                data-email="<?= esc($user['email'] ?? '') ?>">
                            <input type="checkbox" class="user-checkbox mr-3 h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" 
                                   value="<?= $user['id'] ?>">
                            <div class="flex-1">
                                <div class="font-medium text-gray-900"><?= esc($user['first_name'] . ' ' . $user['last_name']) ?></div>
                                <?php if (!empty($user['email'])): ?>
                                    <div class="text-sm text-gray-500"><?= esc($user['email']) ?></div>
                                <?php endif; ?>
                            </div>
                        </label>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <div class="flex justify-between p-4 border-t border-gray-200 bg-gray-50">
            <div class="text-sm text-gray-600">
                <span id="currentSelection">0</span> of <span id="totalUsers"><?= count($users ?? []) ?></span> users selected
            </div>
            <div class="flex space-x-3">
                <button type="button" id="cancelModalBtn" class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-100">
                    Cancel
                </button>
                <button type="button" id="confirmModalBtn" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    Confirm Selection
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Modal Elements
    const modal = document.getElementById('recipientModal');
    const openModalBtn = document.getElementById('openRecipientModal');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const cancelModalBtn = document.getElementById('cancelModalBtn');
    const confirmModalBtn = document.getElementById('confirmModalBtn');
    const userSearch = document.getElementById('userSearch');
    const selectAllBtn = document.getElementById('selectAllBtn');
    const clearAllBtn = document.getElementById('clearAllBtn');
    const userList = document.getElementById('userList');
    const userItems = document.querySelectorAll('.user-item');
    const checkboxes = document.querySelectorAll('.user-checkbox');
    const selectedUsersContainer = document.getElementById('selectedUsersContainer');
    const selectedUsersText = document.getElementById('selectedUsersText');
    const selectedCount = document.getElementById('selectedCount');
    const currentSelection = document.getElementById('currentSelection');
    const totalUsers = document.getElementById('totalUsers');
    const recipientIds = document.getElementById('recipientIds');
    const attachmentInput = document.getElementById('attachmentInput');
    const fileError = document.getElementById('fileError');
    const form = document.querySelector('form');
    
    let selectedUsers = [];

    // File validation
    const MAX_FILE_SIZE = 2 * 1024 * 1024; // 2MB
    const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];
    
    if (attachmentInput) {
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
    }

    // Modal Controls
    function openModal() {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        updateSelectionDisplay();
    }

    function closeModal() {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    openModalBtn.addEventListener('click', openModal);
    closeModalBtn.addEventListener('click', closeModal);
    cancelModalBtn.addEventListener('click', closeModal);

    // Close modal when clicking outside
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModal();
        }
    });

    // Keyboard support
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });

    // Search functionality
    userSearch.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        userItems.forEach(item => {
            const name = item.dataset.name.toLowerCase();
            const email = item.dataset.email.toLowerCase();
            if (name.includes(searchTerm) || email.includes(searchTerm)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });

    // Select All / Clear All
    selectAllBtn.addEventListener('click', function() {
        checkboxes.forEach(checkbox => {
            checkbox.checked = true;
        });
        updateSelectionDisplay();
    });

    clearAllBtn.addEventListener('click', function() {
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        updateSelectionDisplay();
    });

    // Checkbox handling
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectionDisplay);
    });

    // Confirm selection
    confirmModalBtn.addEventListener('click', function() {
        updateSelectedUsers();
        closeModal();
    });

    // Update selection display
    function updateSelectionDisplay() {
        const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
        const count = checkedBoxes.length;
        selectedCount.textContent = count;
        currentSelection.textContent = count;
        
        if (count === 0) {
            selectedUsersText.textContent = 'Select recipients...';
        } else if (count === 1) {
            const firstChecked = checkedBoxes[0];
            const userItem = firstChecked.closest('.user-item');
            selectedUsersText.textContent = userItem.dataset.name;
        } else {
            selectedUsersText.textContent = `${count} recipients selected`;
        }
    }

    // Update selected users display and hidden field
    function updateSelectedUsers() {
        const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
        selectedUsers = Array.from(checkedBoxes).map(cb => cb.value);
        recipientIds.value = selectedUsers.join(',');
        
        // Update visual display
        selectedUsersContainer.innerHTML = '';
        
        if (selectedUsers.length === 0) {
            selectedUsersText.textContent = 'Select recipients...';
            return;
        }

        if (selectedUsers.length === 1) {
            const firstChecked = checkedBoxes[0];
            const userItem = firstChecked.closest('.user-item');
            const userName = userItem.dataset.name;
            selectedUsersText.textContent = userName;
        } else {
            selectedUsersText.textContent = `${selectedUsers.length} recipients selected`;
        }

        // Create badges for selected users
        checkedBoxes.forEach(checkbox => {
            const userItem = checkbox.closest('.user-item');
            const userName = userItem.dataset.name;
            const userId = checkbox.value;
            
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
            selectedUsersContainer.appendChild(badge);
        });

        // Add remove handlers
        document.querySelectorAll('.remove-user').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const userId = this.dataset.id;
                const checkbox = document.querySelector(`.user-checkbox[value="${userId}"]`);
                if (checkbox) {
                    checkbox.checked = false;
                    updateSelectionDisplay();
                    updateSelectedUsers();
                }
            });
        });
    }

    // Form submission validation
    form.addEventListener('submit', function(e) {
        if (selectedUsers.length === 0) {
            e.preventDefault();
            alert('Please select at least one recipient');
            openModal(); // Open modal to prompt user
            return false;
        }
        console.log('Submitting with recipients:', recipientIds.value);
    });

    console.log('Modal-based recipient selector ready');
});
</script>
<?= $this->endSection() ?>
