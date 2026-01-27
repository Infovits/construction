-- SQL script to add missing fields to warehouses table
-- This will enable phone, email, and other fields to be updated properly

-- Add phone field for warehouse contact
ALTER TABLE warehouses 
ADD COLUMN phone VARCHAR(50) NULL COMMENT 'Warehouse phone number' AFTER state;

-- Add email field for warehouse contact  
ALTER TABLE warehouses 
ADD COLUMN email VARCHAR(255) NULL COMMENT 'Warehouse email address' AFTER phone;

-- Add country field (missing from original schema)
ALTER TABLE warehouses 
ADD COLUMN country VARCHAR(100) NULL DEFAULT 'Malawi' COMMENT 'Country where warehouse is located' AFTER state;

-- Add project association fields
ALTER TABLE warehouses 
ADD COLUMN is_project_site TINYINT(1) NULL DEFAULT 0 COMMENT 'Whether this is a project site warehouse' AFTER capacity,
ADD COLUMN project_id BIGINT UNSIGNED NULL COMMENT 'Associated project ID for project site warehouses' AFTER is_project_site;

-- Add notes field for additional information
ALTER TABLE warehouses 
ADD COLUMN notes TEXT NULL COMMENT 'Additional notes about the warehouse' AFTER status;

-- Add created_by field to track who created the warehouse
ALTER TABLE warehouses 
ADD COLUMN created_by BIGINT UNSIGNED NULL COMMENT 'User who created this warehouse' AFTER company_id;

-- Set default values for existing records
UPDATE warehouses SET country = 'Malawi' WHERE country IS NULL;
UPDATE warehouses SET is_project_site = 0 WHERE is_project_site IS NULL;

-- Add foreign key constraint for project_id (if projects table exists)
-- ALTER TABLE warehouses ADD CONSTRAINT fk_warehouse_project FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL;

-- Add foreign key constraint for created_by (if users table exists)  
-- ALTER TABLE warehouses ADD CONSTRAINT fk_warehouse_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL;

-- Add foreign key constraint for manager_id (if users table exists)
-- ALTER TABLE warehouses ADD CONSTRAINT fk_warehouse_manager FOREIGN KEY (manager_id) REFERENCES users(id) ON DELETE SET NULL;

-- Add foreign key constraint for company_id (if companies table exists)
-- ALTER TABLE warehouses ADD CONSTRAINT fk_warehouse_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE;

-- Create index on commonly searched fields for better performance
CREATE INDEX idx_warehouses_company_id ON warehouses(company_id);
CREATE INDEX idx_warehouses_project_id ON warehouses(project_id);
CREATE INDEX idx_warehouses_manager_id ON warehouses(manager_id);
CREATE INDEX idx_warehouses_status ON warehouses(status);

-- Verify the changes
SELECT 'Warehouse table structure updated successfully' as message;
DESCRIBE warehouses;