# PowerShell script to add CSRF token to index form modals
$filePath = "app\Views\procurement\material_requests\index.php"
$content = Get-Content $filePath

# Find the approval and rejection forms and add CSRF field after form tag
$newContent = @()
foreach ($line in $content) {
    $newContent += $line
    if ($line -match '<form id="approvalForm" method="POST">') {
        $newContent += "                <?= csrf_field() ?>"
    }
    if ($line -match '<form id="rejectionForm" method="POST">') {
        $newContent += "                <?= csrf_field() ?>"
    }
}

# Write back to file
$newContent | Set-Content $filePath

Write-Host "CSRF tokens added to index form modals"
