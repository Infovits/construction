<?php
// Clean up and fix the routes file

$file = 'D:/Wamp64/www\construction/app/Config/Routes.php';
$content = file_get_contents($file);

// Remove all accounting-related routes (both corrupted and duplicated ones)
$content = preg_replace('/\s*\/\/ Account Categories.*?}\);\s*}\);\s*/s', '', $content);
$content = preg_replace('/\s*\/\/ Accounting Module Routes.*?}\);\s*}\);\s*/s', '', $content);
$content = preg_replace('/\s*\$routes->group\(\'account-categories\'.*?}\);\s*/s', '', $content);
$content = preg_replace('/\s*\$routes->group\(\'accounting\'.*?}\);\s*}\);\s*/s', '', $content);

// Clean up any malformed lines
$lines = explode("\n", $content);
$cleanLines = [];
foreach ($lines as $line) {
    // Skip lines that look corrupted (contain multiple }); patterns)
    if (!preg_match('/}\);\s*}\);\s*\'\);/', $line) && !preg_match('/AccountCategoriesController.*}\);\s*}\);/', $line)) {
        $cleanLines[] = $line;
    }
}
$content = implode("\n", $cleanLines);

// Now add the accounting routes in the correct place
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
$replacement = $accountingRoutes . '$1$2';

$content = preg_replace($pattern, $replacement, $content);

file_put_contents($file, $content);

echo "Routes file cleaned and accounting routes properly added!\n";
?>
