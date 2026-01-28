<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCriteriaColumnToTables extends Migration
{
    public function up()
    {
        // Add criteria column to quality_inspections table
        $this->forge->addColumn('quality_inspections', [
            'criteria' => [
                'type' => 'JSON',
                'null' => true,
                'after' => 'attachments'
            ]
        ]);

        // Add criteria column to goods_receipt_items table
        $this->forge->addColumn('goods_receipt_items', [
            'criteria' => [
                'type' => 'JSON',
                'null' => true,
                'after' => 'notes'
            ]
        ]);
    }

    public function down()
    {
        // Remove criteria column from quality_inspections table
        $this->forge->dropColumn('quality_inspections', 'criteria');

        // Remove criteria column from goods_receipt_items table
        $this->forge->dropColumn('goods_receipt_items', 'criteria');
    }
}