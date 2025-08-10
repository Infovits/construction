# PowerShell script to update currency to Kwacha
Write-Host "Updating currency to Kwacha (MWK)..."

# Update MaterialRequestController.php
$filePath = "app\Controllers\MaterialRequestController.php"
if (Test-Path $filePath) {
    $content = Get-Content $filePath -Raw
    
    # Update currency amounts
    $content = $content -replace "1000\.00", "50000.00"
    $content = $content -replace "2500\.00", "125000.00"
    $content = $content -replace "1800\.00", "90000.00"
    $content = $content -replace "3200\.00", "160000.00"
    
    # Update material unit costs
    $content = $content -replace "2\.50", "125.00"
    $content = $content -replace "85\.00", "4250.00"
    $content = $content -replace "12\.00", "600.00"
    $content = $content -replace "25\.00", "1250.00"
    $content = $content -replace "30\.00", "1500.00"
    
    $content | Set-Content $filePath
    Write-Host "Updated MaterialRequestController.php"
}

# Update view files
$indexView = "app\Views\procurement\material_requests\index.php"
if (Test-Path $indexView) {
    $content = Get-Content $indexView -Raw
    $content = $content -replace '\$', 'MWK '
    $content | Set-Content $indexView
    Write-Host "Updated index.php"
}

$createView = "app\Views\procurement\material_requests\create.php"
if (Test-Path $createView) {
    $content = Get-Content $createView -Raw
    $content = $content -replace '\$', 'MWK '
    $content | Set-Content $createView
    Write-Host "Updated create.php"
}

Write-Host "Currency updated to Kwacha successfully!"
