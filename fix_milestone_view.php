<?php
$file = 'app/Views/milestones/view.php';
$content = file_get_contents($file);

// Replace all instances of 'due_date' with 'planned_end_date' in the view file
$content = str_replace("milestone['due_date']", "milestone['planned_end_date']", $content);
$content = str_replace("dep['due_date']", "dep['planned_end_date']", $content);
$content = str_replace("task['due_date']", "task['planned_end_date']", $content);

file_put_contents($file, $content);
echo "Fixed due_date references in milestone view file!\n";
?>
