-- Construction Management System (CMS) - Comprehensive MySQL Schema
-- Database: construction_management_system
-- Version: 1.0
-- Created: June 2025

-- Drop database if exists (use with caution in production)
-- DROP DATABASE IF EXISTS construction_management_system;

-- Create database
CREATE DATABASE IF NOT EXISTS construction_management_system 
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE construction_management_system;

-- =====================================================
-- CORE SYSTEM TABLES
-- =====================================================

-- 1. COMPANIES & ORGANIZATIONS
CREATE TABLE companies (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    registration_number VARCHAR(100),
    tax_number VARCHAR(100),
    email VARCHAR(255),
    phone VARCHAR(20),
    address TEXT,
    city VARCHAR(100),
    state VARCHAR(100),
    country VARCHAR(100) DEFAULT 'Malawi',
    postal_code VARCHAR(20),
    website VARCHAR(255),
    logo_url VARCHAR(500),
    industry_type ENUM('residential', 'commercial', 'industrial', 'infrastructure', 'mixed') DEFAULT 'mixed',
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    subscription_plan ENUM('basic', 'professional', 'enterprise') DEFAULT 'basic',
    subscription_expires_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_company_status (status),
    INDEX idx_subscription_plan (subscription_plan),
    INDEX idx_subscription_expires (subscription_expires_at)
);

-- 2. USER MANAGEMENT & AUTHENTICATION
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    employee_id VARCHAR(50),
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    middle_name VARCHAR(100),
    phone VARCHAR(20),
    mobile VARCHAR(20),
    date_of_birth DATE,
    gender ENUM('male', 'female', 'other'),
    national_id VARCHAR(50),
    passport_number VARCHAR(50),
    address TEXT,
    city VARCHAR(100),
    emergency_contact_name VARCHAR(255),
    emergency_contact_phone VARCHAR(20),
    profile_photo_url VARCHAR(500),
    status ENUM('active', 'inactive', 'suspended', 'terminated') DEFAULT 'active',
    is_verified BOOLEAN DEFAULT FALSE,
    last_login_at TIMESTAMP NULL,
    password_changed_at TIMESTAMP NULL,
    two_factor_enabled BOOLEAN DEFAULT FALSE,
    two_factor_secret VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    INDEX idx_user_company (company_id),
    INDEX idx_user_status (status),
    INDEX idx_user_email (email),
    INDEX idx_employee_id (employee_id)
);

-- 3. USER ROLES & PERMISSIONS
CREATE TABLE roles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL,
    description TEXT,
    is_system_role BOOLEAN DEFAULT FALSE,
    permissions JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    UNIQUE KEY unique_company_role_slug (company_id, slug),
    INDEX idx_role_company (company_id)
);

CREATE TABLE user_roles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    role_id BIGINT UNSIGNED NOT NULL,
    assigned_by BIGINT UNSIGNED,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_by) REFERENCES users(id) ON DELETE SET NULL,
    UNIQUE KEY unique_user_role (user_id, role_id),
    INDEX idx_user_roles_user (user_id),
    INDEX idx_user_roles_role (role_id)
);

-- 4. USER SESSIONS & SECURITY
CREATE TABLE user_sessions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    session_token VARCHAR(255) NOT NULL UNIQUE,
    ip_address VARCHAR(45),
    user_agent TEXT,
    device_type VARCHAR(50),
    is_mobile BOOLEAN DEFAULT FALSE,
    location_info JSON,
    expires_at TIMESTAMP NOT NULL,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_session_user (user_id),
    INDEX idx_session_token (session_token),
    INDEX idx_session_expires (expires_at)
);

CREATE TABLE audit_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED,
    company_id BIGINT UNSIGNED NOT NULL,
    action VARCHAR(100) NOT NULL,
    table_name VARCHAR(100),
    record_id BIGINT UNSIGNED,
    old_values JSON,
    new_values JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    INDEX idx_audit_user (user_id),
    INDEX idx_audit_company (company_id),
    INDEX idx_audit_action (action),
    INDEX idx_audit_table (table_name),
    INDEX idx_audit_created (created_at)
);

-- =====================================================
-- PROJECT MANAGEMENT MODULE
-- =====================================================

-- 5. PROJECT MANAGEMENT
CREATE TABLE project_categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    color_code VARCHAR(7),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    INDEX idx_category_company (company_id)
);

CREATE TABLE clients (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    client_code VARCHAR(50),
    name VARCHAR(255) NOT NULL,
    contact_person VARCHAR(255),
    email VARCHAR(255),
    phone VARCHAR(20),
    mobile VARCHAR(20),
    address TEXT,
    city VARCHAR(100),
    state VARCHAR(100),
    country VARCHAR(100),
    postal_code VARCHAR(20),
    tax_number VARCHAR(100),
    payment_terms VARCHAR(100),
    credit_limit DECIMAL(15,2) DEFAULT 0.00,
    client_type ENUM('individual', 'company', 'government') DEFAULT 'individual',
    status ENUM('active', 'inactive', 'blacklisted') DEFAULT 'active',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    INDEX idx_client_company (company_id),
    INDEX idx_client_code (client_code),
    INDEX idx_client_status (status)
);

CREATE TABLE projects (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    client_id BIGINT UNSIGNED,
    category_id BIGINT UNSIGNED,
    project_code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    project_type ENUM('residential', 'commercial', 'industrial', 'infrastructure', 'renovation') NOT NULL,
    priority ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
    status ENUM('planning', 'active', 'on_hold', 'completed', 'cancelled') DEFAULT 'planning',
    progress_percentage DECIMAL(5,2) DEFAULT 0.00,
    
    -- Financial Information
    estimated_budget DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    actual_cost DECIMAL(15,2) DEFAULT 0.00,
    contract_value DECIMAL(15,2) DEFAULT 0.00,
    currency VARCHAR(3) DEFAULT 'MWK',
    
    -- Timeline
    start_date DATE,
    planned_end_date DATE,
    actual_end_date DATE,
    
    -- Location
    site_address TEXT,
    site_city VARCHAR(100),
    site_state VARCHAR(100),
    site_coordinates POINT,
    
    -- Management
    project_manager_id BIGINT UNSIGNED,
    site_supervisor_id BIGINT UNSIGNED,
    
    -- Flags
    is_template BOOLEAN DEFAULT FALSE,
    is_archived BOOLEAN DEFAULT FALSE,
    requires_permit BOOLEAN DEFAULT FALSE,
    
    created_by BIGINT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE SET NULL,
    FOREIGN KEY (category_id) REFERENCES project_categories(id) ON DELETE SET NULL,
    FOREIGN KEY (project_manager_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (site_supervisor_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_project_company (company_id),
    INDEX idx_project_client (client_id),
    INDEX idx_project_status (status),
    INDEX idx_project_code (project_code),
    INDEX idx_project_dates (start_date, planned_end_date),
    INDEX idx_project_manager (project_manager_id)
);

CREATE TABLE project_team_members (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    role VARCHAR(100) NOT NULL,
    responsibilities TEXT,
    hourly_rate DECIMAL(10,2),
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    removed_at TIMESTAMP NULL,
    assigned_by BIGINT UNSIGNED,
    
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_by) REFERENCES users(id) ON DELETE SET NULL,
    
    UNIQUE KEY unique_project_user_active (project_id, user_id, removed_at),
    INDEX idx_team_project (project_id),
    INDEX idx_team_user (user_id)
);

-- 6. TASK MANAGEMENT
CREATE TABLE task_categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    color_code VARCHAR(7),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    INDEX idx_task_category_company (company_id)
);

CREATE TABLE tasks (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_id BIGINT UNSIGNED NOT NULL,
    parent_task_id BIGINT UNSIGNED NULL,
    category_id BIGINT UNSIGNED,
    task_code VARCHAR(50),
    title VARCHAR(255) NOT NULL,
    description TEXT,
    task_type ENUM('milestone', 'task', 'subtask') DEFAULT 'task',
    priority ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
    status ENUM('not_started', 'in_progress', 'review', 'completed', 'cancelled', 'on_hold') DEFAULT 'not_started',
    progress_percentage DECIMAL(5,2) DEFAULT 0.00,
    
    -- Assignment
    assigned_to BIGINT UNSIGNED,
    assigned_by BIGINT UNSIGNED,
    
    -- Timeline
    planned_start_date DATE,
    planned_end_date DATE,
    actual_start_date DATE,
    actual_end_date DATE,
    estimated_hours DECIMAL(8,2),
    actual_hours DECIMAL(8,2) DEFAULT 0.00,
    
    -- Financial
    estimated_cost DECIMAL(12,2) DEFAULT 0.00,
    actual_cost DECIMAL(12,2) DEFAULT 0.00,
    
    -- Dependencies
    depends_on JSON, -- Array of task IDs
    
    -- Flags
    is_critical_path BOOLEAN DEFAULT FALSE,
    requires_approval BOOLEAN DEFAULT FALSE,
    is_billable BOOLEAN DEFAULT TRUE,
    
    created_by BIGINT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES task_categories(id) ON DELETE SET NULL,
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (assigned_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_task_project (project_id),
    INDEX idx_task_assigned (assigned_to),
    INDEX idx_task_status (status),
    INDEX idx_task_parent (parent_task_id),
    INDEX idx_task_dates (planned_start_date, planned_end_date)
);

CREATE TABLE task_comments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    task_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    comment TEXT NOT NULL,
    is_internal BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_comment_task (task_id),
    INDEX idx_comment_user (user_id)
);

CREATE TABLE task_attachments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    task_id BIGINT UNSIGNED NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size BIGINT UNSIGNED,
    file_type VARCHAR(100),
    mime_type VARCHAR(100),
    uploaded_by BIGINT UNSIGNED,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_attachment_task (task_id)
);

-- =====================================================
-- INVENTORY MANAGEMENT MODULE
-- =====================================================

-- 7. INVENTORY MANAGEMENT
CREATE TABLE suppliers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    supplier_code VARCHAR(50),
    name VARCHAR(255) NOT NULL,
    contact_person VARCHAR(255),
    email VARCHAR(255),
    phone VARCHAR(20),
    mobile VARCHAR(20),
    address TEXT,
    city VARCHAR(100),
    state VARCHAR(100),
    country VARCHAR(100),
    tax_number VARCHAR(100),
    payment_terms VARCHAR(100),
    credit_limit DECIMAL(15,2) DEFAULT 0.00,
    supplier_type ENUM('materials', 'equipment', 'services', 'mixed') DEFAULT 'mixed',
    rating DECIMAL(3,2) DEFAULT 0.00,
    status ENUM('active', 'inactive', 'blacklisted') DEFAULT 'active',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    INDEX idx_supplier_company (company_id),
    INDEX idx_supplier_code (supplier_code),
    INDEX idx_supplier_status (status)
);

