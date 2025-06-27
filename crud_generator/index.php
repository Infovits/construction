<?php

require_once 'core/Tingala.php';

require_once 'core/crud_processor.php';
?>
<!doctype html>
<html>
    <head>
        <title>Tingala C4 CRUD Generator</title>
        <link rel="stylesheet" href="core/bootstrap.min.css"/>
        <style>
            body{
                padding: 15px;
            }
            .myDiv {
                width: 400px;
                height: 400px;
                background-color: #ffffff; /* Set your desired background color */
                border-radius: 15px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Set your desired box shadow */
                padding: 20px; /* Adjust padding as needed */
            }
        </style>
    </head>
    <body>
        <div class="row">
            <div class="col-md-3">
                <div class="myDiv">
                <form action="index.php" method="POST">

                    <div class="form-group">
                        <label>Select Table - <a href="<?php echo $_SERVER['PHP_SELF'] ?>">Refresh</a></label>
                        <select id="table_name" name="table_name" class="form-control" onchange="setname()">
                            <option value="">Please Select</option>
                            <?php
                            $table_list = $tingala->table_list();
                            $table_list_selected = isset($_POST['table_name']) ? $_POST['table_name'] : '';
                            foreach ($table_list as $table) {
                                ?>
                                <option value="<?php echo $table['table_name'] ?>" <?php echo $table_list_selected == $table['table_name'] ? 'selected="selected"' : ''; ?>><?php echo $table['table_name'] ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>

                   
                     

                           

                    <div class="form-group">
                        <label>Custom Controller Name</label>
                        <input type="text" id="controller" name="controller"  class="form-control" placeholder="Controller Name" value="<?php echo isset($_POST['controller']) ? $_POST['controller'] : '' ?>" />
                    </div>
                    <div class="form-group">
                        <label>Custom Model Name</label>
                        <input type="text" id="model" name="model"  class="form-control" placeholder="Controller Name" value="<?php echo isset($_POST['model']) ? $_POST['model'] : '' ?>" />
                    </div>
                    <input type="submit" value="Generate" name="generate" class="btn btn-warning" onclick="javascript: return confirm('This will overwrite the existing files. Continue ?')" />
                    <input type="submit" value="Generate All" name="generateall" class="btn btn-danger" onclick="javascript: return confirm('WARNING !! This will generate code for ALL TABLE and overwrite the existing files\
                    \nPlease double check before continue. Continue ?')" />

                </form>
                <br>

                <?php
               
                ?>
                </div>
                <p><strong>Generate status report/s will show here</strong></p>
                <?php
                foreach ($storage as $h) {
                    echo '<p>' . $h . '</p>';
                }
                ?>
            </div>
            <div class="col-md-9">
                <h3 style="margin-top: 1px; color: orange;">Tingala CI4 crud generator</a></h3>
                <hr>
                <h3 style="margin-top: 0px">Codeigniter 4 CRUD Generator 1.0 by <a target="_blank" href="http://infocustech-mw.com">By Misheck Kamuloni (infocus technologies)</a></h3>
                <p><strong>About this project</strong></p>
                <p>
                    Tingala Codeigniter 4 CRUD Generator is a powerful but simple tool to auto generate model, controller and view from your database table/s on a single click of a button. It also generates full pagination to your generated view table. As a bonus I added export to excel, pdf and word which works also cool with the search feature that this tool does to your CRUD.  This tool will reduce up to 70% of the time you take to code the CRUD in codeigniter 4 with search and export.
                    This Tingala Codeigniter 4 CRUD Generator is using bootstrap 4 styling, the generated table,buttons pagination links and forms are as of bootstrap 4, however you can modify to your desired css library.
                </p>
                <p><strong class="text-success">What will Tingala CI 4 Generator create and modify to your project? :</strong></p>
                <ol>
                <li>* This generator generates textarea , text, date, datetime and number types of form inputs based on the table field type. You can modify this in your generated forms to change the data type inputs  should accept.</li>
                <li>* This generator generates form validation rules based on the database field data type.</li>
                <li>* This generator generates Following files : TableName_model model file placed in Model Directory, TableName Controller file placed in Controller Directory, TableName View list placed in TableName folder under Views Directory,TableName View form placed in TableName folder under Views Directory,TableName View read placed in TableName folder under Views Directory, View pdf_export placed in TableName folder under Views Directory,View excel_export placed in TableName folder under Views Directory, View word_export placed in TableName folder under Views Directory,View tingala_pagination custom pagination file placed in layouts folder under Views Directory,Library ExcelExport php file  Class for Excel export placed in Libraries  Directory,Library MpdfWrapper php file  Class for PDF export placed in Libraries Directory, It copies  and create bootstrap.min.js file into public > tingala_assets folder    .</li>
                <li>* This generator generates routes and writes them in Routes,php.</li>
                <li>* This generator generates custom Pager config and writes them in Pager.php.</li>
                </ol><br>
                <p><strong class="text-danger">Preparation before using this  Tingala CI 4 CRUD Generator (Important) :</strong></p>
                <ul>
                    <li>On app/Config/App.php, add  ie public string $baseURL = 'http://localhost/tingalac4project/public/';</li>
                    <li>On app/Config/Database.php, add database connection based on your db connection ie:</li>
                    <pre>

                        public array $default = [
                        'DSN'          => '',
                        'hostname'     => 'localhost',
                        'username'     => 'root',
                        'password'     => '',
                        'database'     => 'tingalac4db',
                        'DBDriver'     => 'MySQLi',
                        'DBPrefix'     => '',
                        'pConnect'     => false,
                        'DBDebug'      => true,
                        'charset'      => 'utf8',
                        'DBCollat'     => 'utf8_general_ci',
                        'swapPre'      => '',
                        'encrypt'      => false,
                        'compress'     => false,
                        'strictOn'     => false,
                        'failover'     => [],
                        'port'         => 3306,
                        'numberNative' => false,
                        ];


                    </pre>
                    <li>Make sure your tables have a field which is a primary key</li>
                    <li>We assume you have already installed or you know how to setup/install composer in your machine else I have tutorial here <a href="">Youtube tutorial composer installation</a> </li>
                    <li>If you want Pdf export to work, install Mpdf in your vendor folder using composer by runnning following command  require mpdf/mpdf or follow installation guide <a href="https://github.com/mpdf/mpdf">link to Mpdf</a >  </li>
                    <li>If you want Excel export to work, install phpoffice/phpspreadsheet in your vendor folder using composer by running following command composer require phpoffice/phpspreadsheet or follow installation guide here  <a href="https://github.com/PHPOffice/PhpSpreadsheet">link to Mpdf</a>  </li>

                </ul>
                <p><strong>Using this Tingala CI4 CRUD Generator :</strong></p>
                <ul>
                    <li>Simply put 'tingalagenerator' folder,  into your project root folder.</li>
                    <li>Open http://localhost/yourprojectname/tingalagenerator.</li>
                    <li>Select table and push generate button.</li>
                    <li>Or click Generate all Button to generate CURD from all your connected db tables.</li>
                </ul>

                <br>
                <p><strong>Thanks for Support Me</strong></p>
                <p>Buy me a cup of coffee :)</p>
                <a href="https://paypal.me/vikatech" > <button style="background: orange; color: black; padding: 0.5em; border-radius: 15px;">Donate something</button></a><br>
                <img alt="" border="0" src="images/PayPal-Logo.png" width="100" height="50">

                <br>
                <p><strong>Updates</strong></p>

                <ul>
                    <li>V.1.0 - 26 January 2024
                        <ul>
                            <li>Initial Project</li>
                        </ul>
                    </li>

                <p><strong>&COPY; 2024 <a target="_blank" href="http://infocustech-mw.com">Infocus technologies</a></strong></p>
            </div>
        </div>
        <script type="text/javascript">
            function capitalize(s) {
                return s && s[0].toUpperCase() + s.slice(1);
            }

            function setname() {
                var table_name = document.getElementById('table_name').value.toLowerCase();
                if (table_name != '') {
                    document.getElementById('controller').value = capitalize(table_name);
                    document.getElementById('model').value = capitalize(table_name) + '_model';
                } else {
                    document.getElementById('controller').value = '';
                    document.getElementById('model').value = '';
                }
            }
        </script>
    </body>
</html>
