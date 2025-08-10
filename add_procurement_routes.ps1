# PowerShell script to add procurement routes to Routes.php
$filePath = "app\Config\Routes.php"
$content = Get-Content $filePath

# Find the line after suppliers group ends
$insertLineIndex = -1
for ($i = 0; $i -lt $content.Length; $i++) {
    if ($content[$i] -match "^\s*\}\);\s*$" -and $content[$i-1] -match "updateDeliveryStatus") {
        $insertLineIndex = $i + 1
        break
    }
}

if ($insertLineIndex -gt -1) {
    # Procurement routes content
    $procurementRoutes = @(
        "",
        "    // Procurement Management Routes",
        "    `$routes->group('material-requests', function(`$routes) {",
        "        `$routes->get('/', 'MaterialRequestController::index');",
        "        `$routes->get('create', 'MaterialRequestController::create');",
        "        `$routes->post('store', 'MaterialRequestController::store');",
        "        `$routes->get('(:num)', 'MaterialRequestController::view/`$1');",
        "        `$routes->get('(:num)/edit', 'MaterialRequestController::edit/`$1');",
        "        `$routes->post('(:num)/update', 'MaterialRequestController::update/`$1');",
        "        `$routes->post('(:num)/submit', 'MaterialRequestController::submit/`$1');",
        "        `$routes->post('(:num)/approve', 'MaterialRequestController::approve/`$1');",
        "        `$routes->post('(:num)/reject', 'MaterialRequestController::reject/`$1');",
        "        `$routes->delete('(:num)/delete', 'MaterialRequestController::delete/`$1');",
        "    });",
        "",
        "    `$routes->group('purchase-orders', function(`$routes) {",
        "        `$routes->get('/', 'PurchaseOrderController::index');",
        "        `$routes->get('create', 'PurchaseOrderController::create');",
        "        `$routes->post('store', 'PurchaseOrderController::store');",
        "        `$routes->get('(:num)', 'PurchaseOrderController::view/`$1');",
        "        `$routes->get('(:num)/edit', 'PurchaseOrderController::edit/`$1');",
        "        `$routes->post('(:num)/update', 'PurchaseOrderController::update/`$1');",
        "        `$routes->post('(:num)/approve', 'PurchaseOrderController::approve/`$1');",
        "        `$routes->post('(:num)/cancel', 'PurchaseOrderController::cancel/`$1');",
        "        `$routes->delete('(:num)/delete', 'PurchaseOrderController::delete/`$1');",
        "        `$routes->get('(:num)/pdf', 'PurchaseOrderController::generatePDF/`$1');",
        "    });",
        "",
        "    `$routes->group('goods-receipt', function(`$routes) {",
        "        `$routes->get('/', 'GoodsReceiptController::index');",
        "        `$routes->get('create', 'GoodsReceiptController::create');",
        "        `$routes->post('store', 'GoodsReceiptController::store');",
        "        `$routes->get('(:num)', 'GoodsReceiptController::view/`$1');",
        "        `$routes->get('(:num)/edit', 'GoodsReceiptController::edit/`$1');",
        "        `$routes->post('(:num)/update', 'GoodsReceiptController::update/`$1');",
        "        `$routes->post('(:num)/accept', 'GoodsReceiptController::accept/`$1');",
        "        `$routes->post('(:num)/reject', 'GoodsReceiptController::reject/`$1');",
        "        `$routes->get('purchase-order-items/(:num)', 'GoodsReceiptController::getPurchaseOrderItems/`$1');",
        "    });",
        "",
        "    `$routes->group('quality-inspections', function(`$routes) {",
        "        `$routes->get('/', 'QualityInspectionController::index');",
        "        `$routes->get('create', 'QualityInspectionController::create');",
        "        `$routes->post('store', 'QualityInspectionController::store');",
        "        `$routes->get('(:num)', 'QualityInspectionController::view/`$1');",
        "        `$routes->get('(:num)/edit', 'QualityInspectionController::edit/`$1');",
        "        `$routes->post('(:num)/update', 'QualityInspectionController::update/`$1');",
        "        `$routes->get('(:num)/inspect', 'QualityInspectionController::inspect/`$1');",
        "        `$routes->post('(:num)/complete', 'QualityInspectionController::complete/`$1');",
        "        `$routes->delete('(:num)/delete', 'QualityInspectionController::delete/`$1');",
        "        `$routes->get('my-inspections', 'QualityInspectionController::myInspections');",
        "        `$routes->get('pending-items', 'QualityInspectionController::getPendingItems');",
        "    });",
        "",
        "    `$routes->group('procurement', function(`$routes) {",
        "        `$routes->get('reports', 'ProcurementReportsController::index');",
        "        `$routes->post('reports/generate', 'ProcurementReportsController::generate');",
        "    });"
    )
    
    # Insert the procurement routes
    $newContent = $content[0..($insertLineIndex-1)] + $procurementRoutes + $content[$insertLineIndex..($content.Length-1)]
    
    # Write back to file
    $newContent | Set-Content $filePath
    
    Write-Host "Procurement routes added successfully at line $insertLineIndex"
} else {
    Write-Host "Could not find insertion point for procurement routes"
}
