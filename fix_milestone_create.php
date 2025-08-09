<?php
// Read the original file
$originalFile = 'app/Views/milestones/create.php';
$content = file_get_contents($originalFile);

// Create the new content with all the necessary changes
$newContent = '<?= $this->extend(\'layouts/main\') ?>

<?= $this->section(\'head\') ?>
<!-- jQuery (required for Select2) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Custom Select2 styling to match Tailwind */
    .select2-container--default .select2-selection--multiple {
        border: 2px solid #d1d5db !important;
        border-radius: 0.5rem !important;
        padding: 0.375rem 0.75rem !important;
        min-height: 2.5rem !important;
        background-color: #ffffff !important;
    }
    
    .select2-container--default .select2-selection--multiple:focus-within {
        border-color: #6366f1 !important;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1) !important;
    }
    
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #6366f1 !important;
        border: 1px solid #6366f1 !important;
        color: white !important;
        border-radius: 0.375rem !important;
        padding: 0.125rem 0.5rem !important;
        margin: 0.125rem !important;
    }
    
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: white !important;
        margin-right: 0.25rem !important;
    }
    
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        color: #fca5a5 !important;
    }
    
    .select2-dropdown {
        border: 2px solid #d1d5db !important;
        border-radius: 0.5rem !important;
    }
    
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #6366f1 !important;
    }
    
    .select2-container {
        width: 100% !important;
    }
</style>
<?= $this->endSection() ?>

';

// Replace the first part of the file
$content = preg_replace('/^<\?= \$this->extend\(\'layouts\/main\'\) \?>\s*<\?= \$this->section\(\'content\'\) \?>/m', $newContent . '<?= $this->section(\'content\') ?>', $content);

// Update the dependencies select element
$content = str_replace(
    '<select id="dependency_milestones" name="dependency_milestones[]" multiple
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <!-- Will be populated dynamically based on project selection -->
                    </select>',
    '<select id="dependency_milestones" name="dependency_milestones[]" multiple class="w-full">
                        <option value="">No Dependencies</option>
                        <!-- Will be populated dynamically based on project selection -->
                    </select>',
    $content
);

// Write the updated content
file_put_contents($originalFile, $content);
echo "Successfully updated milestone create view with Select2!\n";
?>
