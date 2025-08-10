<?php
// Check goods_receipt_items table completely
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'contsruction';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Complete goods_receipt_items Table Structure</h2>";
    
    $stmt = $pdo->query("SHOW FULL COLUMNS FROM goods_receipt_items");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    foreach ($columns as $col) {
        echo "<tr>";
        echo "<td>" . $col['Field'] . "</td>";
        echo "<td>" . $col['Type'] . "</td>";
        echo "<td>" . $col['Null'] . "</td>";
        echo "<td>" . $col['Key'] . "</td>";
        echo "<td>" . ($col['Default'] ?? 'NULL') . "</td>";
        echo "<td>" . $col['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Check what columns the model expects vs what exists
    echo "<h3>Model Expected Columns vs Database Columns</h3>";
    
    $modelColumns = [
        'grn_id', 'purchase_order_item_id', 'material_id', 'quantity_delivered',
        'quantity_accepted', 'quantity_rejected', 'unit_cost', 'batch_number',
        'expiry_date', 'quality_status', 'rejection_reason', 'notes'
    ];
    
    $dbColumns = array_column($columns, 'Field');
    
    echo "<h4>✅ Columns that exist:</h4>";
    foreach ($modelColumns as $col) {
        if (in_array($col, $dbColumns)) {
            echo "- $col<br>";
        }
    }
    
    echo "<h4>❌ Missing columns:</h4>";
    foreach ($modelColumns as $col) {
        if (!in_array($col, $dbColumns)) {
            echo "- <strong style='color: red;'>$col</strong><br>";
        }
    }
    
    echo "<h4>Extra columns in database:</h4>";
    foreach ($dbColumns as $col) {
        if (!in_array($col, $modelColumns) && !in_array($col, ['id', 'created_at', 'updated_at'])) {
            echo "- $col<br>";
        }
    }
    
} catch (PDOException $e) {
    echo "❌ Database Error: " . $e->getMessage();
}
?>