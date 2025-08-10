<?php
// Create suppliers table and sample data
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'construction';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Creating suppliers table...\n";
    
    // Create suppliers table
    $pdo->exec("
    CREATE TABLE IF NOT EXISTS `suppliers` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(255) NOT NULL,
        `contact_person` varchar(255) DEFAULT NULL,
        `email` varchar(255) DEFAULT NULL,
        `phone` varchar(50) DEFAULT NULL,
        `address` text DEFAULT NULL,
        `city` varchar(100) DEFAULT NULL,
        `postal_code` varchar(20) DEFAULT NULL,
        `tax_number` varchar(50) DEFAULT NULL,
        `payment_terms` varchar(100) DEFAULT NULL,
        `credit_limit` decimal(15,2) DEFAULT NULL,
        `status` enum('active','inactive') DEFAULT 'active',
        `rating` decimal(2,1) DEFAULT NULL,
        `notes` text DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `name` (`name`)
    )");
    
    echo "✓ Suppliers table created\n";
    
    // Insert sample suppliers
    $suppliers = [
        [
            'name' => 'Malawi Cement Company',
            'contact_person' => 'John Banda',
            'email' => 'sales@malawicement.com',
            'phone' => '+265-1-234-5678',
            'address' => 'Kanengo Industrial Area',
            'city' => 'Lilongwe',
            'payment_terms' => 'Net 30 days',
            'rating' => 4.5
        ],
        [
            'name' => 'Steel & Iron Works Ltd',
            'contact_person' => 'Mary Phiri',
            'email' => 'orders@steelworks.mw',
            'phone' => '+265-1-987-6543',
            'address' => 'Chirimba Industrial Area',
            'city' => 'Blantyre',
            'payment_terms' => 'Net 15 days',
            'rating' => 4.2
        ],
        [
            'name' => 'Builders Supply Co',
            'contact_person' => 'Peter Mwale',
            'email' => 'info@builderssupply.mw',
            'phone' => '+265-1-456-7890',
            'address' => 'Area 47 Sector 2',
            'city' => 'Lilongwe',
            'payment_terms' => 'Net 45 days',
            'rating' => 4.0
        ]
    ];
    
    $sql = "INSERT IGNORE INTO suppliers (
        name, contact_person, email, phone, address, city, payment_terms, rating
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    
    foreach ($suppliers as $supplier) {
        $stmt->execute([
            $supplier['name'],
            $supplier['contact_person'],
            $supplier['email'],
            $supplier['phone'],
            $supplier['address'],
            $supplier['city'],
            $supplier['payment_terms'],
            $supplier['rating']
        ]);
        echo "✓ Added supplier: {$supplier['name']}\n";
    }
    
    echo "\n✅ Suppliers setup complete!\n";
    echo "You can now create Purchase Orders with supplier selection.\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>