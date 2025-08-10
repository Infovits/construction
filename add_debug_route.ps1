# PowerShell script to add debug route to Routes.php
$filePath = "app\Config\Routes.php"
$content = Get-Content $filePath

# Find the line after the RouteCollection comment
$insertLineIndex = -1
for ($i = 0; $i -lt $content.Length; $i++) {
    if ($content[$i] -match "^\s*\*/\s*$" -and $content[$i-1] -match "RouteCollection") {
        $insertLineIndex = $i + 1
        break
    }
}

if ($insertLineIndex -gt -1) {
    # Debug route content
    $debugRoute = @(
        "",
        "// Debug route",
        "`$routes->get('debug/check-data', 'DebugController::checkData');"
    )
    
    # Insert the debug route
    $newContent = $content[0..($insertLineIndex-1)] + $debugRoute + $content[$insertLineIndex..($content.Length-1)]
    
    # Write back to file
    $newContent | Set-Content $filePath
    
    Write-Host "Debug route added successfully at line $insertLineIndex"
} else {
    Write-Host "Could not find insertion point for debug route"
}
