<?php

use CodeIgniter\Router\RouteCollection;


/**
 * @var RouteCollection $routes
 */

$routes->group('auth', function($routes) {
    $routes->get('login', 'Auth::login');
    $routes->post('authenticate', 'Auth::authenticate');
    $routes->get('logout', 'Auth::logout');
    $routes->get('register', 'Auth::register');
    $routes->post('create-account', 'Auth::createAccount');
    $routes->get('forgot-password', 'Auth::forgotPassword');
    $routes->post('send-reset-link', 'Auth::sendResetLink');
});

// Admin Routes (Protected)
$routes->group('admin', ['filter' => 'auth'], function($routes) {
    $routes->get('dashboard', 'Dashboard::index');

    // User Management Routes
    $routes->group('users', function($routes) {
        $routes->get('/', 'Users::index');
        $routes->get('create', 'Users::create');
        $routes->post('store', 'Users::store');
       // $routes->get('store', 'Users::storeGet'); // Temporary debug route
        $routes->get('(:num)/edit', 'Users::edit/$1');
        $routes->post('(:num)/update', 'Users::update/$1');
        $routes->delete('(:num)/delete', 'Users::delete/$1');
        $routes->get('(:num)/details', 'Users::details/$1');
        $routes->post('(:num)/toggle-status', 'Users::toggleStatus/$1');
        $routes->get('profile', 'Users::profile');
        $routes->post('update-profile', 'Users::updateProfile');
        $routes->post('change-password', 'Users::changePassword');
        $routes->get('supervisors', 'Users::getSupervisors');
        $routes->get('positions-by-department', 'Users::getPositionsByDepartment');
    });
    
    // Inventory Management Routes
    $routes->group('materials', function($routes) {
        $routes->get('/', 'Materials::index');
        $routes->get('new', 'Materials::new');
        $routes->post('create', 'Materials::create');
        $routes->post('store', 'Materials::create'); // Alternative route for consistency
        $routes->get('edit/(:num)', 'Materials::edit/$1');
        $routes->post('update/(:num)', 'Materials::update/$1');
        $routes->get('delete/(:num)', 'Materials::delete/$1');
        $routes->get('stock-movement/(:num)', 'Materials::stockMovement/$1');
        $routes->get('low-stock-notifications', 'Materials::lowStockNotifications');
        $routes->post('save-notification-settings', 'Materials::saveNotificationSettings');
        $routes->post('send-test-notification', 'Materials::sendTestNotification');
        $routes->get('check-low-stock-api', 'Materials::checkLowStockApi');
        $routes->get('send-auto-notifications', 'Materials::sendAutoLowStockNotifications');
        $routes->get('create-purchase-order', 'Materials::createPurchaseOrder');
        $routes->post('save-purchase-order', 'Materials::savePurchaseOrder');
        $routes->post('record-stock-movement/(:num)', 'Materials::recordStockMovement/$1');
        $routes->post('record-stock-movement', 'Materials::recordStockMovementAjax');
        $routes->get('barcode-scanner', 'Materials::barcodeScanner');
        $routes->post('get-material-by-barcode', 'Materials::getMaterialByBarcode');
        $routes->get('report', 'Materials::generateReport');
        $routes->post('report', 'Materials::generateReport');
    });
    
    $routes->group('material-categories', function($routes) {
        $routes->get('/', 'MaterialCategories::index');
        $routes->get('new', 'MaterialCategories::new');
        $routes->post('create', 'MaterialCategories::create');
        $routes->get('edit/(:num)', 'MaterialCategories::edit/$1');
        $routes->post('update/(:num)', 'MaterialCategories::update/$1');
        $routes->get('delete/(:num)', 'MaterialCategories::delete/$1');
    });
    
    $routes->group('warehouses', function($routes) {
        $routes->get('/', 'Warehouses::index');
        $routes->get('new', 'Warehouses::new');
        $routes->post('create', 'Warehouses::create');
        $routes->post('store', 'Warehouses::create'); // Alternative route for consistency
        $routes->get('view/(:num)', 'Warehouses::view/$1');
        $routes->get('get/(:num)', 'Warehouses::get/$1'); // AJAX endpoint for edit modal
        $routes->get('edit/(:num)', 'Warehouses::edit/$1');
        $routes->post('update/(:num)', 'Warehouses::update/$1');
        $routes->put('update/(:num)', 'Warehouses::update/$1'); // PUT method for edit modal
        $routes->get('delete/(:num)', 'Warehouses::delete/$1');
        $routes->get('stock/(:num)', 'Warehouses::stock/$1');
        $routes->post('update-stock/(:num)/(:num)', 'Warehouses::updateStock/$1/$2');
        $routes->post('add-stock-item/(:num)', 'Warehouses::addStockItem/$1');
        $routes->get('remove-stock-item/(:num)/(:num)', 'Warehouses::removeStockItem/$1/$2');
        $routes->get('stock-movement/(:num)', 'Warehouses::stockMovement/$1');
    });
    
    $routes->group('suppliers', function($routes) {
        $routes->get('/', 'Suppliers::index');
        $routes->get('new', 'Suppliers::new');
        $routes->post('create', 'Suppliers::create');
        $routes->post('store', 'Suppliers::create'); // Alternative route for consistency
        $routes->get('view/(:num)', 'Suppliers::view/$1');
        $routes->get('edit/(:num)', 'Suppliers::edit/$1');
        $routes->post('update/(:num)', 'Suppliers::update/$1');
        $routes->get('delete/(:num)', 'Suppliers::delete/$1');
        $routes->post('rate/(:num)', 'Suppliers::rate/$1');
        
        // Supplier Materials Management
        $routes->get('get-materials/(:num)', 'Suppliers::getMaterials/$1');
        $routes->post('add_material/(:num)', 'Suppliers::addMaterial/$1');
        $routes->get('remove_material/(:num)/(:num)', 'Suppliers::removeMaterial/$1/$2');
        
        // Delivery Management
        $routes->post('record_delivery/(:num)', 'Suppliers::recordDelivery/$1');
        $routes->get('delivery/(:num)', 'Suppliers::delivery/$1');
        $routes->get('update-delivery-status/(:num)/(:any)', 'Suppliers::updateDeliveryStatus/$1/$2');
    });

    // Department Management Routes
    $routes->group('departments', function($routes) {
        $routes->get('/', 'Departments::index');
        $routes->get('create', 'Departments::create');
        $routes->post('store', 'Departments::store');
        $routes->get('(:num)/edit', 'Departments::edit/$1');
        $routes->post('(:num)/update', 'Departments::update/$1');
        $routes->delete('(:num)/delete', 'Departments::delete/$1');
        $routes->patch('(:num)/toggle', 'Departments::toggle/$1');
    });

    // Role Management Routes
    $routes->group('roles', function($routes) {
        $routes->get('/', 'Roles::index');
        $routes->get('create', 'Roles::create');
        $routes->post('store', 'Roles::store');
        $routes->get('(:num)/edit', 'Roles::edit/$1');
        $routes->post('(:num)/update', 'Roles::update/$1');
        $routes->delete('(:num)/delete', 'Roles::delete/$1');
        $routes->get('(:num)/duplicate', 'Roles::duplicate/$1');
    });

    // Position Management Routes
    $routes->group('positions', function($routes) {
        $routes->get('/', 'Positions::index');
        $routes->get('create', 'Positions::create');
        $routes->post('store', 'Positions::store');
        $routes->get('(:num)/edit', 'Positions::edit/$1');
        $routes->post('(:num)/update', 'Positions::update/$1');
        $routes->delete('(:num)/delete', 'Positions::delete/$1');
        $routes->patch('(:num)/toggle', 'Positions::toggle/$1');
        $routes->get('by-department', 'Positions::byDepartment');
    });

    // Project Management Routes
    $routes->group('projects', function($routes) {
        $routes->get('/', 'Projects::index');
        $routes->get('create', 'Projects::create');
        $routes->post('store', 'Projects::store');
        $routes->get('edit/(:num)', 'Projects::edit/$1');
        $routes->post('update/(:num)', 'Projects::update/$1');
        $routes->get('view/(:num)', 'Projects::view/$1');
        $routes->get('dashboard/(:num)', 'Projects::dashboard/$1');
        $routes->get('gantt/(:num)', 'Projects::gantt/$1');
        $routes->delete('delete/(:num)', 'Projects::delete/$1');
        
        // Team management
        $routes->get('team/(:num)', 'Projects::team/$1');
        $routes->post('team/add/(:num)', 'Projects::addTeamMember/$1');
        $routes->post('team/update/(:num)/(:num)', 'Projects::updateTeamMember/$1/$2');
        $routes->post('team/toggle/(:num)/(:num)', 'Projects::toggleTeamMemberStatus/$1/$2');
        $routes->post('team/remove/(:num)/(:num)', 'Projects::removeTeamMember/$1/$2');
        
        // Other project management
        $routes->post('clone/(:num)', 'Projects::clone/$1');
        $routes->get('search', 'Projects::search');
        $routes->post('update-progress/(:num)', 'Projects::updateProgress/$1');
        $routes->get('report', 'Projects::report');
        $routes->get('archive/(:num)', 'Projects::archive/$1');
    });

    // Task Management Routes
    $routes->group('tasks', function($routes) {
        $routes->get('/', 'Tasks::index');
        $routes->get('create', 'Tasks::create');
        $routes->post('store', 'Tasks::store');
        
        // Specific routes first (to avoid conflicts with (:num) pattern)
        $routes->get('generate-code', 'Tasks::generateCode');
        $routes->get('by-project/(:num)', 'Tasks::byProject/$1');
        $routes->get('my-tasks', 'Tasks::myTasks');
        $routes->get('calendar', 'Tasks::calendar');
        $routes->post('bulk-update', 'Tasks::bulkUpdate');
        $routes->get('api/project-tasks/(:num)', 'Tasks::getProjectTasks/$1');
        
        // Task-specific routes with ID patterns
        $routes->get('(:num)/edit', 'Tasks::edit/$1');
        $routes->get('edit/(:num)', 'Tasks::edit/$1'); // Alternative edit route
        $routes->post('(:num)/update', 'Tasks::update/$1');
        $routes->post('update/(:num)', 'Tasks::update/$1');
        $routes->get('(:num)/view', 'Tasks::view/$1');
        $routes->get('view/(:num)', 'Tasks::view/$1');
        $routes->post('(:num)/status', 'Tasks::updateStatus/$1');
        $routes->post('update-status/(:num)', 'Tasks::updateStatus/$1');
        $routes->delete('(:num)/delete', 'Tasks::delete/$1');
        $routes->delete('delete/(:num)', 'Tasks::delete/$1');
        
        // Direct access by ID (must be last to avoid conflicts)
        $routes->get('(:num)', 'Tasks::view/$1');
        
        // Comments
        $routes->post('add-comment/(:num)', 'Tasks::addComment/$1');
        $routes->post('delete-comment/(:num)', 'Tasks::deleteComment/$1');
        
        // Attachments
        $routes->post('upload-attachment/(:num)', 'Tasks::uploadAttachment/$1');
        $routes->post('delete-attachment/(:num)', 'Tasks::deleteAttachment/$1');
        $routes->get('download/(:num)', 'Tasks::download/$1');
        
        // Time logging
        $routes->post('log-time/(:num)', 'Tasks::logTime/$1');
    });

    // Milestone Management Routes
    $routes->group('milestones', function($routes) {
        $routes->get('/', 'Milestones::index');
        $routes->get('create', 'Milestones::create');
        $routes->post('store', 'Milestones::store');
        $routes->get('(:num)', 'Milestones::show/$1');
        $routes->get('(:num)/edit', 'Milestones::edit/$1');
        $routes->post('(:num)/update', 'Milestones::update/$1');
        $routes->delete('(:num)/delete', 'Milestones::delete/$1');
        $routes->post('(:num)/complete', 'Milestones::complete/$1');
        $routes->get('upcoming', 'Milestones::upcoming');
        $routes->get('calendar', 'Milestones::calendar');
        $routes->get('report', 'Milestones::report');
    });

    // Project Category Management Routes
    $routes->group('project-categories', function($routes) {
        $routes->get('/', 'ProjectCategories::index');
        $routes->get('create', 'ProjectCategories::create');
        $routes->post('store', 'ProjectCategories::store');
        $routes->get('view/(:num)', 'ProjectCategories::show/$1');
        $routes->get('edit/(:num)', 'ProjectCategories::edit/$1');
        $routes->post('update/(:num)', 'ProjectCategories::update/$1');
        $routes->delete('delete/(:num)', 'ProjectCategories::delete/$1');
        $routes->post('toggle/(:num)', 'ProjectCategories::toggle/$1');
        $routes->get('by-company/(:num)', 'ProjectCategories::getByCompany/$1');
    });
    
    // Client Management Routes
    $routes->group('clients', function($routes) {
        $routes->get('/', 'Clients::index');
        $routes->get('create', 'Clients::create');
        $routes->post('store', 'Clients::store');
        $routes->get('view/(:num)', 'Clients::show/$1');
        $routes->get('edit/(:num)', 'Clients::edit/$1');
        $routes->post('update/(:num)', 'Clients::update/$1');
        $routes->delete('delete/(:num)', 'Clients::delete/$1');
        $routes->post('toggle/(:num)', 'Clients::toggle/$1');
    });
});

// Default redirect to dashboard if logged in, login if not
$routes->get('/', function() {
    return session('user_id') ? redirect()->to('/admin/dashboard') : redirect()->to('/auth/login');
});