CREATE TABLE material_categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    parent_id BIGINT UNSIGNED NULL,
    code VARCHAR(50),
    name VARCHAR(255) NOT NULL,
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES material_categories(id) ON DELETE CASCADE,
    INDEX idx_material_category_company (company_id),
    INDEX idx_material_category_parent (parent_id)
);

CREATE TABLE materials (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    category_id BIGINT UNSIGNED,
    item_code VARCHAR(100) NOT NULL,
    barcode VARCHAR(100),
    name VARCHAR(255) NOT NULL,
    description TEXT,
    brand VARCHAR(100),
    model VARCHAR(100),
    specifications TEXT,
    unit VARCHAR(50) NOT NULL DEFAULT 'pcs',
    unit_cost DECIMAL(12,2) DEFAULT 0.00,
    selling_price DECIMAL(12,2) DEFAULT 0.00,
    
    -- Stock Management
    current_stock DECIMAL(12,2) DEFAULT 0.00,
    minimum_stock DECIMAL(12,2) DEFAULT 0.00,
    maximum_stock DECIMAL(12,2) DEFAULT 0.00,
    reorder_level DECIMAL(12,2) DEFAULT 0.00,
    
    -- Material Properties
    weight DECIMAL(10,3),
    dimensions VARCHAR(100),
    color VARCHAR(50),
    material_type ENUM('consumable', 'tool', 'equipment', 'raw_material', 'finished_good') DEFAULT 'consumable',
    
    -- Tracking
    is_tracked BOOLEAN DEFAULT TRUE,
    is_serialized BOOLEAN DEFAULT FALSE,
    requires_inspection BOOLEAN DEFAULT FALSE,
    shelf_life_days INT DEFAULT NULL,
    
    -- Status
    status ENUM('active', 'inactive', 'discontinued') DEFAULT 'active',
    
    created_by BIGINT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES material_categories(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    
    UNIQUE KEY unique_company_item_code (company_id, item_code),
    INDEX idx_material_company (company_id),
    INDEX idx_material_category (category_id),
    INDEX idx_material_barcode (barcode),
    INDEX idx_material_stock (current_stock, minimum_stock)
);

CREATE TABLE warehouses (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    code VARCHAR(50) NOT NULL,
    name VARCHAR(255) NOT NULL,
    address TEXT,
    city VARCHAR(100),
    state VARCHAR(100),
    manager_id BIGINT UNSIGNED,
    warehouse_type ENUM('main', 'site', 'temporary') DEFAULT 'main',
    capacity DECIMAL(12,2),
    status ENUM('active', 'inactive', 'maintenance') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (manager_id) REFERENCES users(id) ON DELETE SET NULL,
    UNIQUE KEY unique_company_warehouse_code (company_id, code),
    INDEX idx_warehouse_company (company_id)
);

CREATE TABLE stock_movements (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    material_id BIGINT UNSIGNED NOT NULL,
    warehouse_id BIGINT UNSIGNED,
    project_id BIGINT UNSIGNED,
    reference_type ENUM('purchase', 'sale', 'transfer', 'adjustment', 'return', 'consumption', 'production') NOT NULL,
    reference_id BIGINT UNSIGNED,
    movement_type ENUM('in', 'out') NOT NULL,
    quantity DECIMAL(12,2) NOT NULL,
    unit_cost DECIMAL(12,2) DEFAULT 0.00,
    total_cost DECIMAL(12,2) DEFAULT 0.00,
    previous_balance DECIMAL(12,2) DEFAULT 0.00,
    new_balance DECIMAL(12,2) DEFAULT 0.00,
    batch_number VARCHAR(100),
    serial_numbers JSON,
    expiry_date DATE,
    notes TEXT,
    moved_by BIGINT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (material_id) REFERENCES materials(id) ON DELETE CASCADE,
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id) ON DELETE SET NULL,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL,
    FOREIGN KEY (moved_by) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_stock_company (company_id),
    INDEX idx_stock_material (material_id),
    INDEX idx_stock_warehouse (warehouse_id),
    INDEX idx_stock_project (project_id),
    INDEX idx_stock_created (created_at)
);

-- =====================================================
-- HUMAN RESOURCES & PAYROLL MODULE
-- =====================================================

-- 8. HR MANAGEMENT
CREATE TABLE departments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    code VARCHAR(50),
    name VARCHAR(255) NOT NULL,
    description TEXT,
    manager_id BIGINT UNSIGNED,
    parent_department_id BIGINT UNSIGNED,
    budget DECIMAL(15,2),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (manager_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (parent_department_id) REFERENCES departments(id) ON DELETE SET NULL,
    INDEX idx_department_company (company_id)
);

CREATE TABLE job_positions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    department_id BIGINT UNSIGNED,
    title VARCHAR(255) NOT NULL,
    code VARCHAR(50),
    description TEXT,
    requirements TEXT,
    min_salary DECIMAL(12,2),
    max_salary DECIMAL(12,2),
    employment_type ENUM('full_time', 'part_time', 'contract', 'temporary', 'intern') DEFAULT 'full_time',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL,
    INDEX idx_position_company (company_id),
    INDEX idx_position_department (department_id)
);

CREATE TABLE employee_details (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL UNIQUE,
    department_id BIGINT UNSIGNED,
    position_id BIGINT UNSIGNED,
    hire_date DATE NOT NULL,
    contract_start_date DATE,
    contract_end_date DATE,
    employment_status ENUM('active', 'resigned', 'terminated', 'retired', 'on_leave') DEFAULT 'active',
    employment_type ENUM('full_time', 'part_time', 'contract', 'temporary', 'intern') DEFAULT 'full_time',
    
    -- Salary Information
    basic_salary DECIMAL(12,2) DEFAULT 0.00,
    currency VARCHAR(3) DEFAULT 'MWK',
    pay_frequency ENUM('monthly', 'weekly', 'daily', 'hourly') DEFAULT 'monthly',
    
    -- Bank Details
    bank_name VARCHAR(255),
    bank_account_number VARCHAR(100),
    bank_branch VARCHAR(255),
    
    -- Tax Information
    tax_number VARCHAR(100),
    tax_exempt BOOLEAN DEFAULT FALSE,
    
    -- Leave Balances
    annual_leave_balance DECIMAL(5,2) DEFAULT 0.00,
    sick_leave_balance DECIMAL(5,2) DEFAULT 0.00,
    
    -- Supervisor
    supervisor_id BIGINT UNSIGNED,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL,
    FOREIGN KEY (position_id) REFERENCES job_positions(id) ON DELETE SET NULL,
    FOREIGN KEY (supervisor_id) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_employee_department (department_id),
    INDEX idx_employee_position (position_id),
    INDEX idx_employee_supervisor (supervisor_id),
    INDEX idx_employee_status (employment_status)
);

-- 9. ATTENDANCE & LEAVE MANAGEMENT
CREATE TABLE attendance_records (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    project_id BIGINT UNSIGNED,
    attendance_date DATE NOT NULL,
    clock_in_time TIME,
    clock_out_time TIME,
    break_start_time TIME,
    break_end_time TIME,
    total_hours DECIMAL(4,2) DEFAULT 0.00,
    overtime_hours DECIMAL(4,2) DEFAULT 0.00,
    attendance_type ENUM('regular', 'overtime', 'holiday', 'weekend') DEFAULT 'regular',
    location_in VARCHAR(255),
    location_out VARCHAR(255),
    gps_coordinates_in POINT,
    gps_coordinates_out POINT,
    ip_address_in VARCHAR(45),
    ip_address_out VARCHAR(45),
    device_info_in TEXT,
    device_info_out TEXT,
    status ENUM('present', 'absent', 'late', 'half_day', 'on_leave') DEFAULT 'present',
    notes TEXT,
    approved_by BIGINT UNSIGNED,
    approved_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
    
    UNIQUE KEY unique_user_date (user_id, attendance_date),
    INDEX idx_attendance_user (user_id),
    INDEX idx_attendance_date (attendance_date),
    INDEX idx_attendance_project (project_id)
);

CREATE TABLE leave_types (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(20) NOT NULL,
    description TEXT,
    max_days_per_year DECIMAL(5,2),
    is_paid BOOLEAN DEFAULT TRUE,
    requires_approval BOOLEAN DEFAULT TRUE,
    min_notice_days INT DEFAULT 1,
    max_consecutive_days INT,
    carry_forward BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    UNIQUE KEY unique_company_leave_code (company_id, code),
    INDEX idx_leave_type_company (company_id)
);

CREATE TABLE leave_applications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    leave_type_id BIGINT UNSIGNED NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    total_days DECIMAL(5,2) NOT NULL,
    reason TEXT,
    emergency_contact VARCHAR(255),
    emergency_phone VARCHAR(20),
    handover_notes TEXT,
    status ENUM('pending', 'approved', 'rejected', 'cancelled') DEFAULT 'pending',
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reviewed_by BIGINT UNSIGNED,
    reviewed_at TIMESTAMP NULL,
    review_comments TEXT,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (leave_type_id) REFERENCES leave_types(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_leave_user (user_id),
    INDEX idx_leave_type (leave_type_id),
    INDEX idx_leave_dates (start_date, end_date),
    INDEX idx_leave_status (status)
);

-- 10. PAYROLL MANAGEMENT
CREATE TABLE pay_periods (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    pay_date DATE NOT NULL,
    status ENUM('draft', 'processing', 'approved', 'paid', 'closed') DEFAULT 'draft',
    total_gross_pay DECIMAL(15,2) DEFAULT 0.00,
    total_deductions DECIMAL(15,2) DEFAULT 0.00,
    total_net_pay DECIMAL(15,2) DEFAULT 0.00,
    created_by BIGINT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_pay_period_company (company_id),
    INDEX idx_pay_period_dates (start_date, end_date)
);

CREATE TABLE allowance_types (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(20) NOT NULL,
    description TEXT,
    is_taxable BOOLEAN DEFAULT TRUE,
    is_fixed BOOLEAN DEFAULT FALSE,
    default_amount DECIMAL(12,2) DEFAULT 0.00,
    calculation_type ENUM('fixed', 'percentage', 'hourly', 'daily') DEFAULT 'fixed',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    UNIQUE KEY unique_company_allowance_code (company_id, code),
    INDEX idx_allowance_company (company_id)
);

