# PowerShell script to add CSRF token to create form
$filePath = "app\Views\procurement\material_requests\create.php"
$content = Get-Content $filePath

# Find the form opening tag and add CSRF field after it
$newContent = @()
foreach ($line in $content) {
    $newContent += $line
    if ($line -match '<form.*method="POST".*class="p-6 space-y-6">') {
        $newContent += "            <?= csrf_field() ?>"
    }
}

# Write back to file
$newContent | Set-Content $filePath

Write-Host "CSRF token added to create form"
