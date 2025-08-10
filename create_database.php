<?php
// Create database and basic tables
$host = 'localhost';
$username = 'root';
$password = '';

try {
    // Connect to MySQL server (without database)
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database
    echo "Creating database 'construction'...\n";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `construction` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
    echo "Database created successfully!\n";
    
    // Connect to the new database
    $pdo = new PDO("mysql:host=$host;dbname=construction", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create basic tables
    echo "Creating basic tables...\n";
    
    // Users table
    $pdo->exec("
    CREATE TABLE IF NOT EXISTS `users` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `email` varchar(255) NOT NULL,
        `first_name` varchar(100) NOT NULL,
        `last_name` varchar(100) NOT NULL,
        `password` varchar(255) NOT NULL,
        `role_id` int(11) DEFAULT NULL,
        `department_id` int(11) DEFAULT NULL,
        `position_id` int(11) DEFAULT NULL,
        `status` enum('active','inactive') DEFAULT 'active',
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `email` (`email`)
    )");
    
    // Departments table
    $pdo->exec("
    CREATE TABLE IF NOT EXISTS `departments` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(255) NOT NULL,
        `description` text,
        `status` enum('active','inactive') DEFAULT 'active',
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    )");
    
    // Projects table
    $pdo->exec("
    CREATE TABLE IF NOT EXISTS `projects` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(255) NOT NULL,
        `description` text,
        `status` enum('planning','active','on_hold','completed','cancelled') DEFAULT 'planning',
        `start_date` date DEFAULT NULL,
        `end_date` date DEFAULT NULL,
        `budget` decimal(15,2) DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    )");
    
    // Materials table
    $pdo->exec("
    CREATE TABLE IF NOT EXISTS `materials` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(255) NOT NULL,
        `item_code` varchar(100) NOT NULL,
        `category_id` int(11) DEFAULT NULL,
        `unit` varchar(50) NOT NULL,
        `unit_cost` decimal(10,2) DEFAULT NULL,
        `current_stock` decimal(10,3) DEFAULT 0.000,
        `minimum_stock` decimal(10,3) DEFAULT 0.000,
        `description` text,
        `status` enum('active','inactive') DEFAULT 'active',
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `item_code` (`item_code`)
    )");
    
    echo "Basic tables created successfully!\n";
    
    // Insert sample data
    echo "Inserting sample data...\n";
    
    // Insert admin user
    $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $pdo->exec("
    INSERT IGNORE INTO `users` (`id`, `email`, `first_name`, `last_name`, `password`) 
    VALUES (1, 'admin@construction.com', 'Admin', 'User', '$hashedPassword')
    ");
    
    // Insert sample departments
    $pdo->exec("
    INSERT IGNORE INTO `departments` (`id`, `name`, `description`) VALUES
    (1, 'Construction', 'Main construction department'),
    (2, 'Engineering', 'Engineering and design department'),
    (3, 'Procurement', 'Procurement and supply chain'),
    (4, 'Administration', 'Administrative department')
    ");
    
    // Insert sample project
    $pdo->exec("
    INSERT IGNORE INTO `projects` (`id`, `name`, `description`, `status`) VALUES
    (1, 'Sample Construction Project', 'A sample project for testing', 'active')
    ");
    
    // Insert sample materials
    $pdo->exec("
    INSERT IGNORE INTO `materials` (`id`, `name`, `item_code`, `unit`, `unit_cost`, `current_stock`, `minimum_stock`) VALUES
    (1, 'Cement', 'CEM001', 'bags', 25.00, 100.000, 20.000),
    (2, 'Steel Bars 12mm', 'STL012', 'pieces', 120.00, 50.000, 10.000),
    (3, 'Bricks', 'BRK001', 'pieces', 0.50, 5000.000, 1000.000),
    (4, 'Sand', 'SND001', 'cubic meters', 15.00, 10.000, 2.000),
    (5, 'Gravel', 'GRV001', 'cubic meters', 20.00, 8.000, 2.000)
    ");
    
    echo "Sample data inserted successfully!\n";
    echo "\nSetup complete! You can now access the application.\n";
    echo "Login credentials: admin@construction.com / admin123\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>