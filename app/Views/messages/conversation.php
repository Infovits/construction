<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
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
                                <div class="mt-2 space-y-1">
                                    <?php foreach ($attachments as $file): ?>
                                        <a href="<?= base_url($file['file_path']) ?>" target="_blank" class="text-xs underline <?= $message['sender_id'] == session('user_id') ? 'text-indigo-100' : 'text-indigo-600' ?>">
                                            <?= esc($file['file_name']) ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <form method="POST" action="<?= base_url('admin/messages/' . $conversation['id'] . '/send') ?>" enctype="multipart/form-data" class="border-t pt-4">
            <?= csrf_field() ?>
            <div class="flex items-center space-x-3">
                <textarea id="messageInput" name="message" rows="2" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" placeholder="Type a message..."></textarea>
                <input type="file" name="attachment" class="hidden" id="attachmentInput">
                <label for="attachmentInput" class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 cursor-pointer">Attach</label>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Send</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const messageInput = document.getElementById('messageInput');
    const typingIndicator = document.getElementById('typingIndicator');
    let typingTimeout;

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
