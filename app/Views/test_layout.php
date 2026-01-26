<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Test Layout<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="p-6">
    <h1 class="text-2xl font-bold text-gray-900">Layout Test Page</h1>
    <p class="text-gray-600 mt-2">This is a test to verify the layout is working correctly.</p>
    
    <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <p class="text-blue-800">If you can see this styled box with proper colors and layout, the main layout is working.</p>
    </div>
</div>
<?= $this->endSection() ?>