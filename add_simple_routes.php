<?php
// Add simple accounting routes

$file = 'D:/Wamp64/www/construction/app/Config/Routes.php';
$content = file_get_contents($file);

// Find the end of procurement section and add accounting routes
$accountingRoutes = '
    // Accounting Module Routes
    $routes->group(\'accounting\', [\'namespace\' => \'App\\Controllers\\Accounting\'], function($routes) {
        // Account Categories
        $routes->get(\'account-categories\', \'AccountCategoriesController::index\');
        $routes->get(\'account-categories/create\', \'AccountCategoriesController::create\');
        $routes->post(\'account-categories\', \'AccountCategoriesController::store\');
        $routes->get(\'account-categories/(:num)\', \'AccountCategoriesController::show/$1\');
        $routes->get(\'account-categories/(:num)/edit\', \'AccountCategoriesController::edit/$1\');
        $routes->post(\'account-categories/(:num)\', \'AccountCategoriesController::update/$1\');
        $routes->delete(\'account-categories/(:num)\', \'AccountCategoriesController::delete/$1\');
        $routes->post(\'account-categories/(:num)/toggle\', \'AccountCategoriesController::toggle/$1\');
        
        // Chart of Accounts
        $routes->get(\'chart-of-accounts\', \'ChartOfAccountsController::index\');
        $routes->get(\'chart-of-accounts/create\', \'ChartOfAccountsController::create\');
        $routes->post(\'chart-of-accounts\', \'ChartOfAccountsController::store\');
        $routes->get(\'chart-of-accounts/(:num)\', \'ChartOfAccountsController::show/$1\');
        $routes->get(\'chart-of-accounts/(:num)/edit\', \'ChartOfAccountsController::edit/$1\');
        $routes->post(\'chart-of-accounts/(:num)\', \'ChartOfAccountsController::update/$1\');
        $routes->delete(\'chart-of-accounts/(:num)\', \'ChartOfAccountsController::delete/$1\');
        $routes->post(\'chart-of-accounts/(:num)/toggle\', \'ChartOfAccountsController::toggle/$1\');
    });
';

// Insert before the closing of the admin group
$pattern = '/(\s+}\);\s*}\);\s*)(\/\/ Default redirect)/';
$replacement = $accountingRoutes . '$1$2';

$content = preg_replace($pattern, $replacement, $content);

file_put_contents($file, $content);

echo "Simple accounting routes added successfully!\n";
?>
