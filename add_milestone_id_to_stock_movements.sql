-- SQL to add milestone_id column to stock_movements table
-- This enables tracking of material usage at the milestone level

-- Add the milestone_id column
ALTER TABLE stock_movements ADD COLUMN milestone_id INT NULL AFTER task_id;

-- Add foreign key constraint to link to milestones table
ALTER TABLE stock_movements 
ADD CONSTRAINT fk_stock_movements_milestone_id 
FOREIGN KEY (milestone_id) REFERENCES milestones(id) 
ON DELETE SET NULL 
ON UPDATE CASCADE;

-- Optional: Add index for better performance when querying by milestone
CREATE INDEX idx_stock_movements_milestone_id ON stock_movements(milestone_id);

-- Verify the column was added successfully
DESCRIBE stock_movements;

-- Show the current structure to confirm milestone_id is present
SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_KEY, COLUMN_DEFAULT
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'stock_movements' 
AND COLUMN_NAME = 'milestone_id';