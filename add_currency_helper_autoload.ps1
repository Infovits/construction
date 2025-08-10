# Add currency helper to autoload
$filePath = "app\Config\Autoload.php"
$content = Get-Content $filePath -Raw

# Add currency to the helpers array
$content = $content -replace "public \$helpers = \['utility', 'form', 'url', 'project'\];", "public `$helpers = ['utility', 'form', 'url', 'project', 'currency'];"

$content | Set-Content $filePath

Write-Host "Added currency helper to autoload configuration"
