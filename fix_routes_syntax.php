<?php
// Fix the corrupted routes file

$file = 'D:/Wamp64/www/construction/app/Config/Routes.php';
$content = file_get_contents($file);

// Remove all the corrupted lines starting from line 336
$lines = explode("\n", $content);
$cleanLines = [];

foreach ($lines as $lineNum => $line) {
    // Keep lines up to the procurement reports section
    if ($lineNum < 335) {
        $cleanLines[] = $line;
    }
    // Skip corrupted lines (336 onwards that contain malformed syntax)
    elseif (strpos($line, "});');") !== false || 
            strpos($line, "AccountCategoriesController") !== false ||
            strpos($line, "});") === 0) {
        // Skip these corrupted lines
        continue;
    }
    // Keep lines after the corruption (if any)
    elseif ($lineNum > 400 && strpos($line, "// Default redirect") !== false) {
        $cleanLines[] = $line;
        // Add remaining lines
        for ($i = $lineNum + 1; $i < count($lines); $i++) {
            $cleanLines[] = $lines[$i];
        }
        break;
    }
}

// Add the proper closing for procurement section and admin group
$cleanLines[] = "    });";
$cleanLines[] = "";
$cleanLines[] = "    // Accounting Module Routes";
$cleanLines[] = "    \$routes->group('accounting', ['namespace' => 'App\\Controllers\\Accounting'], function(\$routes) {";
$cleanLines[] = "        // Account Categories";
$cleanLines[] = "        \$routes->get('account-categories', 'AccountCategoriesController::index');";
$cleanLines[] = "        \$routes->get('account-categories/create', 'AccountCategoriesController::create');";
$cleanLines[] = "        \$routes->post('account-categories', 'AccountCategoriesController::store');";
$cleanLines[] = "        \$routes->get('account-categories/(:num)', 'AccountCategoriesController::show/\$1');";
$cleanLines[] = "        \$routes->get('account-categories/(:num)/edit', 'AccountCategoriesController::edit/\$1');";
$cleanLines[] = "        \$routes->post('account-categories/(:num)', 'AccountCategoriesController::update/\$1');";
$cleanLines[] = "        \$routes->delete('account-categories/(:num)', 'AccountCategoriesController::delete/\$1');";
$cleanLines[] = "        \$routes->post('account-categories/(:num)/toggle', 'AccountCategoriesController::toggle/\$1');";
$cleanLines[] = "";
$cleanLines[] = "        // Chart of Accounts";
$cleanLines[] = "        \$routes->get('chart-of-accounts', 'ChartOfAccountsController::index');";
$cleanLines[] = "        \$routes->get('chart-of-accounts/create', 'ChartOfAccountsController::create');";
$cleanLines[] = "        \$routes->post('chart-of-accounts', 'ChartOfAccountsController::store');";
$cleanLines[] = "        \$routes->get('chart-of-accounts/(:num)', 'ChartOfAccountsController::show/\$1');";
$cleanLines[] = "        \$routes->get('chart-of-accounts/(:num)/edit', 'ChartOfAccountsController::edit/\$1');";
$cleanLines[] = "        \$routes->post('chart-of-accounts/(:num)', 'ChartOfAccountsController::update/\$1');";
$cleanLines[] = "        \$routes->delete('chart-of-accounts/(:num)', 'ChartOfAccountsController::delete/\$1');";
$cleanLines[] = "        \$routes->post('chart-of-accounts/(:num)/toggle', 'ChartOfAccountsController::toggle/\$1');";
$cleanLines[] = "    });";
$cleanLines[] = "});";
$cleanLines[] = "";
$cleanLines[] = "// Default redirect to dashboard if logged in, login if not";
$cleanLines[] = "\$routes->get('/', function() {";
$cleanLines[] = "    return session('user_id') ? redirect()->to('/admin/dashboard') : redirect()->to('/auth/login');";
$cleanLines[] = "});";

$content = implode("\n", $cleanLines);

file_put_contents($file, $content);

echo "Routes file syntax fixed!\n";
?>
