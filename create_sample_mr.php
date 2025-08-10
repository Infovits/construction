<?php
// Create a sample approved material request for testing
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'construction';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Creating sample approved Material Request...\n";
    
    // Insert material request
    $sql = "INSERT INTO material_requests (
        request_number, project_id, requested_by, department_id, request_date, required_date, 
        status, priority, total_estimated_cost, approved_by, approved_date, notes
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'MR-2025-001',
        1, // project_id
        1, // requested_by (admin user)
        1, // department_id
        date('Y-m-d'),
        date('Y-m-d', strtotime('+7 days')),
        'approved',
        'medium',
        675.00,
        1, // approved_by (admin user)
        date('Y-m-d H:i:s'),
        'Sample material request for testing PO creation'
    ]);
    
    $materialRequestId = $pdo->lastInsertId();
    echo "✓ Created Material Request with ID: $materialRequestId\n";
    
    // Insert material request items
    $items = [
        [
            'material_id' => 1, // Cement
            'quantity_requested' => 15.000,
            'quantity_approved' => 12.000,
            'estimated_unit_cost' => 25.00,
            'estimated_total_cost' => 375.00,
            'specification_notes' => 'High grade Portland cement for foundation work'
        ],
        [
            'material_id' => 2, // Steel Bars
            'quantity_requested' => 25.000,
            'quantity_approved' => 20.000,
            'estimated_unit_cost' => 15.00,
            'estimated_total_cost' => 300.00,
            'specification_notes' => '12mm reinforcement bars, Grade 60'
        ]
    ];
    
    $itemSql = "INSERT INTO material_request_items (
        material_request_id, material_id, quantity_requested, quantity_approved,
        estimated_unit_cost, estimated_total_cost, specification_notes
    ) VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $itemStmt = $pdo->prepare($itemSql);
    
    foreach ($items as $item) {
        $itemStmt->execute([
            $materialRequestId,
            $item['material_id'],
            $item['quantity_requested'],
            $item['quantity_approved'],
            $item['estimated_unit_cost'],
            $item['estimated_total_cost'],
            $item['specification_notes']
        ]);
        echo "✓ Added item: Material ID {$item['material_id']}, Approved Qty: {$item['quantity_approved']}\n";
    }
    
    echo "\n✅ Sample Material Request created successfully!\n";
    echo "Request Number: MR-2025-001\n";
    echo "Status: Approved (ready for PO creation)\n";
    echo "Total Cost: MWK 675.00\n";
    echo "\nYou can now test the PO creation by selecting this Material Request.\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>