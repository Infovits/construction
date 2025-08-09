<?php
$file = 'app/Views/milestones/create.php';
$content = file_get_contents($file);

// Find the DOMContentLoaded section and add Select2 initialization
$oldDOMReady = 'document.addEventListener(\'DOMContentLoaded\', function() {
    // Form validation
    const form = document.getElementById(\'milestoneForm\');
    const submitBtn = document.getElementById(\'submitBtn\');';

$newDOMReady = 'document.addEventListener(\'DOMContentLoaded\', function() {
    // Initialize Select2 for dependency milestones
    $(\'#dependency_milestones\').select2({
        placeholder: \'Select milestone dependencies (optional)\',
        allowClear: true,
        multiple: true,
        width: \'100%\',
        theme: \'default\',
        dropdownAutoWidth: true,
        escapeMarkup: function(markup) {
            return markup;
        }
    });

    // Form validation
    const form = document.getElementById(\'milestoneForm\');
    const submitBtn = document.getElementById(\'submitBtn\');';

$content = str_replace($oldDOMReady, $newDOMReady, $content);

file_put_contents($file, $content);
echo "Added Select2 initialization!\n";
?>
