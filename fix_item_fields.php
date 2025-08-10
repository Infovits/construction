<?php

// Fix the item field aliases in GoodsReceiptNoteModel.php

$file = 'app/Models/GoodsReceiptNoteModel.php';

if (file_exists($file)) {
    echo "Processing file: $file\n";
    
    // Read the file content
    $content = file_get_contents($file);
    
    // Fix the field aliases in the items query
    $oldSelect = "->select('goods_receipt_items.*, 
                materials.name as material_name,
                materials.item_code,
                materials.unit,
                purchase_order_items.quantity_ordered as ordered_quantity,
                purchase_order_items.unit_cost as ordered_unit_cost')";
    
    $newSelect = "->select('goods_receipt_items.*, 
                materials.name as material_name,
                materials.item_code as material_code,
                materials.unit as material_unit,
                purchase_order_items.quantity_ordered as quantity_ordered,
                purchase_order_items.unit_cost as ordered_unit_cost')";
    
    // Replace the select statement
    $updatedContent = str_replace($oldSelect, $newSelect, $content);
    
    // Check if any changes were made
    if ($content !== $updatedContent) {
        // Write the updated content back to the file
        file_put_contents($file, $updatedContent);
        echo "✅ Fixed item field aliases in $file\n";
    } else {
        echo "ℹ️  No changes needed in $file (pattern not found)\n";
        echo "Trying alternative patterns...\n";
        
        // Try individual replacements
        $changes = 0;
        
        // Fix item_code alias
        $pattern1 = "/materials\.item_code(?![\w])/";
        $replacement1 = "materials.item_code as material_code";
        $updatedContent = preg_replace($pattern1, $replacement1, $content);
        if ($content !== $updatedContent) {
            $content = $updatedContent;
            $changes++;
            echo "  ✅ Fixed item_code alias\n";
        }
        
        // Fix unit alias
        $pattern2 = "/materials\.unit(?![\w])/";
        $replacement2 = "materials.unit as material_unit";
        $updatedContent = preg_replace($pattern2, $replacement2, $content);
        if ($content !== $updatedContent) {
            $content = $updatedContent;
            $changes++;
            echo "  ✅ Fixed unit alias\n";
        }
        
        // Fix quantity_ordered alias
        $pattern3 = "/purchase_order_items\.quantity_ordered as ordered_quantity/";
        $replacement3 = "purchase_order_items.quantity_ordered as quantity_ordered";
        $updatedContent = preg_replace($pattern3, $replacement3, $content);
        if ($content !== $updatedContent) {
            $content = $updatedContent;
            $changes++;
            echo "  ✅ Fixed quantity_ordered alias\n";
        }
        
        if ($changes > 0) {
            file_put_contents($file, $content);
            echo "✅ Applied $changes field alias fixes\n";
        } else {
            echo "❌ Could not find any patterns to replace\n";
        }
    }
} else {
    echo "❌ File not found: $file\n";
}

echo "\nDone! The item field aliases should now match what the view expects.\n";
?>
