<?php
$file = 'app/Views/milestones/create.php';
$content = file_get_contents($file);

// Find the JS section and add Select2 library
$jsSection = '<?= $this->section(\'js\') ?>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>';

// Replace the existing JS section start
$content = str_replace('<?= $this->section(\'js\') ?>
<script>', $jsSection, $content);

// Update the loadProjectMilestones function to work with Select2
$oldFunction = 'function loadProjectMilestones(projectId) {
    const depSelect = document.getElementById(\'dependency_milestones\');
    depSelect.innerHTML = \'<option value="">Loading...</option>\';

    fetch(\'<?= base_url(\'admin/milestones/getProjectMilestones\') ?>/\' + projectId)
        .then(response => {
            if (!response.ok) {
                throw new Error(\'Network response was not ok\');
            }
            return response.json();
        })
        .then(data => {
            depSelect.innerHTML = \'\';

            if (data.milestones && data.milestones.length > 0) {
                data.milestones.forEach(function(milestone) {
                    // Exclude current milestone from dropdown if editing
                    <?php if (isset($milestone)): ?>
                    if (milestone.id != <?= $milestone[\'id\'] ?>) {
                    <?php endif; ?>
                        var option = document.createElement(\'option\');
                        option.value = milestone.id;
                        option.textContent = milestone.title;
                        depSelect.appendChild(option);
                    <?php if (isset($milestone)): ?>
                    }
                    <?php endif; ?>
                });
            } else {
                depSelect.innerHTML = \'<option value="">No milestones available</option>\';
            }

            // Restore dependency selections if editing
            <?php if (isset($milestone) && isset($milestone_dependencies) && is_array($milestone_dependencies)): ?>
            var selectedDeps = [<?= implode(\',\', array_column($milestone_dependencies, \'id\')) ?>];

            // Convert selectedDeps to strings for comparison
            selectedDeps = selectedDeps.map(String);

            // Set selected options
            Array.from(depSelect.options).forEach(function(option) {
                if (selectedDeps.includes(option.value)) {
                    option.selected = true;
                }
            });
            <?php endif; ?>
        })
        .catch(error => {
            console.error(\'Failed to load project milestones:\', error);
            depSelect.innerHTML = \'<option value="">Error loading milestones</option>\';
        });
}';

$newFunction = 'function loadProjectMilestones(projectId) {
    const depSelect = $(\'#dependency_milestones\');
    
    // Clear existing options except "No Dependencies"
    depSelect.empty().append(\'<option value="">No Dependencies</option>\');
    depSelect.append(\'<option value="" disabled>Loading...</option>\');
    
    // Trigger Select2 update
    depSelect.trigger(\'change\');

    fetch(\'<?= base_url(\'admin/milestones/getProjectMilestones\') ?>/\' + projectId)
        .then(response => {
            if (!response.ok) {
                throw new Error(\'Network response was not ok\');
            }
            return response.json();
        })
        .then(data => {
            // Clear loading option
            depSelect.find(\'option[disabled]\').remove();

            if (data.milestones && data.milestones.length > 0) {
                data.milestones.forEach(function(milestone) {
                    // Exclude current milestone from dropdown if editing
                    <?php if (isset($milestone)): ?>
                    if (milestone.id != <?= $milestone[\'id\'] ?>) {
                    <?php endif; ?>
                        var option = new Option(milestone.title + \' (Due: \' + milestone.planned_end_date + \')\', milestone.id);
                        depSelect.append(option);
                    <?php if (isset($milestone)): ?>
                    }
                    <?php endif; ?>
                });
            } else {
                depSelect.append(\'<option value="" disabled>No milestones available</option>\');
            }

            // Restore dependency selections if editing
            <?php if (isset($milestone) && isset($milestone_dependencies) && is_array($milestone_dependencies)): ?>
            var selectedDeps = [<?= implode(\',\', array_column($milestone_dependencies, \'id\')) ?>];
            depSelect.val(selectedDeps);
            <?php endif; ?>
            
            // Trigger Select2 update
            depSelect.trigger(\'change\');
        })
        .catch(error => {
            console.error(\'Failed to load project milestones:\', error);
            depSelect.find(\'option[disabled]\').remove();
            depSelect.append(\'<option value="" disabled>Error loading milestones</option>\');
            depSelect.trigger(\'change\');
        });
}';

$content = str_replace($oldFunction, $newFunction, $content);

file_put_contents($file, $content);
echo "Updated JavaScript section with Select2 support!\n";
?>
