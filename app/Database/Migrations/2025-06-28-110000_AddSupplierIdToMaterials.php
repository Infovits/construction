<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSupplierIdToMaterials extends Migration
{
    public function up()
    {
        $this->forge->addColumn('materials', [
            'primary_supplier_id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'null' => true,
                'after' => 'category_id'
            ]
        ]);
        
        // Add foreign key
        $this->db->query('ALTER TABLE materials ADD CONSTRAINT fk_material_supplier FOREIGN KEY (primary_supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL');
        
        // Add index
        $this->db->query('CREATE INDEX idx_material_supplier ON materials(primary_supplier_id)');
    }

    public function down()
    {
        // Drop foreign key first
        $this->db->query('ALTER TABLE materials DROP FOREIGN KEY fk_material_supplier');
        
        // Drop column
        $this->forge->dropColumn('materials', 'primary_supplier_id');
    }
}
