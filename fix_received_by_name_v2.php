<?php

// Fix the received_by_name field in GoodsReceiptNoteModel.php

$file = 'app/Models/GoodsReceiptNoteModel.php';

if (file_exists($file)) {
    echo "Processing file: $file\n";
    
    // Read the file content
    $content = file_get_contents($file);
    
    // Find and replace the specific select statement in getGRNWithItems method
    $oldSelect = "receiver.first_name as receiver_first_name,
                receiver.last_name as receiver_last_name')";
    
    $newSelect = "receiver.first_name as receiver_first_name,
                receiver.last_name as receiver_last_name,
                CONCAT(receiver.first_name, ' ', receiver.last_name) as received_by_name')";
    
    // Replace the select statement
    $updatedContent = str_replace($oldSelect, $newSelect, $content);
    
    // Check if any changes were made
    if ($content !== $updatedContent) {
        // Write the updated content back to the file
        file_put_contents($file, $updatedContent);
        echo "✅ Added received_by_name field to $file\n";
    } else {
        echo "ℹ️  No changes needed in $file (pattern not found)\n";
        
        // Let's try a different approach - add the field manually
        $pattern = "/(receiver\.last_name as receiver_last_name')(\s*->join)/";
        $replacement = "receiver.last_name as receiver_last_name,\n                CONCAT(receiver.first_name, ' ', receiver.last_name) as received_by_name')$2";
        
        $updatedContent = preg_replace($pattern, $replacement, $content);
        
        if ($content !== $updatedContent) {
            file_put_contents($file, $updatedContent);
            echo "✅ Added received_by_name field using regex pattern\n";
        } else {
            echo "❌ Could not find the pattern to replace\n";
        }
    }
} else {
    echo "❌ File not found: $file\n";
}

echo "\nDone! The received_by_name field should now be available.\n";
?>
