<?php

$storage = array();

if (isset($_POST['generate'])) {
    // Validate and sanitize user inputs
    $table_name = $_POST['table_name'];
    $controller = $_POST['controller'];
    $model = $_POST['model'];

    if ($table_name !== '') {
        // Set data

        $c = $controller !== '' ? ucfirst($controller) : ucfirst($table_name);
        $m = $model !== '' ? ucfirst($model) : ucfirst($table_name) . '_model';
        $v_list = $table_name . "_list";
        $v_read = $table_name . "_read";
        $v_form = $table_name . "_form";

        // URL
        $c_url = strtolower($c);

        // Filename
        $c_file = $c . '.php';
        $m_file = $m . '.php';
        $v_list_file = $v_list . '.php';
        $v_read_file = $v_read . '.php';
        $v_form_file = $v_form . '.php';
        $v_pdf_file =  'pdf_export.php';
        $v_word_file =  'word_export.php';
        $excelexport_lib_file = 'ExcelExport.php';
        $pdfexport_lib_file = 'MpdfWrapper.php';
        $tingala_pagination_file = 'tingala_pagination.php';
        // Read setting
        

        $pk = $tingala->primary_field($table_name);
        $non_pk = $tingala->not_primary_field($table_name);
        $all = $tingala->all_field($table_name);

        // Generate
        // $configPagination = createConfigPagination($table_name, $c);
        $controller_content = createController($table_name, $c, $m, $pk, $non_pk);
        if($controller_content){
            createControllerWithRoutes($table_name, $c, $m, $pk, $non_pk);
            addCustomTingalaPaginationConfig();
        }

        $modelContent = createModel($table_name, $c, $pk, $non_pk, $all);
        $viewListContent = createViewList($table_name, $c, $pk, $non_pk);
        $viewPdfContent = createPdfViewList($table_name, $c, $pk, $non_pk);
        $viewFormContent = createViewForm($table_name, $c, $pk, $non_pk);
        $viewReadContent = createViewRead($table_name, $c, $pk, $non_pk);
        $WordExportContent = createWordViewList($table_name, $c, $pk, $non_pk);
        $TingalaPaginationContent = createTingalaPagination($table_name, $c, $pk, $non_pk);
        $ExcelExportLibContent = createExcelExportLib();
        $PdfExportLibContent = createPdfExportLib();


        // Save content to storage
         $storage['Controller'] = '/app/Controllers/'.$c_file;
         $storage['Model'] = '/app/Models/'.$m_file;
         $storage['ViewList'] = '/app/Views/'.$table_name.'/'.$v_list_file;
         $storage['ViewForm'] = '/app/Views/'.$table_name.'/'.$v_form_file;
         $storage['ViewRead'] = '/app/Views/'.$table_name.'/'.$v_read_file;
         $storage['ViewPdf'] = '/app/Views/'.$table_name.'/'.$v_pdf_file;
         $storage['ViewWord'] = '/app/Views/'.$table_name.'/'.$v_word_file;


        // Optionally, save content to files or download
        
        saveToFile( $m_file, $modelContent,'/app/Models/');
        saveToFile($c_file, $controller_content,'/app/Controllers/');
        saveToFile($v_list_file, $viewListContent,'/app/Views/'.$table_name.'/');
        saveToFile($v_form_file, $viewFormContent,'/app/Views/'.$table_name.'/');
        saveToFile($v_read_file, $viewReadContent,'/app/Views/'.$table_name.'/');
        saveToFile($v_pdf_file, $viewPdfContent,'/app/Views/'.$table_name.'/');
        saveToFile($v_word_file, $WordExportContent,'/app/Views/'.$table_name.'/');
        saveToFile($tingala_pagination_file, $TingalaPaginationContent, '/app/Views/layouts/');
        saveToFile($excelexport_lib_file, $ExcelExportLibContent,'/app/Libraries/');
        saveToFile($pdfexport_lib_file, $PdfExportLibContent,'/app/Libraries/');



    } else {
        $storage[] = 'No table selected.';
    }
}

