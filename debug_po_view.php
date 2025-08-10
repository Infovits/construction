<?php
// Debug Purchase Order view issue
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'contsruction';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== DEBUGGING PURCHASE ORDER VIEW ===\n\n";
    
    // Check if PO ID 7 exists
    $stmt = $pdo->prepare("SELECT * FROM purchase_orders WHERE id = ?");
    $stmt->execute([7]);
    $po = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($po) {
        echo "✅ Purchase Order ID 7 found:\n";
        echo "PO Number: {$po['po_number']}\n";
        echo "Supplier ID: {$po['supplier_id']}\n";
        echo "Status: {$po['status']}\n";
        echo "Total: MWK " . number_format($po['total_amount'], 2) . "\n\n";
    } else {
        echo "❌ Purchase Order ID 7 not found!\n";
        
        // Show what POs exist
        $stmt = $pdo->query("SELECT id, po_number, status FROM purchase_orders ORDER BY id");
        $pos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Available Purchase Orders:\n";
        foreach ($pos as $p) {
            echo "- ID: {$p['id']}, Number: {$p['po_number']}, Status: {$p['status']}\n";
        }
        exit;
    }
    
    // Check if PO has items
    $stmt = $pdo->prepare("SELECT * FROM purchase_order_items WHERE purchase_order_id = ?");
    $stmt->execute([7]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Purchase Order Items (" . count($items) . " found):\n";
    foreach ($items as $item) {
        echo "- Material ID: {$item['material_id']}, Quantity: {$item['quantity_ordered']}, Cost: MWK {$item['unit_cost']}\n";
    }
    
    // Test the full query that the controller would use
    echo "\n=== TESTING FULL JOIN QUERY ===\n";
    
    $sql = "SELECT purchase_orders.*, 
            suppliers.name as supplier_name,
            suppliers.contact_person,
            suppliers.phone,
            suppliers.email,
            suppliers.address as supplier_address,
            creator.first_name as creator_first_name,
            creator.last_name as creator_last_name,
            projects.name as project_name,
            material_requests.request_number as material_request_number
        FROM purchase_orders
        LEFT JOIN suppliers ON suppliers.id = purchase_orders.supplier_id
        LEFT JOIN users as creator ON creator.id = purchase_orders.created_by
        LEFT JOIN projects ON projects.id = purchase_orders.project_id
        LEFT JOIN material_requests ON material_requests.id = purchase_orders.material_request_id
        WHERE purchase_orders.id = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([7]);
    $fullPo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($fullPo) {
        echo "✅ Full query successful:\n";
        echo "PO: {$fullPo['po_number']}\n";
        echo "Supplier: " . ($fullPo['supplier_name'] ?? 'NULL') . "\n";
        echo "Creator: " . ($fullPo['creator_first_name'] ?? 'NULL') . " " . ($fullPo['creator_last_name'] ?? 'NULL') . "\n";
        echo "Project: " . ($fullPo['project_name'] ?? 'NULL') . "\n";
        echo "MR Number: " . ($fullPo['material_request_number'] ?? 'NULL') . "\n";
    } else {
        echo "❌ Full query failed!\n";
    }
    
    // Test items query with material info
    echo "\n=== TESTING ITEMS WITH MATERIAL INFO ===\n";
    
    $itemsSql = "SELECT poi.*, m.name as material_name, m.item_code, m.unit
        FROM purchase_order_items poi
        LEFT JOIN materials m ON m.id = poi.material_id
        WHERE poi.purchase_order_id = ?";
    
    $stmt = $pdo->prepare($itemsSql);
    $stmt->execute([7]);
    $itemsWithMaterial = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Items with material info (" . count($itemsWithMaterial) . " found):\n";
    foreach ($itemsWithMaterial as $item) {
        echo "- {$item['material_name']} ({$item['item_code']}): {$item['quantity_ordered']} @ MWK {$item['unit_cost']}\n";
    }
    
    echo "\n✅ Database queries working fine!\n";
    echo "The issue might be with CodeIgniter routing or controller logic.\n";
    
} catch (PDOException $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
}
?>