CREATE TABLE deduction_types (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(20) NOT NULL,
    description TEXT,
    is_mandatory BOOLEAN DEFAULT FALSE,
    is_pre_tax BOOLEAN DEFAULT FALSE,
    calculation_type ENUM('fixed', 'percentage', 'tiered') DEFAULT 'fixed',
    default_amount DECIMAL(12,2) DEFAULT 0.00,
    default_percentage DECIMAL(5,2) DEFAULT 0.00,
    max_amount DECIMAL(12,2),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    UNIQUE KEY unique_company_deduction_code (company_id, code),
    INDEX idx_deduction_company (company_id)
);

CREATE TABLE employee_allowances (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    allowance_type_id BIGINT UNSIGNED NOT NULL,
    amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    percentage DECIMAL(5,2) DEFAULT 0.00,
    is_active BOOLEAN DEFAULT TRUE,
    effective_from DATE,
    effective_to DATE,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (allowance_type_id) REFERENCES allowance_types(id) ON DELETE CASCADE,
    INDEX idx_employee_allowance_user (user_id),
    INDEX idx_employee_allowance_type (allowance_type_id)
);

CREATE TABLE employee_deductions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    deduction_type_id BIGINT UNSIGNED NOT NULL,
    amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    percentage DECIMAL(5,2) DEFAULT 0.00,
    is_active BOOLEAN DEFAULT TRUE,
    effective_from DATE,
    effective_to DATE,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (deduction_type_id) REFERENCES deduction_types(id) ON DELETE CASCADE,
    INDEX idx_employee_deduction_user (user_id),
    INDEX idx_employee_deduction_type (deduction_type_id)
);

CREATE TABLE payroll_records (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pay_period_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    
    -- Basic Salary Components
    basic_salary DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    gross_salary DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    
    -- Working Hours
    regular_hours DECIMAL(6,2) DEFAULT 0.00,
    overtime_hours DECIMAL(6,2) DEFAULT 0.00,
    total_hours DECIMAL(6,2) DEFAULT 0.00,
    
    -- Rates
    hourly_rate DECIMAL(10,2) DEFAULT 0.00,
    overtime_rate DECIMAL(10,2) DEFAULT 0.00,
    
    -- Earnings
    regular_pay DECIMAL(12,2) DEFAULT 0.00,
    overtime_pay DECIMAL(12,2) DEFAULT 0.00,
    total_allowances DECIMAL(12,2) DEFAULT 0.00,
    bonus DECIMAL(12,2) DEFAULT 0.00,
    commission DECIMAL(12,2) DEFAULT 0.00,
    total_earnings DECIMAL(12,2) DEFAULT 0.00,
    
    -- Deductions
    total_deductions DECIMAL(12,2) DEFAULT 0.00,
    tax_deduction DECIMAL(12,2) DEFAULT 0.00,
    social_security DECIMAL(12,2) DEFAULT 0.00,
    other_deductions DECIMAL(12,2) DEFAULT 0.00,
    
    -- Net Pay
    net_pay DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    
    -- Payment Details
    payment_method ENUM('bank_transfer', 'cash', 'cheque', 'mobile_money') DEFAULT 'bank_transfer',
    payment_reference VARCHAR(100),
    payment_date DATE,
    payment_status ENUM('pending', 'processed', 'failed', 'cancelled') DEFAULT 'pending',
    
    -- Audit
    calculated_by BIGINT UNSIGNED,
    approved_by BIGINT UNSIGNED,
    paid_by BIGINT UNSIGNED,
    notes TEXT,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (pay_period_id) REFERENCES pay_periods(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (calculated_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (paid_by) REFERENCES users(id) ON DELETE SET NULL,
    
    UNIQUE KEY unique_payroll_period_user (pay_period_id, user_id),
    INDEX idx_payroll_period (pay_period_id),
    INDEX idx_payroll_user (user_id),
    INDEX idx_payroll_payment_status (payment_status)
);

CREATE TABLE payroll_allowance_details (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    payroll_record_id BIGINT UNSIGNED NOT NULL,
    allowance_type_id BIGINT UNSIGNED NOT NULL,
    amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    is_taxable BOOLEAN DEFAULT TRUE,
    
    FOREIGN KEY (payroll_record_id) REFERENCES payroll_records(id) ON DELETE CASCADE,
    FOREIGN KEY (allowance_type_id) REFERENCES allowance_types(id) ON DELETE CASCADE,
    INDEX idx_payroll_allowance_record (payroll_record_id)
);

CREATE TABLE payroll_deduction_details (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    payroll_record_id BIGINT UNSIGNED NOT NULL,
    deduction_type_id BIGINT UNSIGNED NOT NULL,
    amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    
    FOREIGN KEY (payroll_record_id) REFERENCES payroll_records(id) ON DELETE CASCADE,
    FOREIGN KEY (deduction_type_id) REFERENCES deduction_types(id) ON DELETE CASCADE,
    INDEX idx_payroll_deduction_record (payroll_record_id)
);

-- =====================================================
-- ACCOUNTING & FINANCIAL MODULE
-- =====================================================

-- 11. CHART OF ACCOUNTS & FINANCIAL MANAGEMENT
CREATE TABLE account_categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(20),
    account_type ENUM('asset', 'liability', 'equity', 'revenue', 'expense') NOT NULL,
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    INDEX idx_account_category_company (company_id),
    INDEX idx_account_type (account_type)
);

CREATE TABLE chart_of_accounts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    category_id BIGINT UNSIGNED,
    parent_account_id BIGINT UNSIGNED,
    account_code VARCHAR(50) NOT NULL,
    account_name VARCHAR(255) NOT NULL,
    account_type ENUM('asset', 'liability', 'equity', 'revenue', 'expense') NOT NULL,
    account_subtype VARCHAR(100),
    description TEXT,
    is_system_account BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    balance DECIMAL(15,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES account_categories(id) ON DELETE SET NULL,
    FOREIGN KEY (parent_account_id) REFERENCES chart_of_accounts(id) ON DELETE SET NULL,
    
    UNIQUE KEY unique_company_account_code (company_id, account_code),
    INDEX idx_chart_company (company_id),
    INDEX idx_chart_type (account_type),
    INDEX idx_chart_parent (parent_account_id)
);

CREATE TABLE budget_categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    budget_type ENUM('revenue', 'expense', 'capital') NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    INDEX idx_budget_category_company (company_id)
);

CREATE TABLE budgets (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    project_id BIGINT UNSIGNED,
    category_id BIGINT UNSIGNED,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    budget_period ENUM('monthly', 'quarterly', 'yearly', 'project') DEFAULT 'yearly',
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    total_budget DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    allocated_budget DECIMAL(15,2) DEFAULT 0.00,
    spent_amount DECIMAL(15,2) DEFAULT 0.00,
    remaining_budget DECIMAL(15,2) DEFAULT 0.00,
    status ENUM('draft', 'approved', 'active', 'completed', 'cancelled') DEFAULT 'draft',
    created_by BIGINT UNSIGNED,
    approved_by BIGINT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES budget_categories(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_budget_company (company_id),
    INDEX idx_budget_project (project_id),
    INDEX idx_budget_dates (start_date, end_date)
);

CREATE TABLE budget_line_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    budget_id BIGINT UNSIGNED NOT NULL,
    account_id BIGINT UNSIGNED,
    line_item_name VARCHAR(255) NOT NULL,
    description TEXT,
    budgeted_amount DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    spent_amount DECIMAL(15,2) DEFAULT 0.00,
    variance DECIMAL(15,2) DEFAULT 0.00,
    variance_percentage DECIMAL(5,2) DEFAULT 0.00,
    
    FOREIGN KEY (budget_id) REFERENCES budgets(id) ON DELETE CASCADE,
    FOREIGN KEY (account_id) REFERENCES chart_of_accounts(id) ON DELETE SET NULL,
    INDEX idx_budget_line_budget (budget_id)
);

-- 12. TRANSACTIONS & JOURNAL ENTRIES
CREATE TABLE transaction_categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(20),
    transaction_type ENUM('income', 'expense', 'transfer') NOT NULL,
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    INDEX idx_transaction_category_company (company_id)
);

