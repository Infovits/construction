<?php
// Quick fix to approve Material Request ID 3
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'construction';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $materialRequestId = 3;
    
    echo "=== APPROVING MATERIAL REQUEST ID 3 ===\n\n";
    
    // Start transaction
    $pdo->beginTransaction();
    
    // 1. Update the Material Request to approved status
    $stmt = $pdo->prepare("
        UPDATE material_requests 
        SET status = 'approved', 
            approved_by = 1, 
            approved_date = NOW() 
        WHERE id = ?
    ");
    $stmt->execute([$materialRequestId]);
    
    echo "✅ Updated Material Request status to approved\n";
    
    // 2. Set approved quantities for all items (approve the full requested quantity)
    $stmt = $pdo->prepare("
        UPDATE material_request_items 
        SET quantity_approved = quantity_requested 
        WHERE material_request_id = ? AND quantity_approved IS NULL
    ");
    $stmt->execute([$materialRequestId]);
    
    $approvedItems = $stmt->rowCount();
    echo "✅ Approved quantities for $approvedItems items\n";
    
    // 3. Verify the results
    $stmt = $pdo->prepare("
        SELECT mri.*, m.name as material_name, m.item_code, m.unit
        FROM material_request_items mri
        LEFT JOIN materials m ON m.id = mri.material_id
        WHERE mri.material_request_id = ?
    ");
    $stmt->execute([$materialRequestId]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\n📋 Approved Items:\n";
    foreach ($items as $item) {
        echo "- {$item['material_name']} ({$item['item_code']})\n";
        echo "  Requested: {$item['quantity_requested']}, Approved: {$item['quantity_approved']} {$item['unit']}\n";
        echo "  Unit Cost: MWK " . number_format($item['estimated_unit_cost'], 2) . "\n\n";
    }
    
    // Commit transaction
    $pdo->commit();
    
    echo "🎉 SUCCESS! Material Request ID 3 is now approved and ready for PO creation!\n";
    echo "\nNow test the PO creation - items should populate correctly.\n";
    
} catch (PDOException $e) {
    $pdo->rollback();
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>