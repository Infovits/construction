<?php
// Quick database connectivity check
$host = 'localhost';
$username = 'root'; // Default WAMP username
$password = ''; // Default WAMP password
$database = 'construction';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== DATABASE CONNECTION SUCCESS ===\n";
    
    // Check if tables exist
    $tables = ['materials', 'material_requests', 'material_request_items', 'users', 'projects', 'departments'];
    
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        $exists = $stmt->rowCount() > 0 ? 'EXISTS' : 'NOT EXISTS';
        echo "$table: $exists\n";
    }
    
    // Check if we have sample data
    echo "\n=== SAMPLE DATA CHECK ===\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM materials");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Materials count: " . $result['count'] . "\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Users count: " . $result['count'] . "\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM material_requests");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Material Requests count: " . $result['count'] . "\n";
    
    // Show some sample materials
    echo "\n=== SAMPLE MATERIALS (first 3) ===\n";
    $stmt = $pdo->query("SELECT id, name, item_code, unit, unit_cost FROM materials LIMIT 3");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "ID: {$row['id']}, Name: {$row['name']}, Code: {$row['item_code']}\n";
    }
    
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
}
?>