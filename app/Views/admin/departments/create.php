<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Department Create Form -->
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Add New Department</h1>
            <p class="text-gray-600">Create a new organizational department</p>
        </div>
        <a href="<?= base_url('admin/departments') ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Back to Departments
        </a>
    </div>

    <!-- Form -->
    <form action="<?= base_url('admin/departments/store') ?>" method="post" class="space-y-6">
        <?= csrf_field() ?>
        
        <!-- Basic Information -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Department Code *</label>
                    <input type="text" name="code" value="<?= old('code') ?>" required
                           placeholder="e.g., ENG, HR, FIN"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?= $validation->hasError('code') ? 'border-red-500' : '' ?>">
                    <?php if ($validation->hasError('code')): ?>
                        <p class="text-red-500 text-sm mt-1"><?= $validation->getError('code') ?></p>
                    <?php endif; ?>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Department Name *</label>
                    <input type="text" name="name" value="<?= old('name') ?>" required
                           placeholder="e.g., Engineering Department"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?= $validation->hasError('name') ? 'border-red-500' : '' ?>">
                    <?php if ($validation->hasError('name')): ?>
                        <p class="text-red-500 text-sm mt-1"><?= $validation->getError('name') ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="3" 
                              placeholder="Brief description of the department's role and responsibilities"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"><?= old('description') ?></textarea>
                </div>
            </div>
        </div>

        <!-- Organizational Structure -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Organizational Structure</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Parent Department</label>
                    <select name="parent_department_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Root Department (No Parent)</option>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?= $dept['id'] ?>" <?= old('parent_department_id') == $dept['id'] ? 'selected' : '' ?>>
                                <?= esc($dept['name']) ?> (<?= esc($dept['code']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="text-sm text-gray-500 mt-1">Select a parent department if this is a sub-department</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Department Manager</label>
                    <select name="manager_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">No Manager Assigned</option>
                        <?php foreach ($managers as $manager): ?>
                            <option value="<?= $manager['id'] ?>" <?= old('manager_id') == $manager['id'] ? 'selected' : '' ?>>
                                <?= esc($manager['first_name'] . ' ' . $manager['last_name']) ?> - <?= esc($manager['role_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <!-- Budget Information -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Budget Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Annual Budget</label>
                    <input type="number" name="budget" value="<?= old('budget') ?>" step="0.01" min="0"
                           placeholder="0.00"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <p class="text-sm text-gray-500 mt-1">Optional: Set annual budget allocation for this department</p>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end gap-4">
            <a href="<?= base_url('admin/departments') ?>" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                Create Department
            </button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
// Auto-generate department code from name
document.querySelector('input[name="name"]').addEventListener('input', function() {
    const codeInput = document.querySelector('input[name="code"]');
    if (!codeInput.value) {
        // Generate code from first letters of words
        const words = this.value.split(' ');
        let code = '';
        words.forEach(word => {
            if (word.length > 0) {
                code += word.charAt(0).toUpperCase();
            }
        });
        codeInput.value = code.substring(0, 10); // Limit to 10 characters
    }
});

// Initialize Lucide icons
lucide.createIcons();
</script>
<?= $this->endSection() ?>
