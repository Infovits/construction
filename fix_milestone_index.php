<?php
$file = 'app/Views/milestones/index.php';
$content = file_get_contents($file);

// Replace all instances of 'due_date' with 'planned_end_date'
$content = str_replace("milestone['due_date']", "milestone['planned_end_date']", $content);

file_put_contents($file, $content);
echo "Fixed due_date references in milestone index view!\n";
?>
