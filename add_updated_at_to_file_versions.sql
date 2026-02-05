-- Add updated_at column to file_versions table
ALTER TABLE `file_versions` 
ADD COLUMN `updated_at` DATETIME NULL AFTER `created_at`;
