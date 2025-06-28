<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSupplierMaterialsTable extends Migration
{
    public function up()
    {
        // Define table structure
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'supplier_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
            ],
            'material_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
            ],
            'unit_price' => [
                'type'           => 'DECIMAL',
                'constraint'     => '15,2',
                'default'        => 0.00,
            ],
            'min_order_qty' => [
                'type'           => 'DECIMAL',
                'constraint'     => '15,2',
                'null'           => true,
            ],
            'lead_time' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'null'           => true,
                'comment'        => 'Lead time in days',
            ],
            'notes' => [
                'type'           => 'TEXT',
                'null'           => true,
            ],
            'is_preferred' => [
                'type'           => 'TINYINT',
                'constraint'     => 1,
                'default'        => 0,
                'comment'        => '1=preferred supplier for this material, 0=not preferred',
            ],
            'created_at' => [
                'type'           => 'DATETIME',
                'null'           => true,
            ],
            'updated_at' => [
                'type'           => 'DATETIME',
                'null'           => true,
            ],
        ]);

        // Define primary key
        $this->forge->addKey('id', true);
        
        // Define foreign keys
        $this->forge->addForeignKey('supplier_id', 'suppliers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('material_id', 'materials', 'id', 'CASCADE', 'CASCADE');
        
        // Define unique key to prevent duplicates
        $this->forge->addUniqueKey(['supplier_id', 'material_id']);
        
        // Create the table
        $this->forge->createTable('supplier_materials');
    }

    public function down()
    {
        // Drop the table
        $this->forge->dropTable('supplier_materials');
    }
}
