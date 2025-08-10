<?php
// Debug Purchase Order URLs issue
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'contsruction';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== DEBUGGING PURCHASE ORDER URL ACCESS ===\n\n";
    
    // Check if PO ID 7 exists
    $stmt = $pdo->prepare("SELECT * FROM purchase_orders WHERE id = ?");
    $stmt->execute([7]);
    $po = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$po) {
        echo "❌ Purchase Order ID 7 not found!\n";
        exit;
    }
    
    echo "✅ Purchase Order ID 7 found: {$po['po_number']} - Status: {$po['status']}\n\n";
    
    // Test URL patterns that should work:
    $baseUrl = 'http://localhost/construction/public';
    $testUrls = [
        "{$baseUrl}/admin/purchase-orders/7" => "View PO 7",
        "{$baseUrl}/admin/purchase-orders/7/edit" => "Edit PO 7",
        "{$baseUrl}/admin/purchase-orders/" => "PO Index",
        "{$baseUrl}/admin/purchase-orders" => "PO Index (no slash)"
    ];
    
    echo "URLs that should work:\n";
    foreach ($testUrls as $url => $description) {
        echo "- $url ($description)\n";
    }
    
    echo "\n=== CODEIGNITER ROUTE ANALYSIS ===\n";
    echo "Based on Routes.php configuration:\n";
    echo "- Route: admin/purchase-orders/(:num) → PurchaseOrderController::view/\$1\n";
    echo "- Route: admin/purchase-orders/(:num)/edit → PurchaseOrderController::edit/\$1\n";
    echo "- So /admin/purchase-orders/7 should call PurchaseOrderController::view(7)\n";
    echo "- And /admin/purchase-orders/7/edit should call PurchaseOrderController::edit(7)\n";
    
    echo "\n=== CHECK MODEL METHOD ===\n";
    // Simulate what the view method would call
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
            material_requests.request_number
        FROM purchase_orders
        LEFT JOIN suppliers ON suppliers.id = purchase_orders.supplier_id
        LEFT JOIN users as creator ON creator.id = purchase_orders.created_by
        LEFT JOIN users as approver ON approver.id = purchase_orders.approved_by
        LEFT JOIN projects ON projects.id = purchase_orders.project_id
        LEFT JOIN material_requests ON material_requests.id = purchase_orders.material_request_id
        WHERE purchase_orders.id = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([7]);
    $fullPo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($fullPo) {
        echo "✅ Model query successful - Data would be available to view\n";
        echo "PO: {$fullPo['po_number']}\n";
        echo "Supplier: " . ($fullPo['supplier_name'] ?? 'NULL') . "\n";
        echo "Created by: " . ($fullPo['creator_first_name'] ?? 'NULL') . " " . ($fullPo['creator_last_name'] ?? 'NULL') . "\n";
    } else {
        echo "❌ Model query failed - This would cause view method to fail\n";
    }
    
    // Check if items exist
    $stmt = $pdo->prepare("SELECT COUNT(*) as item_count FROM purchase_order_items WHERE purchase_order_id = ?");
    $stmt->execute([7]);
    $itemCount = $stmt->fetch(PDO::FETCH_ASSOC)['item_count'];
    echo "Items count: $itemCount\n";
    
    echo "\n=== POSSIBLE ISSUES ===\n";
    echo "1. Check if authentication filter is working properly\n";
    echo "2. Check if there are any PHP errors in CodeIgniter error logs\n";
    echo "3. Verify that PurchaseOrderController class exists and methods are public\n";
    echo "4. Check if there are any .htaccess issues\n";
    echo "5. Verify database connection in CodeIgniter config\n";
    
    echo "\n=== NEXT STEPS ===\n";
    echo "1. Access the URLs in browser and check for specific error messages\n";
    echo "2. Check CodeIgniter logs at: writable/logs/\n";
    echo "3. Enable debug mode in .env file: CI_ENVIRONMENT = development\n";
    
} catch (PDOException $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
}
?>