CREATE TABLE journal_entries (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    entry_number VARCHAR(50) NOT NULL,
    reference_type ENUM('manual', 'invoice', 'payment', 'payroll', 'adjustment', 'accrual', 'depreciation') DEFAULT 'manual',
    reference_id BIGINT UNSIGNED,
    entry_date DATE NOT NULL,
    description TEXT,
    total_debit DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    total_credit DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    status ENUM('draft', 'posted', 'reversed') DEFAULT 'draft',
    posted_by BIGINT UNSIGNED,
    posted_at TIMESTAMP NULL,
    reversed_by BIGINT UNSIGNED,
    reversed_at TIMESTAMP NULL,
    reversal_reason TEXT,
    created_by BIGINT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (posted_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (reversed_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    
    UNIQUE KEY unique_company_entry_number (company_id, entry_number),
    INDEX idx_journal_company (company_id),
    INDEX idx_journal_date (entry_date),
    INDEX idx_journal_status (status),
    INDEX idx_journal_reference (reference_type, reference_id)
);

CREATE TABLE journal_entry_lines (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    journal_entry_id BIGINT UNSIGNED NOT NULL,
    account_id BIGINT UNSIGNED NOT NULL,
    project_id BIGINT UNSIGNED,
    description TEXT,
    debit_amount DECIMAL(15,2) DEFAULT 0.00,
    credit_amount DECIMAL(15,2) DEFAULT 0.00,
    line_order INT DEFAULT 1,
    
    FOREIGN KEY (journal_entry_id) REFERENCES journal_entries(id) ON DELETE CASCADE,
    FOREIGN KEY (account_id) REFERENCES chart_of_accounts(id) ON DELETE CASCADE,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL,
    
    INDEX idx_journal_line_entry (journal_entry_id),
    INDEX idx_journal_line_account (account_id),
    INDEX idx_journal_line_project (project_id)
);

-- 13. INVOICING & BILLING
CREATE TABLE invoice_templates (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    template_html TEXT,
    is_default BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    INDEX idx_invoice_template_company (company_id)
);

CREATE TABLE invoices (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    client_id BIGINT UNSIGNED NOT NULL,
    project_id BIGINT UNSIGNED,
    invoice_number VARCHAR(50) NOT NULL,
    invoice_date DATE NOT NULL,
    due_date DATE NOT NULL,
    reference_number VARCHAR(100),
    
    -- Financial Details
    subtotal DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    tax_amount DECIMAL(15,2) DEFAULT 0.00,
    discount_amount DECIMAL(15,2) DEFAULT 0.00,
    total_amount DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    paid_amount DECIMAL(15,2) DEFAULT 0.00,
    balance_due DECIMAL(15,2) DEFAULT 0.00,
    currency VARCHAR(3) DEFAULT 'MWK',
    
    -- Status & Terms
    status ENUM('draft', 'sent', 'viewed', 'partial', 'paid', 'overdue', 'cancelled') DEFAULT 'draft',
    payment_terms VARCHAR(255),
    notes TEXT,
    terms_conditions TEXT,
    
    -- Tracking
    sent_at TIMESTAMP NULL,
    viewed_at TIMESTAMP NULL,
    paid_at TIMESTAMP NULL,
    
    created_by BIGINT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    
    UNIQUE KEY unique_company_invoice_number (company_id, invoice_number),
    INDEX idx_invoice_company (company_id),
    INDEX idx_invoice_client (client_id),
    INDEX idx_invoice_project (project_id),
    INDEX idx_invoice_status (status),
    INDEX idx_invoice_dates (invoice_date, due_date)
);

CREATE TABLE invoice_line_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    invoice_id BIGINT UNSIGNED NOT NULL,
    material_id BIGINT UNSIGNED,
    description TEXT NOT NULL,
    quantity DECIMAL(12,2) NOT NULL DEFAULT 1.00,
    unit_price DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    line_total DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    tax_rate DECIMAL(5,2) DEFAULT 0.00,
    tax_amount DECIMAL(12,2) DEFAULT 0.00,
    line_order INT DEFAULT 1,
    
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE,
    FOREIGN KEY (material_id) REFERENCES materials(id) ON DELETE SET NULL,
    INDEX idx_invoice_line_invoice (invoice_id)
);

-- 14. PAYMENTS & RECEIPTS
CREATE TABLE payment_methods (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(20),
    account_id BIGINT UNSIGNED,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (account_id) REFERENCES chart_of_accounts(id) ON DELETE SET NULL,
    INDEX idx_payment_method_company (company_id)
);

CREATE TABLE payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    invoice_id BIGINT UNSIGNED,
    client_id BIGINT UNSIGNED,
    supplier_id BIGINT UNSIGNED,
    payment_method_id BIGINT UNSIGNED,
    receipt_number VARCHAR(50) NOT NULL,
    payment_date DATE NOT NULL,
    payment_type ENUM('received', 'paid') NOT NULL,
    amount DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    currency VARCHAR(3) DEFAULT 'MWK',
    exchange_rate DECIMAL(10,4) DEFAULT 1.0000,
    reference_number VARCHAR(100),
    bank_reference VARCHAR(100),
    notes TEXT,
    status ENUM('pending', 'cleared', 'bounced', 'cancelled') DEFAULT 'cleared',
    recorded_by BIGINT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE SET NULL,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE SET NULL,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL,
    FOREIGN KEY (payment_method_id) REFERENCES payment_methods(id) ON DELETE SET NULL,
    FOREIGN KEY (recorded_by) REFERENCES users(id) ON DELETE SET NULL,
    
    UNIQUE KEY unique_company_receipt_number (company_id, receipt_number),
    INDEX idx_payment_company (company_id),
    INDEX idx_payment_invoice (invoice_id),
    INDEX idx_payment_client (client_id),
    INDEX idx_payment_supplier (supplier_id),
    INDEX idx_payment_date (payment_date),
    INDEX idx_payment_type (payment_type)
);

-- =====================================================
-- EQUIPMENT & ASSET MANAGEMENT MODULE
-- =====================================================

-- 15. EQUIPMENT & ASSET TRACKING
CREATE TABLE asset_categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(20),
    description TEXT,
    depreciation_method ENUM('straight_line', 'declining_balance', 'units_of_production') DEFAULT 'straight_line',
    useful_life_years INT DEFAULT 5,
    salvage_value_percentage DECIMAL(5,2) DEFAULT 0.00,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    INDEX idx_asset_category_company (company_id)
);

CREATE TABLE assets (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    category_id BIGINT UNSIGNED,
    asset_code VARCHAR(100) NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    brand VARCHAR(100),
    model VARCHAR(100),
    serial_number VARCHAR(100),
    barcode VARCHAR(100),
    
    -- Financial Information
    purchase_cost DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    current_value DECIMAL(15,2) DEFAULT 0.00,
    accumulated_depreciation DECIMAL(15,2) DEFAULT 0.00,
    salvage_value DECIMAL(15,2) DEFAULT 0.00,
    
    -- Purchase Details
    supplier_id BIGINT UNSIGNED,
    purchase_date DATE,
    warranty_start_date DATE,
    warranty_end_date DATE,
    purchase_order_number VARCHAR(100),
    
    -- Location & Assignment
    current_location VARCHAR(255),
    assigned_to BIGINT UNSIGNED,
    project_id BIGINT UNSIGNED,
    warehouse_id BIGINT UNSIGNED,
    
    -- Status & Condition
    status ENUM('available', 'in_use', 'maintenance', 'repair', 'disposed', 'sold', 'stolen') DEFAULT 'available',
    condition_status ENUM('excellent', 'good', 'fair', 'poor', 'damaged') DEFAULT 'good',
    
    -- Maintenance
    last_maintenance_date DATE,
    next_maintenance_date DATE,
    maintenance_interval_days INT,
    
    -- Insurance
    insurance_policy_number VARCHAR(100),
    insurance_expiry_date DATE,
    
    created_by BIGINT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES asset_categories(id) ON DELETE SET NULL,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL,
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL,
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    
    UNIQUE KEY unique_company_asset_code (company_id, asset_code),
    INDEX idx_asset_company (company_id),
    INDEX idx_asset_category (category_id),
    INDEX idx_asset_status (status),
    INDEX idx_asset_assigned (assigned_to),
    INDEX idx_asset_project (project_id),
    INDEX idx_asset_serial (serial_number)
);

