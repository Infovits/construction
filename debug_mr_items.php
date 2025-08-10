<?php
// Debug Material Request items
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'construction';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $materialRequestId = 3; // The request that returned empty items from WAMP
    
    echo "=== DEBUGGING MATERIAL REQUEST ITEMS ===\n";
    echo "Material Request ID: $materialRequestId\n\n";
    
    // First, check if the material request exists
    $stmt = $pdo->prepare("SELECT * FROM material_requests WHERE id = ?");
    $stmt->execute([$materialRequestId]);
    $mr = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($mr) {
        echo "1. Material Request found:\n";
        echo "   Request Number: {$mr['request_number']}\n";
        echo "   Status: {$mr['status']}\n";
        echo "   Total Cost: MWK " . number_format($mr['total_estimated_cost'], 2) . "\n\n";
    } else {
        echo "1. Material Request NOT found!\n";
        exit;
    }
    
    // Check raw material request items
    $stmt = $pdo->prepare("SELECT * FROM material_request_items WHERE material_request_id = ?");
    $stmt->execute([$materialRequestId]);
    $rawItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "2. Raw Material Request Items (" . count($rawItems) . " found):\n";
    foreach ($rawItems as $item) {
        echo "   - ID: {$item['id']}, Material ID: {$item['material_id']}\n";
        echo "     Requested: {$item['quantity_requested']}, Approved: " . ($item['quantity_approved'] ?? 'NULL') . "\n";
        echo "     Unit Cost: " . ($item['estimated_unit_cost'] ?? 'NULL') . "\n\n";
    }
    
    // Test the exact query used in getItemsToBePurchased
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
    
    echo "3. Testing getItemsToBePurchased query:\n";
    echo "   SQL: $sql\n";
    echo "   Parameters: [$materialRequestId]\n\n";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$materialRequestId]);
    $purchaseableItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "   Results (" . count($purchaseableItems) . " items):\n";
    if (empty($purchaseableItems)) {
        echo "   ❌ No items found! This explains why items array is empty.\n\n";
        
        // Check if materials table has the referenced material IDs
        echo "4. Checking materials table:\n";
        foreach ($rawItems as $item) {
            $stmt = $pdo->prepare("SELECT id, name, item_code, unit, unit_cost FROM materials WHERE id = ?");
            $stmt->execute([$item['material_id']]);
            $material = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($material) {
                echo "   ✓ Material ID {$item['material_id']}: {$material['name']}\n";
            } else {
                echo "   ❌ Material ID {$item['material_id']}: NOT FOUND in materials table!\n";
            }
        }
        
        // Check the specific issue
        echo "\n5. Checking specific issues:\n";
        foreach ($rawItems as $item) {
            $issues = [];
            
            if ($item['quantity_approved'] === null || $item['quantity_approved'] <= 0) {
                $issues[] = "quantity_approved is " . ($item['quantity_approved'] ?? 'NULL') . " (needs > 0)";
            }
            
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM materials WHERE id = ?");
            $stmt->execute([$item['material_id']]);
            $materialExists = $stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;
            
            if (!$materialExists) {
                $issues[] = "material_id {$item['material_id']} doesn't exist in materials table";
            }
            
            if ($issues) {
                echo "   ❌ Item ID {$item['id']}: " . implode(', ', $issues) . "\n";
            } else {
                echo "   ✓ Item ID {$item['id']}: No issues found\n";
            }
        }
        
    } else {
        foreach ($purchaseableItems as $item) {
            echo "   ✓ {$item['material_name']} - Qty: {$item['quantity_approved']}, Cost: MWK {$item['estimated_unit_cost']}\n";
        }
    }
    
} catch (PDOException $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
}
?>