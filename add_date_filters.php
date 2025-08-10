<?php
// Add date filter support to model methods

$models = [
    'MaterialRequestModel' => [
        'file' => 'D:/Wamp64/www/construction/app/Models/MaterialRequestModel.php',
        'method' => 'getMaterialRequestsWithDetails',
        'date_field' => 'request_date'
    ],
    'PurchaseOrderModel' => [
        'file' => 'D:/Wamp64/www/construction/app/Models/PurchaseOrderModel.php', 
        'method' => 'getPurchaseOrdersWithDetails',
        'date_field' => 'po_date'
    ],
    'GoodsReceiptNoteModel' => [
        'file' => 'D:/Wamp64/www/construction/app/Models/GoodsReceiptNoteModel.php',
        'method' => 'getGRNsWithDetails', 
        'date_field' => 'delivery_date'
    ],
    'QualityInspectionModel' => [
        'file' => 'D:/Wamp64/www/construction/app/Models/QualityInspectionModel.php',
        'method' => 'getInspectionsWithDetails',
        'date_field' => 'inspection_date'
    ]
];

foreach ($models as $modelName => $config) {
    $content = file_get_contents($config['file']);
    
    // Add date filters before the return statement in each method
    $dateFilterCode = "
        // Apply date filters
        if (!empty(\$filters['date_from'])) {
            \$builder->where('{$modelName}s.{$config['date_field']} >=', \$filters['date_from']);
        }

        if (!empty(\$filters['date_to'])) {
            \$builder->where('{$modelName}s.{$config['date_field']} <=', \$filters['date_to']);
        }
";
    
    // Find the return statement in the method and add date filters before it
    $pattern = '/(\s+)(return \$builder->orderBy\([^;]+;)/';
    $replacement = '$1' . $dateFilterCode . '$1$2';
    
    $content = preg_replace($pattern, $replacement, $content);
    
    file_put_contents($config['file'], $content);
    echo "Added date filters to {$modelName}\n";
}

echo "Date filters added successfully!\n";
?>
