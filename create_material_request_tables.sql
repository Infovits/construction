-- Create material_request_items table if it doesn't exist
CREATE TABLE IF NOT EXISTS `material_request_items` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `material_request_id` INT NOT NULL,
    `material_id` INT NOT NULL,
    `quantity_requested` DECIMAL(10,3) NOT NULL,
    `quantity_approved` DECIMAL(10,3),
    `estimated_unit_cost` DECIMAL(10,2),
    `estimated_total_cost` DECIMAL(15,2),
    `specification_notes` TEXT,
    `urgency_notes` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (material_request_id) REFERENCES material_requests(id) ON DELETE CASCADE,
    FOREIGN KEY (material_id) REFERENCES materials(id)
);

-- Ensure material_requests table exists with correct structure
CREATE TABLE IF NOT EXISTS `material_requests` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `request_number` VARCHAR(50) UNIQUE NOT NULL,
    `project_id` INT,
    `requested_by` INT NOT NULL,
    `department_id` INT,
    `request_date` DATE NOT NULL,
    `required_date` DATE,
    `status` ENUM('draft', 'pending_approval', 'approved', 'rejected', 'partially_fulfilled', 'completed') DEFAULT 'draft',
    `priority` ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    `total_estimated_cost` DECIMAL(15,2),
    `approved_by` INT,
    `approved_date` DATETIME,
    `rejection_reason` TEXT,
    `notes` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id),
    FOREIGN KEY (requested_by) REFERENCES users(id),
    FOREIGN KEY (approved_by) REFERENCES users(id),
    FOREIGN KEY (department_id) REFERENCES departments(id)
);