CREATE TABLE asset_assignments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    asset_id BIGINT UNSIGNED NOT NULL,
    assigned_to BIGINT UNSIGNED NOT NULL,
    project_id BIGINT UNSIGNED,
    assignment_date DATE NOT NULL,
    expected_return_date DATE,
    actual_return_date DATE,
    assignment_notes TEXT,
    return_notes TEXT,
    condition_at_assignment ENUM('excellent', 'good', 'fair', 'poor', 'damaged') DEFAULT 'good',
    condition_at_return ENUM('excellent', 'good', 'fair', 'poor', 'damaged'),
    assigned_by BIGINT UNSIGNED,
    returned_by BIGINT UNSIGNED,
    status ENUM('active', 'returned', 'overdue') DEFAULT 'active',
    
    FOREIGN KEY (asset_id) REFERENCES assets(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL,
    FOREIGN KEY (assigned_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (returned_by) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_assignment_asset (asset_id),
    INDEX idx_assignment_user (assigned_to),
    INDEX idx_assignment_project (project_id),
    INDEX idx_assignment_status (status)
);

CREATE TABLE maintenance_schedules (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    asset_id BIGINT UNSIGNED NOT NULL,
    maintenance_type ENUM('preventive', 'corrective', 'inspection', 'calibration') NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    frequency_type ENUM('days', 'weeks', 'months', 'years', 'hours', 'kilometers') NOT NULL,
    frequency_value INT NOT NULL,
    next_due_date DATE NOT NULL,
    assigned_to BIGINT UNSIGNED,
    estimated_cost DECIMAL(12,2) DEFAULT 0.00,
    estimated_hours DECIMAL(6,2) DEFAULT 0.00,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (asset_id) REFERENCES assets(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_maintenance_asset (asset_id),
    INDEX idx_maintenance_due_date (next_due_date),
    INDEX idx_maintenance_assigned (assigned_to)
);

CREATE TABLE maintenance_records (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    asset_id BIGINT UNSIGNED NOT NULL,
    schedule_id BIGINT UNSIGNED,
    maintenance_type ENUM('preventive', 'corrective', 'inspection', 'calibration', 'emergency') NOT NULL,
    work_order_number VARCHAR(50),
    title VARCHAR(255) NOT NULL,
    description TEXT,
    maintenance_date DATE NOT NULL,
    start_time TIME,
    end_time TIME,
    
    -- Personnel
    performed_by BIGINT UNSIGNED,
    supervised_by BIGINT UNSIGNED,
    
    -- Costs
    labor_cost DECIMAL(12,2) DEFAULT 0.00,
    parts_cost DECIMAL(12,2) DEFAULT 0.00,
    other_cost DECIMAL(12,2) DEFAULT 0.00,
    total_cost DECIMAL(12,2) DEFAULT 0.00,
    
    -- Status
    status ENUM('scheduled', 'in_progress', 'completed', 'cancelled') DEFAULT 'scheduled',
    completion_notes TEXT,
    next_maintenance_date DATE,
    
    created_by BIGINT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (asset_id) REFERENCES assets(id) ON DELETE CASCADE,
    FOREIGN KEY (schedule_id) REFERENCES maintenance_schedules(id) ON DELETE SET NULL,
    FOREIGN KEY (performed_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (supervised_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_maintenance_record_asset (asset_id),
    INDEX idx_maintenance_record_date (maintenance_date),
    INDEX idx_maintenance_record_status (status)
);

-- =====================================================
-- TIMESHEET & TIME TRACKING MODULE
-- =====================================================

-- 16. TIMESHEET MANAGEMENT
CREATE TABLE timesheet_periods (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status ENUM('open', 'locked', 'processed') DEFAULT 'open',
    submission_deadline DATE,
    approval_deadline DATE,
    created_by BIGINT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_timesheet_period_company (company_id),
    INDEX idx_timesheet_period_dates (start_date, end_date)
);

CREATE TABLE timesheets (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    period_id BIGINT UNSIGNED NOT NULL,
    total_hours DECIMAL(6,2) DEFAULT 0.00,
    regular_hours DECIMAL(6,2) DEFAULT 0.00,
    overtime_hours DECIMAL(6,2) DEFAULT 0.00,
    billable_hours DECIMAL(6,2) DEFAULT 0.00,
    non_billable_hours DECIMAL(6,2) DEFAULT 0.00,
    status ENUM('draft', 'submitted', 'approved', 'rejected', 'processed') DEFAULT 'draft',
    submitted_at TIMESTAMP NULL,
    approved_by BIGINT UNSIGNED,
    approved_at TIMESTAMP NULL,
    approval_comments TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (period_id) REFERENCES timesheet_periods(id) ON DELETE CASCADE,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
    
    UNIQUE KEY unique_user_period (user_id, period_id),
    INDEX idx_timesheet_user (user_id),
    INDEX idx_timesheet_period (period_id),
    INDEX idx_timesheet_status (status)
);

CREATE TABLE timesheet_entries (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    timesheet_id BIGINT UNSIGNED NOT NULL,
    project_id BIGINT UNSIGNED NOT NULL,
    task_id BIGINT UNSIGNED,
    work_date DATE NOT NULL,
    start_time TIME,
    end_time TIME,
    break_time DECIMAL(4,2) DEFAULT 0.00,
    total_hours DECIMAL(6,2) NOT NULL DEFAULT 0.00,
    overtime_hours DECIMAL(6,2) DEFAULT 0.00,
    work_type ENUM('regular', 'overtime', 'holiday', 'weekend') DEFAULT 'regular',
    is_billable BOOLEAN DEFAULT TRUE,
    hourly_rate DECIMAL(10,2),
    description TEXT,
    location VARCHAR(255),
    approved_hours DECIMAL(6,2),
    approval_comments TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (timesheet_id) REFERENCES timesheets(id) ON DELETE CASCADE,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE SET NULL,
    
    INDEX idx_timesheet_entry_timesheet (timesheet_id),
    INDEX idx_timesheet_entry_project (project_id),
    INDEX idx_timesheet_entry_task (task_id),
    INDEX idx_timesheet_entry_date (work_date)
);

-- =====================================================
-- INCIDENT & SAFETY REPORTING MODULE
-- =====================================================

-- 17. SAFETY & INCIDENT MANAGEMENT
CREATE TABLE incident_categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(20),
    description TEXT,
    severity_level ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
    requires_investigation BOOLEAN DEFAULT FALSE,
    notification_required BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    INDEX idx_incident_category_company (company_id)
);

CREATE TABLE incidents (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    project_id BIGINT UNSIGNED,
    category_id BIGINT UNSIGNED,
    incident_number VARCHAR(50) NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    incident_date DATE NOT NULL,
    incident_time TIME,
    location VARCHAR(255),
    
    -- Severity & Classification
    severity ENUM('minor', 'moderate', 'major', 'critical', 'fatal') NOT NULL,
    incident_type ENUM('accident', 'near_miss', 'unsafe_condition', 'unsafe_act', 'environmental', 'security') NOT NULL,
    
    -- People Involved
    reported_by BIGINT UNSIGNED NOT NULL,
    persons_involved JSON, -- Array of user IDs or names
    witnesses JSON, -- Array of user IDs or names
    
    -- Impact Assessment
    injury_sustained BOOLEAN DEFAULT FALSE,
    property_damage BOOLEAN DEFAULT FALSE,
    environmental_impact BOOLEAN DEFAULT FALSE,
    work_disruption BOOLEAN DEFAULT FALSE,
    estimated_cost DECIMAL(12,2) DEFAULT 0.00,
    
    -- Response & Investigation
    immediate_action_taken TEXT,
    investigation_required BOOLEAN DEFAULT FALSE,
    investigation_assigned_to BIGINT UNSIGNED,
    investigation_deadline DATE,
    root_cause_analysis TEXT,
    corrective_actions TEXT,
    preventive_actions TEXT,
    
    -- Status & Follow-up
    status ENUM('open', 'investigating', 'resolved', 'closed') DEFAULT 'open',
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    resolved_date DATE,
    resolved_by BIGINT UNSIGNED,
    closure_notes TEXT,
    
    -- Notifications
    authorities_notified BOOLEAN DEFAULT FALSE,
    notification_date DATE,
    notification_reference VARCHAR(100),
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL,
    FOREIGN KEY (category_id) REFERENCES incident_categories(id) ON DELETE SET NULL,
    FOREIGN KEY (reported_by) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (investigation_assigned_to) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (resolved_by) REFERENCES users(id) ON DELETE SET NULL,
    
    UNIQUE KEY unique_company_incident_number (company_id, incident_number),
    INDEX idx_incident_company (company_id),
    INDEX idx_incident_project (project_id),
    INDEX idx_incident_date (incident_date),
    INDEX idx_incident_status (status),
    INDEX idx_incident_severity (severity)
);

CREATE TABLE incident_attachments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    incident_id BIGINT UNSIGNED NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size BIGINT UNSIGNED,
    file_type VARCHAR(100),
    mime_type VARCHAR(100),
    attachment_type ENUM('photo', 'video', 'document', 'audio', 'other') DEFAULT 'photo',
    description TEXT,
    uploaded_by BIGINT UNSIGNED,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (incident_id) REFERENCES incidents(id) ON DELETE CASCADE,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_incident_attachment_incident (incident_id)
);

CREATE TABLE safety_inspections (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    project_id BIGINT UNSIGNED,
    inspection_number VARCHAR(50) NOT NULL,
    inspection_type ENUM('routine', 'surprise', 'regulatory', 'post_incident', 'pre_work') NOT NULL,
    inspection_date DATE NOT NULL,
    inspection_time TIME,
    location VARCHAR(255),
    
    -- Inspector Details
    inspector_id BIGINT UNSIGNED NOT NULL,
    inspector_type ENUM('internal', 'external', 'regulatory') DEFAULT 'internal',
    external_inspector_name VARCHAR(255),
    external_company VARCHAR(255),
    
    -- Inspection Results
    overall_score DECIMAL(5,2),
    max_possible_score DECIMAL(5,2),
    compliance_percentage DECIMAL(5,2),
    
    -- Status
    status ENUM('scheduled', 'in_progress', 'completed', 'cancelled') DEFAULT 'scheduled',
    findings_summary TEXT,
    recommendations TEXT,
    follow_up_required BOOLEAN DEFAULT FALSE,
    follow_up_date DATE,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL,
    FOREIGN KEY (inspector_id) REFERENCES users(id) ON DELETE CASCADE,
    
    UNIQUE KEY unique_company_inspection_number (company_id, inspection_number),
    INDEX idx_safety_inspection_company (company_id),
    INDEX idx_safety_inspection_project (project_id),
    INDEX idx_safety_inspection_date (inspection_date)
);

CREATE TABLE safety_inspection_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    inspection_id BIGINT UNSIGNED NOT NULL,
    category VARCHAR(255) NOT NULL,
    item_description TEXT NOT NULL,
    compliance_status ENUM('compliant', 'non_compliant', 'partial', 'not_applicable') NOT NULL,
    severity ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
    score DECIMAL(5,2) DEFAULT 0.00,
    max_score DECIMAL(5,2) DEFAULT 0.00,
    observations TEXT,
    corrective_action_required TEXT,
    target_completion_date DATE,
    assigned_to BIGINT UNSIGNED,
    item_order INT DEFAULT 1,
    
    FOREIGN KEY (inspection_id) REFERENCES safety_inspections(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_safety_item_inspection (inspection_id)
);

-- =====================================================
-- FILE MANAGEMENT MODULE
-- =====================================================

-- 18. DOCUMENT & FILE MANAGEMENT
CREATE TABLE file_categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    parent_id BIGINT UNSIGNED NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    color_code VARCHAR(7),
    icon VARCHAR(50),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES file_categories(id) ON DELETE CASCADE,
    INDEX idx_file_category_company (company_id),
    INDEX idx_file_category_parent (parent_id)
);

CREATE TABLE folders (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    parent_folder_id BIGINT UNSIGNED NULL,
    project_id BIGINT UNSIGNED,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    folder_path VARCHAR(1000),
    access_level ENUM('public', 'private', 'restricted', 'confidential') DEFAULT 'private',
    created_by BIGINT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_folder_id) REFERENCES folders(id) ON DELETE CASCADE,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_folder_company (company_id),
    INDEX idx_folder_parent (parent_folder_id),
    INDEX idx_folder_project (project_id)
);

CREATE TABLE files (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    folder_id BIGINT UNSIGNED,
    category_id BIGINT UNSIGNED,
    project_id BIGINT UNSIGNED,
    
    -- File Details
    original_name VARCHAR(255) NOT NULL,
    stored_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size BIGINT UNSIGNED NOT NULL,
    file_extension VARCHAR(10),
    mime_type VARCHAR(100),
    file_hash VARCHAR(64), -- For duplicate detection
    
    -- Metadata
    title VARCHAR(255),
    description TEXT,
    tags JSON,
    version_number VARCHAR(20) DEFAULT '1.0',
    is_current_version BOOLEAN DEFAULT TRUE,
    parent_file_id BIGINT UNSIGNED, -- For versioning
    
    -- Access Control
    access_level ENUM('public', 'private', 'restricted', 'confidential') DEFAULT 'private',
    download_count INT DEFAULT 0,
    view_count INT DEFAULT 0,
    last_accessed_at TIMESTAMP NULL,
    
    -- Status
    status ENUM('active', 'archived', 'deleted') DEFAULT 'active',
    is_locked BOOLEAN DEFAULT FALSE,
    locked_by BIGINT UNSIGNED,
    locked_at TIMESTAMP NULL,
    
    -- Audit
    uploaded_by BIGINT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (folder_id) REFERENCES folders(id) ON DELETE SET NULL,
    FOREIGN KEY (category_id) REFERENCES file_categories(id) ON DELETE SET NULL,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL,
    FOREIGN KEY (parent_file_id) REFERENCES files(id) ON DELETE SET NULL,
    FOREIGN KEY (locked_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_file_company (company_id),
    INDEX idx_file_folder (folder_id),
    INDEX idx_file_project (project_id),
    INDEX idx_file_hash (file_hash),
    INDEX idx_file_status (status),
    INDEX idx_file_uploaded (uploaded_by)
);

CREATE TABLE file_permissions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    file_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED,
    role_id BIGINT UNSIGNED,
    permission_type ENUM('view', 'download', 'edit', 'delete', 'share') NOT NULL,
    granted_by BIGINT UNSIGNED,
    granted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NULL,
    
    FOREIGN KEY (file_id) REFERENCES files(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (granted_by) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_file_permission_file (file_id),
    INDEX idx_file_permission_user (user_id),
    INDEX idx_file_permission_role (role_id)
);

CREATE TABLE file_comments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    file_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    comment TEXT NOT NULL,
    is_resolved BOOLEAN DEFAULT FALSE,
    resolved_by BIGINT UNSIGNED,
    resolved_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (file_id) REFERENCES files(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (resolved_by) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_file_comment_file (file_id),
    INDEX idx_file_comment_user (user_id)
);

CREATE TABLE file_access_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    file_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED,
    action ENUM('view', 'download', 'edit', 'delete', 'share', 'comment') NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    accessed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (file_id) REFERENCES files(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_file_access_file (file_id),
    INDEX idx_file_access_user (user_id),
    INDEX idx_file_access_date (accessed_at)
);

-- =====================================================
-- REPORTING & ANALYTICS MODULE
-- =====================================================

-- 19. CUSTOM REPORTS & DASHBOARDS
CREATE TABLE report_categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    icon VARCHAR(50),
    sort_order INT DEFAULT 1,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    INDEX idx_report_category_company (company_id)
);

