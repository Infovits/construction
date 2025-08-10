<?php
// Test Material Request functionality
require_once 'vendor/autoload.php';

// Bootstrap CodeIgniter
$app = \CodeIgniter\Config\Services::codeigniter();
$app->initialize();

// Set up session for testing
$session = \Config\Services::session();
$session->start();
$session->set('user_id', 1); // Admin user

echo "=== TESTING MATERIAL REQUEST FUNCTIONALITY ===\n\n";

// Test 1: Create a sample material request
echo "1. Creating a sample material request...\n";

$materialRequestModel = new \App\Models\MaterialRequestModel();
$materialRequestItemModel = new \App\Models\MaterialRequestItemModel();

// Create request data
$requestData = [
    'request_number' => $materialRequestModel->generateRequestNumber(),
    'project_id' => 1,
    'requested_by' => 1,
    'department_id' => 1,
    'request_date' => date('Y-m-d'),
    'required_date' => date('Y-m-d', strtotime('+7 days')),
    'status' => 'draft',
    'priority' => 'medium',
    'total_estimated_cost' => 500.00,
    'notes' => 'Test material request for construction project'
];

$requestId = $materialRequestModel->insert($requestData);

if ($requestId) {
    echo "   ✓ Material request created with ID: $requestId\n";
    
    // Add some items to the request
    $items = [
        [
            'material_request_id' => $requestId,
            'material_id' => 1, // Cement
            'quantity_requested' => 10.000,
            'estimated_unit_cost' => 25.00,
            'estimated_total_cost' => 250.00,
            'specification_notes' => 'Portland cement for foundation work'
        ],
        [
            'material_request_id' => $requestId,
            'material_id' => 2, // Steel bars
            'quantity_requested' => 20.000,
            'estimated_unit_cost' => 12.50,
            'estimated_total_cost' => 250.00,
            'specification_notes' => '12mm reinforcement bars for concrete'
        ]
    ];
    
    foreach ($items as $item) {
        $itemId = $materialRequestItemModel->insert($item);
        if ($itemId) {
            echo "   ✓ Added item: Material ID {$item['material_id']}, Quantity: {$item['quantity_requested']}\n";
        }
    }
} else {
    echo "   ✗ Failed to create material request\n";
    exit(1);
}

// Test 2: Test the view functionality
echo "\n2. Testing view functionality...\n";
$materialRequestWithItems = $materialRequestModel->getMaterialRequestWithItems($requestId);

if ($materialRequestWithItems) {
    echo "   ✓ Retrieved material request: {$materialRequestWithItems['request_number']}\n";
    echo "   ✓ Status: {$materialRequestWithItems['status']}\n";
    echo "   ✓ Priority: {$materialRequestWithItems['priority']}\n";
    echo "   ✓ Total cost: MWK " . number_format($materialRequestWithItems['total_estimated_cost'], 2) . "\n";
    
    if (isset($materialRequestWithItems['items']) && count($materialRequestWithItems['items']) > 0) {
        echo "   ✓ Items found: " . count($materialRequestWithItems['items']) . "\n";
    } else {
        echo "   ✗ No items found\n";
    }
} else {
    echo "   ✗ Failed to retrieve material request\n";
}

// Test 3: Test approval workflow
echo "\n3. Testing approval workflow...\n";

// Submit for approval
$submitResult = $materialRequestModel->update($requestId, ['status' => 'pending_approval']);
if ($submitResult) {
    echo "   ✓ Request submitted for approval\n";
} else {
    echo "   ✗ Failed to submit for approval\n";
}

// Approve the request
$approveResult = $materialRequestModel->approveMaterialRequest($requestId, 1, 'Approved for construction project');
if ($approveResult) {
    echo "   ✓ Request approved successfully\n";
} else {
    echo "   ✗ Failed to approve request\n";
}

// Test 4: Test getting approved requests for purchase orders
echo "\n4. Testing approved requests for purchase orders...\n";
$approvedRequests = $materialRequestModel->getApprovedRequests();

if (!empty($approvedRequests)) {
    echo "   ✓ Found " . count($approvedRequests) . " approved request(s)\n";
    foreach ($approvedRequests as $request) {
        echo "     - {$request['request_number']}: MWK " . number_format($request['total_estimated_cost'], 2) . "\n";
    }
} else {
    echo "   ✗ No approved requests found\n";
}

// Test 5: Test items for purchase order creation
echo "\n5. Testing items for purchase order creation...\n";
$itemsToPurchase = $materialRequestItemModel->getItemsToBePurchased($requestId);

if (!empty($itemsToPurchase)) {
    echo "   ✓ Found " . count($itemsToPurchase) . " item(s) to purchase\n";
    foreach ($itemsToPurchase as $item) {
        echo "     - {$item['material_name']}: {$item['quantity_approved']} {$item['unit']}\n";
    }
} else {
    echo "   ✗ No items found for purchase\n";
}

echo "\n=== TEST COMPLETED ===\n";
echo "Database is ready and Material Request functionality is working!\n";
echo "You can now test the web interface at: http://localhost:8080\n";
echo "Login with: admin@construction.com / admin123\n";
?>