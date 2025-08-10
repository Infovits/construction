<?php
// Test authentication status via web browser
echo "Testing auth status - please access this via browser at: http://localhost:8080/test_auth_status.php\n";
echo "Or use curl with cookies\n";

// Test direct database login
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'construction';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get user info
    $stmt = $pdo->query("SELECT id, email, first_name, last_name FROM users LIMIT 1");
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "\nUser exists in database:\n";
        echo "ID: {$user['id']}\n";
        echo "Email: {$user['email']}\n";
        echo "Name: {$user['first_name']} {$user['last_name']}\n";
    } else {
        echo "No users found in database\n";
    }
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
?>