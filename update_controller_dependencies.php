<?php
$file = 'app/Controllers/Milestones.php';
$content = file_get_contents($file);

// Update the store method to handle dependency_milestones
$oldStore = '$data = [
            \'project_id\' => $this->request->getPost(\'project_id\'),
            \'title\' => $this->request->getPost(\'title\'),
            \'description\' => $this->request->getPost(\'description\'),
            \'priority\' => $this->request->getPost(\'priority\'),
            \'status\' => \'not_started\',
            \'planned_start_date\' => $this->request->getPost(\'planned_start_date\') ?: $this->request->getPost(\'planned_end_date\'),
            \'planned_end_date\' => $this->request->getPost(\'planned_end_date\')
        ];';

$newStore = '// Handle dependency milestones
        $dependencyMilestones = $this->request->getPost(\'dependency_milestones\');
        $dependencyNote = \'\';
        if (!empty($dependencyMilestones) && is_array($dependencyMilestones)) {
            // Filter out empty values
            $dependencyMilestones = array_filter($dependencyMilestones);
            if (!empty($dependencyMilestones)) {
                $dependencyNote = \'Dependencies: \' . implode(\', \', $dependencyMilestones);
            }
        }

        $data = [
            \'project_id\' => $this->request->getPost(\'project_id\'),
            \'title\' => $this->request->getPost(\'title\'),
            \'description\' => $this->request->getPost(\'description\'),
            \'priority\' => $this->request->getPost(\'priority\'),
            \'status\' => \'not_started\',
            \'planned_start_date\' => $this->request->getPost(\'planned_start_date\') ?: $this->request->getPost(\'planned_end_date\'),
            \'planned_end_date\' => $this->request->getPost(\'planned_end_date\'),
            \'notes\' => trim(($this->request->getPost(\'notes\') ?: \'\') . \' \' . $dependencyNote)
        ];';

$content = str_replace($oldStore, $newStore, $content);

// Also fix the redirect URL to use admin/milestones
$content = str_replace(
    'return redirect()->to(\'/milestones\')->with(\'success\', \'Milestone created successfully\');',
    'return redirect()->to(\'/admin/milestones\')->with(\'success\', \'Milestone created successfully\');',
    $content
);

file_put_contents($file, $content);
echo "Updated controller to handle dependency milestones!\n";
?>
