<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class SetupBudgetTablesController extends BaseController
{
    public function setupBudgetTables()
    {
        $db = \Config\Database::connect();
        $forge = \Config\Database::forge();

        $output = '<h2>Budget Tables Setup</h2>';
        $output .= '<pre>';

        try {
            // Check and create budgets table
            if (!$db->tableExists('budgets')) {
                $output .= "Creating budgets table...\n";

                $forge->addField([
                    'id' => [
                        'type' => 'BIGINT',
                        'constraint' => 20,
                        'unsigned' => true,
                        'auto_increment' => true
                    ],
                    'company_id' => [
                        'type' => 'BIGINT',
                        'constraint' => 20,
                        'unsigned' => true
                    ],
                    'project_id' => [
                        'type' => 'BIGINT',
                        'constraint' => 20,
                        'unsigned' => true,
                        'null' => true
                    ],
                    'name' => [
                        'type' => 'VARCHAR',
                        'constraint' => 255
                    ],
                    'description' => [
                        'type' => 'TEXT',
                        'null' => true
                    ],
                    'budget_period' => [
                        'type' => 'ENUM',
                        'constraint' => ['monthly', 'quarterly', 'yearly', 'project'],
                        'default' => 'yearly'
                    ],
                    'start_date' => [
                        'type' => 'DATE',
                        'null' => true
                    ],
                    'end_date' => [
                        'type' => 'DATE',
                        'null' => true
                    ],
                    'total_budget' => [
                        'type' => 'DECIMAL',
                        'constraint' => '15,2',
                        'default' => 0.00
                    ],
                    'allocated_budget' => [
                        'type' => 'DECIMAL',
                        'constraint' => '15,2',
                        'default' => 0.00
                    ],
                    'spent_amount' => [
                        'type' => 'DECIMAL',
                        'constraint' => '15,2',
                        'default' => 0.00
                    ],
                    'remaining_budget' => [
                        'type' => 'DECIMAL',
                        'constraint' => '15,2',
                        'default' => 0.00
                    ],
                    'status' => [
                        'type' => 'ENUM',
                        'constraint' => ['draft', 'active', 'completed', 'cancelled'],
                        'default' => 'draft'
                    ],
                    'created_by' => [
                        'type' => 'BIGINT',
                        'constraint' => 20,
                        'unsigned' => true,
                        'null' => true
                    ],
                    'created_at' => [
                        'type' => 'TIMESTAMP',
                        'null' => true
                    ],
                    'updated_at' => [
                        'type' => 'TIMESTAMP',
                        'null' => true
                    ]
                ]);

                $forge->addKey('id', true);
                $forge->addKey('company_id');
                $forge->addKey('project_id');
                $forge->createTable('budgets', true);

                $output .= "✓ Budgets table created successfully\n\n";
            } else {
                $output .= "✓ Budgets table already exists\n\n";
            }

            // Check and create budget_categories table
            if (!$db->tableExists('budget_categories')) {
                $output .= "Creating budget_categories table...\n";

                $forge->addField([
                    'id' => [
                        'type' => 'BIGINT',
                        'constraint' => 20,
                        'unsigned' => true,
                        'auto_increment' => true
                    ],
                    'company_id' => [
                        'type' => 'BIGINT',
                        'constraint' => 20,
                        'unsigned' => true
                    ],
                    'name' => [
                        'type' => 'VARCHAR',
                        'constraint' => 100
                    ],
                    'budget_type' => [
                        'type' => 'ENUM',
                        'constraint' => ['revenue', 'expense', 'capital']
                    ],
                    'status' => [
                        'type' => 'ENUM',
                        'constraint' => ['active', 'inactive'],
                        'default' => 'active'
                    ],
                    'created_at' => [
                        'type' => 'TIMESTAMP',
                        'null' => true
                    ],
                    'updated_at' => [
                        'type' => 'TIMESTAMP',
                        'null' => true
                    ]
                ]);

                $forge->addKey('id', true);
                $forge->addKey('company_id');
                $forge->createTable('budget_categories', true);

                // Insert default categories
                $db->table('budget_categories')->insertBatch([
                    [
                        'company_id' => 1,
                        'name' => 'Labor Costs',
                        'budget_type' => 'expense',
                        'status' => 'active'
                    ],
                    [
                        'company_id' => 1,
                        'name' => 'Materials',
                        'budget_type' => 'expense',
                        'status' => 'active'
                    ],
                    [
                        'company_id' => 1,
                        'name' => 'Equipment',
                        'budget_type' => 'expense',
                        'status' => 'active'
                    ],
                    [
                        'company_id' => 1,
                        'name' => 'Subcontractors',
                        'budget_type' => 'expense',
                        'status' => 'active'
                    ],
                    [
                        'company_id' => 1,
                        'name' => 'Overhead',
                        'budget_type' => 'expense',
                        'status' => 'active'
                    ],
                    [
                        'company_id' => 1,
                        'name' => 'Project Revenue',
                        'budget_type' => 'revenue',
                        'status' => 'active'
                    ]
                ]);

                $output .= "✓ Budget_categories table created with default categories\n\n";
            } else {
                $output .= "✓ Budget_categories table already exists\n\n";
            }

            // Check and create budget_line_items table
            if (!$db->tableExists('budget_line_items')) {
                $output .= "Creating budget_line_items table...\n";

                $forge->addField([
                    'id' => [
                        'type' => 'BIGINT',
                        'constraint' => 20,
                        'unsigned' => true,
                        'auto_increment' => true
                    ],
                    'budget_id' => [
                        'type' => 'BIGINT',
                        'constraint' => 20,
                        'unsigned' => true
                    ],
                    'category_id' => [
                        'type' => 'BIGINT',
                        'constraint' => 20,
                        'unsigned' => true,
                        'null' => true
                    ],
                    'cost_code_id' => [
                        'type' => 'BIGINT',
                        'constraint' => 20,
                        'unsigned' => true,
                        'null' => true
                    ],
                    'description' => [
                        'type' => 'VARCHAR',
                        'constraint' => 255
                    ],
                    'budgeted_amount' => [
                        'type' => 'DECIMAL',
                        'constraint' => '15,2',
                        'default' => 0.00
                    ],
                    'actual_amount' => [
                        'type' => 'DECIMAL',
                        'constraint' => '15,2',
                        'default' => 0.00
                    ],
                    'variance' => [
                        'type' => 'DECIMAL',
                        'constraint' => '15,2',
                        'default' => 0.00
                    ],
                    'created_at' => [
                        'type' => 'TIMESTAMP',
                        'null' => true
                    ],
                    'updated_at' => [
                        'type' => 'TIMESTAMP',
                        'null' => true
                    ]
                ]);

                $forge->addKey('id', true);
                $forge->addKey('budget_id');
                $forge->createTable('budget_line_items', true);

                $output .= "✓ Budget_line_items table created successfully\n\n";
            } else {
                $output .= "✓ Budget_line_items table already exists\n\n";
            }

            // Summary
            $output .= "\n" . str_repeat('=', 50) . "\n";
            $output .= "SETUP COMPLETE!\n";
            $output .= str_repeat('=', 50) . "\n\n";

            $output .= "Tables Status:\n";
            $output .= "✓ budgets - " . ($db->tableExists('budgets') ? 'EXISTS' : 'MISSING') . "\n";
            $output .= "✓ budget_categories - " . ($db->tableExists('budget_categories') ? 'EXISTS' : 'MISSING') . "\n";
            $output .= "✓ budget_line_items - " . ($db->tableExists('budget_line_items') ? 'EXISTS' : 'MISSING') . "\n\n";

            $output .= "You can now access:\n";
            $output .= "• Job Cost Tracking: <a href='/admin/accounting/job-cost-tracking'>/admin/accounting/job-cost-tracking</a>\n";
            $output .= "• Job Budgets: <a href='/admin/accounting/job-budgets'>/admin/accounting/job-budgets</a>\n";

        } catch (\Exception $e) {
            $output .= "\n✗ ERROR: " . $e->getMessage() . "\n";
            $output .= "\nStack trace:\n" . $e->getTraceAsString();
        }

        $output .= '</pre>';

        return $output;
    }
}
