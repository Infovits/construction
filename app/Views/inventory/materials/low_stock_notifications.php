<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Low Stock Notifications<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Low Stock Notifications</h1>
                <p class="text-gray-600">Materials that need attention and reordering</p>
            </div>
            <div class="flex space-x-2">
                <a href="<?= base_url('admin/materials') ?>" class="inline-flex items-center px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    <i data-lucide="package" class="w-4 h-4 mr-2"></i> Materials
                </a>
                <a href="<?= base_url('admin/materials/report') ?>" class="inline-flex items-center px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Reports
                </a>
            </div>
        </div>
    </div>

    <?php if (empty($lowStockItems)): ?>
    <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <i data-lucide="check-circle" class="h-5 w-5 text-green-500"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-green-700">
                    All materials are at healthy stock levels. No reordering needed at this time.
                </p>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Low Stock Items -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content: List of Low Stock Materials -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Critical Stock Items -->
            <?php 
            $criticalItems = array_filter($lowStockItems, function($item) {
                return $item['stock_status'] === 'critical';
            });
            
            if (!empty($criticalItems)):
            ?>
            <div class="bg-white rounded-lg shadow-sm border border-red-300">
                <div class="px-6 py-4 border-b border-red-200 bg-red-50 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-red-800">Critical Stock <span class="ml-2 px-2 py-0.5 bg-red-200 text-red-800 text-xs rounded-full"><?= count($criticalItems) ?></span></h3>
                    <span class="text-sm text-red-700">Below minimum stock level</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Material</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Stock</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Min Stock</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($criticalItems as $item): ?>
                            <tr class="hover:bg-red-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="ml-2">
                                            <div class="text-sm font-medium text-gray-900"><?= esc($item['name']) ?></div>
                                            <div class="text-xs text-gray-500"><?= esc($item['item_code']) ?></div>
                                            <div class="text-xs text-gray-500"><?= esc($item['category_name']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <?= number_format($item['current_stock']) ?> <?= esc($item['unit']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= number_format($item['minimum_stock']) ?> <?= esc($item['unit']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if (!empty($item['preferred_supplier'])): ?>
                                    <div class="text-sm text-gray-900"><?= esc($item['preferred_supplier']) ?></div>
                                    <div class="text-xs text-gray-500">Lead time: <?= number_format($item['avg_lead_time'] ?? 0) ?> days</div>
                                    <?php else: ?>
                                    <span class="text-xs text-gray-500">No supplier info</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="<?= base_url('admin/materials/view/' . $item['id']) ?>" class="text-blue-600 hover:text-blue-900 mr-3">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                    <a href="<?= base_url('admin/materials/stock-update/' . $item['id']) ?>" class="text-green-600 hover:text-green-900">
                                        <i data-lucide="arrow-up-circle" class="w-4 h-4"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>

            <!-- Low Stock Items -->
            <?php 
            $lowItems = array_filter($lowStockItems, function($item) {
                return $item['stock_status'] === 'low';
            });
            
            if (!empty($lowItems)):
            ?>
            <div class="bg-white rounded-lg shadow-sm border border-yellow-300">
                <div class="px-6 py-4 border-b border-yellow-200 bg-yellow-50 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-yellow-800">Low Stock <span class="ml-2 px-2 py-0.5 bg-yellow-200 text-yellow-800 text-xs rounded-full"><?= count($lowItems) ?></span></h3>
                    <span class="text-sm text-yellow-700">Below reorder level</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Material</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Level</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reorder Level</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($lowItems as $item): ?>
                            <tr class="hover:bg-yellow-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="ml-2">
                                            <div class="text-sm font-medium text-gray-900"><?= esc($item['name']) ?></div>
                                            <div class="text-xs text-gray-500"><?= esc($item['item_code']) ?></div>
                                            <div class="text-xs text-gray-500"><?= esc($item['category_name']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <?= number_format($item['current_stock']) ?> <?= esc($item['unit']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= number_format($item['reorder_level']) ?> <?= esc($item['unit']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if (!empty($item['preferred_supplier'])): ?>
                                    <div class="text-sm text-gray-900"><?= esc($item['preferred_supplier']) ?></div>
                                    <div class="text-xs text-gray-500">Lead time: <?= number_format($item['avg_lead_time'] ?? 0) ?> days</div>
                                    <?php else: ?>
                                    <span class="text-xs text-gray-500">No supplier info</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="<?= base_url('admin/materials/view/' . $item['id']) ?>" class="text-blue-600 hover:text-blue-900 mr-3">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                    <a href="<?= base_url('admin/materials/stock-update/' . $item['id']) ?>" class="text-green-600 hover:text-green-900">
                                        <i data-lucide="arrow-up-circle" class="w-4 h-4"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Side Panel: Summary and Actions -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Stock Summary Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Stock Summary</h3>
                </div>
                <div class="p-6 space-y-4">
                    <!-- Critical Stock Summary -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="h-4 w-4 rounded-full bg-red-500 mr-2"></div>
                            <span class="text-sm font-medium text-gray-700">Critical Stock</span>
                        </div>
                        <span class="text-sm text-red-600 font-semibold"><?= count($criticalItems) ?> items</span>
                    </div>
                    
                    <!-- Low Stock Summary -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="h-4 w-4 rounded-full bg-yellow-500 mr-2"></div>
                            <span class="text-sm font-medium text-gray-700">Low Stock</span>
                        </div>
                        <span class="text-sm text-yellow-600 font-semibold"><?= count($lowItems) ?> items</span>
                    </div>
                    
                    <!-- Total Items Need Attention -->
                    <div class="pt-4 border-t border-gray-100">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Total Attention Needed</span>
                            <span class="text-sm text-gray-900 font-semibold"><?= count($lowStockItems) ?> items</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
                </div>
                <div class="p-6 space-y-4">
                    <a href="<?= base_url('admin/materials/export-low-stock') ?>" class="w-full inline-flex justify-center items-center px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        <i data-lucide="file-down" class="w-4 h-4 mr-2"></i> Export Low Stock Report
                    </a>
                    
                    <a href="#" onclick="createPurchaseOrdersModal()" class="w-full inline-flex justify-center items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                        <i data-lucide="shopping-bag" class="w-4 h-4 mr-2"></i> Create Purchase Orders
                    </a>
                    
                    <a href="<?= base_url('admin/suppliers') ?>" class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i data-lucide="truck" class="w-4 h-4 mr-2"></i> View Suppliers
                    </a>
                    
                    <a href="#" onclick="updateMinimumLevelsModal()" class="w-full inline-flex justify-center items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <i data-lucide="settings" class="w-4 h-4 mr-2"></i> Optimize Stock Levels
                    </a>
                </div>
            </div>

            <!-- Notifications Settings -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Notification Settings</h3>
                </div>
                <div class="p-6">
                    <form id="notificationSettingsForm" class="space-y-4">
                        <?= csrf_field() ?>
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" id="emailNotifications" name="email_notifications" class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50" <?= isset($settings['email_enabled']) && $settings['email_enabled'] ? 'checked' : '' ?>>
                                <span class="ml-2 text-sm text-gray-700">Daily email notifications</span>
                            </label>
                        </div>
                        
                        <div id="emailRecipientsContainer" class="<?= isset($settings['email_enabled']) && $settings['email_enabled'] ? '' : 'hidden' ?>">
                            <label for="emailRecipients" class="block text-sm font-medium text-gray-700 mb-1">Email recipients</label>
                            <textarea id="emailRecipients" name="email_recipients" rows="2" placeholder="Enter email addresses separated by commas" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50 text-sm"><?= isset($settings['email_recipients']) ? esc($settings['email_recipients']) : '' ?></textarea>
                            <p class="text-xs text-gray-500 mt-1">Leave empty to use department manager emails</p>
                        </div>
                        
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" id="pushNotifications" name="push_notifications" class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50" <?= isset($settings['push_enabled']) && $settings['push_enabled'] ? 'checked' : '' ?>>
                                <span class="ml-2 text-sm text-gray-700">Push notifications</span>
                            </label>
                        </div>
                        
                        <div>
                            <label for="notificationFrequency" class="block text-sm font-medium text-gray-700 mb-1">Notification frequency</label>
                            <select id="notificationFrequency" name="notification_frequency" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                                <option value="daily" <?= isset($settings['frequency']) && $settings['frequency'] == 'daily' ? 'selected' : '' ?>>Daily</option>
                                <option value="weekly" <?= isset($settings['frequency']) && $settings['frequency'] == 'weekly' ? 'selected' : '' ?>>Weekly</option>
                                <option value="immediate" <?= isset($settings['frequency']) && $settings['frequency'] == 'immediate' ? 'selected' : '' ?>>Immediate</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="notificationThreshold" class="block text-sm font-medium text-gray-700 mb-1">Notification threshold</label>
                            <select id="notificationThreshold" name="notification_threshold" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                                <option value="reorder" <?= isset($settings['threshold']) && $settings['threshold'] == 'reorder' ? 'selected' : '' ?>>At reorder level</option>
                                <option value="minimum" <?= isset($settings['threshold']) && $settings['threshold'] == 'minimum' ? 'selected' : '' ?>>At minimum level only</option>
                                <option value="custom" <?= isset($settings['threshold']) && $settings['threshold'] == 'custom' ? 'selected' : '' ?>>Custom percentage</option>
                            </select>
                        </div>
                        
                        <div id="customThresholdContainer" class="<?= isset($settings['threshold']) && $settings['threshold'] == 'custom' ? '' : 'hidden' ?>">
                            <label for="customThresholdValue" class="block text-sm font-medium text-gray-700 mb-1">Custom threshold percentage</label>
                            <div class="flex items-center">
                                <input type="number" id="customThresholdValue" name="custom_threshold_value" min="1" max="100" value="<?= isset($settings['custom_threshold_value']) ? $settings['custom_threshold_value'] : 20 ?>" class="w-20 rounded-md border-gray-300 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">% above minimum level</span>
                            </div>
                        </div>
                        
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                            <i data-lucide="save" class="w-4 h-4 mr-2"></i> Save Settings
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Send Test Notification -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Test Notifications</h3>
                </div>
                <div class="p-6">
                    <button id="sendTestNotification" class="w-full inline-flex justify-center items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        <i data-lucide="bell-ring" class="w-4 h-4 mr-2"></i> Send Test Notification
                    </button>
                    <p class="text-xs text-gray-500 mt-2 text-center">Send a test notification to verify your settings</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide icons
    lucide.createIcons();
    
    // Email notifications toggle
    const emailNotificationsCheckbox = document.getElementById('emailNotifications');
    const emailRecipientsContainer = document.getElementById('emailRecipientsContainer');
    
    emailNotificationsCheckbox.addEventListener('change', function() {
        if (this.checked) {
            emailRecipientsContainer.classList.remove('hidden');
        } else {
            emailRecipientsContainer.classList.add('hidden');
        }
    });
    
    // Custom threshold toggle
    const notificationThresholdSelect = document.getElementById('notificationThreshold');
    const customThresholdContainer = document.getElementById('customThresholdContainer');
    
    notificationThresholdSelect.addEventListener('change', function() {
        if (this.value === 'custom') {
            customThresholdContainer.classList.remove('hidden');
        } else {
            customThresholdContainer.classList.add('hidden');
        }
    });
    
    // Notification settings form
    document.getElementById('notificationSettingsForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get form data
        const formData = new FormData(this);
        
        // Show loading state
        const submitButton = this.querySelector('button[type="submit"]');
        const originalButtonText = submitButton.innerHTML;
        submitButton.disabled = true;
        submitButton.innerHTML = `
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Saving...`;
        
        // Send AJAX request to save settings
        fetch('<?= base_url('admin/materials/save-notification-settings') ?>', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            // Reset button state
            submitButton.disabled = false;
            submitButton.innerHTML = originalButtonText;
            
            if (data.success) {
                // Show success message
                const successAlert = document.createElement('div');
                successAlert.className = 'mt-4 bg-green-50 border-l-4 border-green-400 p-4';
                successAlert.innerHTML = `
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i data-lucide="check-circle" class="h-5 w-5 text-green-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">${data.message}</p>
                        </div>
                    </div>
                `;
                
                this.appendChild(successAlert);
                lucide.createIcons();
                
                // Remove the alert after 3 seconds
                setTimeout(() => {
                    successAlert.remove();
                }, 3000);
            } else {
                // Show error message
                const errorAlert = document.createElement('div');
                errorAlert.className = 'mt-4 bg-red-50 border-l-4 border-red-400 p-4';
                errorAlert.innerHTML = `
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i data-lucide="alert-circle" class="h-5 w-5 text-red-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">${data.message || 'An error occurred while saving settings.'}</p>
                        </div>
                    </div>
                `;
                
                this.appendChild(errorAlert);
                lucide.createIcons();
                
                // Remove the alert after 5 seconds
                setTimeout(() => {
                    errorAlert.remove();
                }, 5000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            submitButton.disabled = false;
            submitButton.innerHTML = originalButtonText;
            
            // Show error message
            alert('An error occurred while saving settings. Please try again.');
        });
    });
    
    // Send Test Notification
    document.getElementById('sendTestNotification').addEventListener('click', function() {
        const button = this;
        const originalText = button.innerHTML;
        
        // Show loading state
        button.disabled = true;
        button.innerHTML = `
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Sending...`;
        
        // Send AJAX request
        fetch('<?= base_url('admin/materials/send-test-notification') ?>', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            // Reset button state
            button.disabled = false;
            button.innerHTML = originalText;
            
            // Show result
            if (data.success) {
                alert('Test notification sent successfully!');
            } else {
                alert('Error: ' + (data.message || 'Failed to send test notification.'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            button.disabled = false;
            button.innerHTML = originalText;
            alert('An error occurred while sending the test notification. Please try again.');
        });
    });
});

function createPurchaseOrdersModal() {
    // Show modal to create purchase orders for low stock items
    // This would normally create a modal with supplier selection, quantities, etc.
    const result = confirm('Would you like to create purchase orders for all low stock items?');
    
    if (result) {
        window.location.href = '<?= base_url('admin/suppliers/create-purchase-orders') ?>';
    }
}

function updateMinimumLevelsModal() {
    // Show confirmation
    const result = confirm('This will optimize minimum stock levels based on historical usage patterns. Continue?');
    
    if (result) {
        // Show loading overlay
        const loadingOverlay = document.createElement('div');
        loadingOverlay.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
        loadingOverlay.innerHTML = `
            <div class="bg-white p-6 rounded-lg shadow-xl">
                <svg class="animate-spin h-10 w-10 mb-4 mx-auto text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-lg font-semibold text-gray-900 mb-2">Optimizing Stock Levels</p>
                <p class="text-gray-600">This may take a few moments...</p>
            </div>
        `;
        document.body.appendChild(loadingOverlay);
        
        // Send AJAX request
        fetch('<?= base_url('admin/materials/optimize-stock-levels') ?>', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            // Remove loading overlay
            document.body.removeChild(loadingOverlay);
            
            if (data.success) {
                alert(`Stock levels optimized successfully! Updated ${data.updated} items.`);
                // Reload the page to show updated levels
                window.location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to optimize stock levels.'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.body.removeChild(loadingOverlay);
            alert('An error occurred while optimizing stock levels. Please try again.');
        });
    }
}
</script>
<?= $this->endSection() ?>
