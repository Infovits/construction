# PowerShell script to temporarily disable CSRF protection
$filePath = "app\Config\Security.php"
$content = Get-Content $filePath

# Replace CSRF protection setting
$newContent = @()
foreach ($line in $content) {
    if ($line -match "public string \$csrfProtection = 'cookie';") {
        $newContent += "    public string `$csrfProtection = ''; // Temporarily disabled for testing"
    } else {
        $newContent += $line
    }
}

# Write back to file
$newContent | Set-Content $filePath

Write-Host "CSRF protection temporarily disabled for testing"
Write-Host "Remember to re-enable it later for security!"