CREATE TABLE custom_reports (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    category_id BIGINT UNSIGNED,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    report_type ENUM('tabular', 'chart', 'dashboard', 'pivot') DEFAULT 'tabular',
    data_source VARCHAR(100) NOT NULL, -- Table or view name
    sql_query TEXT,
    report_config JSON, -- Chart config, filters, etc.
    is_public BOOLEAN DEFAULT FALSE,
    is_scheduled BOOLEAN DEFAULT FALSE,
    schedule_frequency ENUM('daily', 'weekly', 'monthly', 'quarterly', 'yearly'),
    schedule_config JSON,
    created_by BIGINT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES report_categories(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_custom_report_company (company_id),
    INDEX idx_custom_report_category (category_id),
    INDEX idx_custom_report_created_by (created_by)
);

CREATE TABLE dashboard_widgets (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    widget_type ENUM('chart', 'kpi', 'table', 'calendar', 'task_list', 'recent_activity') NOT NULL,
    title VARCHAR(255) NOT NULL,
    data_source VARCHAR(100),
    widget_config JSON,
    position_x INT DEFAULT 0,
    position_y INT DEFAULT 0,
    width INT DEFAULT 4,
    height INT DEFAULT 3,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    
    INDEX idx_dashboard_widget_company (company_id),
    INDEX idx_dashboard_widget_user (user_id)
);

-- =====================================================
-- NOTIFICATION & COMMUNICATION MODULE
-- =====================================================

-- 20. NOTIFICATIONS & ALERTS
CREATE TABLE notification_templates (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    notification_type ENUM('email', 'sms', 'push', 'in_app') NOT NULL,
    event_trigger VARCHAR(100) NOT NULL,
    subject_template VARCHAR(500),
    body_template TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    INDEX idx_notification_template_company (company_id),
    INDEX idx_notification_template_trigger (event_trigger)
);

CREATE TABLE notifications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    notification_type ENUM('email', 'sms', 'push', 'in_app') NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    related_type VARCHAR(100), -- e.g., 'project', 'task', 'invoice'
    related_id BIGINT UNSIGNED,
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    status ENUM('pending', 'sent', 'delivered', 'failed', 'read') DEFAULT 'pending',
    is_read BOOLEAN DEFAULT FALSE,
    read_at TIMESTAMP NULL,
    sent_at TIMESTAMP NULL,
    delivery_status TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    
    INDEX idx_notification_company (company_id),
    INDEX idx_notification_user (user_id),
    INDEX idx_notification_status (status),
    INDEX idx_notification_type (notification_type),
    INDEX idx_notification_related (related_type, related_id)
);

CREATE TABLE system_alerts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    alert_type ENUM('low_stock', 'overdue_task', 'budget_exceeded', 'maintenance_due', 'safety_incident', 'payment_overdue', 'contract_expiry') NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    severity ENUM('info', 'warning', 'error', 'critical') DEFAULT 'info',
    related_type VARCHAR(100),
    related_id BIGINT UNSIGNED,
    threshold_value DECIMAL(15,2),
    current_value DECIMAL(15,2),
    is_acknowledged BOOLEAN DEFAULT FALSE,
    acknowledged_by BIGINT UNSIGNED,
    acknowledged_at TIMESTAMP NULL,
    is_resolved BOOLEAN DEFAULT FALSE,
    resolved_by BIGINT UNSIGNED,
    resolved_at TIMESTAMP NULL,
    resolution_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (acknowledged_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (resolved_by) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_system_alert_company (company_id),
    INDEX idx_system_alert_type (alert_type),
    INDEX idx_system_alert_severity (severity),
    INDEX idx_system_alert_status (is_acknowledged, is_resolved)
);

-- =====================================================
-- MOBILE APP SUPPORT TABLES
-- =====================================================

-- 21. MOBILE APP SPECIFIC FEATURES
CREATE TABLE mobile_app_versions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    version_number VARCHAR(20) NOT NULL,
    build_number INT NOT NULL,
    platform ENUM('android', 'ios') NOT NULL,
    min_os_version VARCHAR(20),
    release_date DATE NOT NULL,
    is_mandatory_update BOOLEAN DEFAULT FALSE,
    download_url VARCHAR(500),
    release_notes TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_platform_version (platform, version_number),
    INDEX idx_mobile_version_platform (platform),
    INDEX idx_mobile_version_active (is_active)
);

CREATE TABLE device_registrations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    device_token VARCHAR(500) NOT NULL,
    device_type ENUM('android', 'ios') NOT NULL,
    device_model VARCHAR(100),
    os_version VARCHAR(20),
    app_version VARCHAR(20),
    is_active BOOLEAN DEFAULT TRUE,
    last_used_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_device_token (user_id, device_token),
    INDEX idx_device_user (user_id),
    INDEX idx_device_token (device_token),
    INDEX idx_device_active (is_active)
);

CREATE TABLE offline_sync_queue (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    device_token VARCHAR(500),
    table_name VARCHAR(100) NOT NULL,
    record_id BIGINT UNSIGNED NOT NULL,
    action ENUM('create', 'update', 'delete') NOT NULL,
    data_payload JSON,
    sync_status ENUM('pending', 'synced', 'failed', 'conflict') DEFAULT 'pending',
    conflict_resolution JSON,
    attempted_at TIMESTAMP NULL,
    synced_at TIMESTAMP NULL,
    error_message TEXT,
    retry_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_offline_sync_user (user_id),
    INDEX idx_offline_sync_status (sync_status),
    INDEX idx_offline_sync_table (table_name),
    INDEX idx_offline_sync_created (created_at)
);

-- =====================================================
-- SYSTEM CONFIGURATION TABLES
-- =====================================================

-- 22. SYSTEM SETTINGS & CONFIGURATION
CREATE TABLE system_settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    setting_key VARCHAR(100) NOT NULL,
    setting_value TEXT,
    setting_type ENUM('string', 'number', 'boolean', 'json', 'encrypted') DEFAULT 'string',
    category VARCHAR(50) DEFAULT 'general',
    description TEXT,
    is_public BOOLEAN DEFAULT FALSE,
    updated_by BIGINT UNSIGNED,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL,
    
    UNIQUE KEY unique_company_setting (company_id, setting_key),
    INDEX idx_system_setting_company (company_id),
    INDEX idx_system_setting_category (category)
);

CREATE TABLE email_templates (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    template_name VARCHAR(100) NOT NULL,
    template_code VARCHAR(50) NOT NULL,
    subject VARCHAR(500) NOT NULL,
    body_html TEXT,
    body_text TEXT,
    variables JSON,
    is_system_template BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    created_by BIGINT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    
    UNIQUE KEY unique_company_template_code (company_id, template_code),
    INDEX idx_email_template_company (company_id)
);

-- =====================================================
-- BACKUP & ARCHIVE TABLES
-- =====================================================

-- 23. DATA BACKUP & ARCHIVAL
CREATE TABLE data_backups (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    backup_type ENUM('full', 'incremental', 'differential') NOT NULL,
    backup_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500),
    file_size BIGINT UNSIGNED,
    backup_status ENUM('in_progress', 'completed', 'failed', 'corrupted') DEFAULT 'in_progress',
    tables_included JSON,
    start_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completion_time TIMESTAMP NULL,
    error_message TEXT,
    retention_days INT DEFAULT 90,
    is_encrypted BOOLEAN DEFAULT TRUE,
    created_by BIGINT UNSIGNED,
    
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_backup_company (company_id),
    INDEX idx_backup_status (backup_status),
    INDEX idx_backup_date (start_time)
);

-- =====================================================
-- INTEGRATION & API TABLES
-- =====================================================

-- 24. THIRD-PARTY INTEGRATIONS
CREATE TABLE integration_providers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    provider_code VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    api_base_url VARCHAR(500),
    documentation_url VARCHAR(500),
    supported_features JSON,
    authentication_type ENUM('api_key', 'oauth', 'basic_auth', 'bearer_token') NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_integration_provider_code (provider_code)
);

CREATE TABLE company_integrations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    provider_id BIGINT UNSIGNED NOT NULL,
    integration_name VARCHAR(255) NOT NULL,
    configuration JSON,
    credentials JSON, -- Encrypted
    sync_frequency ENUM('real_time', 'hourly', 'daily', 'weekly', 'manual') DEFAULT 'daily',
    last_sync_at TIMESTAMP NULL,
    next_sync_at TIMESTAMP NULL,
    sync_status ENUM('active', 'paused', 'error', 'disabled') DEFAULT 'active',
    error_log TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    configured_by BIGINT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (provider_id) REFERENCES integration_providers(id) ON DELETE CASCADE,
    FOREIGN KEY (configured_by) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_company_integration_company (company_id),
    INDEX idx_company_integration_provider (provider_id),
    INDEX idx_company_integration_sync (next_sync_at)
);

CREATE TABLE api_tokens (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED,
    token_name VARCHAR(255) NOT NULL,
    token_hash VARCHAR(255) NOT NULL,
    token_prefix VARCHAR(10),
    permissions JSON,
    allowed_ips JSON,
    rate_limit_per_minute INT DEFAULT 60,
    expires_at TIMESTAMP NULL,
    last_used_at TIMESTAMP NULL,
    usage_count BIGINT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_by BIGINT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    
    UNIQUE KEY unique_token_hash (token_hash),
    INDEX idx_api_token_company (company_id),
    INDEX idx_api_token_user (user_id),
    INDEX idx_api_token_prefix (token_prefix)
);

CREATE TABLE api_usage_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT UNSIGNED NOT NULL,
    token_id BIGINT UNSIGNED,
    endpoint VARCHAR(255) NOT NULL,
    method ENUM('GET', 'POST', 'PUT', 'DELETE', 'PATCH') NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    request_payload JSON,
    response_status INT,
    response_time_ms INT,
    error_message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (token_id) REFERENCES api_tokens(id) ON DELETE SET NULL,
    
    INDEX idx_api_usage_company (company_id),
    INDEX idx_api_usage_token (token_id),
    INDEX idx_api_usage_endpoint (endpoint),
    INDEX idx_api_usage_date (created_at)
);

