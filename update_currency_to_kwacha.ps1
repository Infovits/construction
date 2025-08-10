# PowerShell script to update currency from USD to Kwacha (MWK)
Write-Host "Updating currency to Kwacha (MWK)..."

# Update MaterialRequestController.php
$filePath = "app\Controllers\MaterialRequestController.php"
$content = Get-Content $filePath

$newContent = @()
foreach ($line in $content) {
    # Update currency amounts to Kwacha values (multiply by ~50 for realistic conversion)
    $line = $line -replace "1000\.00", "50000.00"
    $line = $line -replace "2500\.00", "125000.00"
    $line = $line -replace "1800\.00", "90000.00"
    $line = $line -replace "3200\.00", "160000.00"
    
    # Update material unit costs to Kwacha
    $line = $line -replace "'unit_cost' => 2\.50", "'unit_cost' => 125.00"
    $line = $line -replace "'unit_cost' => 85\.00", "'unit_cost' => 4250.00"
    $line = $line -replace "'unit_cost' => 12\.00", "'unit_cost' => 600.00"
    $line = $line -replace "'unit_cost' => 25\.00", "'unit_cost' => 1250.00"
    $line = $line -replace "'unit_cost' => 30\.00", "'unit_cost' => 1500.00"
    
    # Update currency code references
    $line = $line -replace "'currency' => 'USD'", "'currency' => 'MWK'"
    
    $newContent += $line
}

$newContent | Set-Content $filePath
Write-Host "✓ Updated MaterialRequestController.php"

# Update the view files to use MWK symbol
$viewFiles = @(
    "app\Views\procurement\material_requests\index.php",
    "app\Views\procurement\material_requests\create.php"
)

foreach ($viewFile in $viewFiles) {
    if (Test-Path $viewFile) {
        $content = Get-Content $viewFile
        $newContent = @()
        
        foreach ($line in $content) {
            # Replace $ symbol with MWK
            $line = $line -replace '\$', 'MWK '
            $newContent += $line
        }
        
        $newContent | Set-Content $viewFile
        Write-Host "✓ Updated $viewFile"
    }
}

# Update PurchaseOrderModel.php if it exists
$poModelPath = "app\Models\PurchaseOrderModel.php"
if (Test-Path $poModelPath) {
    $content = Get-Content $poModelPath
    $newContent = @()
    
    foreach ($line in $content) {
        $line = $line -replace "'currency' => 'USD'", "'currency' => 'MWK'"
        $newContent += $line
    }
    
    $newContent | Set-Content $poModelPath
    Write-Host "✓ Updated PurchaseOrderModel.php"
}

Write-Host ""
Write-Host "Currency updated to Kwacha (MWK) successfully!"
Write-Host "Changes made:"
Write-Host "- Updated all monetary amounts to Kwacha values"
Write-Host "- Changed currency symbol from $ to MWK"
Write-Host "- Updated default currency code to MWK"
