<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class DatabaseSetupController extends BaseController
{
    public function setupJournalEntries()
    {
        $db = \Config\Database::connect();
        
        try {
            // Check if tables already exist
            if ($db->tableExists('journal_entries')) {
                return redirect()->to('/admin/accounting/journal-entries')->with('info', 'Journal entries tables already exist.');
            }

            // Create journal_entries table
            $sql1 = "CREATE TABLE `journal_entries` (
              `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
              `company_id` bigint UNSIGNED NOT NULL,
              `journal_number` varchar(50) NOT NULL,
              `entry_date` date NOT NULL,
              `reference` varchar(100) DEFAULT NULL,
              `description` text NOT NULL,
              `total_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
              `status` enum('draft','posted','reversed') NOT NULL DEFAULT 'draft',
              `posted_at` timestamp NULL DEFAULT NULL,
              `posted_by` bigint UNSIGNED DEFAULT NULL,
              `reversed_at` timestamp NULL DEFAULT NULL,
              `reversed_by` bigint UNSIGNED DEFAULT NULL,
              `reversal_entry_id` bigint UNSIGNED DEFAULT NULL,
              `notes` text DEFAULT NULL,
              `created_by` bigint UNSIGNED DEFAULT NULL,
              `updated_by` bigint UNSIGNED DEFAULT NULL,
              `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
              `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              UNIQUE KEY `unique_company_journal_number` (`company_id`, `journal_number`),
              KEY `idx_journal_company` (`company_id`),
              KEY `idx_journal_date` (`entry_date`),
              KEY `idx_journal_status` (`status`),
              KEY `idx_journal_number` (`journal_number`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci";
            
            $db->query($sql1);

            // Create journal_entry_lines table
            $sql2 = "CREATE TABLE `journal_entry_lines` (
              `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
              `journal_entry_id` bigint UNSIGNED NOT NULL,
              `account_id` bigint UNSIGNED NOT NULL,
              `description` varchar(255) DEFAULT NULL,
              `debit_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
              `credit_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
              `project_id` bigint UNSIGNED DEFAULT NULL,
              `cost_code_id` bigint UNSIGNED DEFAULT NULL,
              `reference` varchar(100) DEFAULT NULL,
              `line_number` int NOT NULL DEFAULT '1',
              `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
              `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              KEY `idx_journal_line_entry` (`journal_entry_id`),
              KEY `idx_journal_line_account` (`account_id`),
              KEY `idx_journal_line_project` (`project_id`),
              KEY `idx_journal_line_debit` (`debit_amount`),
              KEY `idx_journal_line_credit` (`credit_amount`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci";
            
            $db->query($sql2);

            // Insert sample data
            $sampleEntries = [
                [
                    'company_id' => 1,
                    'journal_number' => 'JE-2025-001',
                    'entry_date' => '2025-08-10',
                    'reference' => 'SETUP-001',
                    'description' => 'Initial setup - Owner investment',
                    'total_amount' => 10000.00,
                    'status' => 'posted',
                    'created_by' => 1
                ],
                [
                    'company_id' => 1,
                    'journal_number' => 'JE-2025-002',
                    'entry_date' => '2025-08-10',
                    'reference' => 'SETUP-002',
                    'description' => 'Equipment purchase',
                    'total_amount' => 5000.00,
                    'status' => 'posted',
                    'created_by' => 1
                ],
                [
                    'company_id' => 1,
                    'journal_number' => 'JE-2025-003',
                    'entry_date' => '2025-08-10',
                    'reference' => 'SETUP-003',
                    'description' => 'Office rent payment',
                    'total_amount' => 2000.00,
                    'status' => 'draft',
                    'created_by' => 1
                ]
            ];

            $builder = $db->table('journal_entries');
            $builder->insertBatch($sampleEntries);
            
            // Get inserted IDs and create lines
            $sampleLines = [
                // JE-001: Owner investment
                ['journal_entry_id' => 1, 'account_id' => 1, 'description' => 'Cash received from owner', 'debit_amount' => 10000.00, 'credit_amount' => 0.00, 'line_number' => 1],
                ['journal_entry_id' => 1, 'account_id' => 7, 'description' => 'Owner capital investment', 'debit_amount' => 0.00, 'credit_amount' => 10000.00, 'line_number' => 2],
                // JE-002: Equipment purchase
                ['journal_entry_id' => 2, 'account_id' => 4, 'description' => 'Equipment purchase', 'debit_amount' => 5000.00, 'credit_amount' => 0.00, 'line_number' => 1],
                ['journal_entry_id' => 2, 'account_id' => 1, 'description' => 'Cash paid for equipment', 'debit_amount' => 0.00, 'credit_amount' => 5000.00, 'line_number' => 2],
                // JE-003: Office rent
                ['journal_entry_id' => 3, 'account_id' => 12, 'description' => 'Office rent expense', 'debit_amount' => 2000.00, 'credit_amount' => 0.00, 'line_number' => 1],
                ['journal_entry_id' => 3, 'account_id' => 1, 'description' => 'Cash paid for rent', 'debit_amount' => 0.00, 'credit_amount' => 2000.00, 'line_number' => 2]
            ];

            $builder = $db->table('journal_entry_lines');
            $builder->insertBatch($sampleLines);

            return redirect()->to('/admin/accounting/journal-entries?setup=complete')->with('success', 'Journal entries database tables created successfully with sample data!');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create journal entries tables: ' . $e->getMessage());
        }
    }
    
    public function setupCostCodes()
    {
        try {
            $db = \Config\Database::connect();
            
            // Create cost_codes table
            $sql = "
            CREATE TABLE IF NOT EXISTS `cost_codes` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `company_id` int(11) NOT NULL DEFAULT 1,
                `code` varchar(50) NOT NULL,
                `name` varchar(255) NOT NULL,
                `description` text,
                `category` enum('labor','material','equipment','subcontractor','overhead','other') NOT NULL,
                `cost_type` enum('direct','indirect') NOT NULL DEFAULT 'direct',
                `unit_of_measure` varchar(50),
                `standard_rate` decimal(10,2),
                `is_active` tinyint(1) NOT NULL DEFAULT 1,
                `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `unique_company_code` (`company_id`, `code`),
                KEY `idx_category` (`category`),
                KEY `idx_cost_type` (`cost_type`),
                KEY `idx_is_active` (`is_active`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ";
            
            $db->query($sql);
            
            // Insert sample data
            $sampleData = [
                ['company_id' => 1, 'code' => 'LAB-001', 'name' => 'General Labor', 'description' => 'General construction labor', 'category' => 'labor', 'cost_type' => 'direct', 'unit_of_measure' => 'hour', 'standard_rate' => 25.00],
                ['company_id' => 1, 'code' => 'LAB-002', 'name' => 'Skilled Labor', 'description' => 'Skilled construction workers', 'category' => 'labor', 'cost_type' => 'direct', 'unit_of_measure' => 'hour', 'standard_rate' => 35.00],
                ['company_id' => 1, 'code' => 'LAB-003', 'name' => 'Supervisor', 'description' => 'Site supervisor/foreman', 'category' => 'labor', 'cost_type' => 'direct', 'unit_of_measure' => 'hour', 'standard_rate' => 45.00],
                ['company_id' => 1, 'code' => 'MAT-001', 'name' => 'Concrete', 'description' => 'Ready-mix concrete', 'category' => 'material', 'cost_type' => 'direct', 'unit_of_measure' => 'cubic_meter', 'standard_rate' => 150.00],
                ['company_id' => 1, 'code' => 'MAT-002', 'name' => 'Steel Rebar', 'description' => 'Reinforcement steel bars', 'category' => 'material', 'cost_type' => 'direct', 'unit_of_measure' => 'kg', 'standard_rate' => 2.50],
                ['company_id' => 1, 'code' => 'EQP-001', 'name' => 'Excavator', 'description' => 'Heavy excavator rental', 'category' => 'equipment', 'cost_type' => 'direct', 'unit_of_measure' => 'hour', 'standard_rate' => 200.00],
                ['company_id' => 1, 'code' => 'SUB-001', 'name' => 'Electrical', 'description' => 'Electrical contractor', 'category' => 'subcontractor', 'cost_type' => 'direct', 'unit_of_measure' => 'lump_sum', 'standard_rate' => null],
                ['company_id' => 1, 'code' => 'OVH-001', 'name' => 'Site Office', 'description' => 'Site office overhead', 'category' => 'overhead', 'cost_type' => 'indirect', 'unit_of_measure' => 'month', 'standard_rate' => 5000.00]
            ];
            
            $builder = $db->table('cost_codes');
            foreach ($sampleData as $data) {
                try {
                    $builder->insert($data);
                } catch (\Exception $e) {
                    // Ignore duplicate key errors
                    if (strpos($e->getMessage(), 'Duplicate entry') === false) {
                        throw $e;
                    }
                }
            }
            
            return redirect()->to('/admin/accounting/cost-codes')->with('success', 'Cost codes table created successfully with sample data!');
            
        } catch (\Exception $e) {
            log_message('error', 'Cost Codes Setup Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error creating cost codes table: ' . $e->getMessage());
        }
    }
}