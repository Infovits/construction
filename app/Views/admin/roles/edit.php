<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Role</h1>
            <p class="text-gray-600">Update role: <?= esc($role['name']) ?></p>
        </div>
        <a href="<?= base_url('admin/roles') ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Back to Roles
        </a>
    </div>

    <form action="<?= base_url('admin/roles/update/' . $role['id']) ?>" method="post" class="space-y-6">
        <?= csrf_field() ?>

        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Role Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Role Name *</label>
                    <input type="text" name="name" value="<?= old('name', $role['name']) ?>" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?= $validation->hasError('name') ? 'border-red-500' : '' ?>">
                    <?php if ($validation->hasError('name')): ?>
                        <p class="text-red-500 text-sm mt-1"><?= $validation->getError('name') ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Slug *</label>
                    <input type="text" name="slug" value="<?= old('slug', $role['slug']) ?>" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?= $validation->hasError('slug') ? 'border-red-500' : '' ?>">
                    <?php if ($validation->hasError('slug')): ?>
                        <p class="text-red-500 text-sm mt-1"><?= $validation->getError('slug') ?></p>
                    <?php endif; ?>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"><?= old('description', $role['description']) ?></textarea>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Permissions</h3>
            <?php $selected = old('permissions') ?? $selectedPermissions; if (!is_array($selected)) { $selected = []; } ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php foreach ($permissions as $group => $groupPermissions): ?>
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h4 class="text-sm font-semibold text-gray-800 mb-3"><?= esc($group) ?></h4>
                        <div class="space-y-2">
                            <?php foreach ($groupPermissions as $key => $label): ?>
                                <label class="flex items-center space-x-2 text-sm text-gray-700">
                                    <input type="checkbox" name="permissions[]" value="<?= esc($key) ?>"
                                           class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                           <?= in_array($key, $selected, true) ? 'checked' : '' ?>>
                                    <span><?= esc($label) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="flex justify-end gap-4">
            <a href="<?= base_url('admin/roles') ?>" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                Update Role
            </button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
