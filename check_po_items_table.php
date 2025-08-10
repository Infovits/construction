<?php
// Check purchase_order_items table structure
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'contsruction'; // WAMP database

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== CHECKING PURCHASE ORDER ITEMS TABLE STRUCTURE ===\n\n";
    
    // Get table structure
    $stmt = $pdo->query("DESCRIBE purchase_order_items");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Table: purchase_order_items\n";
    echo "Columns:\n";
    foreach ($columns as $column) {
        echo "- {$column['Field']}: {$column['Type']} " . 
             ($column['Null'] == 'YES' ? '(nullable)' : '(NOT NULL)') . 
             ($column['Default'] !== null ? " default: {$column['Default']}" : '') . "\n";
    }
    
    echo "\n=== EXPECTED vs ACTUAL COLUMNS ===\n";
    
    $expectedColumns = [
        'id', 'purchase_order_id', 'material_id', 'material_request_item_id',
        'quantity_ordered', 'unit_cost', 'total_cost', 'quantity_received', 
        'specification_notes', 'created_at', 'updated_at'
    ];
    
    $actualColumns = array_column($columns, 'Field');
    
    echo "Expected columns that are missing:\n";
    $missingColumns = array_diff($expectedColumns, $actualColumns);
    if (empty($missingColumns)) {
        echo "✅ All expected columns exist!\n";
    } else {
        foreach ($missingColumns as $missing) {
            echo "❌ Missing: $missing\n";
        }
    }
    
    echo "\nActual columns that are unexpected:\n";
    $extraColumns = array_diff($actualColumns, $expectedColumns);
    if (empty($extraColumns)) {
        echo "✅ No unexpected columns!\n";
    } else {
        foreach ($extraColumns as $extra) {
            echo "⚠️  Extra: $extra\n";
        }
    }
    
    // Test a sample insert to see what fails
    echo "\n=== TESTING SAMPLE INSERT ===\n";
    
    try {
        $testData = [
            'purchase_order_id' => 999999, // Use a non-existent PO ID for testing
            'material_id' => 1,
            'quantity_ordered' => 5.000,
            'unit_cost' => 100.00,
            'total_cost' => 500.00,
            'quantity_received' => 0.000,
            'specification_notes' => 'Test insert'
        ];
        
        $columns = array_keys($testData);
        $placeholders = ':' . implode(', :', $columns);
        $sql = "INSERT INTO purchase_order_items (" . implode(', ', $columns) . ") VALUES ($placeholders)";
        
        echo "Test SQL: $sql\n";
        echo "Test Data: " . json_encode($testData) . "\n";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($testData);
        
        echo "✅ Sample insert would work! (Rolling back...)\n";
        $pdo->rollback(); // This won't work since we didn't start a transaction, but that's OK
        
    } catch (Exception $e) {
        echo "❌ Sample insert failed: " . $e->getMessage() . "\n";
        
        // Check if it's a foreign key constraint issue
        if (strpos($e->getMessage(), 'foreign key constraint') !== false || 
            strpos($e->getMessage(), 'purchase_order_id') !== false) {
            echo "💡 This is likely a foreign key constraint issue (expected with test data)\n";
        }
        
        if (strpos($e->getMessage(), 'Unknown column') !== false) {
            echo "💡 This indicates a column name mismatch\n";
        }
    }
    
} catch (PDOException $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
}
?>