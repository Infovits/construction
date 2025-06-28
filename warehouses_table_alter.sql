-- ALTER statements to add missing columns to warehouses table
-- Run these statements to make your table compatible with the application

-- Check current table structure first (run this to see what columns exist):
-- DESCRIBE warehouses;

-- Add missing columns that are expected by the application
-- Add these one by one and skip any that already exist

-- Add country column
ALTER TABLE `warehouses` 
ADD COLUMN `country` VARCHAR(100) DEFAULT NULL AFTER `state`;

-- Add warehouse_type column if missing
ALTER TABLE `warehouses` 
ADD COLUMN `warehouse_type` ENUM('main', 'site', 'temporary') DEFAULT 'main' AFTER `email`;

-- Add capacity column if missing
ALTER TABLE `warehouses` 
ADD COLUMN `capacity` DECIMAL(12,2) DEFAULT NULL AFTER `warehouse_type`;

-- Add is_project_site column if missing
ALTER TABLE `warehouses` 
ADD COLUMN `is_project_site` BOOLEAN DEFAULT FALSE AFTER `capacity`;

-- Add project_id column if missing
ALTER TABLE `warehouses` 
ADD COLUMN `project_id` BIGINT UNSIGNED DEFAULT NULL AFTER `is_project_site`;

-- Add status column if missing
ALTER TABLE `warehouses` 
ADD COLUMN `status` ENUM('active', 'inactive', 'maintenance') DEFAULT 'active' AFTER `project_id`;

-- Add notes column if missing
ALTER TABLE `warehouses` 
ADD COLUMN `notes` TEXT DEFAULT NULL AFTER `status`;

-- Add created_by column if missing
ALTER TABLE `warehouses` 
ADD COLUMN `created_by` BIGINT UNSIGNED DEFAULT NULL AFTER `notes`;

-- Add updated_at column if missing
ALTER TABLE `warehouses` 
ADD COLUMN `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`;

-- Add foreign key constraints (run these after adding the columns)
-- Skip any that already exist

-- Foreign key for project_id (only if projects table exists)
ALTER TABLE `warehouses` 
ADD CONSTRAINT `fk_warehouses_project` 
FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`) ON DELETE SET NULL;

-- Foreign key for created_by
ALTER TABLE `warehouses` 
ADD CONSTRAINT `fk_warehouses_created_by` 
FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL;

-- Add indexes for better performance
ALTER TABLE `warehouses` 
ADD INDEX `idx_warehouse_project` (`project_id`);

-- Add unique constraint for company_id + code combination
ALTER TABLE `warehouses` 
ADD CONSTRAINT `unique_company_warehouse_code` 
UNIQUE (`company_id`, `code`);

-- Update existing records to have default values
UPDATE `warehouses` SET 
    `warehouse_type` = 'main' WHERE `warehouse_type` IS NULL,
    `status` = 'active' WHERE `status` IS NULL,
    `is_project_site` = FALSE WHERE `is_project_site` IS NULL;
