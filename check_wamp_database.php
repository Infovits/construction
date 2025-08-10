<?php
// Check WAMP database - using the correct database name from .env
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'contsruction'; // Note: this matches the typo in .env file

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== WAMP DATABASE CHECK (database: $database) ===\n\n";
    
    // Check Material Request ID 3
    $stmt = $pdo->prepare("SELECT * FROM material_requests WHERE id = 3");
    $stmt->execute();
    $mr = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($mr) {
        echo "📋 Material Request ID 3 Found:\n";
        echo "Request Number: {$mr['request_number']}\n";
        echo "Status: {$mr['status']}\n";
        echo "Total Cost: MWK " . number_format($mr['total_estimated_cost'], 2) . "\n";
        echo "Approved By: " . ($mr['approved_by'] ?? 'NULL') . "\n";
        echo "Approved Date: " . ($mr['approved_date'] ?? 'NULL') . "\n\n";
    } else {
        echo "❌ Material Request ID 3 not found in WAMP database!\n";
        
        // Show what Material Requests exist
        $stmt = $pdo->query("SELECT id, request_number, status FROM material_requests ORDER BY id");
        $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Available Material Requests:\n";
        foreach ($requests as $req) {
            echo "- ID: {$req['id']}, Number: {$req['request_number']}, Status: {$req['status']}\n";
        }
        exit;
    }
    
    // Check items for Material Request ID 3
    $stmt = $pdo->prepare("
        SELECT mri.*, m.name as material_name, m.item_code, m.unit
        FROM material_request_items mri
        LEFT JOIN materials m ON m.id = mri.material_id
        WHERE mri.material_request_id = 3
    ");
    $stmt->execute();
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "📦 Items for Material Request ID 3 (" . count($items) . " found):\n";
    
    foreach ($items as $item) {
        echo "\n--- Item ID: {$item['id']} ---\n";
        echo "Material: " . ($item['material_name'] ?? 'NOT FOUND') . " ({$item['item_code']})\n";
        echo "Requested: {$item['quantity_requested']} {$item['unit']}\n";
        echo "Approved: " . ($item['quantity_approved'] === null ? 'NULL (PENDING)' : $item['quantity_approved']) . "\n";
        echo "Unit Cost: MWK " . number_format($item['estimated_unit_cost'], 2) . "\n";
    }
    
    // Check why items are not being returned
    echo "\n🔍 Testing getItemsToBePurchased query:\n";
    
    $sql = "SELECT material_request_items.*, 
            materials.name as material_name,
            materials.item_code,
            materials.unit,
            materials.unit_cost as current_unit_cost
        FROM material_request_items 
        LEFT JOIN materials ON materials.id = material_request_items.material_id
        WHERE material_request_id = 3 AND quantity_approved > 0";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $purchaseableItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Items with quantity_approved > 0: " . count($purchaseableItems) . "\n";
    
    if (empty($purchaseableItems)) {
        echo "❌ No items have approved quantities > 0\n";
        echo "🔧 SOLUTION: Need to approve the Material Request first!\n\n";
        
        echo "=== FIXING MATERIAL REQUEST ID 3 ===\n";
        
        // Start transaction
        $pdo->beginTransaction();
        
        try {
            // Update Material Request to approved
            $stmt = $pdo->prepare("
                UPDATE material_requests 
                SET status = 'approved', approved_by = 1, approved_date = NOW() 
                WHERE id = 3
            ");
            $stmt->execute();
            echo "✅ Updated Material Request status to approved\n";
            
            // Set approved quantities (approve full requested amounts)
            $stmt = $pdo->prepare("
                UPDATE material_request_items 
                SET quantity_approved = quantity_requested 
                WHERE material_request_id = 3
            ");
            $stmt->execute();
            $updated = $stmt->rowCount();
            echo "✅ Set approved quantities for $updated items\n";
            
            // Verify the fix
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $fixedItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "✅ Now " . count($fixedItems) . " items are available for purchase:\n";
            foreach ($fixedItems as $item) {
                echo "- {$item['material_name']}: {$item['quantity_approved']} {$item['unit']} @ MWK {$item['estimated_unit_cost']}\n";
            }
            
            $pdo->commit();
            echo "\n🎉 SUCCESS! Material Request ID 3 is now ready for PO creation!\n";
            
        } catch (Exception $e) {
            $pdo->rollback();
            echo "❌ Error during fix: " . $e->getMessage() . "\n";
        }
        
    } else {
        echo "✅ Items are ready for purchase:\n";
        foreach ($purchaseableItems as $item) {
            echo "- {$item['material_name']}: {$item['quantity_approved']} @ MWK {$item['estimated_unit_cost']}\n";
        }
    }
    
} catch (PDOException $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
    echo "Make sure WAMP MySQL is running and database '$database' exists.\n";
}
?>