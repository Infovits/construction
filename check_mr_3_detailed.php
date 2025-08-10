<?php
// Check detailed status of Material Request ID 3
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'construction';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $materialRequestId = 3;
    
    echo "=== DETAILED CHECK OF MATERIAL REQUEST ID 3 ===\n\n";
    
    // Check the material request itself
    $stmt = $pdo->prepare("SELECT * FROM material_requests WHERE id = ?");
    $stmt->execute([$materialRequestId]);
    $mr = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($mr) {
        echo "📋 Material Request Details:\n";
        echo "ID: {$mr['id']}\n";
        echo "Request Number: {$mr['request_number']}\n";
        echo "Status: {$mr['status']}\n";
        echo "Total Cost: MWK " . number_format($mr['total_estimated_cost'], 2) . "\n";
        echo "Approved By: " . ($mr['approved_by'] ?? 'NULL') . "\n";
        echo "Approved Date: " . ($mr['approved_date'] ?? 'NULL') . "\n\n";
    } else {
        echo "❌ Material Request ID 3 not found!\n";
        exit;
    }
    
    // Check items with full details
    $stmt = $pdo->prepare("
        SELECT mri.*, m.name as material_name, m.item_code, m.unit, m.unit_cost as current_unit_cost
        FROM material_request_items mri
        LEFT JOIN materials m ON m.id = mri.material_id
        WHERE mri.material_request_id = ?
    ");
    $stmt->execute([$materialRequestId]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "📦 Material Request Items (" . count($items) . " total):\n";
    
    if (empty($items)) {
        echo "❌ No items found!\n";
        exit;
    }
    
    foreach ($items as $item) {
        echo "\n--- Item ID: {$item['id']} ---\n";
        echo "Material ID: {$item['material_id']}\n";
        echo "Material Name: " . ($item['material_name'] ?? 'NOT FOUND') . "\n";
        echo "Item Code: " . ($item['item_code'] ?? 'N/A') . "\n";
        echo "Unit: " . ($item['unit'] ?? 'N/A') . "\n";
        echo "Quantity Requested: {$item['quantity_requested']}\n";
        echo "Quantity Approved: " . ($item['quantity_approved'] === null ? 'NULL' : $item['quantity_approved']) . "\n";
        echo "Estimated Unit Cost: " . ($item['estimated_unit_cost'] ?? 'NULL') . "\n";
        echo "Current Unit Cost: " . ($item['current_unit_cost'] ?? 'NULL') . "\n";
        echo "Specification Notes: " . ($item['specification_notes'] ?? 'None') . "\n";
        
        // Check why this item wouldn't be returned by getItemsToBePurchased
        $issues = [];
        
        if ($item['quantity_approved'] === null || $item['quantity_approved'] <= 0) {
            $issues[] = "❌ quantity_approved is " . ($item['quantity_approved'] === null ? 'NULL' : $item['quantity_approved']) . " (needs > 0)";
        } else {
            $issues[] = "✅ quantity_approved = {$item['quantity_approved']} (good)";
        }
        
        if (!$item['material_name']) {
            $issues[] = "❌ Material ID {$item['material_id']} not found in materials table";
        } else {
            $issues[] = "✅ Material found: {$item['material_name']}";
        }
        
        foreach ($issues as $issue) {
            echo "$issue\n";
        }
    }
    
    // Test the exact getItemsToBePurchased query
    echo "\n🔍 Testing getItemsToBePurchased query:\n";
    
    $sql = "SELECT material_request_items.*, 
            materials.name as material_name,
            materials.item_code,
            materials.unit,
            materials.unit_cost as current_unit_cost,
            suppliers.name as preferred_supplier_name,
            suppliers.id as preferred_supplier_id
        FROM material_request_items 
        LEFT JOIN materials ON materials.id = material_request_items.material_id
        LEFT JOIN suppliers ON suppliers.id = materials.primary_supplier_id
        WHERE material_request_id = ? AND quantity_approved > 0";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$materialRequestId]);
    $purchaseableItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Query returned " . count($purchaseableItems) . " purchaseable items:\n";
    
    if (empty($purchaseableItems)) {
        echo "❌ No items meet the criteria for purchase!\n";
        echo "This explains why the AJAX response has empty items array.\n";
    } else {
        foreach ($purchaseableItems as $item) {
            echo "✅ {$item['material_name']}: {$item['quantity_approved']} @ MWK {$item['estimated_unit_cost']}\n";
        }
    }
    
} catch (PDOException $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
}
?>