-- =====================================================
-- VIEWS FOR COMMON QUERIES
-- =====================================================

-- 25. USEFUL DATABASE VIEWS
CREATE VIEW v_project_summary AS
SELECT 
    p.id,
    p.company_id,
    p.project_code,
    p.name,
    p.status,
    p.priority,
    p.progress_percentage,
    p.estimated_budget,
    p.actual_cost,
    p.start_date,
    p.planned_end_date,
    p.actual_end_date,
    c.name as client_name,
    CONCAT(pm.first_name, ' ', pm.last_name) as project_manager_name,
    CONCAT(ss.first_name, ' ', ss.last_name) as site_supervisor_name,
    (SELECT COUNT(*) FROM tasks t WHERE t.project_id = p.id AND t.status != 'cancelled') as total_tasks,
    (SELECT COUNT(*) FROM tasks t WHERE t.project_id = p.id AND t.status = 'completed') as completed_tasks,
    (SELECT COUNT(*) FROM project_team_members ptm WHERE ptm.project_id = p.id AND ptm.removed_at IS NULL) as team_size,
    CASE 
        WHEN p.planned_end_date < CURDATE() AND p.status NOT IN ('completed', 'cancelled') THEN 'overdue'
        WHEN p.planned_end_date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY) AND p.status NOT IN ('completed', 'cancelled') THEN 'due_soon'
        ELSE 'on_track'
    END as timeline_status
FROM projects p
LEFT JOIN clients c ON p.client_id = c.id
LEFT JOIN users pm ON p.project_manager_id = pm.id
LEFT JOIN users ss ON p.site_supervisor_id = ss.id
WHERE p.is_archived = FALSE;

CREATE VIEW v_employee_summary AS
SELECT 
    u.id,
    u.company_id,
    u.employee_id,
    CONCAT(u.first_name, ' ', u.last_name) as full_name,
    u.email,
    u.phone,
    u.status as user_status,
    ed.employment_status,
    ed.employment_type,
    ed.hire_date,
    ed.basic_salary,
    d.name as department_name,
    jp.title as position_title,
    CONCAT(supervisor.first_name, ' ', supervisor.last_name) as supervisor_name,
    ed.annual_leave_balance,
    ed.sick_leave_balance,
    (SELECT COUNT(*) FROM attendance_records ar 
     WHERE ar.user_id = u.id 
     AND ar.attendance_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
     AND ar.status = 'present') as attendance_last_30_days
FROM users u
INNER JOIN employee_details ed ON u.id = ed.user_id
LEFT JOIN departments d ON ed.department_id = d.id
LEFT JOIN job_positions jp ON ed.position_id = jp.id
LEFT JOIN users supervisor ON ed.supervisor_id = supervisor.id
WHERE u.status = 'active';

CREATE VIEW v_inventory_summary AS
SELECT 
    m.id,
    m.company_id,
    m.item_code,
    m.name,
    m.brand,
    m.unit,
    m.unit_cost,
    m.current_stock,
    m.minimum_stock,
    m.maximum_stock,
    mc.name as category_name,
    m.status,
    CASE 
        WHEN m.current_stock <= m.minimum_stock THEN 'low_stock'
        WHEN m.current_stock <= (m.minimum_stock * 1.2) THEN 'warning'
        ELSE 'normal'
    END as stock_status,
    (m.current_stock * m.unit_cost) as total_value,
    (SELECT COUNT(*) FROM stock_movements sm 
     WHERE sm.material_id = m.id 
     AND sm.created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)) as movements_last_30_days
FROM materials m
LEFT JOIN material_categories mc ON m.category_id = mc.id
WHERE m.status = 'active';

CREATE VIEW v_financial_summary AS
SELECT 
    company_id,
    'revenue' as account_type,
    SUM(CASE WHEN jel.credit_amount > 0 THEN jel.credit_amount ELSE 0 END) as total_amount,
    YEAR(je.entry_date) as year,
    MONTH(je.entry_date) as month
FROM journal_entries je
INNER JOIN journal_entry_lines jel ON je.id = jel.journal_entry_id
INNER JOIN chart_of_accounts coa ON jel.account_id = coa.id
WHERE je.status = 'posted' AND coa.account_type = 'revenue'
GROUP BY company_id, YEAR(je.entry_date), MONTH(je.entry_date)

UNION ALL

SELECT 
    company_id,
    'expense' as account_type,
    SUM(CASE WHEN jel.debit_amount > 0 THEN jel.debit_amount ELSE 0 END) as total_amount,
    YEAR(je.entry_date) as year,
    MONTH(je.entry_date) as month
FROM journal_entries je
INNER JOIN journal_entry_lines jel ON je.id = jel.journal_entry_id
INNER JOIN chart_of_accounts coa ON jel.account_id = coa.id
WHERE je.status = 'posted' AND coa.account_type = 'expense'
GROUP BY company_id, YEAR(je.entry_date), MONTH(je.entry_date);

-- =====================================================
-- STORED PROCEDURES FOR COMMON OPERATIONS
-- =====================================================

-- 26. USEFUL STORED PROCEDURES
DELIMITER //

-- Procedure to calculate project progress
CREATE PROCEDURE CalculateProjectProgress(IN project_id_param BIGINT UNSIGNED)
BEGIN
    DECLARE total_tasks INT DEFAULT 0;
    DECLARE completed_tasks INT DEFAULT 0;
    DECLARE progress_percentage DECIMAL(5,2) DEFAULT 0.00;
    
    SELECT COUNT(*) INTO total_tasks 
    FROM tasks 
    WHERE project_id = project_id_param AND status != 'cancelled';
    
    SELECT COUNT(*) INTO completed_tasks 
    FROM tasks 
    WHERE project_id = project_id_param AND status = 'completed';
    
    IF total_tasks > 0 THEN
        SET progress_percentage = (completed_tasks / total_tasks) * 100;
    END IF;
    
    UPDATE projects 
    SET progress_percentage = progress_percentage 
    WHERE id = project_id_param;
END //

-- Procedure to update stock levels
CREATE PROCEDURE UpdateStockLevels(
    IN material_id_param BIGINT UNSIGNED,
    IN movement_type_param ENUM('in', 'out'),
    IN quantity_param DECIMAL(12,2),
    IN user_id_param BIGINT UNSIGNED
)
BEGIN
    DECLARE current_balance DECIMAL(12,2) DEFAULT 0.00;
    DECLARE new_balance DECIMAL(12,2) DEFAULT 0.00;
    
    SELECT current_stock INTO current_balance 
    FROM materials 
    WHERE id = material_id_param;
    
    IF movement_type_param = 'in' THEN
        SET new_balance = current_balance + quantity_param;
    ELSE
        SET new_balance = current_balance - quantity_param;
    END IF;
    
    -- Prevent negative stock
    IF new_balance < 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Insufficient stock available';
    END IF;
    
    UPDATE materials 
    SET current_stock = new_balance 
    WHERE id = material_id_param;
END //

-- Procedure to calculate payroll
CREATE PROCEDURE CalculatePayroll(
    IN pay_period_id_param BIGINT UNSIGNED,
    IN user_id_param BIGINT UNSIGNED
)
BEGIN
    DECLARE basic_salary_amount DECIMAL(12,2) DEFAULT 0.00;
    DECLARE total_hours DECIMAL(6,2) DEFAULT 0.00;
    DECLARE overtime_hours DECIMAL(6,2) DEFAULT 0.00;
    DECLARE hourly_rate DECIMAL(10,2) DEFAULT 0.00;
    DECLARE total_allowances DECIMAL(12,2) DEFAULT 0.00;
    DECLARE total_deductions DECIMAL(12,2) DEFAULT 0.00;
    DECLARE gross_pay DECIMAL(12,2) DEFAULT 0.00;
    DECLARE net_pay DECIMAL(12,2) DEFAULT 0.00;
    
    -- Get basic salary
    SELECT ed.basic_salary INTO basic_salary_amount
    FROM employee_details ed
    WHERE ed.user_id = user_id_param;
    
    -- Calculate hours from attendance
    SELECT 
        COALESCE(SUM(ar.total_hours), 0),
        COALESCE(SUM(ar.overtime_hours), 0)
    INTO total_hours, overtime_hours
    FROM attendance_records ar
    INNER JOIN pay_periods pp ON ar.attendance_date BETWEEN pp.start_date AND pp.end_date
    WHERE ar.user_id = user_id_param AND pp.id = pay_period_id_param;
    
    -- Calculate hourly rate (assuming monthly salary)
    SET hourly_rate = basic_salary_amount / 160; -- 160 hours per month
    
    -- Calculate allowances
    SELECT COALESCE(SUM(ea.amount), 0) INTO total_allowances
    FROM employee_allowances ea
    INNER JOIN allowance_types at ON ea.allowance_type_id = at.id
    WHERE ea.user_id = user_id_param AND ea.is_active = TRUE;
    
    -- Calculate deductions
    SELECT COALESCE(SUM(ed.amount), 0) INTO total_deductions
    FROM employee_deductions ed
    INNER JOIN deduction_types dt ON ed.deduction_type_id = dt.id
    WHERE ed.user_id = user_id_param AND ed.is_active = TRUE;
    
    -- Calculate gross and net pay
    SET gross_pay = basic_salary_amount + total_allowances + (overtime_hours * hourly_rate * 1.5);
    SET net_pay = gross_pay - total_deductions;
    
    -- Insert or update payroll record
    INSERT INTO payroll_records (
        pay_period_id, user_id, basic_salary, gross_salary,
        regular_hours, overtime_hours, total_hours,
        hourly_rate, overtime_rate,
        total_allowances, total_deductions, net_pay,
        payment_status
    ) VALUES (
        pay_period_id_param, user_id_param, basic_salary_amount, gross_pay,
        total_hours - overtime_hours, overtime_hours, total_hours,
        hourly_rate, hourly_rate * 1.5,
        total_allowances, total_deductions, net_pay,
        'pending'
    ) ON DUPLICATE KEY UPDATE
        basic_salary = basic_salary_amount,
        gross_salary = gross_pay,
        regular_hours = total_hours - overtime_hours,
        overtime_hours = overtime_hours,
        total_hours = total_hours,
        hourly_rate = hourly_rate,
        overtime_rate = hourly_rate * 1.5,
        total_allowances = total_allowances,
        total_deductions = total_deductions,
        net_pay = net_pay,
        updated_at = CURRENT_TIMESTAMP;
END //

DELIMITER ;

