<?php
// Check if Purchase Order tables exist in WAMP database
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'contsruction'; // WAMP database name

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== CHECKING PURCHASE ORDER TABLES ===\n\n";
    
    // Check if purchase_orders table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'purchase_orders'");
    $poTableExists = $stmt->rowCount() > 0;
    
    echo "purchase_orders table: " . ($poTableExists ? "✅ EXISTS" : "❌ MISSING") . "\n";
    
    // Check if purchase_order_items table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'purchase_order_items'");
    $poItemsTableExists = $stmt->rowCount() > 0;
    
    echo "purchase_order_items table: " . ($poItemsTableExists ? "✅ EXISTS" : "❌ MISSING") . "\n";
    
    if (!$poTableExists || !$poItemsTableExists) {
        echo "\n🔧 CREATING MISSING TABLES...\n\n";
        
        // Create purchase_orders table
        if (!$poTableExists) {
            $pdo->exec("
            CREATE TABLE `purchase_orders` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `po_number` varchar(50) NOT NULL,
                `supplier_id` int(11) NOT NULL,
                `material_request_id` int(11) DEFAULT NULL,
                `project_id` int(11) DEFAULT NULL,
                `po_date` date NOT NULL,
                `expected_delivery_date` date DEFAULT NULL,
                `status` enum('draft','sent','confirmed','partially_received','received','cancelled') DEFAULT 'draft',
                `payment_terms` varchar(100) DEFAULT NULL,
                `delivery_terms` varchar(100) DEFAULT NULL,
                `subtotal` decimal(15,2) DEFAULT '0.00',
                `tax_amount` decimal(15,2) DEFAULT '0.00',
                `freight_cost` decimal(15,2) DEFAULT '0.00',
                `total_amount` decimal(15,2) DEFAULT '0.00',
                `currency` varchar(3) DEFAULT 'MWK',
                `notes` text DEFAULT NULL,
                `terms_conditions` text DEFAULT NULL,
                `created_by` int(11) NOT NULL,
                `approved_by` int(11) DEFAULT NULL,
                `approved_date` datetime DEFAULT NULL,
                `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `po_number` (`po_number`),
                KEY `supplier_id` (`supplier_id`),
                KEY `material_request_id` (`material_request_id`),
                KEY `project_id` (`project_id`)
            )");
            echo "✅ Created purchase_orders table\n";
        }
        
        // Create purchase_order_items table
        if (!$poItemsTableExists) {
            $pdo->exec("
            CREATE TABLE `purchase_order_items` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `purchase_order_id` int(11) NOT NULL,
                `material_id` int(11) NOT NULL,
                `quantity_ordered` decimal(10,3) NOT NULL,
                `quantity_received` decimal(10,3) DEFAULT '0.000',
                `unit_cost` decimal(10,2) NOT NULL,
                `total_cost` decimal(15,2) NOT NULL,
                `specification_notes` text DEFAULT NULL,
                `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `purchase_order_id` (`purchase_order_id`),
                KEY `material_id` (`material_id`)
            )");
            echo "✅ Created purchase_order_items table\n";
        }
        
        echo "\n🎉 All Purchase Order tables are now ready!\n";
    } else {
        echo "\n✅ All Purchase Order tables exist!\n";
    }
    
    // Check if suppliers table exists (needed for PO creation)
    $stmt = $pdo->query("SHOW TABLES LIKE 'suppliers'");
    $suppliersExist = $stmt->rowCount() > 0;
    
    echo "suppliers table: " . ($suppliersExist ? "✅ EXISTS" : "❌ MISSING") . "\n";
    
    if (!$suppliersExist) {
        echo "⚠️  WARNING: suppliers table is missing! Creating it...\n";
        
        $pdo->exec("
        CREATE TABLE `suppliers` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(255) NOT NULL,
            `contact_person` varchar(255) DEFAULT NULL,
            `email` varchar(255) DEFAULT NULL,
            `phone` varchar(50) DEFAULT NULL,
            `address` text DEFAULT NULL,
            `city` varchar(100) DEFAULT NULL,
            `status` enum('active','inactive') DEFAULT 'active',
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        )");
        
        // Insert sample supplier
        $pdo->exec("
        INSERT INTO suppliers (name, contact_person, email, phone, address) VALUES
        ('Sample Supplier Co.', 'John Doe', 'john@supplier.com', '+265-123-456', 'Lilongwe, Malawi')
        ");
        
        echo "✅ Created suppliers table with sample data\n";
    }
    
    // Show sample data counts
    echo "\n=== DATA SUMMARY ===\n";
    
    if ($poTableExists) {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM purchase_orders");
        $poCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "Purchase Orders: $poCount\n";
    }
    
    if ($suppliersExist) {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM suppliers");
        $supplierCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "Suppliers: $supplierCount\n";
        
        // Show suppliers for dropdown
        echo "\nAvailable Suppliers:\n";
        $stmt = $pdo->query("SELECT id, name FROM suppliers ORDER BY name");
        $suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($suppliers as $supplier) {
            echo "- ID: {$supplier['id']}, Name: {$supplier['name']}\n";
        }
    }
    
} catch (PDOException $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
}
?>