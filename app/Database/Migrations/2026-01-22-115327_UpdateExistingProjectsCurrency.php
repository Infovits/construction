<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateExistingProjectsCurrency extends Migration
{
    public function up()
    {
        // Update all existing projects to use MWK currency
        $this->db->query("UPDATE projects SET currency = 'MWK' WHERE currency IS NULL OR currency = ''");
    }

    public function down()
    {
        // Revert to USD for all projects
        $this->db->query("UPDATE projects SET currency = 'USD'");
    }
}
