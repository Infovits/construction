<?php
$file = 'app/Views/milestones/create.php';
$content = file_get_contents($file);

// Add jQuery before Select2
$oldHead = '<?= $this->section(\'head\') ?>
<!-- Select2 CSS -->';

$newHead = '<?= $this->section(\'head\') ?>
<!-- jQuery (required for Select2) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Select2 CSS -->';

$content = str_replace($oldHead, $newHead, $content);

file_put_contents($file, $content);
echo "Added jQuery dependency!\n";
?>
