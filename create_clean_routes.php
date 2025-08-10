<?php
// Create a completely clean routes file with accounting routes in the right place

$file = 'D:/Wamp64/www/construction/app/Config/Routes.php';
$content = file_get_contents($file);

// Read the file line by line and rebuild it properly
$lines = explode("\n", $content);
$cleanLines = [];
$insideAdminGroup = false;
$accountingAdded = false;

foreach ($lines as $lineNum => $line) {
    // Track when we enter the admin group
    if (strpos($line, "\$routes->group('admin'") !== false) {
        $insideAdminGroup = true;
        $cleanLines[] = $line;
        continue;
    }
    
    // If we're at the end of procurement section and haven't added accounting yet
    if ($insideAdminGroup && !$accountingAdded && 
        (strpos($line, "    });") !== false && 
         isset($lines[$lineNum + 1]) && 
         strpos($lines[$lineNum + 1], "});") !== false)) {
        
        // This is the end of procurement section, add accounting routes here
        $cleanLines[] = $line; // Add the procurement closing brace
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
        
        $accountingAdded = true;
        continue;
    }
    
    // Track when we exit the admin group
    if ($insideAdminGroup && strpos($line, "});") !== false && 
        isset($lines[$lineNum + 1]) && 
        strpos($lines[$lineNum + 1], "// Default redirect") !== false) {
        
        $insideAdminGroup = false;
        $cleanLines[] = $line;
        continue;
    }
    
    // Skip any existing accounting routes that are outside the admin group
    if (strpos($line, "// Accounting Module Routes") !== false ||
        strpos($line, "AccountCategoriesController") !== false ||
        strpos($line, "ChartOfAccountsController") !== false ||
        (strpos($line, "\$routes->group('accounting'") !== false)) {
        
        // Skip this line and continue until we find the end of the accounting section
        while ($lineNum < count($lines) - 1 && 
               (strpos($lines[$lineNum], "    });") === false || 
                strpos($lines[$lineNum + 1], "// Default redirect") === false)) {
            $lineNum++;
        }
        continue;
    }
    
    // Add all other lines normally
    $cleanLines[] = $line;
}

$content = implode("\n", $cleanLines);
file_put_contents($file, $content);

echo "Clean routes file created with accounting routes properly placed!\n";
?>
