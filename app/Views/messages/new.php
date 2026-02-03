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
            <label class="block text-sm font-medium text-gray-700 mb-2">Recipient</label>
            <select name="recipient_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" required>
                <option value="">Select User</option>
                <?php foreach ($users as $user): ?>
                    <option value="<?= $user['id'] ?>">
                        <?= esc($user['first_name'] . ' ' . $user['last_name']) ?> (<?= esc($user['email']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Message</label>
            <textarea name="message" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"></textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Attachment (optional)</label>
            <input type="file" name="attachment" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            <p class="text-xs text-gray-500 mt-1">Allowed: images, PDF, Word, Excel. Max 2MB.</p>
        </div>
        <div class="flex justify-end">
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Send</button>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
