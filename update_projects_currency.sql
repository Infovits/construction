-- Update all existing projects to use MWK currency
-- Run this SQL script to set MWK as the currency for all projects

UPDATE projects SET currency = 'MWK' WHERE currency IS NULL OR currency = '';

-- Optional: Update any other tables that might have currency fields
-- Add more UPDATE statements here if other tables have currency fields

-- Example for future reference:
-- UPDATE budgets SET currency = 'MWK' WHERE currency IS NULL OR currency = '';
-- UPDATE purchase_orders SET currency = 'MWK' WHERE currency IS NULL OR currency = '';

-- Verify the update
SELECT 'Projects updated to MWK currency' as message, COUNT(*) as affected_projects
FROM projects
WHERE currency = 'MWK';
