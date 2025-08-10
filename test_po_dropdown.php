<?php
// Test what Material Requests are being returned for the PO dropdown
require_once 'vendor/autoload.php';

// Bootstrap CodeIgniter environment variables
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/test';

// Load CodeIgniter
$app = \CodeIgniter\Config\Services::codeigniter();
$app->initialize();

// Set up session for testing
$session = \Config\Services::session();
$session->start();
$session->set('user_id', 1); // Admin user

echo "=== TESTING PURCHASE ORDER DROPDOWN DATA ===\n\n";

try {
    // Test the MaterialRequestModel directly
    $materialRequestModel = new \App\Models\MaterialRequestModel();
    $approvedRequests = $materialRequestModel->getApprovedRequests();
    
    echo "1. Direct MaterialRequestModel::getApprovedRequests() Results:\n";
    echo "   Found " . count($approvedRequests) . " approved requests:\n\n";
    
    if (empty($approvedRequests)) {
        echo "   ❌ No approved requests found!\n";
    } else {
        foreach ($approvedRequests as $request) {
            echo "   ✅ ID: {$request['id']}\n";
            echo "      Request Number: {$request['request_number']}\n";
            echo "      Project: " . ($request['project_name'] ?? 'No Project') . "\n";
            echo "      Status: {$request['status']}\n";
            echo "      Total Cost: MWK " . number_format($request['total_estimated_cost'], 2) . "\n";
            echo "      Created: {$request['created_at']}\n\n";
        }
    }
    
    // Test what the Purchase Order create method would get
    echo "2. Testing PurchaseOrderController::create() data:\n";
    
    $purchaseOrderController = new \App\Controllers\PurchaseOrderController();
    
    // Use reflection to access the protected materialRequestModel
    $reflection = new ReflectionClass($purchaseOrderController);
    $materialRequestModelProperty = $reflection->getProperty('materialRequestModel');
    $materialRequestModelProperty->setAccessible(true);
    $modelFromController = $materialRequestModelProperty->getValue($purchaseOrderController);
    
    $controllerApprovedRequests = $modelFromController->getApprovedRequests();
    
    echo "   Controller getApprovedRequests() returned " . count($controllerApprovedRequests) . " requests:\n";
    
    if (empty($controllerApprovedRequests)) {
        echo "   ❌ No approved requests from controller!\n";
    } else {
        foreach ($controllerApprovedRequests as $request) {
            echo "   ✅ ID: {$request['id']} - {$request['request_number']}\n";
        }
    }
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
?>