# PowerShell script to re-enable CSRF protection
$filePath = "app\Config\Security.php"
$content = Get-Content $filePath

# Replace CSRF protection setting back to cookie
$newContent = @()
foreach ($line in $content) {
    if ($line -match "public string \$csrfProtection = ''; // Temporarily disabled for testing") {
        $newContent += "    public string `$csrfProtection = 'cookie';"
    } else {
        $newContent += $line
    }
}

# Write back to file
$newContent | Set-Content $filePath

Write-Host "CSRF protection re-enabled"
