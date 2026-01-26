<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= $title ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="max-w-4xl mx-auto space-y-8">
    <!-- Header -->
    <div class="text-center">
        <i data-lucide="database" class="w-16 h-16 mx-auto mb-4 text-indigo-600"></i>
        <h1 class="text-3xl font-bold text-gray-900">Journal Entries Setup Required</h1>
        <p class="mt-2 text-lg text-gray-600">The journal entries database tables need to be created before you can start using this feature.</p>
    </div>

    <!-- Error Message (if any) -->
    <?php if (isset($error_message)): ?>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-red-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Database Error</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <p><?= esc($error_message) ?></p>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Automatic Setup Option -->
    <div class="bg-white rounded-xl shadow-lg border overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
            <h2 class="text-xl font-semibold text-white flex items-center">
                <i data-lucide="zap" class="w-5 h-5 mr-2"></i>
                Option 1: Automatic Setup (Recommended)
            </h2>
        </div>
        <div class="p-6">
            <p class="text-gray-600 mb-4">Click the button below to automatically create the journal entries database tables with sample data.</p>
            
            <div class="space-y-3 mb-6">
                <div class="flex items-center text-sm text-gray-700">
                    <i data-lucide="check" class="w-4 h-4 text-green-500 mr-2"></i>
                    Creates journal_entries and journal_entry_lines tables
                </div>
                <div class="flex items-center text-sm text-gray-700">
                    <i data-lucide="check" class="w-4 h-4 text-green-500 mr-2"></i>
                    Adds sample journal entries for testing
                </div>
                <div class="flex items-center text-sm text-gray-700">
                    <i data-lucide="check" class="w-4 h-4 text-green-500 mr-2"></i>
                    Sets up proper indexes and relationships
                </div>
            </div>

            <a href="<?= $setup_url ?>" 
               class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-lucide="play" class="w-5 h-5 mr-2"></i>
                Run Automatic Setup
            </a>
        </div>
    </div>

    <!-- Manual Setup Option -->
    <div class="bg-white rounded-xl shadow-lg border overflow-hidden">
        <div class="bg-gradient-to-r from-gray-500 to-gray-600 px-6 py-4">
            <h2 class="text-xl font-semibold text-white flex items-center">
                <i data-lucide="terminal" class="w-5 h-5 mr-2"></i>
                Option 2: Manual Setup
            </h2>
        </div>
        <div class="p-6">
            <p class="text-gray-600 mb-4">If you prefer to run the SQL manually, follow these steps:</p>
            
            <div class="space-y-4">
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="font-semibold text-gray-900 mb-2">Step 1: Access phpMyAdmin</h3>
                    <p class="text-sm text-gray-600 mb-2">Open phpMyAdmin in your browser:</p>
                    <code class="bg-gray-100 px-2 py-1 rounded text-sm">http://localhost/phpmyadmin/</code>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="font-semibold text-gray-900 mb-2">Step 2: Select Database</h3>
                    <p class="text-sm text-gray-600">Select your database: <code class="bg-gray-100 px-2 py-1 rounded">contsruction</code></p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="font-semibold text-gray-900 mb-2">Step 3: Run SQL</h3>
                    <p class="text-sm text-gray-600 mb-2">Execute the SQL from this file:</p>
                    <code class="bg-gray-100 px-2 py-1 rounded text-sm">D:\Wamp64\www\construction\create_journal_tables.sql</code>
                </div>
            </div>
        </div>
    </div>

    <!-- What Gets Created -->
    <div class="bg-blue-50 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-blue-900 mb-4 flex items-center">
            <i data-lucide="info" class="w-5 h-5 mr-2"></i>
            What Gets Created
        </h3>
        <div class="grid md:grid-cols-2 gap-4 text-sm">
            <div>
                <h4 class="font-semibold text-blue-800 mb-2">Database Tables:</h4>
                <ul class="space-y-1 text-blue-700">
                    <li>• journal_entries (headers)</li>
                    <li>• journal_entry_lines (detail lines)</li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold text-blue-800 mb-2">Sample Data:</h4>
                <ul class="space-y-1 text-blue-700">
                    <li>• Owner investment entry</li>
                    <li>• Equipment purchase entry</li>
                    <li>• Office rent entry (draft)</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <div class="text-center">
        <a href="<?= base_url('admin/accounting/chart-of-accounts') ?>" 
           class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Back to Chart of Accounts
        </a>
    </div>
</div>

<script>
// Auto-refresh page after setup to check if tables were created
if (window.location.search.includes('setup=complete')) {
    setTimeout(() => {
        window.location.href = '<?= base_url('admin/accounting/journal-entries') ?>';
    }, 2000);
}
</script>

<?= $this->endSection() ?>