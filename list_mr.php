<?php
// List all Material Requests in database
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'construction';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== ALL MATERIAL REQUESTS IN DATABASE ===\n\n";
    
    $stmt = $pdo->query("
        SELECT mr.*, p.name as project_name, 
               u.first_name, u.last_name,
               COUNT(mri.id) as item_count
        FROM material_requests mr
        LEFT JOIN projects p ON p.id = mr.project_id
        LEFT JOIN users u ON u.id = mr.requested_by
        LEFT JOIN material_request_items mri ON mri.material_request_id = mr.id
        GROUP BY mr.id
        ORDER BY mr.id
    ");
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($requests)) {
        echo "❌ No Material Requests found in database!\n";
        exit;
    }
    
    foreach ($requests as $mr) {
        echo "ID: {$mr['id']}\n";
        echo "Request Number: {$mr['request_number']}\n";
        echo "Project: " . ($mr['project_name'] ?? 'No Project') . "\n";
        echo "Requested by: {$mr['first_name']} {$mr['last_name']}\n";
        echo "Status: {$mr['status']}\n";
        echo "Priority: {$mr['priority']}\n";
        echo "Total Cost: MWK " . number_format($mr['total_estimated_cost'], 2) . "\n";
        echo "Items: {$mr['item_count']}\n";
        echo "Created: {$mr['created_at']}\n";
        
        if ($mr['status'] === 'approved') {
            echo "✅ APPROVED - Can create PO\n";
        } else {
            echo "⏳ Status: {$mr['status']}\n";
        }
        
        echo str_repeat("-", 50) . "\n";
    }
    
    // Also show items for each request
    echo "\n=== ITEMS BREAKDOWN ===\n\n";
    
    foreach ($requests as $mr) {
        echo "Material Request {$mr['id']} ({$mr['request_number']}) Items:\n";
        
        $stmt = $pdo->prepare("
            SELECT mri.*, m.name as material_name, m.item_code, m.unit
            FROM material_request_items mri
            LEFT JOIN materials m ON m.id = mri.material_id
            WHERE mri.material_request_id = ?
        ");
        $stmt->execute([$mr['id']]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($items)) {
            echo "  ❌ No items found\n";
        } else {
            foreach ($items as $item) {
                echo "  - {$item['material_name']} ({$item['item_code']})\n";
                echo "    Requested: {$item['quantity_requested']}, Approved: " . ($item['quantity_approved'] ?? 'NULL') . "\n";
                echo "    Unit Cost: MWK " . ($item['estimated_unit_cost'] ?? 'NULL') . "\n";
            }
        }
        echo "\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
}
?>