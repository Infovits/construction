-- Add criteria column to quality_inspections table
ALTER TABLE `quality_inspections` 
ADD COLUMN `criteria` JSON NULL AFTER `attachments`;

-- Add criteria column to goods_receipt_items table  
ALTER TABLE `goods_receipt_items`
ADD COLUMN `criteria` JSON NULL AFTER `notes`;

-- Update existing records to have empty JSON arrays for criteria
UPDATE `quality_inspections` SET `criteria` = '[]' WHERE `criteria` IS NULL;
UPDATE `goods_receipt_items` SET `criteria` = '[]' WHERE `criteria` IS NULL;