if (isset($_POST['generateall'])) {


    $table_list = $tingala->table_list();
    addCustomTingalaPaginationConfig();
    foreach ($table_list as $row) {
        // Validate and sanitize user inputs


        $table_name = $row['table_name'];


        $model = $_POST['model'];
        if ($table_name !== '') {
            // Set data

            $c = ucfirst($table_name);
            $m = ucfirst($table_name) . '_model';
            $v_list = $table_name . "_list";
            $v_read = $table_name . "_read";
            $v_form = $table_name . "_form";

            // URL
            $c_url = strtolower($c);

            // Filename
            $c_file = $c . '.php';
            $m_file = $m . '.php';
            $v_list_file = $v_list . '.php';
            $v_read_file = $v_read . '.php';
            $v_form_file = $v_form . '.php';
            $v_pdf_file = 'pdf_export.php';
            $v_word_file = 'word_export.php';
            $excelexport_lib_file = 'ExcelExport.php';
            $pdfexport_lib_file = 'MpdfWrapper.php';
            $tingala_pagination_file = 'tingala_pagination.php';


            // Read setting


            $pk = $tingala->primary_field($table_name);
            $non_pk = $tingala->not_primary_field($table_name);
            $all = $tingala->all_field($table_name);

            // Generate
            // $configPagination = createConfigPagination($table_name, $c);
            $controller_content = createController($table_name, $c, $m, $pk, $non_pk);
            if ($controller_content) {
                createControllerWithRoutes($table_name, $c, $m, $pk, $non_pk);
            }

            $modelContent = createModel($table_name, $c, $pk, $non_pk, $all);
            $viewListContent = createViewList($table_name, $c, $pk, $non_pk);
            $viewPdfContent = createPdfViewList($table_name, $c, $pk, $non_pk);
            $viewFormContent = createViewForm($table_name, $c, $pk, $non_pk);
            $viewReadContent = createViewRead($table_name, $c, $pk, $non_pk);
            $WordExportContent = createWordViewList($table_name, $c, $pk, $non_pk);
            $ExcelExportLibContent = createExcelExportLib();
            $PdfExportLibContent = createPdfExportLib();
            $TingalaPaginationContent = createTingalaPagination($table_name, $c, $pk, $non_pk);


            $storage['Controller'] = 'CRUD has been generated please your Model view controller to verify ';


            // Optionally, save content to files or download

            saveToFile($m_file, $modelContent, '/app/Models/');
            saveToFile($c_file, $controller_content, '/app/Controllers/');
            saveToFile($v_list_file, $viewListContent, '/app/Views/' . $table_name . '/');
            saveToFile($v_form_file, $viewFormContent, '/app/Views/' . $table_name . '/');
            saveToFile($v_read_file, $viewReadContent, '/app/Views/' . $table_name . '/');
            saveToFile($v_pdf_file, $viewPdfContent, '/app/Views/' . $table_name . '/');
            saveToFile($v_word_file, $WordExportContent, '/app/Views/' . $table_name . '/');
            saveToFile($tingala_pagination_file, $TingalaPaginationContent, '/app/Views/layouts/');
            saveToFile($excelexport_lib_file, $ExcelExportLibContent, '/app/Libraries/');
            saveToFile($pdfexport_lib_file, $PdfExportLibContent, '/app/Libraries/');


        } else {
            $storage[] = 'No table selected.';
        }
    }
}

// Helper function to save content to a file
// Helper function to determine the root path of the CodeIgniter 4 project
function findCodeIgniterRoot()
{
    $currentDirectory = __DIR__;

    // Navigate up the directory structure until you find a known CodeIgniter file or folder
    while (!file_exists($currentDirectory . '/public/index.php') && $currentDirectory !== '/') {
        $currentDirectory = dirname($currentDirectory);
    }

    return $currentDirectory;
}

// Helper function to save content to a file
function saveToFile($filePath, $content,$save_path)
{
    // Find the root path of the CodeIgniter 4 project
    $rootPath = findCodeIgniterRoot();

    // Specify the path to the Models folder
    $modelsFolderPath = $rootPath .$save_path;

    // Check if the Models folder exists, create it if not
    if (!is_dir($modelsFolderPath)) {
        mkdir($modelsFolderPath, 0777, true);
    }

    // Save the file to the Models folder
    file_put_contents($modelsFolderPath . $filePath, $content);
}




