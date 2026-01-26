<?php

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

// Note: Redirects removed to avoid conflicts with admin routes

$routes->group("admin", ["filter" => "auth"], function($routes) {
    $routes->get("dashboard", "Dashboard::index");

    // User Management Routes
    $routes->group("users", function($routes) {
        $routes->get("/", "Users::index");
        $routes->get("create", "Users::create");
        $routes->post("store", "Users::store");
        $routes->get("edit/(:num)", "Users::edit/$1");
        $routes->post("update/(:num)", "Users::update/$1");
        $routes->delete("(:num)", "Users::delete/$1");
    });

    // Warehouses Routes (Admin)
    $routes->group("warehouses", function($routes) {
        $routes->get("/", "Warehouses::index");
        $routes->get("create", "Warehouses::create");
        $routes->get("new", "Warehouses::create"); // Alias for 'create'
        $routes->get("add", "Warehouses::new");
        $routes->post("/", "Warehouses::store");
        $routes->get("(:num)", "Warehouses::view/$1");
        $routes->get("(:num)/edit", "Warehouses::edit/$1");
        $routes->post("(:num)", "Warehouses::update/$1");
        $routes->delete("(:num)", "Warehouses::delete/$1");
        $routes->get("(:num)/stock", "Warehouses::stock/$1");
        $routes->get("report/(:num)", "Warehouses::report/$1");
        $routes->get("get/(:num)", "Warehouses::get/$1");
    });
    // Project Management Routes
    $routes->group("projects", function($routes) {
        $routes->get("/", "Projects::index");
        $routes->get("create", "Projects::create");
        $routes->post("/", "Projects::store");
        $routes->get("search", "Projects::search");
        $routes->get("(:num)", "Projects::show/$1");
        $routes->get("(:num)/view", "Projects::view/$1");
        $routes->get("view/(:num)", "Projects::view/$1");
        $routes->get("(:num)/edit", "Projects::edit/$1");
        $routes->post("update/(:num)", "Projects::update/$1");
        $routes->delete("(:num)", "Projects::delete/$1");
        $routes->get("(:num)/team", "Projects::team/$1");
        $routes->post("(:num)/team/add", "Projects::addTeamMember/$1");
        $routes->post("(:num)/team/(:num)", "Projects::updateTeamMember/$1/$2");
        $routes->post("(:num)/team/(:num)/toggle", "Projects::toggleTeamMemberStatus/$1/$2");
        $routes->delete("(:num)/team/(:num)", "Projects::removeTeamMember/$1/$2");
        $routes->get("(:num)/gantt", "Projects::gantt/$1");
        $routes->get("(:num)/gantt/pdf", "Projects::exportGanttPdf/$1");
        $routes->get("(:num)/dashboard", "Projects::dashboard/$1");
        $routes->post("(:num)/clone", "Projects::clone/$1");
    });

    // Project Categories Routes
    $routes->group("project-categories", function($routes) {
        $routes->get("/", "ProjectCategories::index");
        $routes->get("create", "ProjectCategories::create");
        $routes->post("/", "ProjectCategories::store");
        $routes->get("(:num)", "ProjectCategories::show/$1");
        $routes->get("(:num)/edit", "ProjectCategories::edit/$1");
        $routes->post("update/(:num)", "ProjectCategories::update/$1");
        $routes->delete("(:num)", "ProjectCategories::delete/$1");
        $routes->get("delete/(:num)", "ProjectCategories::delete/$1");
        $routes->post("toggle/(:num)", "ProjectCategories::toggle/$1");
    });

    // Tasks Routes
    $routes->group("tasks", function($routes) {
        $routes->get("/", "Tasks::index");
        $routes->get("create", "Tasks::create");
        $routes->post("/", "Tasks::store");
        $routes->get("calendar", "Tasks::calendar");
        $routes->get("my-tasks", "Tasks::myTasks");
        $routes->get("generate-code", "Tasks::generateCode");
        $routes->get("by-project/(:num)", "Tasks::byProject/$1");
        $routes->get("project/(:num)", "Tasks::getProjectTasks/$1");
        $routes->get("(:num)", "Tasks::show/$1");
        $routes->get("(:num)/view", "Tasks::view/$1");
        $routes->get("(:num)/edit", "Tasks::edit/$1");
        $routes->post("(:num)", "Tasks::update/$1");
        $routes->delete("(:num)", "Tasks::delete/$1");
        $routes->delete("(:num)/delete", "Tasks::delete/$1");
        $routes->post("(:num)/delete", "Tasks::delete/$1");
        $routes->post("(:num)/status", "Tasks::updateStatus/$1");
        $routes->post("(:num)/comment", "Tasks::addComment/$1");
        $routes->post("add-comment/(:num)", "Tasks::addComment/$1");
        $routes->delete("comment/(:num)", "Tasks::deleteComment/$1");
        $routes->post("delete-comment/(:num)", "Tasks::deleteComment/$1");
        $routes->post("(:num)/attachment", "Tasks::uploadAttachment/$1");
        $routes->delete("attachment/(:num)", "Tasks::deleteAttachment/$1");
        $routes->post("delete-attachment/(:num)", "Tasks::deleteAttachment/$1");
        $routes->get("attachment/(:num)/download", "Tasks::download/$1");
        $routes->post("(:num)/log-time", "Tasks::logTime/$1");

        // API routes for calendar
        $routes->get("api/calendar-events", "Tasks::apiCalendarEvents");
        $routes->get("api/details/(:num)", "Tasks::apiTaskDetails/$1");
    });

    // Milestones Routes
    $routes->group("milestones", function($routes) {
        $routes->get("/", "Milestones::index");
        $routes->get("create", "Milestones::create");
        $routes->post("/", "Milestones::store");
        $routes->get("upcoming", "Milestones::upcoming");
        $routes->get("calendar", "Milestones::calendar");
        $routes->get("report", "Milestones::report");
        $routes->get("project/(:num)", "Milestones::getProjectMilestones/$1");
        $routes->get("(:num)/view", "Milestones::show/$1");
        $routes->get("(:num)/edit", "Milestones::edit/$1");
        $routes->get("(:num)", "Milestones::show/$1");
        $routes->post("(:num)", "Milestones::update/$1");
        $routes->delete("(:num)", "Milestones::delete/$1");
        $routes->post("(:num)/complete", "Milestones::complete/$1");
        $routes->get("api/calendar-events", "Milestones::apiCalendarEvents");
    });

    // Clients Routes
    $routes->group("clients", function($routes) {
        $routes->get("/", "Clients::index");
        $routes->get("create", "Clients::create");
        $routes->post("/", "Clients::store");
        $routes->get("(:num)", "Clients::show/$1");
        $routes->get("(:num)/edit", "Clients::edit/$1");
        $routes->post("(:num)", "Clients::update/$1");
        $routes->delete("(:num)", "Clients::delete/$1");
        $routes->post("(:num)/toggle", "Clients::toggle/$1");
    });

    // Materials/Inventory Routes
    $routes->group("materials", function($routes) {
        $routes->get("/", "Materials::index");
        $routes->get("new", "Materials::new");
        $routes->post("/", "Materials::create");
        $routes->get("edit/(:num)", "Materials::edit/$1");
        $routes->post("(:num)", "Materials::update/$1");
        $routes->delete("(:num)", "Materials::delete/$1");
        $routes->get("stock-movement/(:num)", "Materials::stockMovement/$1");
        $routes->post("stock-movement/(:num)", "Materials::recordStockMovement/$1");
        $routes->get("barcode-scanner", "Materials::barcodeScanner");
        $routes->post("barcode-lookup", "Materials::getMaterialByBarcode");
        $routes->post("stock-movement-ajax", "Materials::recordStockMovementAjax");
        $routes->get("low-stock-notifications", "Materials::lowStockNotifications");
        $routes->get("check-low-stock", "Materials::checkLowStockApi");
        $routes->post("notification-settings", "Materials::saveNotificationSettings");
        $routes->post("test-notification", "Materials::sendTestNotification");
        $routes->post("optimize-stock", "Materials::optimizeStockLevels");
        $routes->get("report", "Materials::generateReport");
        $routes->post("report", "Materials::generateReport");
        $routes->get("generate-report", "Materials::generateReport");
        $routes->post("generate-report", "Materials::generateReport");
        $routes->get("create-purchase-order", "Materials::createPurchaseOrder");
        $routes->post("create-purchase-order", "Materials::savePurchaseOrder");
        $routes->get("get-json", "Materials::getJson");
    });

    // Material Categories Routes
    $routes->group("material-categories", function($routes) {
        $routes->get("/", "MaterialCategories::index");
        $routes->get("create", "MaterialCategories::create");
        $routes->post("/", "MaterialCategories::store");
        $routes->get("(:num)", "MaterialCategories::show/$1");
        $routes->get("(:num)/edit", "MaterialCategories::edit/$1");
        $routes->post("(:num)", "MaterialCategories::update/$1");
        $routes->delete("(:num)", "MaterialCategories::delete/$1");
    });

    // Warehouses Routes
    $routes->group("warehouses", function($routes) {
        $routes->get("/", "Warehouses::index");
        $routes->get("create", "Warehouses::create");
        $routes->post("/", "Warehouses::store");
        $routes->get("(:num)", "Warehouses::view/$1");
        $routes->get("(:num)/edit", "Warehouses::edit/$1");
        $routes->post("(:num)", "Warehouses::update/$1");
        $routes->delete("(:num)", "Warehouses::delete/$1");
        $routes->get("(:num)/stock", "Warehouses::stock/$1");
        $routes->get("get-json", "Warehouses::getJson");
    });

    // Suppliers Routes
    $routes->group("suppliers", function($routes) {
        $routes->get("/", "Suppliers::index");
        $routes->get("create", "Suppliers::new");
        $routes->get("new", "Suppliers::new"); // Alias for 'create'
        $routes->post("create", "Suppliers::create");
        $routes->get("(:num)", "Suppliers::view/$1");
        $routes->get("edit/(:num)", "Suppliers::edit/$1");
        $routes->post("(:num)", "Suppliers::update/$1");
        $routes->delete("(:num)", "Suppliers::delete/$1");
        $routes->post("rate/(:num)", "Suppliers::rate/$1");
        $routes->get("get-materials/(:num)", "Suppliers::getMaterials/$1");
        $routes->post("add_material/(:num)", "Suppliers::addMaterial/$1");
        $routes->post("record_delivery/(:num)", "Suppliers::recordDelivery/$1");
        $routes->get("delivery/(:num)", "Suppliers::delivery/$1");
        $routes->get("remove_material/(:num)/(:num)", "Suppliers::removeMaterial/$1/$2");
    });

    // Material Requests Routes (Direct under admin for easier access)
    $routes->group("material-requests", function($routes) {
        $routes->get("/", "MaterialRequestController::index");
        $routes->get("create", "MaterialRequestController::create");
        $routes->post("/", "MaterialRequestController::store");
        $routes->get("(:num)", "MaterialRequestController::view/$1");
        $routes->get("(:num)/edit", "MaterialRequestController::edit/$1");
        $routes->post("(:num)", "MaterialRequestController::update/$1");
        $routes->delete("(:num)", "MaterialRequestController::delete/$1");
    });

    // Purchase Orders Routes (Direct under admin for easier access)
    $routes->group("purchase-orders", function($routes) {
        $routes->get("/", "PurchaseOrderController::index");
        $routes->get("create", "PurchaseOrderController::create");
        $routes->post("/", "PurchaseOrderController::store");
        $routes->get("(:num)", "PurchaseOrderController::view/$1");
        $routes->get("(:num)/edit", "PurchaseOrderController::edit/$1");
        $routes->post("(:num)", "PurchaseOrderController::update/$1");
        $routes->delete("(:num)", "PurchaseOrderController::delete/$1");
    });

    // Goods Receipt Routes (Direct under admin for easier access)
    $routes->group("goods-receipt", function($routes) {
        $routes->get("/", "GoodsReceiptController::index");
        $routes->get("create", "GoodsReceiptController::create");
        $routes->post("/", "GoodsReceiptController::store");
        $routes->get("(:num)", "GoodsReceiptController::view/$1");
        $routes->get("(:num)/edit", "GoodsReceiptController::edit/$1");
        $routes->post("(:num)", "GoodsReceiptController::update/$1");
        $routes->post("(:num)/accept", "GoodsReceiptController::accept/$1");
        $routes->post("(:num)/reject", "GoodsReceiptController::reject/$1");
        $routes->get("purchase-order-items/(:num)", "GoodsReceiptController::getPurchaseOrderItems/$1");
    });

    // Quality Inspections Routes (Direct under admin for easier access)
    $routes->group("quality-inspections", function($routes) {
        $routes->get("/", "QualityInspectionController::index");
        $routes->get("create", "QualityInspectionController::create");
        $routes->post("/", "QualityInspectionController::store");
        $routes->get("my-inspections", "QualityInspectionController::myInspections");
        $routes->get("pending-items", "QualityInspectionController::getPendingItems");
        $routes->get("(:num)", "QualityInspectionController::view/$1");
        $routes->get("(:num)/edit", "QualityInspectionController::edit/$1");
        $routes->post("(:num)", "QualityInspectionController::update/$1");
        $routes->delete("(:num)", "QualityInspectionController::delete/$1");
        $routes->get("(:num)/inspect", "QualityInspectionController::inspect/$1");
        $routes->post("(:num)/complete", "QualityInspectionController::complete/$1");
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

        // Goods Receipt Routes
        $routes->group("goods-receipt", function($routes) {
            $routes->get("/", "GoodsReceiptController::index");
            $routes->get("create", "GoodsReceiptController::create");
            $routes->post("/", "GoodsReceiptController::store");
            $routes->get("(:num)", "GoodsReceiptController::view/$1");
            $routes->get("(:num)/edit", "GoodsReceiptController::edit/$1");
            $routes->post("(:num)", "GoodsReceiptController::update/$1");
            $routes->post("(:num)/accept", "GoodsReceiptController::accept/$1");
            $routes->post("(:num)/reject", "GoodsReceiptController::reject/$1");
            $routes->get("purchase-order-items/(:num)", "GoodsReceiptController::getPurchaseOrderItems/$1");
        });

        // Quality Inspections Routes
        $routes->group("quality-inspections", function($routes) {
            $routes->get("/", "QualityInspectionController::index");
            $routes->get("create", "QualityInspectionController::create");
            $routes->post("/", "QualityInspectionController::store");
            $routes->get("my-inspections", "QualityInspectionController::myInspections");
            $routes->get("pending-items", "QualityInspectionController::getPendingItems");
            $routes->get("(:num)", "QualityInspectionController::view/$1");
            $routes->get("(:num)/edit", "QualityInspectionController::edit/$1");
            $routes->post("(:num)", "QualityInspectionController::update/$1");
            $routes->delete("(:num)", "QualityInspectionController::delete/$1");
            $routes->get("(:num)/inspect", "QualityInspectionController::inspect/$1");
            $routes->post("(:num)/complete", "QualityInspectionController::complete/$1");
        });

        // Reports
        $routes->get("reports", "ProcurementReportsController::index");
        $routes->post("reports/generate", "ProcurementReportsController::generate");
    });

    // Accounting Module Routes
    $routes->group("accounting", ["namespace" => "App\Controllers\Accounting"], function($routes) {
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

        // Journal Entries
        $routes->get("journal-entries", "JournalEntriesController::index");
        $routes->get("journal-entries/create", "JournalEntriesController::create");
        $routes->post("journal-entries", "JournalEntriesController::store");
        $routes->get("journal-entries/(:num)", "JournalEntriesController::show/$1");
        $routes->get("journal-entries/(:num)/edit", "JournalEntriesController::edit/$1");
        $routes->post("journal-entries/(:num)", "JournalEntriesController::update/$1");
        $routes->delete("journal-entries/(:num)", "JournalEntriesController::delete/$1");
        $routes->post("journal-entries/(:num)/post", "JournalEntriesController::post/$1");
        $routes->post("journal-entries/(:num)/reverse", "JournalEntriesController::reverse/$1");
        $routes->get("journal-entries/account/(:num)", "JournalEntriesController::getAccount/$1");

        // General Ledger
        $routes->get("general-ledger", "GeneralLedgerController::index");
        $routes->get("general-ledger/account/(:num)", "GeneralLedgerController::account/$1");
        $routes->get("general-ledger/trial-balance", "GeneralLedgerController::trialBalance");

        // Cost Codes
        $routes->get("cost-codes", "CostCodesController::index");
        $routes->get("cost-codes/create", "CostCodesController::create");
        $routes->post("cost-codes", "CostCodesController::store");
        $routes->get("cost-codes/(:num)", "CostCodesController::show/$1");
        $routes->get("cost-codes/(:num)/edit", "CostCodesController::edit/$1");
        $routes->post("cost-codes/(:num)", "CostCodesController::update/$1");
        $routes->delete("cost-codes/(:num)", "CostCodesController::delete/$1");
        $routes->post("cost-codes/(:num)/toggle", "CostCodesController::toggle/$1");

        // Job Cost Tracking
        $routes->get("job-cost-tracking", "JobCostTrackingController::index");
        $routes->get("job-cost-tracking/create", "JobCostTrackingController::create");
        $routes->post("job-cost-tracking", "JobCostTrackingController::store");
        $routes->get("job-cost-tracking/(:num)", "JobCostTrackingController::show/$1");
        $routes->get("job-cost-tracking/(:num)/edit", "JobCostTrackingController::edit/$1");
        $routes->post("job-cost-tracking/(:num)", "JobCostTrackingController::update/$1");
        $routes->delete("job-cost-tracking/(:num)", "JobCostTrackingController::delete/$1");
        $routes->get("job-cost-tracking/project/(:num)", "JobCostTrackingController::projectSummary/$1");

        // Job Budgets
        $routes->get("job-budgets", "JobBudgetsController::index");
        $routes->get("job-budgets/create", "JobBudgetsController::create");
        $routes->post("job-budgets", "JobBudgetsController::store");
        $routes->get("job-budgets/(:num)", "JobBudgetsController::show/$1");
        $routes->get("job-budgets/(:num)/edit", "JobBudgetsController::edit/$1");
        $routes->post("job-budgets/(:num)", "JobBudgetsController::update/$1");
        $routes->delete("job-budgets/(:num)", "JobBudgetsController::delete/$1");
        $routes->post("job-budgets/(:num)/update-actuals", "JobBudgetsController::updateActuals/$1");
        $routes->get("job-budgets/project/(:num)/comparison", "JobBudgetsController::projectBudgetComparison/$1");

        // Budget Categories
        $routes->get("budget-categories", "BudgetCategoriesController::index");
        $routes->get("budget-categories/create", "BudgetCategoriesController::create");
        $routes->post("budget-categories", "BudgetCategoriesController::store");
        $routes->get("budget-categories/(:num)/edit", "BudgetCategoriesController::edit/$1");
        $routes->post("budget-categories/(:num)", "BudgetCategoriesController::update/$1");
        $routes->delete("budget-categories/(:num)", "BudgetCategoriesController::delete/$1");
    });

    // Database setup routes (temporary)
    $routes->get("setup/journal-entries", "Admin\DatabaseSetupController::setupJournalEntries", ["namespace" => "App\Controllers"]);
    $routes->get("setup/cost-codes", "Admin\DatabaseSetupController::setupCostCodes", ["namespace" => "App\Controllers"]);
    $routes->get("setup/budget-tables", "Admin\SetupBudgetTablesController::setupBudgetTables", ["namespace" => "App\Controllers"]);

    // Test routes
    $routes->get("test/delete", "TestController::testDelete");
    $routes->get("test/categories", "TestController::listCategories");

});

// Default redirect to dashboard if logged in, login if not
$routes->get("/", function() {
    return session("user_id") ? redirect()->to("/admin/dashboard") : redirect()->to("/auth/login");
});
