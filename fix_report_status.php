<?php
// Fix status comparisons in report_results.php

$file = 'D:/Wamp64/www/construction/app/Views/procurement/reports/report_results.php';
$content = file_get_contents($file);

// Fix Material Request status comparisons
$content = str_replace("=== 'Approved'", "=== 'approved'", $content);
$content = str_replace("=== 'Pending'", "=== 'pending_approval'", $content);

// Fix Purchase Order status comparisons  
$content = str_replace("=== 'Completed'", "=== 'completed'", $content);

// Fix Quality Inspection status comparisons (these should stay as they were fixed)
// The 'passed' and 'failed' values are correct

// Write back to file
file_put_contents($file, $content);

echo "Status comparisons fixed successfully!\n";
?>
