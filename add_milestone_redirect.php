<?php
$file = 'app/Config/Routes.php';
$content = file_get_contents($file);

// Add a redirect route for milestones to admin/milestones
$redirectRoute = '
// Redirect milestones to admin/milestones for convenience
$routes->get(\'milestones\', function() {
    return redirect()->to(\'/admin/milestones\');
});
$routes->get(\'milestones/(.*)\', function($path) {
    return redirect()->to(\'/admin/milestones/\' . $path);
});
';

// Find a good place to insert this - before the admin routes
$content = str_replace(
    '// Admin Routes (Protected)',
    $redirectRoute . '// Admin Routes (Protected)',
    $content
);

file_put_contents($file, $content);
echo "Added milestone redirect routes!\n";
?>
