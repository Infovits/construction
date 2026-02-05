<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
// Load the file helper for formatFileSize function
helper('file');
?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Conversation</h1>
            <p class="text-gray-600">
                <?php foreach ($participants as $index => $p): ?>
                    <?= esc(trim($p['first_name'] . ' ' . $p['last_name'])) ?><?= $index < count($participants) - 1 ? ', ' : '' ?>
                <?php endforeach; ?>
            </p>
            <p id="typingIndicator" class="text-sm text-indigo-600 mt-1 hidden"></p>
        </div>
        <a href="<?= base_url('admin/messages') ?>" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Back to Inbox</a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border p-6 space-y-4">
        <div class="space-y-4 max-h-[60vh] overflow-y-auto">
            <?php if (empty($messages)): ?>
                <div class="text-center text-gray-500">No messages yet.</div>
            <?php else: ?>
                <?php foreach ($messages as $message): ?>
                    <?php $attachments = $attachmentsByMessage[$message['id']] ?? []; ?>
                    <div class="flex <?= $message['sender_id'] == session('user_id') ? 'justify-end' : 'justify-start' ?>">
                        <div class="max-w-[70%] p-3 rounded-lg <?= $message['sender_id'] == session('user_id') ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-900' ?>">
                            <div class="text-xs mb-1 <?= $message['sender_id'] == session('user_id') ? 'text-indigo-100' : 'text-gray-500' ?>">
                                <?= esc(trim($message['first_name'] . ' ' . $message['last_name'])) ?> • <?= format_datetime($message['created_at']) ?>
                            </div>
                            <div class="text-sm whitespace-pre-line"><?= esc($message['body']) ?></div>
                            <?php if (!empty($attachments)): ?>
                                <div class="mt-3 space-y-3">
                                    <?php foreach ($attachments as $file): ?>
                                        <?php 
                                            $isImage = in_array($file['mime_type'], ['image/png', 'image/jpeg', 'image/gif', 'image/webp']);
                                            $isDocument = in_array($file['mime_type'], [
                                                'application/pdf',
                                                'application/msword',
                                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                                'application/vnd.ms-excel',
                                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                                'application/vnd.ms-powerpoint',
                                                'application/vnd.openxmlformats-officedocument.presentationml.presentation'
                                            ]);
                                        ?>
                                        
                                        <div class="bg-gray-50 rounded-lg p-3 border border-gray-200 <?= $message['sender_id'] == session('user_id') ? 'bg-indigo-50 border-indigo-200' : 'bg-gray-50 border-gray-200' ?>">
                                            <?php if ($isImage): ?>
                                                <!-- Image Preview -->
                                                <div class="space-y-2">
                                                    <div class="text-xs <?= $message['sender_id'] == session('user_id') ? 'text-indigo-600' : 'text-gray-500' ?> font-medium">Image Attachment</div>
                                                    <div class="flex items-center space-x-3">
                                                        <img src="<?= base_url($file['file_path']) ?>" 
                                                             alt="<?= esc($file['file_name']) ?>"
                                                             class="w-16 h-16 object-cover rounded-md border border-gray-300 cursor-pointer hover:shadow-lg transition-shadow"
                                                             onclick="openImagePreview('<?= base_url($file['file_path']) ?>', '<?= esc($file['file_name']) ?>')">
                                                        <div class="flex-1 min-w-0">
                                                            <div class="font-medium <?= $message['sender_id'] == session('user_id') ? 'text-indigo-900' : 'text-gray-900' ?> text-sm truncate"><?= esc($file['file_name']) ?></div>
                                                            <div class="text-xs <?= $message['sender_id'] == session('user_id') ? 'text-indigo-600' : 'text-gray-500' ?>"><?= formatFileSize($file['file_size']) ?></div>
                                                            <a href="<?= base_url($file['file_path']) ?>" 
                                                               target="_blank" 
                                                               class="text-xs text-blue-600 hover:text-blue-800 mt-1 inline-block">
                                                                View Full Size
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php elseif ($isDocument): ?>
                                                <!-- Document Preview -->
                                                <div class="space-y-2">
                                                    <div class="flex items-center space-x-3">
                                                        <div class="w-12 h-12 bg-white border border-gray-300 rounded-md flex items-center justify-center">
                                                            <?php if (strpos($file['mime_type'], 'pdf') !== false): ?>
                                                                <i class="fas fa-file-pdf text-red-500 text-xl"></i>
                                                            <?php elseif (strpos($file['mime_type'], 'word') !== false || strpos($file['mime_type'], 'msword') !== false): ?>
                                                                <i class="fas fa-file-word text-blue-500 text-xl"></i>
                                                            <?php elseif (strpos($file['mime_type'], 'excel') !== false || strpos($file['mime_type'], 'spreadsheetml') !== false): ?>
                                                                <i class="fas fa-file-excel text-green-500 text-xl"></i>
                                                            <?php elseif (strpos($file['mime_type'], 'powerpoint') !== false || strpos($file['mime_type'], 'presentationml') !== false): ?>
                                                                <i class="fas fa-file-powerpoint text-orange-500 text-xl"></i>
                                                            <?php else: ?>
                                                                <i class="fas fa-file-alt text-gray-500 text-xl"></i>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="flex-1 min-w-0">
                                                            <div class="font-medium <?= $message['sender_id'] == session('user_id') ? 'text-indigo-900' : 'text-gray-900' ?> text-sm truncate"><?= esc($file['file_name']) ?></div>
                                                            <div class="text-xs <?= $message['sender_id'] == session('user_id') ? 'text-indigo-600' : 'text-gray-500' ?>"><?= formatFileSize($file['file_size']) ?></div>
                                                            <a href="<?= base_url($file['file_path']) ?>" 
                                                               target="_blank" 
                                                               class="text-xs text-blue-600 hover:text-blue-800 mt-1 inline-block">
                                                                Download Document
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <!-- Other File Types -->
                                                <div class="space-y-2">
                                                    <div class="flex items-center space-x-3">
                                                        <div class="w-12 h-12 bg-gray-200 rounded-md flex items-center justify-center">
                                                            <i class="fas fa-file text-gray-500 text-xl"></i>
                                                        </div>
                                                        <div class="flex-1 min-w-0">
                                                            <div class="font-medium <?= $message['sender_id'] == session('user_id') ? 'text-indigo-900' : 'text-gray-900' ?> text-sm truncate"><?= esc($file['file_name']) ?></div>
                                                            <div class="text-xs <?= $message['sender_id'] == session('user_id') ? 'text-indigo-600' : 'text-gray-500' ?>"><?= formatFileSize($file['file_size']) ?></div>
                                                            <a href="<?= base_url($file['file_path']) ?>" 
                                                               target="_blank" 
                                                               class="text-xs text-blue-600 hover:text-blue-800 mt-1 inline-block">
                                                                Download File
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <form method="POST" action="<?= base_url('admin/messages/' . $conversation['id'] . '/send') ?>" enctype="multipart/form-data" class="border-t pt-4" id="messageForm">
            <?= csrf_field() ?>
            <div class="flex items-center space-x-3">
                <textarea id="messageInput" name="message" rows="2" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" placeholder="Type a message..."></textarea>
                <input type="file" name="attachment" class="hidden" id="attachmentInput">
                <label for="attachmentInput" class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 cursor-pointer">Attach</label>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Send</button>
            </div>
            <p id="attachmentError" class="text-xs text-red-600 mt-2 hidden"></p>
            
            <!-- Attachment Preview Area -->
            <div id="attachmentPreview" class="mt-3 space-y-2 hidden">
                <div class="text-xs text-gray-600 font-medium">Attachment Preview</div>
                <div id="previewContainer" class="grid grid-cols-1 gap-2">
                    <!-- Preview items will be added here -->
                </div>
                <div class="flex justify-between items-center pt-2 border-t">
                    <button type="button" id="clearAttachment" class="text-sm text-red-600 hover:text-red-800">Remove Attachment</button>
                    <div id="previewFileSize" class="text-xs text-gray-500"></div>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
