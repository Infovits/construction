<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Job Position Create Form -->
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Add New Job Position</h1>
            <p class="text-gray-600">Create a new job position in the organization</p>
        </div>
        <a href="<?= base_url('admin/positions') ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Back to Positions
        </a>
    </div>

    <!-- Form -->
    <form action="<?= base_url('admin/positions/store') ?>" method="post" class="space-y-6">
        <?= csrf_field() ?>
        
        <!-- Basic Information -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Position Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Position Title *</label>
                    <input type="text" name="title" value="<?= old('title') ?>" required
                           placeholder="e.g., Project Manager, Site Engineer"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?= $validation->hasError('title') ? 'border-red-500' : '' ?>">
                    <?php if ($validation->hasError('title')): ?>
                        <p class="text-red-500 text-sm mt-1"><?= $validation->getError('title') ?></p>
                    <?php endif; ?>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Position Code *</label>
                    <input type="text" name="code" value="<?= old('code') ?>" required
                           placeholder="e.g., PM, SE, QA"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?= $validation->hasError('code') ? 'border-red-500' : '' ?>">
                    <?php if ($validation->hasError('code')): ?>
                        <p class="text-red-500 text-sm mt-1"><?= $validation->getError('code') ?></p>
                    <?php endif; ?>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Department *</label>
                    <select name="department_id" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?= $validation->hasError('department_id') ? 'border-red-500' : '' ?>">
                        <option value="">Select Department</option>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?= $dept['id'] ?>" <?= old('department_id') == $dept['id'] ? 'selected' : '' ?>>
                                <?= esc($dept['name']) ?> (<?= esc($dept['code']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($validation->hasError('department_id')): ?>
                        <p class="text-red-500 text-sm mt-1"><?= $validation->getError('department_id') ?></p>
                    <?php endif; ?>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Employment Type</label>
                    <select name="employment_type" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="full_time" <?= old('employment_type') == 'full_time' ? 'selected' : '' ?>>Full Time</option>
                        <option value="part_time" <?= old('employment_type') == 'part_time' ? 'selected' : '' ?>>Part Time</option>
                        <option value="contract" <?= old('employment_type') == 'contract' ? 'selected' : '' ?>>Contract</option>
                        <option value="temporary" <?= old('employment_type') == 'temporary' ? 'selected' : '' ?>>Temporary</option>
                        <option value="intern" <?= old('employment_type') == 'intern' ? 'selected' : '' ?>>Intern</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Description and Requirements -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Details</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Position Description</label>
                    <textarea name="description" rows="4" 
                              placeholder="Detailed description of roles, responsibilities, and day-to-day activities"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"><?= old('description') ?></textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Position Requirements</label>
                    <textarea name="requirements" rows="4" 
                              placeholder="Required skills, qualifications, experience, and education"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"><?= old('requirements') ?></textarea>
                </div>
            </div>
        </div>

        <!-- Compensation -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Compensation</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Salary</label>
                    <input type="number" name="min_salary" value="<?= old('min_salary') ?>" step="0.01" min="0"
                           placeholder="0.00"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Maximum Salary</label>
                    <input type="number" name="max_salary" value="<?= old('max_salary') ?>" step="0.01" min="0"
                           placeholder="0.00"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
        </div>

        <!-- Status -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex items-center space-x-2">
                <input type="checkbox" id="is_active" name="is_active" value="1" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" <?= old('is_active', '1') == '1' ? 'checked' : '' ?>>
                <label for="is_active" class="text-sm font-medium text-gray-700">Set position as active</label>
            </div>
            <p class="text-sm text-gray-500 mt-1">Active positions can be assigned to employees</p>
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end space-x-3">
            <a href="<?= base_url('admin/positions') ?>" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                Create Position
            </button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
