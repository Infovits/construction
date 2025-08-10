<?php
// Quick debug script to check table structure
require_once 'vendor/autoload.php';

// Load CodeIgniter
$app = \CodeIgniter\Config\Services::codeigniter();
$app->initialize();

$db = \Config\Database::connect();

echo "=== DEBUGGING MATERIAL REQUEST TABLES ===\n\n";

// Check if tables exist
echo "1. Checking if tables exist:\n";
echo "material_requests: " . ($db->tableExists('material_requests') ? 'EXISTS' : 'NOT EXISTS') . "\n";
echo "material_request_items: " . ($db->tableExists('material_request_items') ? 'EXISTS' : 'NOT EXISTS') . "\n";
echo "materials: " . ($db->tableExists('materials') ? 'EXISTS' : 'NOT EXISTS') . "\n\n";

// Show table structure if exists
if ($db->tableExists('material_request_items')) {
    echo "2. material_request_items table structure:\n";
    $fields = $db->getFieldData('material_request_items');
    foreach ($fields as $field) {
        echo "  - {$field->name} ({$field->type}";
        if ($field->max_length) echo ", max_length: {$field->max_length}";
        if ($field->nullable) echo ", nullable";
        echo ")\n";
    }
} else {
    echo "2. material_request_items table does not exist!\n";
}

if ($db->tableExists('materials')) {
    echo "\n3. Sample materials (first 3):\n";
    $materials = $db->table('materials')->limit(3)->get()->getResultArray();
    foreach ($materials as $material) {
        echo "  - ID: {$material['id']}, Name: {$material['name']}\n";
    }
} else {
    echo "\n3. materials table does not exist!\n";
}

echo "\n=== END DEBUG ===\n";
?>