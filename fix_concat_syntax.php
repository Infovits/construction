<?php

// Fix the CONCAT syntax error in GoodsReceiptNoteModel.php

$file = 'app/Models/GoodsReceiptNoteModel.php';

if (file_exists($file)) {
    echo "Processing file: $file\n";
    
    // Read the file content
    $content = file_get_contents($file);
    
    // Fix the CONCAT syntax - use double quotes or escape the single quotes properly
    $oldConcat = "CONCAT(receiver.first_name, ' ', receiver.last_name) as received_by_name')";
    $newConcat = 'CONCAT(receiver.first_name, " ", receiver.last_name) as received_by_name\')';
    
    // Replace the problematic CONCAT
    $updatedContent = str_replace($oldConcat, $newConcat, $content);
    
    // Check if any changes were made
    if ($content !== $updatedContent) {
        // Write the updated content back to the file
        file_put_contents($file, $updatedContent);
        echo "✅ Fixed CONCAT syntax in $file\n";
    } else {
        echo "ℹ️  First pattern not found, trying alternative fix...\n";
        
        // Alternative approach - use CONCAT with proper escaping
        $pattern = "/CONCAT\(receiver\.first_name, ' ', receiver\.last_name\) as received_by_name'\)/";
        $replacement = 'CONCAT(receiver.first_name, " ", receiver.last_name) as received_by_name\')';
        
        $updatedContent = preg_replace($pattern, $replacement, $content);
        
        if ($content !== $updatedContent) {
            file_put_contents($file, $updatedContent);
            echo "✅ Fixed CONCAT syntax using regex pattern\n";
        } else {
            echo "❌ Could not find the CONCAT pattern to fix\n";
            
            // Show what we're looking for
            echo "Looking for pattern containing: CONCAT(receiver.first_name, ' ', receiver.last_name)\n";
            
            // Try to find any CONCAT in the file
            if (strpos($content, 'CONCAT') !== false) {
                echo "Found CONCAT in file, but pattern doesn't match exactly\n";
                
                // Manual fix - replace the space with double quotes
                $updatedContent = str_replace("CONCAT(receiver.first_name, ' ', receiver.last_name)", 'CONCAT(receiver.first_name, " ", receiver.last_name)', $content);
                
                if ($content !== $updatedContent) {
                    file_put_contents($file, $updatedContent);
                    echo "✅ Fixed CONCAT syntax manually\n";
                } else {
                    echo "❌ Manual fix also failed\n";
                }
            } else {
                echo "No CONCAT found in file\n";
            }
        }
    }
} else {
    echo "❌ File not found: $file\n";
}

echo "\nDone! The CONCAT syntax error should now be fixed.\n";
?>
