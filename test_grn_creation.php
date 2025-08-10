<?php
// Test GRN creation with minimal data
require_once 'vendor/autoload.php';

// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'contsruction';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Testing GRN Creation Step by Step</h2>";
    
    // Test data from the debug output
    $testData = [
        'purchase_order_id' => 7,
        'warehouse_id' => 1, // assuming warehouse 1 exists
        'delivery_date' => date('Y-m-d'),
        'received_by' => 1,
        'items' => [
            [
                'purchase_order_item_id' => 3,
                'material_id' => 2,
                'quantity_delivered' => 8.000,
                'unit_cost' => 20000.00,
                'batch_number' => '8',
                'expiry_date' => null,
                'notes' => ''
            ]
        ]
    ];
    
    echo "<h3>1. Check Purchase Order exists:</h3>";
    $stmt = $pdo->prepare("SELECT id, po_number, status, supplier_id FROM purchase_orders WHERE id = ?");
    $stmt->execute([7]);
    $po = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($po) {
        echo "✅ PO found: {$po['po_number']} | Status: {$po['status']} | Supplier: {$po['supplier_id']}<br>";
    } else {
        echo "❌ PO 7 not found<br>";
        exit;
    }
    
    echo "<h3>2. Check Purchase Order Item exists:</h3>";
    $stmt = $pdo->prepare("SELECT id, material_id, quantity_ordered, quantity_received, quantity_pending FROM purchase_order_items WHERE id = ?");
    $stmt->execute([3]);
    $poItem = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($poItem) {
        echo "✅ PO Item found: Material {$poItem['material_id']} | Ordered: {$poItem['quantity_ordered']} | Received: {$poItem['quantity_received']} | Pending: {$poItem['quantity_pending']}<br>";
    } else {
        echo "❌ PO Item 3 not found<br>";
        exit;
    }
    
    echo "<h3>3. Check Material exists:</h3>";
    $stmt = $pdo->prepare("SELECT id, name FROM materials WHERE id = ?");
    $stmt->execute([2]);
    $material = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($material) {
        echo "✅ Material found: {$material['name']}<br>";
    } else {
        echo "❌ Material 2 not found<br>";
        exit;
    }
    
    echo "<h3>4. Check Warehouse exists:</h3>";
    $stmt = $pdo->prepare("SELECT id, name FROM warehouses WHERE id = ?");
    $stmt->execute([1]);
    $warehouse = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($warehouse) {
        echo "✅ Warehouse found: {$warehouse['name']}<br>";
    } else {
        echo "❌ Warehouse 1 not found<br>";
        
        // Show available warehouses
        $stmt = $pdo->query("SELECT id, name FROM warehouses LIMIT 5");
        $warehouses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "Available warehouses:<br>";
        foreach ($warehouses as $wh) {
            echo "- ID: {$wh['id']}, Name: {$wh['name']}<br>";
        }
        exit;
    }
    
    echo "<h3>5. Test GRN Number Generation:</h3>";
    // Get last GRN
    $stmt = $pdo->query("SELECT grn_number FROM goods_receipt_notes ORDER BY id DESC LIMIT 1");
    $lastGrn = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($lastGrn) {
        echo "Last GRN: {$lastGrn['grn_number']}<br>";
    } else {
        echo "No existing GRNs found<br>";
    }
    
    // Generate next GRN number
    $prefix = 'GRN-' . date('Ymd') . '-';
    $nextGrn = $prefix . '0001';
    echo "Next GRN would be: $nextGrn<br>";
    
    echo "<h3>6. Test Direct Database Insert:</h3>";
    echo "Testing direct insert without CodeIgniter models...<br>";
    
    // Test inserting GRN header
    $stmt = $pdo->prepare("
        INSERT INTO goods_receipt_notes 
        (grn_number, purchase_order_id, supplier_id, warehouse_id, delivery_date, received_by, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    
    $testGrnNumber = 'TEST-' . date('YmdHis');
    $result = $stmt->execute([
        $testGrnNumber,
        $po['id'],
        $po['supplier_id'],
        1,  // warehouse_id
        date('Y-m-d'),
        1,  // received_by
        'pending_inspection'
    ]);
    
    if ($result) {
        $testGrnId = $pdo->lastInsertId();
        echo "✅ Test GRN header inserted with ID: $testGrnId<br>";
        
        // Test inserting GRN item
        $stmt = $pdo->prepare("
            INSERT INTO goods_receipt_items 
            (grn_id, purchase_order_item_id, material_id, quantity_delivered, unit_cost, batch_number, quality_status) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $itemResult = $stmt->execute([
            $testGrnId,
            3,  // purchase_order_item_id
            2,  // material_id
            8.000,  // quantity_delivered
            20000.00,  // unit_cost
            '8',  // batch_number
            'pending'
        ]);
        
        if ($itemResult) {
            echo "✅ Test GRN item inserted successfully<br>";
            
            // Clean up test data
            $pdo->prepare("DELETE FROM goods_receipt_items WHERE grn_id = ?")->execute([$testGrnId]);
            $pdo->prepare("DELETE FROM goods_receipt_notes WHERE id = ?")->execute([$testGrnId]);
            echo "🧹 Test data cleaned up<br>";
            
            echo "<h3>✅ CONCLUSION:</h3>";
            echo "Database tables and data are all correct. The issue is likely in the CodeIgniter model logic or validation.<br>";
            
        } else {
            echo "❌ Test GRN item insert failed<br>";
        }
        
    } else {
        echo "❌ Test GRN header insert failed<br>";
    }
    
} catch (PDOException $e) {
    echo "❌ Database Error: " . $e->getMessage() . "<br>";
    echo "Stack trace: " . $e->getTraceAsString() . "<br>";
}
?>