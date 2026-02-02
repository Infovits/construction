<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- User Edit Form -->
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit User</h1>
            <p class="text-gray-600">Update user account information</p>
        </div>
        <a href="<?= base_url('admin/users') ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Back to Users
        </a>
    </div>

    <!-- Validation Errors -->
    <?php if ($validation && $validation->getErrors()): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <strong>Validation Errors:</strong>
            <ul class="list-disc ml-6 mt-2">
                <?php foreach ($validation->getErrors() as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Form -->
    <form id="userEditForm" action="<?= base_url('admin/users/update/' . $user['id']) ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
        <?= csrf_field() ?>
        
        <!-- Basic Information -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">First Name *</label>
                    <input type="text" name="first_name" value="<?= old('first_name', $user['first_name'] ?? '') ?>" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?= $validation && $validation->hasError('first_name') ? 'border-red-500' : '' ?>">
                    <?php if ($validation && $validation->hasError('first_name')): ?>
                        <p class="text-red-500 text-sm mt-1"><?= $validation->getError('first_name') ?></p>
                    <?php endif; ?>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Last Name *</label>
                    <input type="text" name="last_name" value="<?= old('last_name', $user['last_name'] ?? '') ?>" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?= $validation && $validation->hasError('last_name') ? 'border-red-500' : '' ?>">
                    <?php if ($validation && $validation->hasError('last_name')): ?>
                        <p class="text-red-500 text-sm mt-1"><?= $validation->getError('last_name') ?></p>
                    <?php endif; ?>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Middle Name</label>
                    <input type="text" name="middle_name" value="<?= old('middle_name', $user['middle_name'] ?? '') ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Username *</label>
                    <input type="text" name="username" value="<?= old('username', $user['username'] ?? '') ?>" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?= $validation && $validation->hasError('username') ? 'border-red-500' : '' ?>">
                    <?php if ($validation && $validation->hasError('username')): ?>
                        <p class="text-red-500 text-sm mt-1"><?= $validation->getError('username') ?></p>
                    <?php endif; ?>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                    <input type="email" name="email" value="<?= old('email', $user['email'] ?? '') ?>" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?= $validation && $validation->hasError('email') ? 'border-red-500' : '' ?>">
                    <?php if ($validation && $validation->hasError('email')): ?>
                        <p class="text-red-500 text-sm mt-1"><?= $validation->getError('email') ?></p>
                    <?php endif; ?>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                    <input type="tel" name="phone" value="<?= old('phone', $user['phone'] ?? '') ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?= $validation && $validation->hasError('phone') ? 'border-red-500' : '' ?>">
                    <?php if ($validation && $validation->hasError('phone')): ?>
                        <p class="text-red-500 text-sm mt-1"><?= $validation->getError('phone') ?></p>
                    <?php endif; ?>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mobile</label>
                    <input type="tel" name="mobile" value="<?= old('mobile', $user['mobile'] ?? '') ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                    <input type="date" name="date_of_birth" value="<?= old('date_of_birth', $user['date_of_birth'] ?? '') ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                    <select name="gender" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Gender</option>
                        <?php foreach ($genderOptions as $value => $label): ?>
                            <option value="<?= $value ?>" <?= old('gender', $user['gender'] ?? '') === $value ? 'selected' : '' ?>>
                                <?= $label ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">National ID</label>
                    <input type="text" name="national_id" value="<?= old('national_id', $user['national_id'] ?? '') ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                    <textarea name="address" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"><?= old('address', $user['address'] ?? '') ?></textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                    <input type="text" name="city" value="<?= old('city', $user['city'] ?? '') ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
        </div>

        <!-- Emergency Contact -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Emergency Contact</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Emergency Contact Name</label>
                    <input type="text" name="emergency_contact_name" value="<?= old('emergency_contact_name', $user['emergency_contact_name'] ?? '') ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Emergency Contact Phone</label>
                    <input type="tel" name="emergency_contact_phone" value="<?= old('emergency_contact_phone', $user['emergency_contact_phone'] ?? '') ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
        </div>

        <!-- Account Information -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Account Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input type="password" name="password"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?= $validation && $validation->hasError('password') ? 'border-red-500' : '' ?>">
                    <p class="text-sm text-gray-500 mt-1">Leave blank to keep current password. Minimum 8 characters if changed</p>
                    <?php if ($validation && $validation->hasError('password')): ?>
                        <p class="text-red-500 text-sm mt-1"><?= $validation->getError('password') ?></p>
                    <?php endif; ?>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                    <input type="password" name="password_confirm"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?= $validation && $validation->hasError('password_confirm') ? 'border-red-500' : '' ?>">
                    <?php if ($validation && $validation->hasError('password_confirm')): ?>
                        <p class="text-red-500 text-sm mt-1"><?= $validation->getError('password_confirm') ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Role *</label>
                    <select name="role_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?= $validation && $validation->hasError('role_id') ? 'border-red-500' : '' ?>">
                        <option value="">Select Role</option>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?= $role['id'] ?>" <?= old('role_id', $user['role_id'] ?? '') == $role['id'] ? 'selected' : '' ?>>
                                <?= esc($role['name']) ?>
                                <?php if ($role['description']): ?>
                                    - <?= esc($role['description']) ?>
                                <?php endif; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($validation && $validation->hasError('role_id')): ?>
                        <p class="text-red-500 text-sm mt-1"><?= $validation->getError('role_id') ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Employment Information -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Employment Information <span class="text-sm font-normal text-gray-500">(Optional)</span></h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                    <select id="department_id" name="department_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Department</option>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?= $dept['id'] ?>" <?= old('department_id', $user['department_id'] ?? '') == $dept['id'] ? 'selected' : '' ?>>
                                <?= esc($dept['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Position</label>
                    <select id="position_id" name="position_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Position</option>
                        <?php foreach ($positions as $position): ?>
                            <option value="<?= $position['id'] ?>" data-department="<?= $position['department_id'] ?>" <?= old('position_id', $user['position_id'] ?? '') == $position['id'] ? 'selected' : '' ?>>
                                <?= esc($position['title']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Employment Type</label>
                    <select name="employment_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Employment Type</option>
                        <?php foreach ($employmentTypes as $value => $label): ?>
                            <option value="<?= $value ?>" <?= old('employment_type', $user['employment_type'] ?? '') === $value ? 'selected' : '' ?>>
                                <?= $label ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Hire Date</label>
                    <input type="date" name="hire_date" value="<?= old('hire_date', $user['hire_date'] ?? '') ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Basic Salary</label>
                    <input type="number" name="basic_salary" value="<?= old('basic_salary', $user['basic_salary'] ?? '') ?>" step="0.01" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Supervisor</label>
                    <select name="supervisor_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Supervisor</option>
                        <!-- Will be populated via JavaScript based on department -->
                    </select>
                </div>
            </div>
        </div>

        <!-- Banking Information -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Banking Information <span class="text-sm font-normal text-gray-500">(Optional)</span></h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bank Name</label>
                    <input type="text" name="bank_name" value="<?= old('bank_name', $user['bank_name'] ?? '') ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Account Number</label>
                    <input type="text" name="bank_account_number" value="<?= old('bank_account_number', $user['bank_account_number'] ?? '') ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bank Branch</label>
                    <input type="text" name="bank_branch" value="<?= old('bank_branch', $user['bank_branch'] ?? '') ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tax Number</label>
                    <input type="text" name="tax_number" value="<?= old('tax_number', $user['tax_number'] ?? '') ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end gap-4">
            <a href="<?= base_url('admin/users') ?>" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                <span class="submit-text">Update User</span>
                <span class="loading-text hidden">Updating...</span>
            </button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('userEditForm');
    const submitButton = form.querySelector('button[type="submit"]');
    const submitText = submitButton.querySelector('.submit-text');
    const loadingText = submitButton.querySelector('.loading-text');
    
    // Prevent double submission
    let isSubmitting = false;
    
    form.addEventListener('submit', function(e) {
        if (isSubmitting) {
            e.preventDefault();
            return false;
        }
        
        // Check password confirmation if password is entered
        const password = form.querySelector('input[name="password"]').value;
        const passwordConfirm = form.querySelector('input[name="password_confirm"]').value;
        
        if (password && password !== passwordConfirm) {
            form.querySelector('input[name="password_confirm"]').classList.add('border-red-500');
            alert('Passwords do not match.');
            e.preventDefault();
            return false;
        }
        
        // Show loading state
        isSubmitting = true;
        submitButton.disabled = true;
        submitText.classList.add('hidden');
        loadingText.classList.remove('hidden');
    });
    
    // Reset button state if there's an error and page reloads
    window.addEventListener('pageshow', function() {
        isSubmitting = false;
        submitButton.disabled = false;
        submitText.classList.remove('hidden');
        loadingText.classList.add('hidden');
    });

    // Department-Position relationship
    const departmentDropdown = document.getElementById('department_id');
    const positionDropdown = document.getElementById('position_id');
    
    departmentDropdown.addEventListener('change', function() {
        const departmentId = this.value;
        
        // Clear position dropdown
        positionDropdown.innerHTML = '<option value="">Select Position</option>';
        
        if (!departmentId) return;
        
        // Filter positions based on department
        <?php if (!empty($positions)): ?>
        const positions = <?= json_encode($positions) ?>;
        positions.forEach(position => {
            if (position.department_id == departmentId) {
                const option = document.createElement('option');
                option.value = position.id;
                option.textContent = position.title;
                positionDropdown.appendChild(option);
            }
        });
        <?php endif; ?>
    });
    
    // Initialize positions if department is pre-selected
    if (departmentDropdown.value) {
        departmentDropdown.dispatchEvent(new Event('change'));
    }
});
</script>
<?= $this->endSection() ?>
