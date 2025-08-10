<?php

// Fix the item field aliases in GoodsReceiptItemModel.php

$file = 'app/Models/GoodsReceiptItemModel.php';

if (file_exists($file)) {
    echo "Processing file: $file\n";
    
    // Read the file content
    $content = file_get_contents($file);
    
    // Fix the field aliases in the getItemsWithMaterialInfo method
    $oldSelect = "materials.name as material_name,
                materials.item_code,
                materials.unit,
                purchase_order_items.quantity_ordered as ordered_quantity,";
    
    $newSelect = "materials.name as material_name,
                materials.item_code as material_code,
                materials.unit as material_unit,
                purchase_order_items.quantity_ordered as quantity_ordered,";
    
    // Replace the select statement
    $updatedContent = str_replace($oldSelect, $newSelect, $content);
    
    // Check if any changes were made
    if ($content !== $updatedContent) {
        // Write the updated content back to the file
        file_put_contents($file, $updatedContent);
        echo "✅ Fixed item field aliases in $file\n";
    } else {
        echo "ℹ️  No changes needed in $file (pattern not found)\n";
        
        // Try individual replacements
        $changes = 0;
        
        // Fix item_code alias
        if (strpos($content, 'materials.item_code,') !== false) {
            $updatedContent = str_replace('materials.item_code,', 'materials.item_code as material_code,', $content);
            if ($content !== $updatedContent) {
                $content = $updatedContent;
                $changes++;
                echo "  ✅ Fixed item_code alias\n";
            }
        }
        
        // Fix unit alias
        if (strpos($content, 'materials.unit,') !== false) {
            $updatedContent = str_replace('materials.unit,', 'materials.unit as material_unit,', $content);
            if ($content !== $updatedContent) {
                $content = $updatedContent;
                $changes++;
                echo "  ✅ Fixed unit alias\n";
            }
        }
        
        // Fix quantity_ordered alias
        if (strpos($content, 'purchase_order_items.quantity_ordered as ordered_quantity,') !== false) {
            $updatedContent = str_replace('purchase_order_items.quantity_ordered as ordered_quantity,', 'purchase_order_items.quantity_ordered as quantity_ordered,', $content);
            if ($content !== $updatedContent) {
                $content = $updatedContent;
                $changes++;
                echo "  ✅ Fixed quantity_ordered alias\n";
            }
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

echo "\nDone! The GoodsReceiptItemModel field aliases should now match what views expect.\n";
?>
