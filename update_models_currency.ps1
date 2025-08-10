# Update models to use MWK currency
$files = @(
    "app\Models\PurchaseOrderModel.php",
    "app\Controllers\PurchaseOrderController.php"
)

foreach ($file in $files) {
    if (Test-Path $file) {
        $content = Get-Content $file -Raw
        $content = $content -replace "'currency' => 'USD'", "'currency' => 'MWK'"
        $content = $content -replace '"currency" => "USD"', '"currency" => "MWK"'
        $content = $content -replace "?? 'USD'", "?? 'MWK'"
        $content | Set-Content $file
        Write-Host "Updated $file"
    }
}

Write-Host "Models updated to use MWK currency"
