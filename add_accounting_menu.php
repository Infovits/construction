<?php
// Add accounting menu to the sidebar

$file = 'D:/Wamp64/www/construction/app/Views/layouts/main.php';
$content = file_get_contents($file);

$accountingMenu = '                </div>

                <!-- Accounting Module -->
                <div class="px-4 md:px-6 py-3 hover:bg-gray-50 transition-colors">
                    <a href="#" class="flex items-center space-x-3 text-gray-600 nav-item relative" onclick="toggleSubmenu(event, \'accounting-submenu\')">
                        <i data-lucide="calculator" class="w-5 h-5 flex-shrink-0"></i>
                        <span class="sidebar-text overflow-hidden whitespace-nowrap">Accounting</span>
                        <i data-lucide="chevron-down" class="w-4 h-4 ml-auto sidebar-text menu-chevron" id="accounting-chevron"></i>
                        <div class="tooltip absolute left-16 bg-gray-800 text-white px-2 py-1 rounded text-sm whitespace-nowrap">
                            Accounting
                        </div>
                    </a>
                    <div class="submenu" id="accounting-submenu">
                        <div class="sidebar-text ml-8 mt-2 space-y-1">
                            <!-- Chart of Accounts -->
                            <a href="<?= base_url(\'admin/accounting/chart-of-accounts\') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700">Chart of Accounts</a>
                            <a href="<?= base_url(\'admin/accounting/account-categories\') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700">Account Categories</a>
                            
                            <!-- Journal Entries -->
                            <a href="<?= base_url(\'admin/accounting/journal-entries\') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700">Journal Entries</a>
                            
                            <!-- Accounts Payable -->
                            <a href="<?= base_url(\'admin/accounting/supplier-invoices\') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700">Supplier Invoices</a>
                            <a href="<?= base_url(\'admin/accounting/supplier-payments\') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700">Supplier Payments</a>
                            
                            <!-- Accounts Receivable -->
                            <a href="<?= base_url(\'admin/accounting/invoices\') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700">Customer Invoices</a>
                            <a href="<?= base_url(\'admin/accounting/payments\') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700">Customer Payments</a>
                            
                            <!-- Budget Management -->
                            <a href="<?= base_url(\'admin/accounting/budgets\') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700">Budgets</a>
                            <a href="<?= base_url(\'admin/accounting/budget-categories\') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700">Budget Categories</a>
                            
                            <!-- Reports -->
                            <a href="<?= base_url(\'admin/accounting/reports\') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700">Financial Reports</a>
                            <a href="<?= base_url(\'admin/accounting/trial-balance\') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700">Trial Balance</a>
                            <a href="<?= base_url(\'admin/accounting/profit-loss\') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700">Profit & Loss</a>
                            <a href="<?= base_url(\'admin/accounting/balance-sheet\') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700">Balance Sheet</a>
                        </div>
                    </div>
                </div>

                <div class="px-4 md:px-6 py-3 hover:bg-gray-50 transition-colors">
                    <a href="#" class="flex items-center space-x-3 text-gray-600 nav-item relative" onclick="toggleSubmenu(event, \'notification-submenu\')">
                        <i data-lucide="bell" class="w-5 h-5 flex-shrink-0"></i>
                        <span class="sidebar-text overflow-hidden whitespace-nowrap">Notification</span>
                        <i data-lucide="chevron-down" class="w-4 h-4 ml-auto sidebar-text menu-chevron" id="notification-chevron"></i>';

// Find the notification section and replace it
$pattern = '/                <\/div>\s*<div class="px-4 md:px-6 py-3 hover:bg-gray-50 transition-colors">\s*<a href="#" class="flex items-center space-x-3 text-gray-600 nav-item relative" onclick="toggleSubmenu\(event, \'notification-submenu\'\)">\s*<i data-lucide="bell" class="w-5 h-5 flex-shrink-0"><\/i>\s*<span class="sidebar-text overflow-hidden whitespace-nowrap">Notification<\/span>\s*<i data-lucide="chevron-down" class="w-4 h-4 ml-auto sidebar-text menu-chevron" id="notification-chevron"><\/i>/';

$replacement = $accountingMenu;

$content = preg_replace($pattern, $replacement, $content);

file_put_contents($file, $content);

echo "Accounting menu added successfully!\n";
?>
