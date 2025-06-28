-- ALTER statements to add missing columns to stock_movements table
-- Run these statements to make your table compatible with the application

-- Add task_id column for linking to project tasks
ALTER TABLE `stock_movements` 
ADD COLUMN `task_id` bigint UNSIGNED DEFAULT NULL AFTER `project_id`,
ADD INDEX `idx_stock_task` (`task_id`);

-- Add source_warehouse_id for warehouse transfers (rename existing warehouse_id)
ALTER TABLE `stock_movements` 
CHANGE COLUMN `warehouse_id` `source_warehouse_id` bigint UNSIGNED DEFAULT NULL;

-- Add destination_warehouse_id for warehouse transfers
ALTER TABLE `stock_movements` 
ADD COLUMN `destination_warehouse_id` bigint UNSIGNED DEFAULT NULL AFTER `source_warehouse_id`,
ADD INDEX `idx_stock_destination_warehouse` (`destination_warehouse_id`);

-- Update the existing warehouse index name
ALTER TABLE `stock_movements` 
DROP INDEX `idx_stock_warehouse`,
ADD INDEX `idx_stock_source_warehouse` (`source_warehouse_id`);

-- Add updated_at timestamp column
ALTER TABLE `stock_movements` 
ADD COLUMN `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`;

-- Update movement_type enum to include more specific types used by the application
ALTER TABLE `stock_movements` 
MODIFY COLUMN `movement_type` enum('in','out','transfer','adjustment','project_usage','return','consumption','production') NOT NULL;

-- Update reference_type enum to match application usage
ALTER TABLE `stock_movements` 
MODIFY COLUMN `reference_type` enum('purchase','sale','transfer','adjustment','return','consumption','production','project_usage','delivery','manual') NOT NULL;

-- Add comment to the table for documentation
ALTER TABLE `stock_movements` 
COMMENT = 'Stock movements tracking for materials across warehouses and projects';
