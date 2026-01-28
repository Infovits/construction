-- Add updated_at column to goods_receipt_items table
ALTER TABLE `goods_receipt_items` 
ADD COLUMN `updated_at` TIMESTAMP NULL DEFAULT NULL AFTER `created_at`;

-- Update existing records to have the same updated_at as created_at
UPDATE `goods_receipt_items` 
SET `updated_at` = `created_at` 
WHERE `updated_at` IS NULL;