-- =====================================================
-- TRIGGERS FOR AUTOMATION
-- =====================================================

-- 27. USEFUL TRIGGERS

-- Auto-update project progress when task status changes
DELIMITER //
CREATE TRIGGER tr_task_status_update 
    AFTER UPDATE ON tasks
    FOR EACH ROW
BEGIN
    IF OLD.status != NEW.status THEN
        CALL CalculateProjectProgress(NEW.project_id);
    END IF;
END //
DELIMITER ;

-- Auto-create audit log entries
DELIMITER //
CREATE TRIGGER tr_audit_log_users_update
    AFTER UPDATE ON users
    FOR EACH ROW
BEGIN
    INSERT INTO audit_logs (
        user_id, company_id, action, table_name, record_id,
        old_values, new_values, created_at
    ) VALUES (
        NEW.id, NEW.company_id, 'UPDATE', 'users', NEW.id,
        JSON_OBJECT(
            'first_name', OLD.first_name,
            'last_name', OLD.last_name,
            'email', OLD.email,
            'status', OLD.status
        ),
        JSON_OBJECT(
            'first_name', NEW.first_name,
            'last_name', NEW.last_name,
            'email', NEW.email,
            'status', NEW.status
        ),
        CURRENT_TIMESTAMP
    );
END //
DELIMITER ;

-- Auto-generate notifications for low stock
DELIMITER //
CREATE TRIGGER tr_low_stock_alert
    AFTER UPDATE ON materials
    FOR EACH ROW
BEGIN
    IF NEW.current_stock <= NEW.minimum_stock AND OLD.current_stock > OLD.minimum_stock THEN
        INSERT INTO system_alerts (
            company_id, alert_type, title, description, severity,
            related_type, related_id, threshold_value, current_value
        ) VALUES (
            NEW.company_id, 'low_stock', 
            CONCAT('Low Stock Alert: ', NEW.name),
            CONCAT('Material "', NEW.name, '" is running low. Current stock: ', NEW.current_stock, ' ', NEW.unit),
            'warning',
            'material', NEW.id,
            NEW.minimum_stock, NEW.current_stock
        );
    END IF;
END //
DELIMITER ;

-- =====================================================
-- INDEXES FOR PERFORMANCE OPTIMIZATION
-- =====================================================

-- 28. ADDITIONAL PERFORMANCE INDEXES

-- Composite indexes for common queries
CREATE INDEX idx_projects_company_status_dates ON projects(company_id, status, start_date, planned_end_date);
CREATE INDEX idx_tasks_project_status_assigned ON tasks(project_id, status, assigned_to);
CREATE INDEX idx_attendance_user_date_status ON attendance_records(user_id, attendance_date, status);
CREATE INDEX idx_stock_movements_material_date ON stock_movements(material_id, created_at);
CREATE INDEX idx_journal_entries_company_date ON journal_entries(company_id, entry_date, status);
CREATE INDEX idx_invoices_client_status_date ON invoices(client_id, status, invoice_date);
CREATE INDEX idx_notifications_user_read_type ON notifications(user_id, is_read, notification_type);

-- =====================================================
-- FINAL SETUP AND DATA INITIALIZATION
-- =====================================================

-- 29. DEFAULT DATA SETUP

-- Insert default system roles
INSERT INTO roles (company_id, name, slug, description, is_system_role, permissions) VALUES
(1, 'Super Admin', 'super_admin', 'Full system access', TRUE, JSON_ARRAY('*')),
(1, 'Admin', 'admin', 'Administrative access', TRUE, JSON_ARRAY('users.*', 'projects.*', 'inventory.*', 'accounting.*', 'hr.*')),
(1, 'Project Manager', 'project_manager', 'Project management access', TRUE, JSON_ARRAY('projects.*', 'tasks.*', 'timesheets.*', 'files.*')),
(1, 'Site Supervisor', 'site_supervisor', 'On-site management access', TRUE, JSON_ARRAY('projects.view', 'tasks.*', 'attendance.*', 'safety.*')),
(1, 'Employee', 'employee', 'Basic employee access', TRUE, JSON_ARRAY('profile.*', 'timesheets.own', 'attendance.own'));

-- Insert default admin user
INSERT INTO users (company_id, employee_id, username, email, password, first_name, last_name, status, created_at) VALUES
(1, 'EMP-2025-0001', 'admin', 'admin@construction.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System', 'Administrator', 'active', NOW());

-- Assign super admin role to default user
INSERT INTO user_roles (user_id, role_id, assigned_by, assigned_at) VALUES
(1, 1, 1, NOW());

-- Insert default chart of accounts
INSERT INTO chart_of_accounts (company_id, account_code, account_name, account_type, is_system_account) VALUES
(1, '1000', 'Cash and Cash Equivalents', 'asset', TRUE),
(1, '1100', 'Accounts Receivable', 'asset', TRUE),
(1, '1200', 'Inventory', 'asset', TRUE),
(1, '1500', 'Equipment and Tools', 'asset', TRUE),
(1, '2000', 'Accounts Payable', 'liability', TRUE),
(1, '2100', 'Accrued Expenses', 'liability', TRUE),
(1, '3000', 'Owner\'s Equity', 'equity', TRUE),
(1, '4000', 'Construction Revenue', 'revenue', TRUE),
(1, '5000', 'Materials Cost', 'expense', TRUE),
(1, '5100', 'Labor Cost', 'expense', TRUE),
(1, '5200', 'Equipment Rental', 'expense', TRUE),
(1, '6000', 'Administrative Expenses', 'expense', TRUE);

-- Insert default allowance types
INSERT INTO allowance_types (company_id, name, code, description, is_taxable) VALUES
(1, 'Housing Allowance', 'HOUSING', 'Monthly housing allowance', TRUE),
(1, 'Transport Allowance', 'TRANSPORT', 'Monthly transport allowance', TRUE),
(1, 'Meal Allowance', 'MEAL', 'Daily meal allowance', FALSE),
(1, 'Site Allowance', 'SITE', 'Allowance for working on construction sites', TRUE);

-- Insert default deduction types
INSERT INTO deduction_types (company_id, name, code, description, is_mandatory) VALUES
(1, 'Income Tax', 'INCOME_TAX', 'Government income tax', TRUE),
(1, 'Social Security', 'SOCIAL_SEC', 'Social security contribution', TRUE),
(1, 'Medical Insurance', 'MEDICAL', 'Medical insurance premium', FALSE),
(1, 'Loan Repayment', 'LOAN', 'Employee loan repayment', FALSE);

-- Insert default leave types
INSERT INTO leave_types (company_id, name, code, description, max_days_per_year, is_paid) VALUES
(1, 'Annual Leave', 'ANNUAL', 'Annual vacation leave', 21.00, TRUE),
(1, 'Sick Leave', 'SICK', 'Medical sick leave', 14.00, TRUE),
(1, 'Maternity Leave', 'MATERNITY', 'Maternity leave', 90.00, TRUE),
(1, 'Paternity Leave', 'PATERNITY', 'Paternity leave', 7.00, TRUE),
(1, 'Emergency Leave', 'EMERGENCY', 'Emergency family leave', 5.00, TRUE);

-- Insert default material categories
INSERT INTO material_categories (company_id, code, name, description) VALUES
(1, 'CEMENT', 'Cement & Concrete', 'Cement, concrete, and related materials'),
(1, 'STEEL', 'Steel & Metal', 'Steel bars, sheets, and metal materials'),
(1, 'WOOD', 'Timber & Wood', 'Lumber, plywood, and wood materials'),
(1, 'ELECT', 'Electrical', 'Electrical wires, fixtures, and components'),
(1, 'PLUMB', 'Plumbing', 'Pipes, fittings, and plumbing materials'),
(1, 'PAINT', 'Paint & Finishes', 'Paints, varnishes, and finishing materials'),
(1, 'TOOLS', 'Tools & Equipment', 'Hand tools and small equipment'),
(1, 'SAFETY', 'Safety Equipment', 'PPE and safety equipment');

-- =====================================================
-- DATABASE OPTIMIZATION SETTINGS
-- =====================================================

-- Set optimal MySQL settings for construction management system
SET GLOBAL innodb_buffer_pool_size = 1073741824; -- 1GB
SET GLOBAL max_connections = 500;
SET GLOBAL query_cache_size = 67108864; -- 64MB
SET GLOBAL innodb_log_file_size = 268435456; -- 256MB

-- =====================================================
-- SCHEMA DOCUMENTATION
-- =====================================================

/*
CONSTRUCTION MANAGEMENT SYSTEM - DATABASE SCHEMA DOCUMENTATION

This comprehensive MySQL schema is designed for a Construction Management System with the following key features:

1. MULTI-TENANT ARCHITECTURE
   - Supports multiple companies in a single database
   - All major tables include company_id for data isolation
   - Scalable for SaaS deployment

2. CORE MODULES INCLUDED:
   - User Management & Authentication (with 2FA support)
   - Project & Task Management
   - Inventory & Material Management
   - Human Resources & Payroll
   - Accounting & Financial Management
   - Equipment & Asset Tracking
   - Timesheet Management
   - Safety & Incident Reporting
   - File Management
   - Reporting & Analytics
   - Mobile App Support
   - Third-party Integrations

3. ADVANCED FEATURES:
   - Comprehensive audit logging
   - Role-based access control
   - Multi-currency support
   - Document versioning
   - Offline sync capabilities
   - API token management
   - Automated notifications
   - Data backup & archival

4. PERFORMANCE OPTIMIZATIONS:
   - Strategic indexing for common queries
   - Optimized views for reporting
   - Stored procedures for complex operations
   - Triggers for automated processes

5. SECURITY FEATURES:
   - Encrypted sensitive data storage
   - Session management
   - API rate limiting
   - Audit trails
   - Access control lists

6. REPORTING CAPABILITIES:
   - Project progress tracking
   - Financial reporting
   - HR analytics
   - Inventory management
   - Safety compliance
   - Custom report builder

This schema supports companies of all sizes from small contractors to large construction firms, 
with scalability for thousands of projects and users.

DEPLOYMENT NOTES:
- Requires MySQL 8.0+ for JSON support and advanced features
- Recommended minimum 4GB RAM for production deployment
- Consider partitioning large tables (audit_logs, stock_movements) for high-volume installations
- Regular backup strategy recommended for pay_periods and payroll_records tables
- Monitor and optimize slow queries using MySQL performance schema

CUSTOMIZATION:
- Additional custom fields can be added using JSON columns
- New modules can be integrated following the established patterns
- Regional compliance features can be added to payroll and accounting modules
*/