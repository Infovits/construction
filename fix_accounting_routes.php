<?php
// Fix accounting routes placement in Routes.php

$file = 'D:/Wamp64/www/construction/app/Config/Routes.php';
$content = file_get_contents($file);

// First, remove the incorrectly placed accounting routes
$content = preg_replace('/\s*\/\/ Accounting Module Routes.*?}\);\s*}\);\s*/s', '', $content);

// Now add the accounting routes in the correct place within the admin group
$accountingRoutes = '
    // Accounting Module Routes
    $routes->group(\'accounting\', [\'namespace\' => \'App\\Controllers\\Accounting\'], function($routes) {
        // Account Categories
        $routes->group(\'account-categories\', function($routes) {
            $routes->get(\'/\', \'AccountCategoriesController::index\');
            $routes->get(\'create\', \'AccountCategoriesController::create\');
            $routes->post(\'/\', \'AccountCategoriesController::store\');
            $routes->get(\'(:num)\', \'AccountCategoriesController::show/$1\');
            $routes->get(\'(:num)/edit\', \'AccountCategoriesController::edit/$1\');
            $routes->post(\'(:num)\', \'AccountCategoriesController::update/$1\');
            $routes->delete(\'(:num)\', \'AccountCategoriesController::delete/$1\');
            $routes->post(\'(:num)/toggle\', \'AccountCategoriesController::toggle/$1\');
        });

        // Chart of Accounts
        $routes->group(\'chart-of-accounts\', function($routes) {
            $routes->get(\'/\', \'ChartOfAccountsController::index\');
            $routes->get(\'create\', \'ChartOfAccountsController::create\');
            $routes->post(\'/\', \'ChartOfAccountsController::store\');
            $routes->get(\'(:num)\', \'ChartOfAccountsController::show/$1\');
            $routes->get(\'(:num)/edit\', \'ChartOfAccountsController::edit/$1\');
            $routes->post(\'(:num)\', \'ChartOfAccountsController::update/$1\');
            $routes->delete(\'(:num)\', \'ChartOfAccountsController::delete/$1\');
            $routes->post(\'(:num)/toggle\', \'ChartOfAccountsController::toggle/$1\');
        });

        // Journal Entries
        $routes->group(\'journal-entries\', function($routes) {
            $routes->get(\'/\', \'JournalEntriesController::index\');
            $routes->get(\'create\', \'JournalEntriesController::create\');
            $routes->post(\'/\', \'JournalEntriesController::store\');
            $routes->get(\'(:num)\', \'JournalEntriesController::show/$1\');
            $routes->get(\'(:num)/edit\', \'JournalEntriesController::edit/$1\');
            $routes->post(\'(:num)\', \'JournalEntriesController::update/$1\');
            $routes->delete(\'(:num)\', \'JournalEntriesController::delete/$1\');
            $routes->post(\'(:num)/post\', \'JournalEntriesController::post/$1\');
            $routes->post(\'(:num)/reverse\', \'JournalEntriesController::reverse/$1\');
        });

        // Supplier Invoices
        $routes->group(\'supplier-invoices\', function($routes) {
            $routes->get(\'/\', \'SupplierInvoicesController::index\');
            $routes->get(\'create\', \'SupplierInvoicesController::create\');
            $routes->post(\'/\', \'SupplierInvoicesController::store\');
            $routes->get(\'(:num)\', \'SupplierInvoicesController::show/$1\');
            $routes->get(\'(:num)/edit\', \'SupplierInvoicesController::edit/$1\');
            $routes->post(\'(:num)\', \'SupplierInvoicesController::update/$1\');
            $routes->delete(\'(:num)\', \'SupplierInvoicesController::delete/$1\');
            $routes->post(\'(:num)/approve\', \'SupplierInvoicesController::approve/$1\');
            $routes->post(\'(:num)/match\', \'SupplierInvoicesController::threeWayMatch/$1\');
        });

        // Supplier Payments
        $routes->group(\'supplier-payments\', function($routes) {
            $routes->get(\'/\', \'SupplierPaymentsController::index\');
            $routes->get(\'create\', \'SupplierPaymentsController::create\');
            $routes->post(\'/\', \'SupplierPaymentsController::store\');
            $routes->get(\'(:num)\', \'SupplierPaymentsController::show/$1\');
            $routes->get(\'(:num)/edit\', \'SupplierPaymentsController::edit/$1\');
            $routes->post(\'(:num)\', \'SupplierPaymentsController::update/$1\');
            $routes->delete(\'(:num)\', \'SupplierPaymentsController::delete/$1\');
        });

        // Financial Reports
        $routes->group(\'reports\', function($routes) {
            $routes->get(\'/\', \'ReportsController::index\');
            $routes->get(\'trial-balance\', \'ReportsController::trialBalance\');
            $routes->get(\'profit-loss\', \'ReportsController::profitLoss\');
            $routes->get(\'balance-sheet\', \'ReportsController::balanceSheet\');
            $routes->post(\'generate\', \'ReportsController::generate\');
        });

        // Budget Management
        $routes->group(\'budgets\', function($routes) {
            $routes->get(\'/\', \'BudgetsController::index\');
            $routes->get(\'create\', \'BudgetsController::create\');
            $routes->post(\'/\', \'BudgetsController::store\');
            $routes->get(\'(:num)\', \'BudgetsController::show/$1\');
            $routes->get(\'(:num)/edit\', \'BudgetsController::edit/$1\');
            $routes->post(\'(:num)\', \'BudgetsController::update/$1\');
            $routes->delete(\'(:num)\', \'BudgetsController::delete/$1\');
        });

        $routes->group(\'budget-categories\', function($routes) {
            $routes->get(\'/\', \'BudgetCategoriesController::index\');
            $routes->get(\'create\', \'BudgetCategoriesController::create\');
            $routes->post(\'/\', \'BudgetCategoriesController::store\');
            $routes->get(\'(:num)\', \'BudgetCategoriesController::show/$1\');
            $routes->get(\'(:num)/edit\', \'BudgetCategoriesController::edit/$1\');
            $routes->post(\'(:num)\', \'BudgetCategoriesController::update/$1\');
            $routes->delete(\'(:num)\', \'BudgetCategoriesController::delete/$1\');
        });
    });
';

// Insert before the closing of the admin group (before the last });)
$pattern = '/(\s+}\);\s*}\);\s*)(\/\/ Default redirect)/';
$replacement = $accountingRoutes . '$1$2';

$content = preg_replace($pattern, $replacement, $content);

file_put_contents($file, $content);

echo "Accounting routes fixed and properly placed within admin group!\n";
?>
