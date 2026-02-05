<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h1>
                <p class="text-gray-600 mt-1">Manage your profile and account settings</p>
            </div>
            <a href="<?= base_url('admin/users') ?>" class="flex items-center space-x-2 text-gray-600 hover:text-gray-900 transition">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
                <span>Back to Users</span>
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    <?php if (session()->getFlashdata('success')): ?>
    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg flex items-start space-x-3">
        <i data-lucide="check-circle" class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5"></i>
        <div>
            <h3 class="font-semibold text-green-900">Success</h3>
            <p class="text-green-800 text-sm"><?= htmlspecialchars(session()->getFlashdata('success')) ?></p>
        </div>
    </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg flex items-start space-x-3">
        <i data-lucide="alert-circle" class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5"></i>
        <div>
            <h3 class="font-semibold text-red-900">Error</h3>
            <p class="text-red-800 text-sm"><?= htmlspecialchars(session()->getFlashdata('error')) ?></p>
        </div>
    </div>
    <?php endif; ?>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Profile Summary Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-6">
                <!-- Profile Photo -->
                <div class="mb-6">
                    <div class="w-24 h-24 mx-auto mb-4 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white text-3xl font-bold">
                        <?= strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)) ?>
                    </div>
                </div>

                <!-- User Info -->
                <div class="space-y-4">
                    <div class="text-center">
                        <p class="text-gray-600 text-sm font-medium">Username</p>
                        <p class="text-gray-900 font-semibold"><?= htmlspecialchars($user['username']) ?></p>
                    </div>
                    <div class="border-t pt-4">
                        <p class="text-gray-600 text-sm font-medium">Email</p>
                        <p class="text-gray-900 truncate"><?= htmlspecialchars($user['email']) ?></p>
                    </div>
                    <div class="border-t pt-4">
                        <p class="text-gray-600 text-sm font-medium">Status</p>
                        <div class="mt-2">
                            <?php if ($user['status'] === 'active'): ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <span class="w-2 h-2 bg-green-600 rounded-full mr-2"></span>
                                Active
                            </span>
                            <?php else: ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                <span class="w-2 h-2 bg-red-600 rounded-full mr-2"></span>
                                Inactive
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if (!empty($user['role_name'])): ?>
                    <div class="border-t pt-4">
                        <p class="text-gray-600 text-sm font-medium">Role</p>
                        <p class="text-gray-900 font-semibold capitalize"><?= htmlspecialchars($user['role_name']) ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="lg:col-span-2">
            <!-- Tabs -->
            <div class="bg-white rounded-lg shadow-md">
                <div class="border-b">
                    <div class="flex">
                        <button onclick="switchTab('personal')" class="tab-btn flex-1 px-6 py-4 text-center font-medium text-indigo-600 border-b-2 border-indigo-600 transition" id="tab-personal">
                            Personal Information
                        </button>
                        <button onclick="switchTab('security')" class="tab-btn flex-1 px-6 py-4 text-center font-medium text-gray-600 border-b-2 border-transparent hover:border-gray-300 transition" id="tab-security">
                            Security
                        </button>
                    </div>
                </div>

                <div class="p-6">
                    <!-- Personal Information Tab -->
                    <div id="personal-tab" class="space-y-6">
                        <form action="<?= base_url('admin/users/profile/update') ?>" method="POST" id="profileForm">
                            <?= csrf_field() ?>

                            <!-- Display Validation Errors -->
                            <?php if (session()->has('validation')): ?>
                                <?php $errors = session('validation')->getErrors(); ?>
                                <?php if (!empty($errors)): ?>
                                <div class="p-4 bg-red-50 border border-red-200 rounded-lg mb-6">
                                    <h4 class="font-semibold text-red-900 mb-2">Please fix the following errors:</h4>
                                    <ul class="space-y-1">
                                        <?php foreach ($errors as $error): ?>
                                        <li class="text-red-800 text-sm">• <?= htmlspecialchars($error) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <?php endif; ?>
                            <?php endif; ?>

                            <!-- First Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">First Name <span class="text-red-600">*</span></label>
                                <input 
                                    type="text" 
                                    name="first_name" 
                                    value="<?= htmlspecialchars(old('first_name') ?? $user['first_name']) ?>" 
                                    required 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                    placeholder="John"
                                />
                                <?php if (session()->has('validation') && session('validation')->hasError('first_name')): ?>
                                <p class="mt-1 text-sm text-red-600"><?= session('validation')->getError('first_name') ?></p>
                                <?php endif; ?>
                            </div>

                            <!-- Middle Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Middle Name</label>
                                <input 
                                    type="text" 
                                    name="middle_name" 
                                    value="<?= htmlspecialchars(old('middle_name') ?? ($user['middle_name'] ?? '')) ?>" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                    placeholder="Michael"
                                />
                            </div>

                            <!-- Last Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Last Name <span class="text-red-600">*</span></label>
                                <input 
                                    type="text" 
                                    name="last_name" 
                                    value="<?= htmlspecialchars(old('last_name') ?? $user['last_name']) ?>" 
                                    required 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                    placeholder="Doe"
                                />
                                <?php if (session()->has('validation') && session('validation')->hasError('last_name')): ?>
                                <p class="mt-1 text-sm text-red-600"><?= session('validation')->getError('last_name') ?></p>
                                <?php endif; ?>
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email Address <span class="text-red-600">*</span></label>
                                <input 
                                    type="email" 
                                    name="email" 
                                    value="<?= htmlspecialchars(old('email') ?? $user['email']) ?>" 
                                    required 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                    placeholder="john@example.com"
                                />
                                <?php if (session()->has('validation') && session('validation')->hasError('email')): ?>
                                <p class="mt-1 text-sm text-red-600"><?= session('validation')->getError('email') ?></p>
                                <?php endif; ?>
                            </div>

                            <!-- Phone -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                                    <input 
                                        type="tel" 
                                        name="phone" 
                                        value="<?= htmlspecialchars(old('phone') ?? ($user['phone'] ?? '')) ?>" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                        placeholder="+265 1 234 567"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Mobile</label>
                                    <input 
                                        type="tel" 
                                        name="mobile" 
                                        value="<?= htmlspecialchars(old('mobile') ?? ($user['mobile'] ?? '')) ?>" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                        placeholder="+265 99 123 456"
                                    />
                                </div>
                            </div>

                            <!-- Date of Birth & Gender -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                                    <input 
                                        type="date" 
                                        name="date_of_birth" 
                                        value="<?= htmlspecialchars(old('date_of_birth') ?? ($user['date_of_birth'] ?? '')) ?>" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                                    <select name="gender" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                                        <option value="">Select Gender</option>
                                        <?php foreach ($genderOptions as $key => $label): ?>
                                        <option value="<?= htmlspecialchars($key) ?>" <?= (old('gender') ?? $user['gender']) === $key ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($label) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- National ID -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">National ID / Passport</label>
                                <input 
                                    type="text" 
                                    name="national_id" 
                                    value="<?= htmlspecialchars(old('national_id') ?? ($user['national_id'] ?? '')) ?>" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                    placeholder="ID or Passport Number"
                                />
                            </div>

                            <!-- Address -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                                <input 
                                    type="text" 
                                    name="address" 
                                    value="<?= htmlspecialchars(old('address') ?? ($user['address'] ?? '')) ?>" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                    placeholder="Street Address"
                                />
                            </div>

                            <!-- City -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                                <input 
                                    type="text" 
                                    name="city" 
                                    value="<?= htmlspecialchars(old('city') ?? ($user['city'] ?? '')) ?>" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                    placeholder="City"
                                />
                            </div>

                            <!-- Emergency Contact -->
                            <div class="border-t pt-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Emergency Contact</h3>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Contact Name</label>
                                        <input 
                                            type="text" 
                                            name="emergency_contact_name" 
                                            value="<?= htmlspecialchars(old('emergency_contact_name') ?? ($user['emergency_contact_name'] ?? '')) ?>" 
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                            placeholder="Contact Name"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Contact Phone</label>
                                        <input 
                                            type="tel" 
                                            name="emergency_contact_phone" 
                                            value="<?= htmlspecialchars(old('emergency_contact_phone') ?? ($user['emergency_contact_phone'] ?? '')) ?>" 
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                            placeholder="Phone Number"
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="border-t pt-6 flex justify-end space-x-3">
                                <button 
                                    type="button" 
                                    onclick="window.history.back()" 
                                    class="px-6 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 hover:bg-gray-50 transition"
                                >
                                    Cancel
                                </button>
                                <button 
                                    type="submit" 
                                    class="px-6 py-2 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition flex items-center space-x-2"
                                >
                                    <i data-lucide="save" class="w-5 h-5"></i>
                                    <span>Save Changes</span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Security Tab -->
                    <div id="security-tab" class="space-y-6 hidden">
                        <form action="<?= base_url('admin/users/profile/update') ?>" method="POST" id="securityForm">
                            <?= csrf_field() ?>

                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                                <p class="text-blue-900 text-sm">
                                    <strong>Note:</strong> To change your password, you must enter your current password for verification.
                                </p>
                            </div>

                            <!-- Current Password -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                                <input 
                                    type="password" 
                                    name="current_password" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                    placeholder="Enter your current password"
                                />
                                <?php if (session()->has('validation') && session('validation')->hasError('current_password')): ?>
                                <p class="mt-1 text-sm text-red-600"><?= session('validation')->getError('current_password') ?></p>
                                <?php endif; ?>
                            </div>

                            <!-- New Password -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                                <input 
                                    type="password" 
                                    name="new_password" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                    placeholder="Enter new password (min 8 characters)"
                                />
                                <?php if (session()->has('validation') && session('validation')->hasError('new_password')): ?>
                                <p class="mt-1 text-sm text-red-600"><?= session('validation')->getError('new_password') ?></p>
                                <?php endif; ?>
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                                <input 
                                    type="password" 
                                    name="new_password_confirm" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                    placeholder="Confirm new password"
                                />
                                <?php if (session()->has('validation') && session('validation')->hasError('new_password_confirm')): ?>
                                <p class="mt-1 text-sm text-red-600"><?= session('validation')->getError('new_password_confirm') ?></p>
                                <?php endif; ?>
                            </div>

                            <!-- Password Requirements -->
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <h4 class="font-semibold text-gray-900 mb-2">Password Requirements:</h4>
                                <ul class="space-y-1 text-sm text-gray-700">
                                    <li>✓ Minimum 8 characters long</li>
                                    <li>✓ Passwords must match</li>
                                </ul>
                            </div>

                            <!-- Submit Button -->
                            <div class="border-t pt-6 flex justify-end space-x-3">
                                <button 
                                    type="button" 
                                    onclick="switchTab('personal')" 
                                    class="px-6 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 hover:bg-gray-50 transition"
                                >
                                    Cancel
                                </button>
                                <button 
                                    type="submit" 
                                    class="px-6 py-2 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition flex items-center space-x-2"
                                >
                                    <i data-lucide="save" class="w-5 h-5"></i>
                                    <span>Update Password</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function switchTab(tabName) {
    // Hide all tabs
    document.getElementById('personal-tab').classList.add('hidden');
    document.getElementById('security-tab').classList.add('hidden');

    // Remove active state from all buttons
    document.getElementById('tab-personal').classList.remove('text-indigo-600', 'border-indigo-600');
    document.getElementById('tab-security').classList.remove('text-indigo-600', 'border-indigo-600');

    // Show selected tab
    document.getElementById(tabName + '-tab').classList.remove('hidden');

    // Add active state to selected button
    document.getElementById('tab-' + tabName).classList.add('text-indigo-600', 'border-indigo-600');
    document.getElementById('tab-' + tabName).classList.remove('text-gray-600', 'border-transparent');
}
</script>

<?= $this->endSection() ?>
