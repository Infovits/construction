<?php

// Fix the quantity column reference in GoodsReceiptItemModel.php and GoodsReceiptNoteModel.php

$files = [
    'app/Models/GoodsReceiptItemModel.php',
    'app/Models/GoodsReceiptNoteModel.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "Processing file: $file\n";
        
        // Read the file content
        $content = file_get_contents($file);
        
        // Replace purchase_order_items.quantity with purchase_order_items.quantity_ordered
        $updatedContent = str_replace(
            'purchase_order_items.quantity as ordered_quantity',
            'purchase_order_items.quantity_ordered as ordered_quantity',
            $content
        );
        
        // Check if any changes were made
        if ($content !== $updatedContent) {
            // Write the updated content back to the file
            file_put_contents($file, $updatedContent);
            echo "✅ Fixed quantity column reference in $file\n";
        } else {
            echo "ℹ️  No changes needed in $file\n";
        }
    } else {
        echo "❌ File not found: $file\n";
    }
}

echo "\nDone! The quantity column references have been fixed.\n";
echo "The error should now be resolved when accessing goods receipt pages.\n";
?>
