<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLastOrderDateToSuppliers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('suppliers', [
            'last_order_date' => [
                'type' => 'DATE',
                'null' => true,
                'comment' => 'Date of last delivery from this supplier'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('suppliers', 'last_order_date');
    }
}