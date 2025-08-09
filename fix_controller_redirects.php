<?php
$file = 'app/Controllers/Milestones.php';
$content = file_get_contents($file);

// Fix all redirect URLs to use admin/milestones
$redirects = [
    'return redirect()->to(\'/milestones\')' => 'return redirect()->to(\'/admin/milestones\')',
    'href="<?= base_url(\'milestones\') ?>"' => 'href="<?= base_url(\'admin/milestones\') ?>"',
    'return redirect()->to(\'/milestones/' => 'return redirect()->to(\'/admin/milestones/'
];

foreach ($redirects as $old => $new) {
    $content = str_replace($old, $new, $content);
}

file_put_contents($file, $content);
echo "Fixed controller redirect URLs!\n";
?>
