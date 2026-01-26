<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Client Edit Page -->
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Client</h1>
            <p class="text-gray-600">Update client information</p>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-2">
            <a href="<?= base_url('admin/clients') ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to Clients
            </a>
            <a href="<?= base_url('admin/clients/' . $client['id']) ?>" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-lucide="eye" class="w-4 h-4 mr-2"></i>
                View Client
            </a>
        </div>
    </div>

    <!-- Client Form -->
    <div class="bg-white rounded-lg shadow-sm border">
        <form action="<?= base_url('admin/clients/update/' . $client['id']) ?>" method="post" class="p-6 space-y-6">
            <?= csrf_field() ?>
            
            <!-- Basic Information Section -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Client Name *</label>
                        <input type="text" id="name" name="name" required
                               value="<?= old('name', $client['name']) ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    
                    <div>
                        <label for="client_code" class="block text-sm font-medium text-gray-700 mb-2">Client Code</label>
                        <input type="text" id="client_code" name="client_code"
                               value="<?= old('client_code', $client['client_code']) ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                    <div>
                        <label for="company_id" class="block text-sm font-medium text-gray-700 mb-2">Company *</label>
                        <select id="company_id" name="company_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Company</option>
                            <?php foreach($companies as $company): ?>
                                <option value="<?= $company['id'] ?>" <?= old('company_id', $client['company_id']) == $company['id'] ? 'selected' : '' ?>>
                                    <?= esc($company['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label for="client_type" class="block text-sm font-medium text-gray-700 mb-2">Client Type *</label>
                        <select id="client_type" name="client_type" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Type</option>
                            <option value="individual" <?= old('client_type', $client['client_type']) == 'individual' ? 'selected' : '' ?>>Individual</option>
                            <option value="company" <?= old('client_type', $client['client_type']) == 'company' ? 'selected' : '' ?>>Company</option>
                            <option value="government" <?= old('client_type', $client['client_type']) == 'government' ? 'selected' : '' ?>>Government</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select id="status" name="status"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="active" <?= old('status', $client['status']) == 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= old('status', $client['status']) == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Contact Information Section -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Contact Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="contact_person" class="block text-sm font-medium text-gray-700 mb-2">Contact Person</label>
                        <input type="text" id="contact_person" name="contact_person"
                               value="<?= old('contact_person', $client['contact_person']) ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" id="email" name="email"
                               value="<?= old('email', $client['email']) ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                        <input type="tel" id="phone" name="phone"
                               value="<?= old('phone', $client['phone']) ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    
                    <div>
                        <label for="mobile" class="block text-sm font-medium text-gray-700 mb-2">Mobile</label>
                        <input type="tel" id="mobile" name="mobile"
                               value="<?= old('mobile', $client['mobile']) ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
            </div>

            <!-- Address Information Section -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Address Information</h3>
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                        <textarea id="address" name="address" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"><?= old('address', $client['address']) ?></textarea>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-6">
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-2">City</label>
                        <input type="text" id="city" name="city"
                               value="<?= old('city', $client['city']) ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    
                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-700 mb-2">State/Province</label>
                        <input type="text" id="state" name="state"
                               value="<?= old('state', $client['state']) ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    
                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                        <input type="text" id="country" name="country"
                               value="<?= old('country', $client['country']) ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    
                    <div>
                        <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-2">Postal Code</label>
                        <input type="text" id="postal_code" name="postal_code"
                               value="<?= old('postal_code', $client['postal_code']) ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
            </div>

            <!-- Business Information Section -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Business Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="tax_number" class="block text-sm font-medium text-gray-700 mb-2">Tax Number</label>
                        <input type="text" id="tax_number" name="tax_number"
                               value="<?= old('tax_number', $client['tax_number']) ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    
                    <div>
                        <label for="payment_terms" class="block text-sm font-medium text-gray-700 mb-2">Payment Terms (days)</label>
                        <input type="number" id="payment_terms" name="payment_terms"
                               value="<?= old('payment_terms', $client['payment_terms']) ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    
                    <div>
                        <label for="credit_limit" class="block text-sm font-medium text-gray-700 mb-2">Credit Limit</label>
                        <input type="number" id="credit_limit" name="credit_limit" step="0.01"
                               value="<?= old('credit_limit', $client['credit_limit']) ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <div class="mt-6">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea id="notes" name="notes" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"><?= old('notes', $client['notes']) ?></textarea>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="border-t pt-6 flex justify-end space-x-3">
                <a href="<?= base_url('admin/clients') ?>" 
                   class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    Update Client
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
