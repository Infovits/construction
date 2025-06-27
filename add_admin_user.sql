-- Add default admin user to existing database
-- Run these SQL commands in your MySQL database

-- Insert default admin user
INSERT INTO users (company_id, employee_id, username, email, password, first_name, last_name, status, created_at) VALUES
(1, 'EMP-2025-0001', 'admin', 'admin@construction.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System', 'Administrator', 'active', NOW());

-- Assign super admin role to default user (assuming the user gets ID 1)
INSERT INTO user_roles (user_id, role_id, assigned_by, assigned_at) VALUES
(1, 1, 1, NOW());

-- If you need to check the user ID that was created, run:
-- SELECT id FROM users WHERE username = 'admin';
-- Then update the user_roles query with the correct user_id

-- Default login credentials:
-- Username: admin
-- Password: password