// File size formatting helper
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Image Preview Modal
function openImagePreview(imageUrl, imageName) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50';
    modal.innerHTML = `
        <div class="relative max-w-4xl max-h-full">
            <button onclick="this.closest('.fixed').remove()" class="absolute -top-10 right-0 text-white text-2xl hover:text-gray-300">
                ×
            </button>
            <img src="${imageUrl}" alt="${imageName}" class="max-w-full max-h-full object-contain">
            <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 text-white text-center">
                <div class="bg-black bg-opacity-50 px-4 py-2 rounded">${imageName}</div>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
    
    // Close on click outside
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.remove();
        }
    });
    
    // Close on Escape key
    document.addEventListener('keydown', function closeOnEscape(e) {
        if (e.key === 'Escape') {
            modal.remove();
            document.removeEventListener('keydown', closeOnEscape);
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const messageInput = document.getElementById('messageInput');
    const typingIndicator = document.getElementById('typingIndicator');
    const attachmentInput = document.getElementById('attachmentInput');
    const attachmentError = document.getElementById('attachmentError');
    const messageForm = document.getElementById('messageForm');
    const attachmentPreview = document.getElementById('attachmentPreview');
    const previewContainer = document.getElementById('previewContainer');
    const previewFileSize = document.getElementById('previewFileSize');
    const clearAttachment = document.getElementById('clearAttachment');
    let typingTimeout;

    // File validation
    const MAX_FILE_SIZE = 2 * 1024 * 1024; // 2MB
    const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];
    
    attachmentInput.addEventListener('change', function(e) {
        attachmentError.classList.add('hidden');
        attachmentError.textContent = '';
        attachmentPreview.classList.add('hidden');
        previewContainer.innerHTML = '';
        previewFileSize.textContent = '';

        if (!this.files || this.files.length === 0) {
            return;
        }

        const file = this.files[0];
        const extension = file.name.split('.').pop().toLowerCase();

        // Check file size
        if (file.size > MAX_FILE_SIZE) {
            attachmentError.textContent = 'File size exceeds 2MB limit. Please choose a smaller file.';
            attachmentError.classList.remove('hidden');
            attachmentInput.value = '';
            return;
        }

        // Check file extension
        if (!ALLOWED_EXTENSIONS.includes(extension)) {
            attachmentError.textContent = 'File type not allowed. Please use images, PDF, or Office documents.';
            attachmentError.classList.remove('hidden');
            attachmentInput.value = '';
            return;
        }

        // Show preview
        showFilePreview(file);
    });

    // Clear attachment functionality
    clearAttachment.addEventListener('click', function() {
        attachmentInput.value = '';
        attachmentPreview.classList.add('hidden');
        previewContainer.innerHTML = '';
        previewFileSize.textContent = '';
    });

    function showFilePreview(file) {
        previewContainer.innerHTML = '';
        
        // Show preview area
        attachmentPreview.classList.remove('hidden');
        
        // Show file size
        previewFileSize.textContent = formatFileSize(file.size);

        // Create preview element
        const previewItem = document.createElement('div');
        previewItem.className = 'bg-gray-50 rounded-lg p-3 border border-gray-200 flex items-center space-x-3';
        
        // Check if it's an image
        const isImage = file.type.startsWith('image/');
        
        if (isImage) {
            // Image preview
            const img = document.createElement('img');
            img.className = 'w-16 h-16 object-cover rounded-md border border-gray-300 cursor-pointer hover:shadow-lg transition-shadow';
            img.alt = file.name;
            
            // Create object URL for the image
            const objectURL = URL.createObjectURL(file);
            img.src = objectURL;
            
            // Add click handler to open full-size preview
            img.onclick = function() {
                openImagePreview(objectURL, file.name);
            };

            const infoDiv = document.createElement('div');
            infoDiv.className = 'flex-1 min-w-0';
            
            const nameDiv = document.createElement('div');
            nameDiv.className = 'font-medium text-gray-900 text-sm truncate';
            nameDiv.textContent = file.name;
            
            const sizeDiv = document.createElement('div');
            sizeDiv.className = 'text-xs text-gray-500';
            sizeDiv.textContent = formatFileSize(file.size);
            
            const removeBtn = document.createElement('button');
            removeBtn.className = 'text-xs text-red-600 hover:text-red-800';
            removeBtn.textContent = 'Remove';
            removeBtn.type = 'button';
            removeBtn.onclick = function() {
                attachmentInput.value = '';
                attachmentPreview.classList.add('hidden');
                previewContainer.innerHTML = '';
                previewFileSize.textContent = '';
            };

            infoDiv.appendChild(nameDiv);
            infoDiv.appendChild(sizeDiv);
            
            previewItem.appendChild(img);
            previewItem.appendChild(infoDiv);
            previewItem.appendChild(removeBtn);
        } else {
            // Document preview
            const iconDiv = document.createElement('div');
            iconDiv.className = 'w-12 h-12 bg-white border border-gray-300 rounded-md flex items-center justify-center';
            
            const icon = document.createElement('i');
            icon.className = 'text-xl';
            
            if (file.type.includes('pdf')) {
                icon.className += ' fas fa-file-pdf text-red-500';
            } else if (file.type.includes('word') || file.type.includes('msword')) {
                icon.className += ' fas fa-file-word text-blue-500';
            } else if (file.type.includes('excel') || file.type.includes('spreadsheetml')) {
                icon.className += ' fas fa-file-excel text-green-500';
            } else if (file.type.includes('powerpoint') || file.type.includes('presentationml')) {
                icon.className += ' fas fa-file-powerpoint text-orange-500';
            } else {
                icon.className += ' fas fa-file-alt text-gray-500';
            }
            
            iconDiv.appendChild(icon);

            const infoDiv = document.createElement('div');
            infoDiv.className = 'flex-1 min-w-0';
            
            const nameDiv = document.createElement('div');
            nameDiv.className = 'font-medium text-gray-900 text-sm truncate';
            nameDiv.textContent = file.name;
            
            const sizeDiv = document.createElement('div');
            sizeDiv.className = 'text-xs text-gray-500';
            sizeDiv.textContent = formatFileSize(file.size);
            
            const removeBtn = document.createElement('button');
            removeBtn.className = 'text-xs text-red-600 hover:text-red-800';
            removeBtn.textContent = 'Remove';
            removeBtn.type = 'button';
            removeBtn.onclick = function() {
                attachmentInput.value = '';
                attachmentPreview.classList.add('hidden');
                previewContainer.innerHTML = '';
                previewFileSize.textContent = '';
            };

            infoDiv.appendChild(nameDiv);
            infoDiv.appendChild(sizeDiv);
            
            previewItem.appendChild(iconDiv);
            previewItem.appendChild(infoDiv);
            previewItem.appendChild(removeBtn);
        }
        
        previewContainer.appendChild(previewItem);
    }

    // Form submission validation
    messageForm.addEventListener('submit', function(e) {
        const message = messageInput.value.trim();
        const hasAttachment = attachmentInput.files && attachmentInput.files.length > 0;

        if (!message && !hasAttachment) {
            e.preventDefault();
            alert('Please enter a message or attach a file');
            return false;
        }
    });

    function sendTyping() {
        fetch('<?= base_url('admin/messages/' . $conversation['id'] . '/typing') ?>', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        }).catch(() => {});
    }

    function pollTyping() {
        fetch('<?= base_url('admin/messages/' . $conversation['id'] . '/typing-status') ?>', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.users.length > 0) {
                typingIndicator.textContent = data.users.join(', ') + ' typing...';
                typingIndicator.classList.remove('hidden');
            } else {
                typingIndicator.classList.add('hidden');
            }
        })
        .catch(() => {});
    }

    if (messageInput) {
        messageInput.addEventListener('input', function() {
            clearTimeout(typingTimeout);
            sendTyping();
            typingTimeout = setTimeout(() => {}, 1000);
        });
    }

    pollTyping();
    setInterval(pollTyping, 3000);
});
</script>
<?= $this->endSection() ?>
