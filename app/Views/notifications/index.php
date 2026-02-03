<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Notifications</h1>
        <p class="text-gray-600">All notifications</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm border">
        <div class="divide-y">
            <?php if (empty($notifications)): ?>
                <div class="p-6 text-center text-gray-500">No notifications found.</div>
            <?php else: ?>
                <?php foreach ($notifications as $notification): ?>
                    <a href="<?= $notification['link'] ? esc($notification['link']) : '#' ?>" class="block p-4 hover:bg-gray-50">
                        <div class="flex items-start justify-between">
                            <div>
                                <h4 class="font-medium text-gray-900 text-sm"><?= esc($notification['title']) ?></h4>
                                <p class="text-gray-600 text-sm mt-1"><?= esc($notification['message'] ?? '') ?></p>
                                <p class="text-xs text-gray-500 mt-1"><?= format_datetime($notification['created_at']) ?></p>
                            </div>
                            <?php if ((int)$notification['is_read'] === 0): ?>
                                <span class="w-2 h-2 bg-indigo-500 rounded-full mt-2"></span>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
