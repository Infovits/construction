<?php

// Fix the GRN items variable in GoodsReceiptController.php

$file = 'app/Controllers/GoodsReceiptController.php';

if (file_exists($file)) {
    echo "Processing file: $file\n";
    
    // Read the file content
    $content = file_get_contents($file);
    
    // Find and replace the view method data array
    $oldData = '$data = [
            \'title\' => \'Goods Receipt Note Details\',
            \'grn\' => $grn
        ];';
    
    $newData = '$data = [
            \'title\' => \'Goods Receipt Note Details\',
            \'grn\' => $grn,
            \'grnItems\' => $grn[\'items\'] ?? []
        ];';
    
    // Replace the data array
    $updatedContent = str_replace($oldData, $newData, $content);
    
    // Check if any changes were made
    if ($content !== $updatedContent) {
        // Write the updated content back to the file
        file_put_contents($file, $updatedContent);
        echo "✅ Added grnItems variable to controller data in $file\n";
    } else {
        echo "ℹ️  No changes needed in $file (pattern not found)\n";
        
        // Try a more flexible pattern
        $pattern = "/(\\\$data = \[\s*'title' => 'Goods Receipt Note Details',\s*'grn' => \\\$grn\s*\];)/";
        $replacement = "\$data = [\n            'title' => 'Goods Receipt Note Details',\n            'grn' => \$grn,\n            'grnItems' => \$grn['items'] ?? []\n        ];";
        
        $updatedContent = preg_replace($pattern, $replacement, $content);
        
        if ($content !== $updatedContent) {
            file_put_contents($file, $updatedContent);
            echo "✅ Added grnItems variable using regex pattern\n";
        } else {
            echo "❌ Could not find the pattern to replace\n";
        }
    }
} else {
    echo "❌ File not found: $file\n";
}

echo "\nDone! The grnItems variable should now be available in the view.\n";
?>
