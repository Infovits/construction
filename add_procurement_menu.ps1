# PowerShell script to add procurement menu to main.php
$filePath = "app\Views\layouts\main.php"
$content = Get-Content $filePath

# Find the line after inventory section ends
$insertLineIndex = -1
for ($i = 0; $i -lt $content.Length; $i++) {
    if ($content[$i] -match "admin/materials/report.*Reports.*</a>") {
        # Look for the closing </div> tags after this line
        for ($j = $i + 1; $j -lt $content.Length; $j++) {
            if ($content[$j] -match "^\s*</div>\s*$" -and $content[$j+1] -match "^\s*$" -and $content[$j+2] -match "^\s*$") {
                $insertLineIndex = $j + 3
                break
            }
        }
        break
    }
}

if ($insertLineIndex -gt -1) {
    # Procurement menu content
    $procurementMenu = @(
        "",
        "                <!-- Procurement Management Section -->",
        "                <div class=`"px-4 md:px-6 py-3 hover:bg-gray-50 transition-colors`">",
        "                    <a href=`"#`" class=`"flex items-center space-x-3 text-gray-600 nav-item relative`" onclick=`"toggleSubmenu(event, 'procurement-submenu')`">",
        "                        <i data-lucide=`"shopping-cart`" class=`"w-5 h-5 flex-shrink-0`"></i>",
        "                        <span class=`"sidebar-text overflow-hidden whitespace-nowrap`">Procurement</span>",
        "                        <i data-lucide=`"chevron-down`" class=`"w-4 h-4 ml-auto sidebar-text menu-chevron`" id=`"procurement-chevron`"></i>",
        "                        <div class=`"tooltip absolute left-16 bg-gray-800 text-white px-2 py-1 rounded text-sm whitespace-nowrap`">",
        "                            Procurement",
        "                        </div>",
        "                    </a>",
        "                    <div class=`"submenu`" id=`"procurement-submenu`">",
        "                        <div class=`"sidebar-text ml-8 mt-2 space-y-1`">",
        "                            <a href=`"<?= base_url('admin/material-requests') ?>`" class=`"block py-2 text-sm text-gray-500 hover:text-gray-700`">Material Requests</a>",
        "                            <a href=`"<?= base_url('admin/purchase-orders') ?>`" class=`"block py-2 text-sm text-gray-500 hover:text-gray-700`">Purchase Orders</a>",
        "                            <a href=`"<?= base_url('admin/goods-receipt') ?>`" class=`"block py-2 text-sm text-gray-500 hover:text-gray-700`">Goods Receipt</a>",
        "                            <a href=`"<?= base_url('admin/quality-inspections') ?>`" class=`"block py-2 text-sm text-gray-500 hover:text-gray-700`">Quality Inspections</a>",
        "                            <a href=`"<?= base_url('admin/procurement/reports') ?>`" class=`"block py-2 text-sm text-gray-500 hover:text-gray-700`">Procurement Reports</a>",
        "                        </div>",
        "                    </div>",
        "                </div>"
    )
    
    # Insert the procurement menu
    $newContent = $content[0..($insertLineIndex-1)] + $procurementMenu + $content[$insertLineIndex..($content.Length-1)]
    
    # Write back to file
    $newContent | Set-Content $filePath
    
    Write-Host "Procurement menu added successfully at line $insertLineIndex"
} else {
    Write-Host "Could not find insertion point for procurement menu"
}
