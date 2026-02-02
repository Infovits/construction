<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Department</h1>
            <p class="text-gray-600">Update department: <?= esc($department['name']) ?></p>
        </div>
        <a href="<?= base_url('admin/departments') ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Back to Departments
        </a>
    </div>

    <form action="<?= base_url('admin/departments/update/' . $department['id']) ?>" method="post" class="space-y-6">
        <?= csrf_field() ?>

        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Department Code *</label>
                    <input type="text" name="code" value="<?= old('code', $department['code']) ?>" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?= $validation->hasError('code') ? 'border-red-500' : '' ?>">
                    <?php if ($validation->hasError('code')): ?>
                        <p class="text-red-500 text-sm mt-1"><?= $validation->getError('code') ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Department Name *</label>
                    <input type="text" name="name" value="<?= old('name', $department['name']) ?>" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?= $validation->hasError('name') ? 'border-red-500' : '' ?>">
                    <?php if ($validation->hasError('name')): ?>
                        <p class="text-red-500 text-sm mt-1"><?= $validation->getError('name') ?></p>
                    <?php endif; ?>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"><?= old('description', $department['description']) ?></textarea>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Organizational Structure</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Parent Department</label>
                    <select name="parent_department_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Root Department (No Parent)</option>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?= $dept['id'] ?>" <?= old('parent_department_id', $department['parent_department_id']) == $dept['id'] ? 'selected' : '' ?>
                                <?= $dept['id'] == $department['id'] ? 'disabled' : '' ?>>
                                <?= esc($dept['name']) ?> (<?= esc($dept['code']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Department Manager</label>
                    <select name="manager_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">No Manager Assigned</option>
                        <?php foreach ($managers as $manager): ?>
                            <option value="<?= $manager['id'] ?>" <?= old('manager_id', $department['manager_id']) == $manager['id'] ? 'selected' : '' ?>>
                                <?= esc($manager['first_name'] . ' ' . $manager['last_name']) ?> - <?= esc($manager['role_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Budget & Status</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Annual Budget</label>
                    <input type="number" name="budget" value="<?= old('budget', $department['budget']) ?>" step="0.01" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="active" <?= old('status', $department['status']) === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= old('status', $department['status']) === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-4">
            <a href="<?= base_url('admin/departments') ?>" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                Update Department
            </button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
