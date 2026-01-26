<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIsMilestoneToTasks extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tasks', [
            'is_milestone' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'after' => 'created_by'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('tasks', 'is_milestone');
    }
}
