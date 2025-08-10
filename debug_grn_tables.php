<?php
// Debug GRN database tables
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'contsruction';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>GRN Database Tables Check</h2>";
    
    // Check goods_receipt_notes table
    echo "<h3>1. goods_receipt_notes table:</h3>";
    try {
        $stmt = $pdo->query("DESCRIBE goods_receipt_notes");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "<pre>";
        foreach ($columns as $col) {
            echo $col['Field'] . " | " . $col['Type'] . " | " . $col['Null'] . " | " . $col['Key'] . " | " . $col['Default'] . " | " . $col['Extra'] . "\n";
        }
        echo "</pre>";
    } catch (Exception $e) {
        echo "❌ Table doesn't exist: " . $e->getMessage() . "\n";
    }
    
    // Check goods_receipt_items table
    echo "<h3>2. goods_receipt_items table:</h3>";
    try {
        $stmt = $pdo->query("DESCRIBE goods_receipt_items");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "<pre>";
        foreach ($columns as $col) {
            echo $col['Field'] . " | " . $col['Type'] . " | " . $col['Null'] . " | " . $col['Key'] . " | " . $col['Default'] . " | " . $col['Extra'] . "\n";
        }
        echo "</pre>";
    } catch (Exception $e) {
        echo "❌ Table doesn't exist: " . $e->getMessage() . "\n";
    }
    
    // Check if warehouses exist
    echo "<h3>3. Available Warehouses:</h3>";
    try {
        $stmt = $pdo->query("SELECT id, name FROM warehouses LIMIT 5");
        $warehouses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "<pre>";
        if (empty($warehouses)) {
            echo "❌ No warehouses found\n";
        } else {
            foreach ($warehouses as $wh) {
                echo "ID: {$wh['id']} | Name: {$wh['name']}\n";
            }
        }
        echo "</pre>";
    } catch (Exception $e) {
        echo "❌ Warehouses table issue: " . $e->getMessage() . "\n";
    }
    
    // Check if PO 7 exists and has items
    echo "<h3>4. Purchase Order 7 Status:</h3>";
    try {
        $stmt = $pdo->query("SELECT id, po_number, status, supplier_id FROM purchase_orders WHERE id = 7");
        $po = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($po) {
            echo "<pre>";
            echo "✅ PO found: {$po['po_number']} | Status: {$po['status']} | Supplier: {$po['supplier_id']}\n";
            
            // Check PO items
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM purchase_order_items WHERE purchase_order_id = 7");
            $itemCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            echo "Items: $itemCount\n";
            echo "</pre>";
        } else {
            echo "❌ PO 7 not found\n";
        }
    } catch (Exception $e) {
        echo "❌ PO check failed: " . $e->getMessage() . "\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
}
?>