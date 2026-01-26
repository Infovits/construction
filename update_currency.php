<?php

// Simple script to update existing projects to use MWK currency
require_once 'app/Config/Database.php';

$db = \Config\Database::connect();

try {
    // Update all existing projects to use MWK currency
    $result = $db->query("UPDATE projects SET currency = 'MWK' WHERE currency IS NULL OR currency = ''");

    if ($result) {
        echo "Successfully updated projects to use MWK currency!\n";
        echo "Affected rows: " . $db->affectedRows() . "\n";
    } else {
        echo "Failed to update projects.\n";
    }
} catch (Exception $e) {
    echo "Error updating currency: " . $e->getMessage() . "\n";
}
