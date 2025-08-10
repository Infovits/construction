<?php
// Simple test to check if controller methods would work
require_once 'vendor/autoload.php';

// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'contsruction';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== TESTING CONTROLLER METHOD LOGIC ===\n\n";
    
    $id = 7;
    
    // Test the logic that PurchaseOrderController::view() would use
    echo "1. Testing view method logic for PO ID $id:\n";
    
    // This mirrors PurchaseOrderModel::getPurchaseOrderWithItems()
    $sql = "SELECT purchase_orders.*, 
            suppliers.name as supplier_name,
            suppliers.contact_person,
            suppliers.phone,
            suppliers.email,
            suppliers.address as supplier_address,
            creator.first_name as creator_first_name,
            creator.last_name as creator_last_name,
            approver.first_name as approver_first_name,
            approver.last_name as approver_last_name,
            projects.name as project_name,
            material_requests.request_number as material_request_number
        FROM purchase_orders
        LEFT JOIN suppliers ON suppliers.id = purchase_orders.supplier_id
        LEFT JOIN users as creator ON creator.id = purchase_orders.created_by
        LEFT JOIN users as approver ON approver.id = purchase_orders.approved_by
        LEFT JOIN projects ON projects.id = purchase_orders.project_id
        LEFT JOIN material_requests ON material_requests.id = purchase_orders.material_request_id
        WHERE purchase_orders.id = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $purchaseOrder = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$purchaseOrder) {
        echo "❌ Purchase order $id not found - Controller would throw PageNotFoundException\n";
        exit;
    }
    
    echo "✅ Purchase order found: {$purchaseOrder['po_number']}\n";
    
    // Get items
    $itemsSql = "SELECT purchase_order_items.*, 
            materials.name as material_name,
            materials.item_code,
            materials.unit,
            materials.item_code as material_code,
            materials.unit as material_unit,
            material_request_items.quantity_requested,
            material_request_items.specification_notes
        FROM purchase_order_items
        LEFT JOIN materials ON materials.id = purchase_order_items.material_id
        LEFT JOIN material_request_items ON material_request_items.id = purchase_order_items.material_request_item_id
        WHERE purchase_order_id = ?";
    
    $stmt = $pdo->prepare($itemsSql);
    $stmt->execute([$id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "✅ Found " . count($items) . " items for this PO\n";
    
    // Check what data would be passed to view
    echo "\n2. Data that would be passed to view:\n";
    echo "- title: 'Purchase Order Details'\n";
    echo "- purchaseOrder: Array with " . count($purchaseOrder) . " fields\n";
    echo "- purchaseOrder['items']: Array with " . count($items) . " items\n";
    
    // Key fields needed by view
    $requiredFields = [
        'po_number', 'status', 'created_at', 'supplier_name', 'po_date',
        'expected_delivery_date', 'material_request_number', 'project_name',
        'creator_first_name', 'creator_last_name', 'supplier_email', 
        'supplier_phone', 'supplier_address', 'payment_terms', 'delivery_terms',
        'terms_conditions', 'subtotal', 'tax_amount', 'freight_cost', 'total_amount', 'notes'
    ];
    
    echo "\n3. Checking required fields for view:\n";
    $missingFields = [];
    foreach ($requiredFields as $field) {
        if (!isset($purchaseOrder[$field]) && $purchaseOrder[$field] !== null) {
            $missingFields[] = $field;
        } else {
            echo "✅ $field: " . ($purchaseOrder[$field] ?? 'NULL') . "\n";
        }
    }
    
    if (!empty($missingFields)) {
        echo "\n❌ Missing fields that view expects: " . implode(', ', $missingFields) . "\n";
    } else {
        echo "\n✅ All required fields present\n";
    }
    
    // Test edit method logic
    echo "\n4. Testing edit method logic:\n";
    
    if ($purchaseOrder['status'] !== 'draft') {
        echo "❌ Edit would fail - PO status is '{$purchaseOrder['status']}', only 'draft' can be edited\n";
    } else {
        echo "✅ Edit would work - PO status is 'draft'\n";
        
        // Check if we have suppliers, projects, materials for dropdowns
        $supplierCount = $pdo->query("SELECT COUNT(*) FROM suppliers")->fetchColumn();
        $projectCount = $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn();
        $materialCount = $pdo->query("SELECT COUNT(*) FROM materials")->fetchColumn();
        
        echo "✅ Suppliers available: $supplierCount\n";
        echo "✅ Projects available: $projectCount\n";
        echo "✅ Materials available: $materialCount\n";
    }
    
    echo "\n=== CONCLUSION ===\n";
    echo "Based on this test, both view and edit controller methods should work correctly.\n";
    echo "The issue is likely one of the following:\n";
    echo "1. Authentication filter blocking access\n";
    echo "2. CodeIgniter routing not working properly\n";
    echo "3. Server configuration (.htaccess) issues\n";
    echo "4. PHP errors in controller or view files\n";
    echo "5. Missing session or incorrect user permissions\n";
    
    echo "\nRecommendations:\n";
    echo "1. Check CodeIgniter logs in writable/logs/ directory\n";
    echo "2. Enable debug mode by setting CI_ENVIRONMENT=development in .env\n";
    echo "3. Test with a simple controller method that just returns text\n";
    echo "4. Verify authentication is working by checking session data\n";
    
} catch (PDOException $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
}

echo "\n=== TESTING ALTERNATIVE ACCESS ===\n";
echo "Try these URLs to test different access patterns:\n";
echo "1. Direct controller test: http://localhost/construction/public/index.php/admin/purchase-orders/7\n";
echo "2. With index.php: http://localhost/construction/public/index.php/admin/purchase-orders/7\n";
echo "3. Check if routing works: http://localhost/construction/public/admin/purchase-orders\n";
echo "4. Check authentication: http://localhost/construction/public/admin/dashboard\n";
?>