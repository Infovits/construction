<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= $title ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Quality Inspections</h1>
            <p class="text-gray-600">Manage quality control and inspection processes</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
            <a href="<?= base_url('admin/quality-inspections/create') ?>" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">    
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                New Inspection
            </a>
            <a href="<?= base_url('admin/quality-inspections/my-inspections') ?>" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">    
                <i data-lucide="user-check" class="w-4 h-4 mr-2"></i>
                My Inspections
            </a>
        </div>
    </div>

    <!-- Coming Soon Message -->
    <div class="bg-white rounded-lg shadow-sm p-12 text-center">
        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i data-lucide="search-check" class="w-8 h-8 text-blue-600"></i>
        </div>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">Quality Inspections Module</h3>
        <p class="text-gray-600 mb-4">This module is ready and functional. The UI is being developed.</p>
        <p class="text-sm text-gray-500">Backend functionality includes: Inspection creation from GRN items, inspector assignment, pass/fail tracking, and automatic stock movement.</p>
    </div>
</div>

<script>
lucide.createIcons();
</script>

<?= $this->endSection() ?>
