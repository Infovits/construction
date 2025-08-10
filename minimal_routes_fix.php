<?php
// Create a minimal working routes file

$routesContent = '<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Auth Routes
$routes->group("auth", function($routes) {
    $routes->get("login", "Auth::login");
    $routes->post("login", "Auth::authenticate");
    $routes->get("logout", "Auth::logout");
    $routes->get("register", "Auth::register");
    $routes->post("register", "Auth::store");
});

// Redirect milestones to admin/milestones for convenience
$routes->get("milestones", function() {
    return redirect()->to("/admin/milestones");
});
$routes->get("milestones/(.*)", function($path) {
    return redirect()->to("/admin/milestones/" . $path);
});

$routes->group("admin", ["filter" => "auth"], function($routes) {
    $routes->get("dashboard", "Dashboard::index");

    // User Management Routes
    $routes->group("users", function($routes) {
        $routes->get("/", "Users::index");
        $routes->get("create", "Users::create");
        $routes->post("store", "Users::store");
        $routes->get("(:num)/edit", "Users::edit/$1");
        $routes->post("(:num)/update", "Users::update/$1");
        $routes->delete("(:num)", "Users::delete/$1");
    });

    // Project Management Routes
    $routes->group("projects", function($routes) {
        $routes->get("/", "Projects::index");
        $routes->get("create", "Projects::create");
        $routes->post("store", "Projects::store");
        $routes->get("(:num)", "Projects::show/$1");
        $routes->get("(:num)/edit", "Projects::edit/$1");
        $routes->post("(:num)/update", "Projects::update/$1");
        $routes->delete("(:num)", "Projects::delete/$1");
    });

    // Procurement Routes
    $routes->group("procurement", function($routes) {
        // Material Requests
        $routes->group("material-requests", function($routes) {
            $routes->get("/", "MaterialRequestController::index");
            $routes->get("create", "MaterialRequestController::create");
            $routes->post("/", "MaterialRequestController::store");
            $routes->get("(:num)", "MaterialRequestController::show/$1");
            $routes->get("(:num)/edit", "MaterialRequestController::edit/$1");
            $routes->post("(:num)", "MaterialRequestController::update/$1");
            $routes->delete("(:num)", "MaterialRequestController::delete/$1");
        });

        // Purchase Orders
        $routes->group("purchase-orders", function($routes) {
            $routes->get("/", "PurchaseOrderController::index");
            $routes->get("create", "PurchaseOrderController::create");
            $routes->post("/", "PurchaseOrderController::store");
            $routes->get("(:num)", "PurchaseOrderController::show/$1");
            $routes->get("(:num)/edit", "PurchaseOrderController::edit/$1");
            $routes->post("(:num)", "PurchaseOrderController::update/$1");
            $routes->delete("(:num)", "PurchaseOrderController::delete/$1");
        });

        // Reports
        $routes->get("reports", "ProcurementReportsController::index");
        $routes->post("reports/generate", "ProcurementReportsController::generate");
    });

    // Accounting Module Routes
    $routes->group("accounting", ["namespace" => "App\\Controllers\\Accounting"], function($routes) {
        // Account Categories
        $routes->get("account-categories", "AccountCategoriesController::index");
        $routes->get("account-categories/create", "AccountCategoriesController::create");
        $routes->post("account-categories", "AccountCategoriesController::store");
        $routes->get("account-categories/(:num)", "AccountCategoriesController::show/$1");
        $routes->get("account-categories/(:num)/edit", "AccountCategoriesController::edit/$1");
        $routes->post("account-categories/(:num)", "AccountCategoriesController::update/$1");
        $routes->delete("account-categories/(:num)", "AccountCategoriesController::delete/$1");
        $routes->post("account-categories/(:num)/toggle", "AccountCategoriesController::toggle/$1");
        
        // Chart of Accounts
        $routes->get("chart-of-accounts", "ChartOfAccountsController::index");
        $routes->get("chart-of-accounts/create", "ChartOfAccountsController::create");
        $routes->post("chart-of-accounts", "ChartOfAccountsController::store");
        $routes->get("chart-of-accounts/(:num)", "ChartOfAccountsController::show/$1");
        $routes->get("chart-of-accounts/(:num)/edit", "ChartOfAccountsController::edit/$1");
        $routes->post("chart-of-accounts/(:num)", "ChartOfAccountsController::update/$1");
        $routes->delete("chart-of-accounts/(:num)", "ChartOfAccountsController::delete/$1");
        $routes->post("chart-of-accounts/(:num)/toggle", "ChartOfAccountsController::toggle/$1");
    });
});

// Default redirect to dashboard if logged in, login if not
$routes->get("/", function() {
    return session("user_id") ? redirect()->to("/admin/dashboard") : redirect()->to("/auth/login");
});
';

file_put_contents('D:/Wamp64/www/construction/app/Config/Routes.php', $routesContent);

echo "Minimal working routes file created!\n";
?>