function createModel($table_name, $controller_name, $primary_key, $non_primary_keys)
{
    $model_content = "<?php namespace App\Models;

use CodeIgniter\Model;

class {$controller_name}_Model extends Model
{
    protected \$table = '{$table_name}';
    protected \$primaryKey = '{$primary_key}';
    protected \$allowedFields = [";

    // List of allowed fields
    foreach ($non_primary_keys as $field) {
        $model_content .= "'{$field['column_name']}', ";
    }

    $model_content = rtrim($model_content, ', ');

    $model_content .= "];

    // Insert data
    public function insertData(\$data)
    {
        return \$this->insert(\$data);
    }

    // Get all data
    public function getAllData(\$perPage = 10, \$page = 1)
    {
        return \$this->paginate(\$perPage, 'group1', \$page);
    }
public function getAll(\$search)
    {
if(\$search){
            \$search = \$search;
        }else{
            \$search = '';
        }
        return \$this";
    $count = 1;
    foreach ($non_primary_keys as $field) {
        $columnName = $field['column_name'];

        if($count == 1){
            $model_content .= "->like('{$columnName}', \$search)". PHP_EOL;
        }
        $model_content .= "->orLike('{$columnName}', \$search)". PHP_EOL;
        $count ++;
    }

    $model_content .="->findAll();
    }
    // Get single data by primary key
    public function getSingleData(\$id)
    {
        return \$this->find(\$id);
    }

    // Update data by primary key
    public function updateData(\$id, \$data)
    {
        return \$this->update(\$id, \$data);
    }

    // Delete data by primary key
    public function deleteData(\$id)
    {
        return \$this->delete(\$id);
    }
}";

    return $model_content;
}
//create phpoffice/phpspreadsheet  library
function createExcelExportLib (){
    $exelexportcontent = "";
    $exelexportcontent .="<?php
namespace App\Libraries;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelExport
{
    protected \$data;

    public function __construct(\$data)
    {
        \$this->data = \$data;
    }

    public function export()
    {
        \$spreadsheet = new Spreadsheet();
        \$sheet = \$spreadsheet->getActiveSheet();

        // Add column titles as the first row
        \$column = 'A';
        foreach (\$this->data[0] as \$field => \$value) {
            \$sheet->setCellValue(\$column . '1', \$field);
            \$column++;
        }

        // Add data to the sheet
        \$row = 2;
        foreach (\$this->data as \$record) {
            \$column = 'A';
            foreach (\$record as \$value) {
                \$sheet->setCellValue(\$column . \$row, \$value);
                \$column++;
            }
            \$row++;
        }

        // Create a writer and get the file content
        \$writer = new Xlsx(\$spreadsheet);
        ob_start();
        \$writer->save('php://output');
        \$fileContent = ob_get_clean();

        return \$fileContent;
    }
#This code is generated by Tingala CRUD generator

}
    
    ";
    return $exelexportcontent;

}
function createPdfExportLib(){
    $contents = "";
    $contents .="<?php 
namespace App\Libraries;

use Mpdf\Mpdf;

class MpdfWrapper
{
    protected \$mpdf;

    public function __construct()
    {
        // Initialize mPDF
        \$this->mpdf = new Mpdf();
    }

    public function generatePdf(\$htmlContent, \$filename = 'output.pdf')
    {
        // Add the HTML content to mPDF
        \$this->mpdf->WriteHTML(\$htmlContent);

        // Output the PDF
        \$this->mpdf->Output(\$filename, 'D');
    }
}

    
    ";
    return $contents;
}
// create controller
// Helper function to create a CodeIgniter 4 controller
function createController($table_name, $controller_name, $model_name, $primary_key, $non_primary_keys)
{
    $controller_content = "<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\\{$model_name};
use App\Libraries\ExcelExport; // Import the ExcelExport class library that Tingala CI4 CRUD generator created
use App\Libraries\MpdfWrapper; // Import the PdfExport class library that Tingala CI4 CRUD generator created

class {$controller_name} extends Controller
{
    protected \$model;

    public function __construct()
    {
        \$this->model = new {$model_name}();
        helper('form');
    }

    // Index method (display all records)
    public function index()
    {
     \$export =\$this->request->getVar('export');
     \$search =\$this->request->getVar('search');
    if(\$export=='Export pdf'){
    \$this->exportpdf(\$search);
    }
    elseif(\$export=='Export excel')
        {
        \$this->exportexcel(\$search);
        }elseif(\$export=='Export word'){
          return redirect()->to('/{$table_name}/exportWord?search='.\$search);
        }else{
        \$perPage = 10; // Adjust the number of records per page
        \$page = (\$this->request->getVar('page')) ? \$this->request->getVar('page') : 1;
      
        if(\$search){
            \$search = \$search;
        }else{
            \$search = '';
        }
        

        \$data['page'] = \$page;
        \$data['perPage'] = 10;
        \$data['total'] = \$this->model
        ";
        $countt = 1;
        foreach ($non_primary_keys as $field) {
            $columnName = $field['column_name'];
            
    if($countt == 1){
        $controller_content .= "->like('{$columnName}', \$search)". PHP_EOL;
    }
            $controller_content .= "->orLike('{$columnName}', \$search)". PHP_EOL;
            $countt ++;
        }
     $controller_content .="->countAll();
        \$data['records'] = \$this->model";
        $count = 1;
        foreach ($non_primary_keys as $field) {
            $columnName = $field['column_name'];
            
    if($count == 1){
        $controller_content .= "->like('{$columnName}', \$search)". PHP_EOL;
    }
            $controller_content .= "->orLike('{$columnName}', \$search)". PHP_EOL;
            $count ++;
        }
        $controller_content .= "->paginate(\$data['perPage']);
        \$pager = \$this->model->pager;

        // Load the custom pagination view
        // \$pager->setSurroundCount(1); // Customize the number of links each side of the current page
        \$data['pager_links'] = \$pager->makeLinks(\$page, \$perPage, \$data['total'], 'custom_view');

        return view('{$table_name}/{$table_name}_list', \$data);
        }
    }


    // Show method (display a single record)
    public function show(\$id)
    {
        \$data['record'] = \$this->model->getSingleData(\$id);
        return view('{$table_name}/{$table_name}_read', \$data);
    }

    // Create method (show form to create a new record)
    public function create()
    {
        \$data = [
            'title' => 'Create {$table_name}',
            'action' => 'store',
            'record' => [],
        ];

        return view('{$table_name}/{$table_name}_form', \$data);
    }

    // Store method (save a new record)
    public function store()
    {
        \$data = \$this->request->getPost(); // Retrieve form data
        \$validation = \Config\Services::validation();
    
        // Set validation rules
        \$validation->setRules(\$this->_rules());
    
        if (\$validation->withRequest(\$this->request)->run())
        {
            // Data is valid, save the record
            \$this->model->insertData(\$data);
    
            // Redirect to the index page after storing the record
            return redirect()->to('/{$table_name}');
        }
        else
        {
            // Validation failed, show the form with errors
            return view('{$table_name}/{$table_name}_form', ['validation' => \$validation, 'data' => \$data, 'action' => 'store', 'title' => 'Create {$table_name}']);
        }
    }

    // Edit method (show form to edit a record)
    public function edit(\$id)
    {
        \$data['record'] = \$this->model->getSingleData(\$id);
        \$data['title'] = 'Edit {$table_name}';
        \$data['action'] = 'update';

        return view('{$table_name}/{$table_name}_form', \$data);
    }

    // Update method (save edited record)
    public function update()
{
    \$data = \$this->request->getPost(); // Retrieve form data
    \$validation = \Config\Services::validation();

    // Set validation rules
    \$validation->setRules(\$this->_rules());

    if (\$validation->withRequest(\$this->request)->run())
    {
        // Data is valid, update the record
        \$id = \$data['{$primary_key}'];
        \$this->model->updateData(\$id, \$data);

        // Redirect to the index page after updating the record
        return redirect()->to('/{$table_name}');
    }
    else
    {
        // Validation failed, show the form with errors
        return view('{$table_name}/{$table_name}_form', ['validation' => \$validation, 'data' => \$data, 'action' => 'update', 'title' => 'Edit {$table_name}']);
    }
}

    // Destroy method (delete a record)
    public function destroy(\$id)
    {
        // Delete the record
        \$this->model->deleteData(\$id);

        // Redirect to the index page after deleting the record
        return redirect()->to('/{$table_name}');
    }
    protected function _rules()
    {
        \$validationRules = [];";

    foreach ($non_primary_keys as $field) {
        $columnName = $field['column_name'];
        $dataType = $field['data_type'];
        
        $int = $dataType == 'int' || $dataType == 'double' || $dataType == 'decimal' ? '|numeric' : '';
        $date = $dataType == 'date' ? '|valid_date' : '';

        $controller_content .= "\n        \$validationRules['{$columnName}'] = 'required{$int}{$date}';";
    }

    $controller_content .= "\n\n        return \$validationRules;
    }
       // Export method (export records)
    public function exportexcel(\$search )
    {
     
        \$data = \$this->model->getAll(\$search);
        \$export = new ExcelExport(\$data);
        \$date = date('Y-m-d H:i:s');

        // Specify the filename for the Excel file
        \$filename = '{$table_name}_export_'.\$date.'.xlsx';

        // Export data to Excel and get the file content
        \$fileContent = \$export->export();

        // Set appropriate headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=\\\"' . \$filename . '\\\"');
       
        header('Cache-Control: max-age=0');

        // Output the file content
        echo \$fileContent;

        // Stop further script execution
        exit();
    }
    function exportpdf(\$search )
    {
    
        \$data = \$this->model->getAll(\$search);

        ini_set('memory_limit', '32M');
        // Load your renamed mPDF library
        \$mpdfLib = new MpdfWrapper(); // Update the instantiation
 \$filename = '{$table_name}.pdf';
        // Your HTML content with a table

        \$html = view('{$table_name}/pdf_export', ['records' => \$data]);
        // Add the HTML content to mPDF

        \$mpdfLib->generatePdf(\$html, \$filename);
        // Output the PDF

    }
     public function exportWord()
    {
        \$search =\$this->request->getVar('search');
        \$data = \$this->model->getAll(\$search);
        \$filename = '{$table_name}.doc';

        \$content = view('{$table_name}/word_export', ['records' => \$data]);

        return \$this->response
            ->setStatusCode(200)
            ->setContentType('application/msword')
            ->setHeader('Content-Disposition', 'attachment;filename=\\\"' . \$filename . '\\\"')
            ->setBody(\$content);
    }
}";

    return $controller_content;
}


// Helper function to create a CodeIgniter 4 controller file with routes
function createControllerWithRoutes($table_name, $controller_name, $model_name, $primary_key, $non_primary_keys)
{
   // Specify the path to the Routes.php file
$save_path = '/app/Config/Routes.php';
$rootPath = findCodeIgniterRoot();
$fullPath = $rootPath . $save_path;

// Load the existing routes content
$existing_routes_content = file_get_contents($fullPath);

// Generate the new routes content
$new_routes_content = "//routes for {$table_name} Controllers Code generated by Tingala crud generator https://infocustech-mw.com ". PHP_EOL;
$new_routes_content .= "\$routes->get('{$table_name}', '{$controller_name}::index');" . PHP_EOL;
$new_routes_content .= "\$routes->get('{$table_name}/show/(:num)', '{$controller_name}::show/$1');" . PHP_EOL;
$new_routes_content .= "\$routes->get('{$table_name}/create', '{$controller_name}::create');" . PHP_EOL;
$new_routes_content .= "\$routes->post('{$table_name}/store', '{$controller_name}::store');" . PHP_EOL;
$new_routes_content .= "\$routes->get('{$table_name}/edit/(:num)', '{$controller_name}::edit/$1');" . PHP_EOL;
$new_routes_content .= "\$routes->post('{$table_name}/update', '{$controller_name}::update');" . PHP_EOL;
$new_routes_content .= "\$routes->get('{$table_name}/destroy/(:num)', '{$controller_name}::destroy/$1');" . PHP_EOL;
$new_routes_content .= "\$routes->get('{$table_name}/exportexcel', '{$controller_name}::exportexcel');" . PHP_EOL;
$new_routes_content .= "\$routes->get('{$table_name}/exportpdf', '{$controller_name}::exportpdf');" . PHP_EOL;
$new_routes_content .= "\$routes->get('{$table_name}/exportWord', '{$controller_name}::exportWord');" . PHP_EOL;

// Append the new routes content to the existing routes content
$new_routes_content = $existing_routes_content . PHP_EOL . PHP_EOL . $new_routes_content;

// Write the combined content back to the Routes.php file
file_put_contents($fullPath, $new_routes_content);

}
function addCustomTingalaPaginationConfig(){

    $save_path = '/app/Config/pager.php';
    $rootPath = findCodeIgniterRoot();
    $fullPath = $rootPath . $save_path;
// Read the content of Pager.php
    $configContent = file_get_contents($fullPath);



    if (strpos($configContent, "'custom_view' => 'App\\Views\\layouts\\tingala_pagination'") === false) {
        // Modify the $templates array
        $replacement = "'custom_view' => 'App\\Views\\layouts\\tingala_pagination',";
        $pattern = "/'default_head'\s+=>\s+'CodeIgniter\\\\Pager\\\\Views\\\\default_head',/";
        $configContent = preg_replace($pattern, "$0\n\t$replacement", $configContent);

        // Write the modified content back to Pager.php
        file_put_contents($fullPath, $configContent);

    } else {

    }
}
// Helper function to create a CodeIgniter 4 view file for listing records with create link button and search
function createViewList($table_name, $controller_name, $primary_key, $non_primary_keys)
{
    moveBootstrapCssToPublic();
    $controller_name = strtolower($controller_name);

    // Generate the table header
    $table_header = '<tr>' . PHP_EOL;
    foreach ($non_primary_keys as $field) {
        $table_header .= '<th>' . $field['column_name'] . '</th>' . PHP_EOL;
    }
    $table_header .= '<th>Action</th></tr>' . PHP_EOL;

    // Generate the table rows
    $table_rows = '<?php foreach ($records as $record): ?>' . PHP_EOL;
    $table_rows .= '    <tr>' . PHP_EOL;
    foreach ($non_primary_keys as $field) {
        $table_rows .= '        <td><?= $record[\'' . $field['column_name'] . '\'] ?></td>' . PHP_EOL;
    }
    $table_rows .= '        <td>' . PHP_EOL;
    $table_rows .= '            <a class="btn btn-sm btn-info" href="<?= site_url(\'' . $controller_name . '/show/\' . $record[\'' . $primary_key . '\']) ?>">View</a>' . PHP_EOL;
    $table_rows .= ' | ';
    $table_rows .= '            <a class="btn btn-sm btn-warning" href="<?= site_url(\'' . $controller_name . '/edit/\' . $record[\'' . $primary_key . '\']) ?>">Edit</a>' . PHP_EOL;
    $table_rows .= ' | ';
    $table_rows .= '            <a class="btn btn-sm btn-danger" href="<?= site_url(\'' . $controller_name . '/destroy/\' . $record[\'' . $primary_key . '\']) ?>" onclick="return confirm(\'Are you sure you want to delete this record?\')">Delete</a>' . PHP_EOL;
    $table_rows .= '        </td>' . PHP_EOL;
    $table_rows .= '    </tr>' . PHP_EOL;
    $table_rows .= '<?php endforeach; ?>' . PHP_EOL;

    // Generate the pagination links
    $pagination_links = '<div class="pagination-container"><?= $pager_links ?></div>' . PHP_EOL;

    // Generate the entire view content
    $view_content = '<link rel="stylesheet" type="text/css" href="<?= base_url(\'tingala_assets/css/bootstrap.min.css\') ?>">' . PHP_EOL;

    $view_content .= '<h2>' . ucfirst($table_name) . ' List</h2>' . PHP_EOL;
    $view_content .= '<p><a href="<?= site_url(\'' . $controller_name . '/create\') ?>" class="btn btn-sm btn-secondary">Add New</a></p>' . PHP_EOL;
    $view_content .= '<form action="<?= site_url(\'' . $controller_name . '\') ?>" method="get">' . PHP_EOL;
    $view_content .= '    <label for="search">Search:</label>' . PHP_EOL;
    $view_content .= '    <input type="text" name="search" id="search" value="<?= isset($_GET[\'search\']) ? htmlspecialchars($_GET[\'search\'], ENT_QUOTES, \'UTF-8\') : \'\' ?>">' . PHP_EOL;

     $view_content .= '    <input type="submit" name ="export" value="Search" class="btn btn-sm btn-info">' . PHP_EOL;
     $view_content .= '    <?php if(isset($_GET[\'search\'])): ?>' . PHP_EOL;
        $view_content .= '        <a href="<?= site_url(\'' . $controller_name . '\') ?>" class="btn btn-sm btn-secondary">Reset</a>' . PHP_EOL;
        $view_content .= '    <?php endif; ?>' . PHP_EOL;
    $view_content .= '        <input type="submit" name ="export" value="Export excel" class="btn btn-sm btn-success">' . PHP_EOL;
    $view_content .= '        <input type="submit" name = "export" value="Export pdf" class="btn btn-sm btn-danger">' . PHP_EOL;
    $view_content .= '        <input type="submit" name = "export" value="Export word" class="btn btn-sm btn-primary">' . PHP_EOL;
    $view_content .= '</form>' . PHP_EOL;
    $view_content .= '<br>' . PHP_EOL;
    $view_content .= '<table class="table table-striped">' . PHP_EOL;
    $view_content .= '    ' . $table_header;
    $view_content .= '    ' . $table_rows;
    $view_content .= '</table>' . PHP_EOL;
    $view_content .= $pagination_links; // Add pagination links

    return $view_content;
}

function createPdfViewList($table_name, $controller_name, $primary_key, $non_primary_keys){


    // Generate the table header
    $table_header = '<tr>' . PHP_EOL;
    foreach ($non_primary_keys as $field) {
        $table_header .= '<th>' . $field['column_name'] . '</th>' . PHP_EOL;
    }


    // Generate the table rows
    $table_rows = '<?php foreach ($records as $record): ?>' . PHP_EOL;
    $table_rows .= '    <tr>' . PHP_EOL;
    foreach ($non_primary_keys as $field) {
        $table_rows .= '        <td><?= $record[\'' . $field['column_name'] . '\'] ?></td>' . PHP_EOL;
    }

    $table_rows .= '<?php endforeach; ?>' . PHP_EOL;



    // Generate the entire view content
    $view_content = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN""http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"><head>
     <title>'.$table_name.'Data PDF Export</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {width:100%;}
        table.collapse {
            border-collapse: collapse;
        }

        tr td, tr th {
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>' . PHP_EOL;

    $view_content .= '<h2>' . ucfirst($table_name) . ' Information</h2>' . PHP_EOL;

    $view_content .= '<br>' . PHP_EOL;
    $view_content .= '<table>' . PHP_EOL;
    $view_content .= '    ' . $table_header;
    $view_content .= '    ' . $table_rows;
    $view_content .= '</table>' . PHP_EOL;
    $view_content .= '
</body>
</html>
' . PHP_EOL;


    return $view_content;
}
function createWordViewList($table_name, $controller_name, $primary_key, $non_primary_keys){


    // Generate the table header
    $table_header = '<tr>' . PHP_EOL;
    foreach ($non_primary_keys as $field) {
        $table_header .= '<th>' . $field['column_name'] . '</th>' . PHP_EOL;
    }


    // Generate the table rows
    $table_rows = '<?php foreach ($records as $record): ?>' . PHP_EOL;
    $table_rows .= '    <tr>' . PHP_EOL;
    foreach ($non_primary_keys as $field) {
        $table_rows .= '        <td><?= $record[\'' . $field['column_name'] . '\'] ?></td>' . PHP_EOL;
    }

    $table_rows .= '<?php endforeach; ?>' . PHP_EOL;



    // Generate the entire view content
    $view_content = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN""http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"><head>
    <title>'.$table_name.'Data Word Export</title>
   <link rel="stylesheet" type="text/css" href="<?= base_url(\'tingala_assets/css/bootstrap.min.css\') ?>">
    <style>
        .word-table {
            border:1px solid black !important;
            border-collapse: collapse !important;
            width: 100%;
        }
        .word-table tr th, .word-table tr td{
            border:1px solid black !important;
            padding: 5px 10px;
        }
    </style>
</head>
<body>' . PHP_EOL;

    $view_content .= '<h2>' . ucfirst($table_name) . ' Information</h2>' . PHP_EOL;

    $view_content .= '<br>' . PHP_EOL;
    $view_content .= '<table class="word-table">' . PHP_EOL;
    $view_content .= '    ' . $table_header;
    $view_content .= '    ' . $table_rows;
    $view_content .= '</table>' . PHP_EOL;
    $view_content .= '
</body>
</html>
' . PHP_EOL;


    return $view_content;
}
function moveBootstrapCssToPublic() {
    $sourcePath = 'core/bootstrap.min.css';  // Replace with the actual path


    $targetPath = '/public/tingala_assets/css';
    $rootPath = findCodeIgniterRoot();
    $targetDirectory = $rootPath . $targetPath;
   
    if (!is_dir($targetDirectory)) {
        if (!mkdir($targetDirectory, 0777, true)) {
            die('Error: Unable to create target directory.');
        }
    }

    $targetPath = $targetDirectory . '/bootstrap.min.css';

    if (file_exists($sourcePath)) {
        if (copy($sourcePath, $targetPath)) {
            // echo 'Bootstrap CSS moved successfully.';
        } else {
            echo 'Error: Unable to move Bootstrap CSS.';
        }
    } else {
        echo 'Error: Bootstrap CSS not found at the specified source path.';
    }
}

function createViewForm($table_name, $controller_name, $primary_key, $non_primary_keys)
{
    // Generate the form fields
    $form_fields = '';
    $form_fields .= '<link rel="stylesheet" type="text/css" href="<?= base_url(\'tingala_assets/css/bootstrap.min.css\') ?>">'. PHP_EOL;
    $form_fields .= '    <input type="text" hidden class="form-control" id="' . $primary_key . '" name="' . $primary_key . '" value="<?= set_value(\'' . $primary_key . '\', isset($record[\'' . $primary_key . '\']) ? $record[\'' . $primary_key . '\'] : \'\') ?>" />' . PHP_EOL;

    foreach ($non_primary_keys as $field) {
        $form_fields .= '<div class="form-group">' . PHP_EOL;
        $form_fields .= '    <label for="' . $field['column_name'] . '">' . ucfirst($field['column_name']) . ':</label>' . PHP_EOL;

        // Generate appropriate input based on data type
        switch ($field['data_type']) {
          
            case 'int':
            case 'double':
            case 'decimal':
                $form_fields .= '    <input type="number" class="form-control" id="' . $field['column_name'] . '" name="' . $field['column_name'] . '" value="<?= set_value(\'' . $field['column_name'] . '\', isset($record[\'' . $field['column_name'] . '\']) ? $record[\'' . $field['column_name'] . '\'] : \'\') ?>" />' . PHP_EOL;
                break;
            case 'date':
                $form_fields .= '    <input type="date" class="form-control" id="' . $field['column_name'] . '" name="' . $field['column_name'] . '" value="<?= set_value(\'' . $field['column_name'] . '\', isset($record[\'' . $field['column_name'] . '\']) ? $record[\'' . $field['column_name'] . '\'] : \'\') ?>" />' . PHP_EOL;
                break;
                case 'datetime':
                    case 'timestamp':
                        $form_fields .= '    <input type="datetime-local" class="form-control" id="' . $field['column_name'] . '" name="' . $field['column_name'] . '" value="<?= set_value(\'' . $field['column_name'] . '\', isset($record[\'' . $field['column_name'] . '\']) ? $record[\'' . $field['column_name'] . '\'] : \'\') ?>" />' . PHP_EOL;
                        break;
                        case 'text':
                          $form_fields .= '    <textarea class="form-control" id="' . $field['column_name'] . '" name="' . $field['column_name'] . '"><?= set_value(\'' . $field['column_name'] . '\', isset($record[\'' . $field['column_name'] . '\']) ? $record[\'' . $field['column_name'] . '\'] : \'\') ?></textarea>' . PHP_EOL;
                        break;
                     default:
                $form_fields .= '    <input type="text" class="form-control" id="' . $field['column_name'] . '" name="' . $field['column_name'] . '" value="<?= set_value(\'' . $field['column_name'] . '\', isset($record[\'' . $field['column_name'] . '\']) ? $record[\'' . $field['column_name'] . '\'] : \'\') ?>" />' . PHP_EOL;
              
            }

        $form_fields .= '    <span class="text-danger"><?= service(\'validation\')->showError(\'' . $field['column_name'] . '\') ?></span>' . PHP_EOL;
        $form_fields .= '</div>' . PHP_EOL;
    }

    // Generate the entire form content
    $form_content = '<h2>' . ucfirst($table_name) . ' Form</h2>';
    $form_content .= '<form action="<?= site_url(\'' . strtolower($controller_name) . '/\').$action ?>" method="post">' . PHP_EOL;
    $form_content .= $form_fields;
    $form_content .= '<div class="form-group">' . PHP_EOL;
    $form_content .= '    <button type="submit" class="btn btn-primary">Save</button>' . PHP_EOL;
    $form_content .= '</div>' . PHP_EOL;
    $form_content .= '</form>' . PHP_EOL;

    return $form_content;
}


function createViewRead($table_name, $controller_name, $primary_key, $non_primary_keys)
{
    // Generate the table content for displaying a single record
    $table_content = '<link rel="stylesheet" type="text/css" href="<?= base_url(\'tingala_assets/css/bootstrap.min.css\') ?>">'. PHP_EOL;
    $table_content .= '<h2>' . ucfirst($table_name) . ' Details</h2>' . PHP_EOL;
    $table_content .= '<table class="table">' . PHP_EOL;

    foreach ($non_primary_keys as $field) {
        $table_content .= '    <tr>' . PHP_EOL;
        $table_content .= '        <td><strong>' . ucfirst($field['column_name']) . ':</strong></td>' . PHP_EOL;
        $table_content .= '        <td><?= $record[\'' . $field['column_name'] . '\'] ?></td>' . PHP_EOL;
        $table_content .= '    </tr>' . PHP_EOL;
    }

    $table_content .= '</table>' . PHP_EOL;

    // Create the "Back to List" link
    $table_content .= '<p><a class="btn btn-primary" href="<?= site_url(\'' . strtolower($controller_name) . '\') ?>">Back to List</a></p>' . PHP_EOL;

    return $table_content;
}
function createTingalaPagination($table_name, $controller_name, $primary_key, $non_primary_keys)
{
$content  = "";
$content .="
<?php \$pager->setSurroundCount(2) ?>

<nav aria-label=\"Page navigation\">
    <ul class=\"pagination\">
        <?php if (\$pager->hasPrevious()) : ?>
            <li class=\"page-item\">
                <a class=\"page-link\" href=\"<?= \$pager->getFirst() ?>\" aria-label=\"<?= lang('Pager.first') ?>\">
                    <span aria-hidden=\"true\"><?= lang('Pager.first') ?></span>
                </a>
            </li>
            <li class=\"page-item\">
                <a class=\"page-link\" href=\"<?= \$pager->getPrevious() ?>\" aria-label=\"<?= lang('Pager.previous') ?>\">
                    <span aria-hidden=\"true\"><?= lang('Pager.previous') ?></span>
                </a>
            </li>
        <?php endif ?>

        <?php foreach (\$pager->links() as \$link): ?>
            <li class=\"page-item <?= \$link['active'] ? 'active' : '' ?>\">
                <a class=\"page-link\" href=\"<?= \$link['uri'] ?>\">
                    <?= \$link['title'] ?>
                </a>
            </li>
        <?php endforeach ?>

        <?php if (\$pager->hasNext()) : ?>
            <li class=\"page-item\">
                <a class=\"page-link\" href=\"<?= \$pager->getNext() ?>\" aria-label=\"<?= lang('Pager.next') ?>\">
                    <span aria-hidden=\"true\"><?= lang('Pager.next') ?></span>
                </a>
            </li>
            <li class=\"page-item\">
                <a class=\"page-link\" href=\"<?= \$pager->getLast() ?>\" aria-label=\"<?= lang('Pager.last') ?>\">
                    <span aria-hidden=\"true\"><?= lang('Pager.last') ?></span>
                </a>
            </li>
        <?php endif ?>
    </ul>
</nav>


";

    return $content;
}




?>



