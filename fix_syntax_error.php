<?php
// Fix the malformed PHP syntax in report_results.php

$file = 'D:/Wamp64/www/construction/app/Views/procurement/reports/report_results.php';
$content = file_get_contents($file);

// Fix the malformed line 175
$malformed = "<?= \$item['trim(((\['requester_first_name'] ?? '') . ' ' . (\['requester_last_name'] ?? ''))) ?: 'N/A''] ?? 'N/A' ?>";
$correct = "<?= trim((\$item['requester_first_name'] ?? '') . ' ' . (\$item['requester_last_name'] ?? '')) ?: 'N/A' ?>";

$content = str_replace($malformed, $correct, $content);

// Write back to file
file_put_contents($file, $content);

echo "Syntax error fixed successfully!\n";
?>
