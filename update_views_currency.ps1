# Update views to use proper Kwacha formatting
$indexView = "app\Views\procurement\material_requests\index.php"
if (Test-Path $indexView) {
    $content = Get-Content $indexView -Raw
    
    # Replace the currency display line to use proper formatting
    $content = $content -replace 'MWK <?= number_format\(\$request\[''total_estimated_cost''\], 2\) \?>', '<?= format_currency($request[''total_estimated_cost'']) ?>'
    
    # Add helper loading at the top
    $content = "<?php helper('currency'); ?>`n" + $content
    
    $content | Set-Content $indexView
    Write-Host "Updated index view with currency helper"
}

$createView = "app\Views\procurement\material_requests\create.php"
if (Test-Path $createView) {
    $content = Get-Content $createView -Raw
    
    # Replace currency display
    $content = $content -replace 'MWK <span id="totalCost">0\.00</span>', '<span id="totalCost">MWK 0.00</span>'
    
    # Add helper loading at the top
    $content = "<?php helper('currency'); ?>`n" + $content
    
    $content | Set-Content $createView
    Write-Host "Updated create view with currency helper"
}

Write-Host "Views updated with proper Kwacha formatting"
