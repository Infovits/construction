<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Messages</h1>
            <p class="text-gray-600 capitalize"><?= esc($box) ?></p>
        </div>
        <a href="<?= base_url('admin/messages/new') ?>" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
            <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
            New Message
        </a>
    </div>

    <form method="GET" class="flex items-center space-x-3">
        <input type="hidden" name="box" value="<?= esc($box) ?>">
        <input type="text" name="q" value="<?= esc($query ?? '') ?>" placeholder="Search conversations..."
               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
        <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Search</button>
    </form>

    <div class="bg-white rounded-lg shadow-sm border">
        <div class="divide-y">
            <?php if (empty($conversations)): ?>
                <div class="p-6 text-center text-gray-500">No conversations found.</div>
            <?php else: ?>
                <?php foreach ($conversations as $conversation): ?>
                    <a href="<?= base_url('admin/messages/' . $conversation['id']) ?>" class="block p-6 hover:bg-gray-50">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <p class="text-sm text-gray-500">
                                    <?= esc($conversation['participant_names'] ?? 'Conversation') ?>
                                </p>
                                <p class="text-gray-900 font-medium mt-1">
                                    <?= esc($conversation['last_message'] ?? 'No messages yet') ?>
                                </p>
                                <?php if (!empty($conversation['last_message_at'])): ?>
                                    <p class="text-xs text-gray-500 mt-1">Last message: <?= format_datetime($conversation['last_message_at']) ?></p>
                                <?php endif; ?>
                            </div>
                            <?php if ((int)$conversation['unread_count'] > 0): ?>
                                <span class="inline-flex items-center justify-center bg-red-500 text-white text-xs rounded-full w-6 h-6">
                                    <?= (int)$conversation['unread_count'] ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
