<?php
// Add accounting routes to Routes.php

$file = 'D:/Wamp64/www/construction/app/Config/Routes.php';
$content = file_get_contents($file);

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
    });
';

// Insert before the closing of the admin group
$pattern = '/(\s+}\);\s*}\);\s*)(\/\/ Default redirect)/';
$replacement = '$1' . $accountingRoutes . '$2';

$content = preg_replace($pattern, $replacement, $content);

file_put_contents($file, $content);

echo "Accounting routes added successfully!\n";
?>
