<?php

// Fix the received_by_name field in GoodsReceiptNoteModel.php

$file = 'app/Models/GoodsReceiptNoteModel.php';

if (file_exists($file)) {
    echo "Processing file: $file\n";
    
    // Read the file content
    $content = file_get_contents($file);
    
    // Find the getGRNWithItems method and update the select statement
    $oldSelect = "->select('goods_receipt_notes.*,
                purchase_orders.po_number,
                suppliers.name as supplier_name,
                suppliers.contact_person,
                suppliers.phone,
                suppliers.email,
                warehouses.name as warehouse_name,
                warehouses.address as warehouse_address,
                receiver.first_name as receiver_first_name,
                receiver.last_name as receiver_last_name')";
    
    $newSelect = "->select('goods_receipt_notes.*,
                purchase_orders.po_number,
                suppliers.name as supplier_name,
                suppliers.contact_person,
                suppliers.phone,
                suppliers.email,
                warehouses.name as warehouse_name,
                warehouses.address as warehouse_address,
                receiver.first_name as receiver_first_name,
                receiver.last_name as receiver_last_name,
                CONCAT(receiver.first_name, \" \", receiver.last_name) as received_by_name')";
    
    // Replace the select statement
    $updatedContent = str_replace($oldSelect, $newSelect, $content);
    
    // Check if any changes were made
    if ($content !== $updatedContent) {
        // Write the updated content back to the file
        file_put_contents($file, $updatedContent);
        echo "✅ Added received_by_name field to $file\n";
    } else {
        echo "ℹ️  No changes needed in $file\n";
    }
} else {
    echo "❌ File not found: $file\n";
}

echo "\nDone! The received_by_name field has been added.\n";
echo "The error should now be resolved when viewing goods receipt details.\n";
?>
