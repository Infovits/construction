<?php helper('currency'); ?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= $title ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Procurement Reports</h1>
            <p class="text-gray-600">Generate comprehensive reports for procurement activities and analysis</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
            <button onclick="exportAllData()" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                <i data-lucide="download" class="w-4 h-4 mr-2"></i>
                Export All Data
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-lg shadow-sm border border-l-4 border-l-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-blue-600 uppercase tracking-wide">Material Requests</p>
                    <p class="text-2xl font-bold text-gray-900"><?= $materialRequestsCount ?></p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="file-text" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border border-l-4 border-l-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-purple-600 uppercase tracking-wide">Purchase Orders</p>
                    <p class="text-2xl font-bold text-gray-900"><?= $purchaseOrdersCount ?></p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="shopping-cart" class="w-6 h-6 text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border border-l-4 border-l-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-green-600 uppercase tracking-wide">Goods Receipts</p>
                    <p class="text-2xl font-bold text-gray-900"><?= $goodsReceiptsCount ?></p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="package" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border border-l-4 border-l-orange-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-orange-600 uppercase tracking-wide">Quality Inspections</p>
                    <p class="text-2xl font-bold text-gray-900"><?= $inspectionsCount ?></p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="search" class="w-6 h-6 text-orange-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Generation Form -->
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Generate Custom Report</h2>
            <p class="text-sm text-gray-600 mt-1">Select report type and filters to generate detailed reports</p>
        </div>
        <div class="p-6">
            <form action="<?= base_url('admin/procurement/reports/generate') ?>" method="POST" class="space-y-6">
                <?= csrf_field() ?>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Report Type -->
                    <div>
                        <label for="report_type" class="block text-sm font-medium text-gray-700 mb-2">Report Type</label>
                        <select name="report_type" id="report_type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Report Type</option>
                            <option value="material_requests">Material Requests Report</option>
                            <option value="purchase_orders">Purchase Orders Report</option>
                            <option value="goods_receipt">Goods Receipt Report</option>
                            <option value="quality_inspections">Quality Inspections Report</option>
                            <option value="procurement_summary">Procurement Summary Report</option>
                        </select>
                    </div>

                    <!-- Date From -->
                    <div>
                        <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
                        <input type="date" name="date_from" id="date_from" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <!-- Date To -->
                    <div>
                        <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
                        <input type="date" name="date_to" id="date_to" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <!-- Supplier Filter -->
                    <div>
                        <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-2">Supplier (Optional)</label>
                        <select name="supplier_id" id="supplier_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">All Suppliers</option>
                            <?php foreach ($suppliers as $supplier): ?>
                                <option value="<?= $supplier['id'] ?>"><?= esc($supplier['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Project Filter -->
                    <div>
                        <label for="project_id" class="block text-sm font-medium text-gray-700 mb-2">Project (Optional)</label>
                        <select name="project_id" id="project_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">All Projects</option>
                            <?php foreach ($projects as $project): ?>
                                <option value="<?= $project['id'] ?>"><?= esc($project['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="inline-flex items-center px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <i data-lucide="bar-chart-3" class="w-4 h-4 mr-2"></i>
                        Generate Report
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Quick Report Access -->
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Quick Report Access</h2>
            <p class="text-sm text-gray-600 mt-1">Generate common reports with default settings</p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <form action="<?= base_url('admin/procurement/reports/generate') ?>" method="POST" class="inline">
                    <?= csrf_field() ?>
                    <input type="hidden" name="report_type" value="material_requests">
                    <button type="submit" class="w-full p-4 text-left border border-gray-200 rounded-lg hover:border-blue-300 hover:bg-blue-50 transition-colors">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <i data-lucide="file-text" class="w-5 h-5 text-blue-600"></i>
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-900">Material Requests</h3>
                                <p class="text-sm text-gray-600">All material requisitions</p>
                            </div>
                        </div>
                    </button>
                </form>

                <form action="<?= base_url('admin/procurement/reports/generate') ?>" method="POST" class="inline">
                    <?= csrf_field() ?>
                    <input type="hidden" name="report_type" value="purchase_orders">
                    <button type="submit" class="w-full p-4 text-left border border-gray-200 rounded-lg hover:border-purple-300 hover:bg-purple-50 transition-colors">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                <i data-lucide="shopping-cart" class="w-5 h-5 text-purple-600"></i>
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-900">Purchase Orders</h3>
                                <p class="text-sm text-gray-600">All purchase orders</p>
                            </div>
                        </div>
                    </button>
                </form>

                <form action="<?= base_url('admin/procurement/reports/generate') ?>" method="POST" class="inline">
                    <?= csrf_field() ?>
                    <input type="hidden" name="report_type" value="goods_receipt">
                    <button type="submit" class="w-full p-4 text-left border border-gray-200 rounded-lg hover:border-green-300 hover:bg-green-50 transition-colors">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                <i data-lucide="package" class="w-5 h-5 text-green-600"></i>
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-900">Goods Receipts</h3>
                                <p class="text-sm text-gray-600">All goods receipt notes</p>
                            </div>
                        </div>
                    </button>
                </form>

                <form action="<?= base_url('admin/procurement/reports/generate') ?>" method="POST" class="inline">
                    <?= csrf_field() ?>
                    <input type="hidden" name="report_type" value="quality_inspections">
                    <button type="submit" class="w-full p-4 text-left border border-gray-200 rounded-lg hover:border-orange-300 hover:bg-orange-50 transition-colors">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                                <i data-lucide="search" class="w-5 h-5 text-orange-600"></i>
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-900">Quality Inspections</h3>
                                <p class="text-sm text-gray-600">All quality inspections</p>
                            </div>
                        </div>
                    </button>
                </form>

                <form action="<?= base_url('admin/procurement/reports/generate') ?>" method="POST" class="inline">
                    <?= csrf_field() ?>
                    <input type="hidden" name="report_type" value="procurement_summary">
                    <button type="submit" class="w-full p-4 text-left border border-gray-200 rounded-lg hover:border-indigo-300 hover:bg-indigo-50 transition-colors">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                                <i data-lucide="bar-chart-3" class="w-5 h-5 text-indigo-600"></i>
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-900">Summary Report</h3>
                                <p class="text-sm text-gray-600">Comprehensive overview</p>
                            </div>
                        </div>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function exportAllData() {
        // You can implement a comprehensive export function here
        alert('Export functionality will be implemented based on your requirements');
    }

    // Set default date range (last 30 days)
    document.addEventListener('DOMContentLoaded', function() {
        const today = new Date();
        const thirtyDaysAgo = new Date(today.getTime() - (30 * 24 * 60 * 60 * 1000));

        document.getElementById('date_to').value = today.toISOString().split('T')[0];
        document.getElementById('date_from').value = thirtyDaysAgo.toISOString().split('T')[0];
    });
</script>

<?= $this->endSection() ?>
