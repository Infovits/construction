-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Aug 10, 2025 at 03:39 PM
-- Server version: 8.3.0
-- PHP Version: 8.1.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `contsruction`
--

-- --------------------------------------------------------

--
-- Table structure for table `account_categories`
--

DROP TABLE IF EXISTS `account_categories`;
CREATE TABLE IF NOT EXISTS `account_categories` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(20) DEFAULT NULL,
  `account_type` enum('asset','liability','equity','revenue','expense') NOT NULL,
  `description` text,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_account_category_company` (`company_id`),
  KEY `idx_account_type` (`account_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `allowance_types`
--

DROP TABLE IF EXISTS `allowance_types`;
CREATE TABLE IF NOT EXISTS `allowance_types` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(20) NOT NULL,
  `description` text,
  `is_taxable` tinyint(1) DEFAULT '1',
  `is_fixed` tinyint(1) DEFAULT '0',
  `default_amount` decimal(12,2) DEFAULT '0.00',
  `calculation_type` enum('fixed','percentage','hourly','daily') DEFAULT 'fixed',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_company_allowance_code` (`company_id`,`code`),
  KEY `idx_allowance_company` (`company_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `allowance_types`
--

INSERT INTO `allowance_types` (`id`, `company_id`, `name`, `code`, `description`, `is_taxable`, `is_fixed`, `default_amount`, `calculation_type`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'Housing Allowance', 'HOUSING', 'Monthly housing allowance', 1, 0, 0.00, 'fixed', 1, '2025-06-24 19:48:45', '2025-06-24 19:48:45'),
(2, 1, 'Transport Allowance', 'TRANSPORT', 'Monthly transport allowance', 1, 0, 0.00, 'fixed', 1, '2025-06-24 19:48:45', '2025-06-24 19:48:45'),
(3, 1, 'Meal Allowance', 'MEAL', 'Daily meal allowance', 0, 0, 0.00, 'fixed', 1, '2025-06-24 19:48:45', '2025-06-24 19:48:45'),
(4, 1, 'Site Allowance', 'SITE', 'Allowance for working on construction sites', 1, 0, 0.00, 'fixed', 1, '2025-06-24 19:48:45', '2025-06-24 19:48:45');

-- --------------------------------------------------------

--
-- Table structure for table `api_tokens`
--

DROP TABLE IF EXISTS `api_tokens`;
CREATE TABLE IF NOT EXISTS `api_tokens` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `token_name` varchar(250) NOT NULL,
  `token_hash` varchar(250) NOT NULL,
  `token_prefix` varchar(10) DEFAULT NULL,
  `permissions` json DEFAULT NULL,
  `allowed_ips` json DEFAULT NULL,
  `rate_limit_per_minute` int DEFAULT '60',
  `expires_at` timestamp NULL DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `usage_count` bigint DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_token_hash` (`token_hash`),
  KEY `created_by` (`created_by`),
  KEY `idx_api_token_company` (`company_id`),
  KEY `idx_api_token_user` (`user_id`),
  KEY `idx_api_token_prefix` (`token_prefix`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `api_usage_logs`
--

DROP TABLE IF EXISTS `api_usage_logs`;
CREATE TABLE IF NOT EXISTS `api_usage_logs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED NOT NULL,
  `token_id` bigint UNSIGNED DEFAULT NULL,
  `endpoint` varchar(255) NOT NULL,
  `method` enum('GET','POST','PUT','DELETE','PATCH') NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `request_payload` json DEFAULT NULL,
  `response_status` int DEFAULT NULL,
  `response_time_ms` int DEFAULT NULL,
  `error_message` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_api_usage_company` (`company_id`),
  KEY `idx_api_usage_token` (`token_id`),
  KEY `idx_api_usage_endpoint` (`endpoint`(250)),
  KEY `idx_api_usage_date` (`created_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assets`
--

DROP TABLE IF EXISTS `assets`;
CREATE TABLE IF NOT EXISTS `assets` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED NOT NULL,
  `category_id` bigint UNSIGNED DEFAULT NULL,
  `asset_code` varchar(100) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `brand` varchar(100) DEFAULT NULL,
  `model` varchar(100) DEFAULT NULL,
  `serial_number` varchar(100) DEFAULT NULL,
  `barcode` varchar(100) DEFAULT NULL,
  `purchase_cost` decimal(15,2) NOT NULL DEFAULT '0.00',
  `current_value` decimal(15,2) DEFAULT '0.00',
  `accumulated_depreciation` decimal(15,2) DEFAULT '0.00',
  `salvage_value` decimal(15,2) DEFAULT '0.00',
  `supplier_id` bigint UNSIGNED DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `warranty_start_date` date DEFAULT NULL,
  `warranty_end_date` date DEFAULT NULL,
  `purchase_order_number` varchar(100) DEFAULT NULL,
  `current_location` varchar(255) DEFAULT NULL,
  `assigned_to` bigint UNSIGNED DEFAULT NULL,
  `project_id` bigint UNSIGNED DEFAULT NULL,
  `warehouse_id` bigint UNSIGNED DEFAULT NULL,
  `status` enum('available','in_use','maintenance','repair','disposed','sold','stolen') DEFAULT 'available',
  `condition_status` enum('excellent','good','fair','poor','damaged') DEFAULT 'good',
  `last_maintenance_date` date DEFAULT NULL,
  `next_maintenance_date` date DEFAULT NULL,
  `maintenance_interval_days` int DEFAULT NULL,
  `insurance_policy_number` varchar(100) DEFAULT NULL,
  `insurance_expiry_date` date DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_company_asset_code` (`company_id`,`asset_code`),
  KEY `supplier_id` (`supplier_id`),
  KEY `warehouse_id` (`warehouse_id`),
  KEY `created_by` (`created_by`),
  KEY `idx_asset_company` (`company_id`),
  KEY `idx_asset_category` (`category_id`),
  KEY `idx_asset_status` (`status`),
  KEY `idx_asset_assigned` (`assigned_to`),
  KEY `idx_asset_project` (`project_id`),
  KEY `idx_asset_serial` (`serial_number`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `asset_assignments`
--

DROP TABLE IF EXISTS `asset_assignments`;
CREATE TABLE IF NOT EXISTS `asset_assignments` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `asset_id` bigint UNSIGNED NOT NULL,
  `assigned_to` bigint UNSIGNED NOT NULL,
  `project_id` bigint UNSIGNED DEFAULT NULL,
  `assignment_date` date NOT NULL,
  `expected_return_date` date DEFAULT NULL,
  `actual_return_date` date DEFAULT NULL,
  `assignment_notes` text,
  `return_notes` text,
  `condition_at_assignment` enum('excellent','good','fair','poor','damaged') DEFAULT 'good',
  `condition_at_return` enum('excellent','good','fair','poor','damaged') DEFAULT NULL,
  `assigned_by` bigint UNSIGNED DEFAULT NULL,
  `returned_by` bigint UNSIGNED DEFAULT NULL,
  `status` enum('active','returned','overdue') DEFAULT 'active',
  PRIMARY KEY (`id`),
  KEY `assigned_by` (`assigned_by`),
  KEY `returned_by` (`returned_by`),
  KEY `idx_assignment_asset` (`asset_id`),
  KEY `idx_assignment_user` (`assigned_to`),
  KEY `idx_assignment_project` (`project_id`),
  KEY `idx_assignment_status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `asset_categories`
--

DROP TABLE IF EXISTS `asset_categories`;
CREATE TABLE IF NOT EXISTS `asset_categories` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(20) DEFAULT NULL,
  `description` text,
  `depreciation_method` enum('straight_line','declining_balance','units_of_production') DEFAULT 'straight_line',
  `useful_life_years` int DEFAULT '5',
  `salvage_value_percentage` decimal(5,2) DEFAULT '0.00',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_asset_category_company` (`company_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attendance_records`
--

DROP TABLE IF EXISTS `attendance_records`;
CREATE TABLE IF NOT EXISTS `attendance_records` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `project_id` bigint UNSIGNED DEFAULT NULL,
  `attendance_date` date NOT NULL,
  `clock_in_time` time DEFAULT NULL,
  `clock_out_time` time DEFAULT NULL,
  `break_start_time` time DEFAULT NULL,
  `break_end_time` time DEFAULT NULL,
  `total_hours` decimal(4,2) DEFAULT '0.00',
  `overtime_hours` decimal(4,2) DEFAULT '0.00',
  `attendance_type` enum('regular','overtime','holiday','weekend') DEFAULT 'regular',
  `location_in` varchar(255) DEFAULT NULL,
  `location_out` varchar(255) DEFAULT NULL,
  `gps_coordinates_in` point DEFAULT NULL,
  `gps_coordinates_out` point DEFAULT NULL,
  `ip_address_in` varchar(45) DEFAULT NULL,
  `ip_address_out` varchar(45) DEFAULT NULL,
  `device_info_in` text,
  `device_info_out` text,
  `status` enum('present','absent','late','half_day','on_leave') DEFAULT 'present',
  `notes` text,
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_date` (`user_id`,`attendance_date`),
  KEY `approved_by` (`approved_by`),
  KEY `idx_attendance_user` (`user_id`),
  KEY `idx_attendance_date` (`attendance_date`),
  KEY `idx_attendance_project` (`project_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `company_id` bigint UNSIGNED NOT NULL,
  `action` varchar(100) NOT NULL,
  `table_name` varchar(100) DEFAULT NULL,
  `record_id` bigint UNSIGNED DEFAULT NULL,
  `old_values` json DEFAULT NULL,
  `new_values` json DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_audit_user` (`user_id`),
  KEY `idx_audit_company` (`company_id`),
  KEY `idx_audit_action` (`action`),
  KEY `idx_audit_table` (`table_name`),
  KEY `idx_audit_created` (`created_at`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `company_id`, `action`, `table_name`, `record_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 1, 'user_created', 'users', 5, NULL, '{\"email\": \"msmsmsm@mfmf.com\", \"username\": \"mdmdmdmdmdmdm\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-26 20:23:37'),
(2, 1, 1, 'user_created', 'users', 6, NULL, '{\"email\": \"mdmdmMM@GGG.com\", \"username\": \"sgsfg333\", \"employee_id\": \"EMP-2025-0005\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-26 20:33:52'),
(3, 1, 1, 'user_created', 'users', 7, NULL, '{\"email\": \"fvdbdfb2ME@egrg.com\", \"username\": \"sgsfg333444\", \"employee_id\": \"EMP-2025-0006\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-26 20:35:52');

-- --------------------------------------------------------

--
-- Table structure for table `budgets`
--

DROP TABLE IF EXISTS `budgets`;
CREATE TABLE IF NOT EXISTS `budgets` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED NOT NULL,
  `project_id` bigint UNSIGNED DEFAULT NULL,
  `category_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `budget_period` enum('monthly','quarterly','yearly','project') DEFAULT 'yearly',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `total_budget` decimal(15,2) NOT NULL DEFAULT '0.00',
  `allocated_budget` decimal(15,2) DEFAULT '0.00',
  `spent_amount` decimal(15,2) DEFAULT '0.00',
  `remaining_budget` decimal(15,2) DEFAULT '0.00',
  `status` enum('draft','approved','active','completed','cancelled') DEFAULT 'draft',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `created_by` (`created_by`),
  KEY `approved_by` (`approved_by`),
  KEY `idx_budget_company` (`company_id`),
  KEY `idx_budget_project` (`project_id`),
  KEY `idx_budget_dates` (`start_date`,`end_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `budget_categories`
--

DROP TABLE IF EXISTS `budget_categories`;
CREATE TABLE IF NOT EXISTS `budget_categories` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `budget_type` enum('revenue','expense','capital') NOT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_budget_category_company` (`company_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `budget_line_items`
--

DROP TABLE IF EXISTS `budget_line_items`;
CREATE TABLE IF NOT EXISTS `budget_line_items` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `budget_id` bigint UNSIGNED NOT NULL,
  `account_id` bigint UNSIGNED DEFAULT NULL,
  `line_item_name` varchar(255) NOT NULL,
  `description` text,
  `budgeted_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `spent_amount` decimal(15,2) DEFAULT '0.00',
  `variance` decimal(15,2) DEFAULT '0.00',
  `variance_percentage` decimal(5,2) DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `account_id` (`account_id`),
  KEY `idx_budget_line_budget` (`budget_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chart_of_accounts`
--

DROP TABLE IF EXISTS `chart_of_accounts`;
CREATE TABLE IF NOT EXISTS `chart_of_accounts` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED NOT NULL,
  `category_id` bigint UNSIGNED DEFAULT NULL,
  `parent_account_id` bigint UNSIGNED DEFAULT NULL,
  `account_code` varchar(50) NOT NULL,
  `account_name` varchar(255) NOT NULL,
  `account_type` enum('asset','liability','equity','revenue','expense') NOT NULL,
  `account_subtype` varchar(100) DEFAULT NULL,
  `description` text,
  `is_system_account` tinyint(1) DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `balance` decimal(15,2) DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_company_account_code` (`company_id`,`account_code`),
  KEY `category_id` (`category_id`),
  KEY `idx_chart_company` (`company_id`),
  KEY `idx_chart_type` (`account_type`),
  KEY `idx_chart_parent` (`parent_account_id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `chart_of_accounts`
--

INSERT INTO `chart_of_accounts` (`id`, `company_id`, `category_id`, `parent_account_id`, `account_code`, `account_name`, `account_type`, `account_subtype`, `description`, `is_system_account`, `is_active`, `balance`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, NULL, '1000', 'Cash and Cash Equivalents', 'asset', NULL, NULL, 1, 1, 0.00, '2025-06-24 19:48:45', '2025-06-24 19:48:45'),
(2, 1, NULL, NULL, '1100', 'Accounts Receivable', 'asset', NULL, NULL, 1, 1, 0.00, '2025-06-24 19:48:45', '2025-06-24 19:48:45'),
(3, 1, NULL, NULL, '1200', 'Inventory', 'asset', NULL, NULL, 1, 1, 0.00, '2025-06-24 19:48:45', '2025-06-24 19:48:45'),
(4, 1, NULL, NULL, '1500', 'Equipment and Tools', 'asset', NULL, NULL, 1, 1, 0.00, '2025-06-24 19:48:45', '2025-06-24 19:48:45'),
(5, 1, NULL, NULL, '2000', 'Accounts Payable', 'liability', NULL, NULL, 1, 1, 0.00, '2025-06-24 19:48:45', '2025-06-24 19:48:45'),
(6, 1, NULL, NULL, '2100', 'Accrued Expenses', 'liability', NULL, NULL, 1, 1, 0.00, '2025-06-24 19:48:45', '2025-06-24 19:48:45'),
(7, 1, NULL, NULL, '3000', 'Owner\'s Equity', 'equity', NULL, NULL, 1, 1, 0.00, '2025-06-24 19:48:45', '2025-06-24 19:48:45'),
(8, 1, NULL, NULL, '4000', 'Construction Revenue', 'revenue', NULL, NULL, 1, 1, 0.00, '2025-06-24 19:48:45', '2025-06-24 19:48:45'),
(9, 1, NULL, NULL, '5000', 'Materials Cost', 'expense', NULL, NULL, 1, 1, 0.00, '2025-06-24 19:48:45', '2025-06-24 19:48:45'),
(10, 1, NULL, NULL, '5100', 'Labor Cost', 'expense', NULL, NULL, 1, 1, 0.00, '2025-06-24 19:48:45', '2025-06-24 19:48:45'),
(11, 1, NULL, NULL, '5200', 'Equipment Rental', 'expense', NULL, NULL, 1, 1, 0.00, '2025-06-24 19:48:45', '2025-06-24 19:48:45'),
(12, 1, NULL, NULL, '6000', 'Administrative Expenses', 'expense', NULL, NULL, 1, 1, 0.00, '2025-06-24 19:48:45', '2025-06-24 19:48:45');

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

DROP TABLE IF EXISTS `clients`;
CREATE TABLE IF NOT EXISTS `clients` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED NOT NULL,
  `client_code` varchar(50) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `address` text,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `tax_number` varchar(100) DEFAULT NULL,
  `payment_terms` varchar(100) DEFAULT NULL,
  `credit_limit` decimal(15,2) DEFAULT '0.00',
  `client_type` enum('individual','company','government') DEFAULT 'individual',
  `status` enum('active','inactive','blacklisted') DEFAULT 'active',
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_client_company` (`company_id`),
  KEY `idx_client_code` (`client_code`),
  KEY `idx_client_status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `company_id`, `client_code`, `name`, `contact_person`, `email`, `phone`, `mobile`, `address`, `city`, `state`, `country`, `postal_code`, `tax_number`, `payment_terms`, `credit_limit`, `client_type`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 'MCRA1', 'MACRA', 'Misheck Kamuloin', 'kamulonim@gmail.com', '0994099461', '', 'MACRA at boma', 'Lilongwe', 'Lilongwe', 'Malawi', '+265', '', '30', 0.00, 'company', 'active', '', '2025-06-27 05:38:40', '2025-06-27 05:38:40'),
(2, 1, 'k677', 'kamuloni', '', '', '', '', '', '', '', '', '', '', '30', 0.00, 'individual', 'active', '', '2025-06-28 08:41:30', '2025-06-28 08:41:30');

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

DROP TABLE IF EXISTS `companies`;
CREATE TABLE IF NOT EXISTS `companies` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `registration_number` varchar(100) DEFAULT NULL,
  `tax_number` varchar(100) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT 'Malawi',
  `postal_code` varchar(20) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `logo_url` varchar(500) DEFAULT NULL,
  `industry_type` enum('residential','commercial','industrial','infrastructure','mixed') DEFAULT 'mixed',
  `status` enum('active','inactive','suspended') DEFAULT 'active',
  `subscription_plan` enum('basic','professional','enterprise') DEFAULT 'basic',
  `subscription_expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_company_status` (`status`),
  KEY `idx_subscription_plan` (`subscription_plan`),
  KEY `idx_subscription_expires` (`subscription_expires_at`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `name`, `registration_number`, `tax_number`, `email`, `phone`, `address`, `city`, `state`, `country`, `postal_code`, `website`, `logo_url`, `industry_type`, `status`, `subscription_plan`, `subscription_expires_at`, `created_at`, `updated_at`) VALUES
(1, 'Infocus', '103939', NULL, NULL, NULL, NULL, NULL, NULL, 'Malawi', NULL, NULL, NULL, 'mixed', 'active', 'basic', NULL, '2025-06-24 20:50:17', '2025-06-24 20:50:17');

-- --------------------------------------------------------

--
-- Table structure for table `company_integrations`
--

DROP TABLE IF EXISTS `company_integrations`;
CREATE TABLE IF NOT EXISTS `company_integrations` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED NOT NULL,
  `provider_id` bigint UNSIGNED NOT NULL,
  `integration_name` varchar(255) NOT NULL,
  `configuration` json DEFAULT NULL,
  `credentials` json DEFAULT NULL,
  `sync_frequency` enum('real_time','hourly','daily','weekly','manual') DEFAULT 'daily',
  `last_sync_at` timestamp NULL DEFAULT NULL,
  `next_sync_at` timestamp NULL DEFAULT NULL,
  `sync_status` enum('active','paused','error','disabled') DEFAULT 'active',
  `error_log` text,
  `is_active` tinyint(1) DEFAULT '1',
  `configured_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `configured_by` (`configured_by`),
  KEY `idx_company_integration_company` (`company_id`),
  KEY `idx_company_integration_provider` (`provider_id`),
  KEY `idx_company_integration_sync` (`next_sync_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `custom_reports`
--

DROP TABLE IF EXISTS `custom_reports`;
CREATE TABLE IF NOT EXISTS `custom_reports` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED NOT NULL,
  `category_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `report_type` enum('tabular','chart','dashboard','pivot') DEFAULT 'tabular',
  `data_source` varchar(100) NOT NULL,
  `sql_query` text,
  `report_config` json DEFAULT NULL,
  `is_public` tinyint(1) DEFAULT '0',
  `is_scheduled` tinyint(1) DEFAULT '0',
  `schedule_frequency` enum('daily','weekly','monthly','quarterly','yearly') DEFAULT NULL,
  `schedule_config` json DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_custom_report_company` (`company_id`),
  KEY `idx_custom_report_category` (`category_id`),
  KEY `idx_custom_report_created_by` (`created_by`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dashboard_widgets`
--

DROP TABLE IF EXISTS `dashboard_widgets`;
CREATE TABLE IF NOT EXISTS `dashboard_widgets` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `widget_type` enum('chart','kpi','table','calendar','task_list','recent_activity') NOT NULL,
  `title` varchar(255) NOT NULL,
  `data_source` varchar(100) DEFAULT NULL,
  `widget_config` json DEFAULT NULL,
  `position_x` int DEFAULT '0',
  `position_y` int DEFAULT '0',
  `width` int DEFAULT '4',
  `height` int DEFAULT '3',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_dashboard_widget_company` (`company_id`),
  KEY `idx_dashboard_widget_user` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `data_backups`
--

DROP TABLE IF EXISTS `data_backups`;
CREATE TABLE IF NOT EXISTS `data_backups` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED NOT NULL,
  `backup_type` enum('full','incremental','differential') NOT NULL,
  `backup_name` varchar(255) NOT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `file_size` bigint UNSIGNED DEFAULT NULL,
  `backup_status` enum('in_progress','completed','failed','corrupted') DEFAULT 'in_progress',
  `tables_included` json DEFAULT NULL,
  `start_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `completion_time` timestamp NULL DEFAULT NULL,
  `error_message` text,
  `retention_days` int DEFAULT '90',
  `is_encrypted` tinyint(1) DEFAULT '1',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  KEY `idx_backup_company` (`company_id`),
  KEY `idx_backup_status` (`backup_status`),
  KEY `idx_backup_date` (`start_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `deduction_types`
--

DROP TABLE IF EXISTS `deduction_types`;
CREATE TABLE IF NOT EXISTS `deduction_types` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(20) NOT NULL,
  `description` text,
  `is_mandatory` tinyint(1) DEFAULT '0',
  `is_pre_tax` tinyint(1) DEFAULT '0',
  `calculation_type` enum('fixed','percentage','tiered') DEFAULT 'fixed',
  `default_amount` decimal(12,2) DEFAULT '0.00',
  `default_percentage` decimal(5,2) DEFAULT '0.00',
  `max_amount` decimal(12,2) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_company_deduction_code` (`company_id`,`code`),
  KEY `idx_deduction_company` (`company_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `deduction_types`
--

INSERT INTO `deduction_types` (`id`, `company_id`, `name`, `code`, `description`, `is_mandatory`, `is_pre_tax`, `calculation_type`, `default_amount`, `default_percentage`, `max_amount`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'Income Tax', 'INCOME_TAX', 'Government income tax', 1, 0, 'fixed', 0.00, 0.00, NULL, 1, '2025-06-24 19:48:45', '2025-06-24 19:48:45'),
(2, 1, 'Social Security', 'SOCIAL_SEC', 'Social security contribution', 1, 0, 'fixed', 0.00, 0.00, NULL, 1, '2025-06-24 19:48:45', '2025-06-24 19:48:45'),
(3, 1, 'Medical Insurance', 'MEDICAL', 'Medical insurance premium', 0, 0, 'fixed', 0.00, 0.00, NULL, 1, '2025-06-24 19:48:45', '2025-06-24 19:48:45'),
(4, 1, 'Loan Repayment', 'LOAN', 'Employee loan repayment', 0, 0, 'fixed', 0.00, 0.00, NULL, 1, '2025-06-24 19:48:45', '2025-06-24 19:48:45');

-- --------------------------------------------------------

--
-- Table structure for table `deliveries`
--

DROP TABLE IF EXISTS `deliveries`;
CREATE TABLE IF NOT EXISTS `deliveries` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` int UNSIGNED NOT NULL,
  `supplier_id` int UNSIGNED NOT NULL,
  `material_id` int UNSIGNED NOT NULL,
  `warehouse_id` int UNSIGNED NOT NULL,
  `delivery_date` date NOT NULL,
  `reference_number` varchar(100) NOT NULL,
  `quantity` decimal(15,2) NOT NULL DEFAULT '0.00',
  `unit_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `status` varchar(50) NOT NULL DEFAULT 'pending' COMMENT 'pending, received, cancelled',
  `notes` text,
  `created_by` int UNSIGNED NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `deliveries_company_id_foreign` (`company_id`),
  KEY `deliveries_supplier_id_foreign` (`supplier_id`),
  KEY `deliveries_material_id_foreign` (`material_id`),
  KEY `deliveries_warehouse_id_foreign` (`warehouse_id`),
  KEY `deliveries_created_by_foreign` (`created_by`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
CREATE TABLE IF NOT EXISTS `departments` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `manager_id` bigint UNSIGNED DEFAULT NULL,
  `parent_department_id` bigint UNSIGNED DEFAULT NULL,
  `budget` decimal(15,2) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `manager_id` (`manager_id`),
  KEY `parent_department_id` (`parent_department_id`),
  KEY `idx_department_company` (`company_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `company_id`, `code`, `name`, `description`, `manager_id`, `parent_department_id`, `budget`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'ITADMIN', 'IT Department', 'This is an IT Department', NULL, NULL, 10000.00, 'active', '2025-06-26 18:46:54', '2025-06-26 18:46:54'),
(2, 1, 'MGT', 'Operations', 'This Management Level operations', NULL, NULL, 100000.00, 'active', '2025-06-26 19:19:35', '2025-06-26 19:19:35');

-- --------------------------------------------------------

--
-- Table structure for table `device_registrations`
--

DROP TABLE IF EXISTS `device_registrations`;
CREATE TABLE IF NOT EXISTS `device_registrations` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `device_token` varchar(200) NOT NULL,
  `device_type` enum('android','ios') NOT NULL,
  `device_model` varchar(100) DEFAULT NULL,
  `os_version` varchar(20) DEFAULT NULL,
  `app_version` varchar(20) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `last_used_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `registered_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_device_token` (`user_id`,`device_token`),
  KEY `idx_device_user` (`user_id`),
  KEY `idx_device_token` (`device_token`),
  KEY `idx_device_active` (`is_active`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `email_templates`
--

DROP TABLE IF EXISTS `email_templates`;
CREATE TABLE IF NOT EXISTS `email_templates` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED NOT NULL,
  `template_name` varchar(100) NOT NULL,
  `template_code` varchar(50) NOT NULL,
  `subject` varchar(500) NOT NULL,
  `body_html` text,
  `body_text` text,
  `variables` json DEFAULT NULL,
  `is_system_template` tinyint(1) DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_company_template_code` (`company_id`,`template_code`),
  KEY `created_by` (`created_by`),
  KEY `idx_email_template_company` (`company_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_allowances`
--

DROP TABLE IF EXISTS `employee_allowances`;
CREATE TABLE IF NOT EXISTS `employee_allowances` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `allowance_type_id` bigint UNSIGNED NOT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `percentage` decimal(5,2) DEFAULT '0.00',
  `is_active` tinyint(1) DEFAULT '1',
  `effective_from` date DEFAULT NULL,
  `effective_to` date DEFAULT NULL,
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_employee_allowance_user` (`user_id`),
  KEY `idx_employee_allowance_type` (`allowance_type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_deductions`
--

DROP TABLE IF EXISTS `employee_deductions`;
CREATE TABLE IF NOT EXISTS `employee_deductions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `deduction_type_id` bigint UNSIGNED NOT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `percentage` decimal(5,2) DEFAULT '0.00',
  `is_active` tinyint(1) DEFAULT '1',
  `effective_from` date DEFAULT NULL,
  `effective_to` date DEFAULT NULL,
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_employee_deduction_user` (`user_id`),
  KEY `idx_employee_deduction_type` (`deduction_type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_details`
--

DROP TABLE IF EXISTS `employee_details`;
CREATE TABLE IF NOT EXISTS `employee_details` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `department_id` bigint UNSIGNED DEFAULT NULL,
  `position_id` bigint UNSIGNED DEFAULT NULL,
  `hire_date` date NOT NULL,
  `contract_start_date` date DEFAULT NULL,
  `contract_end_date` date DEFAULT NULL,
  `employment_status` enum('active','resigned','terminated','retired','on_leave') DEFAULT 'active',
  `employment_type` enum('full_time','part_time','contract','temporary','intern') DEFAULT 'full_time',
  `basic_salary` decimal(12,2) DEFAULT '0.00',
  `currency` varchar(3) DEFAULT 'MWK',
  `pay_frequency` enum('monthly','weekly','daily','hourly') DEFAULT 'monthly',
  `bank_name` varchar(255) DEFAULT NULL,
  `bank_account_number` varchar(100) DEFAULT NULL,
  `bank_branch` varchar(255) DEFAULT NULL,
  `tax_number` varchar(100) DEFAULT NULL,
  `tax_exempt` tinyint(1) DEFAULT '0',
  `annual_leave_balance` decimal(5,2) DEFAULT '0.00',
  `sick_leave_balance` decimal(5,2) DEFAULT '0.00',
  `supervisor_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  KEY `idx_employee_department` (`department_id`),
  KEY `idx_employee_position` (`position_id`),
  KEY `idx_employee_supervisor` (`supervisor_id`),
  KEY `idx_employee_status` (`employment_status`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `employee_details`
--

INSERT INTO `employee_details` (`id`, `user_id`, `department_id`, `position_id`, `hire_date`, `contract_start_date`, `contract_end_date`, `employment_status`, `employment_type`, `basic_salary`, `currency`, `pay_frequency`, `bank_name`, `bank_account_number`, `bank_branch`, `tax_number`, `tax_exempt`, `annual_leave_balance`, `sick_leave_balance`, `supervisor_id`, `created_at`, `updated_at`) VALUES
(1, 2, 2, 2, '2025-06-01', NULL, NULL, 'active', 'full_time', 100000.00, 'MWK', 'monthly', 'National Bank Of Malawi', '1000893354', NULL, '737373', 0, 0.00, 0.00, NULL, '2025-06-26 19:33:46', '2025-06-26 19:33:46'),
(2, 3, 2, 2, '2025-06-25', NULL, NULL, 'active', 'contract', 3546.00, 'MWK', 'monthly', 'hfhf', '9595', NULL, '353', 0, 0.00, 0.00, NULL, '2025-06-26 20:13:32', '2025-06-26 20:13:32'),
(3, 4, 2, 2, '2025-06-26', NULL, NULL, 'active', 'part_time', 600.00, 'MWK', 'monthly', 'egtrh', '60606', NULL, '5555', 0, 0.00, 0.00, NULL, '2025-06-26 20:19:15', '2025-06-26 20:19:15'),
(4, 5, 2, 2, '2025-06-17', NULL, NULL, 'active', 'full_time', 544446666.00, 'MWK', 'monthly', 'svsffdjb', '848484', NULL, 'fgfed', 0, 0.00, 0.00, NULL, '2025-06-26 20:23:37', '2025-06-26 20:23:37'),
(5, 7, 1, 1, '2025-06-26', NULL, NULL, 'active', 'full_time', 1000.00, 'MWK', 'monthly', NULL, NULL, NULL, NULL, 0, 21.00, 14.00, NULL, '2025-06-26 20:35:52', '2025-06-26 20:35:52');

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

DROP TABLE IF EXISTS `files`;
CREATE TABLE IF NOT EXISTS `files` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED NOT NULL,
  `folder_id` bigint UNSIGNED DEFAULT NULL,
  `category_id` bigint UNSIGNED DEFAULT NULL,
  `project_id` bigint UNSIGNED DEFAULT NULL,
  `original_name` varchar(255) NOT NULL,
  `stored_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` bigint UNSIGNED NOT NULL,
  `file_extension` varchar(10) DEFAULT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  `file_hash` varchar(64) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `tags` json DEFAULT NULL,
  `version_number` varchar(20) DEFAULT '1.0',
  `is_current_version` tinyint(1) DEFAULT '1',
  `parent_file_id` bigint UNSIGNED DEFAULT NULL,
  `access_level` enum('public','private','restricted','confidential') DEFAULT 'private',
  `download_count` int DEFAULT '0',
  `view_count` int DEFAULT '0',
  `last_accessed_at` timestamp NULL DEFAULT NULL,
  `status` enum('active','archived','deleted') DEFAULT 'active',
  `is_locked` tinyint(1) DEFAULT '0',
  `locked_by` bigint UNSIGNED DEFAULT NULL,
  `locked_at` timestamp NULL DEFAULT NULL,
  `uploaded_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `parent_file_id` (`parent_file_id`),
  KEY `locked_by` (`locked_by`),
  KEY `idx_file_company` (`company_id`),
  KEY `idx_file_folder` (`folder_id`),
  KEY `idx_file_project` (`project_id`),
  KEY `idx_file_hash` (`file_hash`),
  KEY `idx_file_status` (`status`),
  KEY `idx_file_uploaded` (`uploaded_by`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `file_access_logs`
--

DROP TABLE IF EXISTS `file_access_logs`;
CREATE TABLE IF NOT EXISTS `file_access_logs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `file_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `action` enum('view','download','edit','delete','share','comment') NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `accessed_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_file_access_file` (`file_id`),
  KEY `idx_file_access_user` (`user_id`),
  KEY `idx_file_access_date` (`accessed_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `file_categories`
--

DROP TABLE IF EXISTS `file_categories`;
CREATE TABLE IF NOT EXISTS `file_categories` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED NOT NULL,
  `parent_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `color_code` varchar(7) DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_file_category_company` (`company_id`),
  KEY `idx_file_category_parent` (`parent_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `file_comments`
--

DROP TABLE IF EXISTS `file_comments`;
CREATE TABLE IF NOT EXISTS `file_comments` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `file_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `comment` text NOT NULL,
  `is_resolved` tinyint(1) DEFAULT '0',
  `resolved_by` bigint UNSIGNED DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `resolved_by` (`resolved_by`),
  KEY `idx_file_comment_file` (`file_id`),
  KEY `idx_file_comment_user` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `file_permissions`
--

DROP TABLE IF EXISTS `file_permissions`;
CREATE TABLE IF NOT EXISTS `file_permissions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `file_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `role_id` bigint UNSIGNED DEFAULT NULL,
  `permission_type` enum('view','download','edit','delete','share') NOT NULL,
  `granted_by` bigint UNSIGNED DEFAULT NULL,
  `granted_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `granted_by` (`granted_by`),
  KEY `idx_file_permission_file` (`file_id`),
  KEY `idx_file_permission_user` (`user_id`),
  KEY `idx_file_permission_role` (`role_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `folders`
--

DROP TABLE IF EXISTS `folders`;
CREATE TABLE IF NOT EXISTS `folders` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED NOT NULL,
  `parent_folder_id` bigint UNSIGNED DEFAULT NULL,
  `project_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `folder_path` varchar(1000) DEFAULT NULL,
  `access_level` enum('public','private','restricted','confidential') DEFAULT 'private',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  KEY `idx_folder_company` (`company_id`),
  KEY `idx_folder_parent` (`parent_folder_id`),
  KEY `idx_folder_project` (`project_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `goods_receipt_items`
--

DROP TABLE IF EXISTS `goods_receipt_items`;
CREATE TABLE IF NOT EXISTS `goods_receipt_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `grn_id` int NOT NULL,
  `purchase_order_item_id` int NOT NULL,
  `material_id` int NOT NULL,
  `quantity_delivered` decimal(10,3) NOT NULL,
  `quantity_accepted` decimal(10,3) DEFAULT '0.000',
  `quantity_rejected` decimal(10,3) DEFAULT '0.000',
  `unit_cost` decimal(10,2) NOT NULL,
  `batch_number` varchar(100) DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `quality_status` enum('pending','passed','failed','conditional') DEFAULT 'pending',
  `rejection_reason` text,
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `grn_id` (`grn_id`),
  KEY `purchase_order_item_id` (`purchase_order_item_id`),
  KEY `material_id` (`material_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `goods_receipt_items`
--

INSERT INTO `goods_receipt_items` (`id`, `grn_id`, `purchase_order_item_id`, `material_id`, `quantity_delivered`, `quantity_accepted`, `quantity_rejected`, `unit_cost`, `batch_number`, `expiry_date`, `quality_status`, `rejection_reason`, `notes`, `created_at`) VALUES
(2, 9, 3, 2, 8.000, 0.000, 0.000, 20000.00, 'hh', '2025-08-13', 'pending', NULL, 'nn', '2025-08-10 06:34:44'),
(3, 10, 4, 3, 7.000, 0.000, 0.000, 5000.00, '', '0000-00-00', 'pending', NULL, '', '2025-08-10 06:48:37'),
(4, 10, 5, 4, 8.000, 0.000, 0.000, 70000.00, '', '0000-00-00', 'pending', NULL, '', '2025-08-10 06:48:37');

-- --------------------------------------------------------

--
-- Table structure for table `goods_receipt_notes`
--

DROP TABLE IF EXISTS `goods_receipt_notes`;
CREATE TABLE IF NOT EXISTS `goods_receipt_notes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `grn_number` varchar(50) NOT NULL,
  `purchase_order_id` int NOT NULL,
  `supplier_id` int NOT NULL,
  `warehouse_id` int NOT NULL,
  `delivery_date` date NOT NULL,
  `received_by` int NOT NULL,
  `delivery_note_number` varchar(100) DEFAULT NULL,
  `vehicle_number` varchar(50) DEFAULT NULL,
  `driver_name` varchar(100) DEFAULT NULL,
  `status` enum('pending_inspection','partially_accepted','accepted','rejected') DEFAULT 'pending_inspection',
  `total_received_value` decimal(15,2) DEFAULT NULL,
  `freight_cost` decimal(15,2) DEFAULT '0.00',
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `grn_number` (`grn_number`),
  KEY `purchase_order_id` (`purchase_order_id`),
  KEY `supplier_id` (`supplier_id`),
  KEY `warehouse_id` (`warehouse_id`),
  KEY `received_by` (`received_by`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `goods_receipt_notes`
--

INSERT INTO `goods_receipt_notes` (`id`, `grn_number`, `purchase_order_id`, `supplier_id`, `warehouse_id`, `delivery_date`, `received_by`, `delivery_note_number`, `vehicle_number`, `driver_name`, `status`, `total_received_value`, `freight_cost`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'GRN-20250810-0001', 7, 1, 2, '2025-08-10', 1, 'nbnbnb', '8890b', 'bbbnbn', 'pending_inspection', NULL, 0.00, '', '2025-08-10 04:16:31', '2025-08-10 04:16:31'),
(2, 'GRN-20250810-0002', 7, 1, 2, '2025-08-10', 1, 'nbnbnb', '8890b', 'bbbnbn', 'pending_inspection', NULL, 0.00, '', '2025-08-10 04:16:52', '2025-08-10 04:16:52'),
(3, 'GRN-20250810-0003', 7, 1, 2, '2025-08-10', 1, 'tggf', 'hhh', 'bbbnb', 'pending_inspection', NULL, 0.00, ' nbnbnb', '2025-08-10 04:22:15', '2025-08-10 04:22:15'),
(4, 'GRN-20250810-0004', 7, 1, 2, '2025-08-10', 1, 'tggf', 'hhh', 'bbbnb', 'pending_inspection', NULL, 0.00, ' nbnbnb', '2025-08-10 04:22:35', '2025-08-10 04:22:35'),
(6, 'GRN-20250810-0005', 7, 1, 1, '2025-08-10', 1, 'mmmm', '666', 'nnnn', 'pending_inspection', NULL, 0.00, 'nnn', '2025-08-10 04:32:19', '2025-08-10 04:32:19'),
(7, 'GRN-20250810-0006', 7, 1, 1, '2025-08-10', 1, 'mmmm', '666', 'nnnn', 'pending_inspection', NULL, 0.00, 'nnn', '2025-08-10 04:32:27', '2025-08-10 04:32:27'),
(8, 'GRN-20250810-0007', 7, 1, 1, '2025-08-10', 1, 'mmmm', '666', 'nnnn', 'pending_inspection', NULL, 0.00, 'nnn', '2025-08-10 04:34:32', '2025-08-10 04:34:32'),
(9, 'GRN-20250810-0008', 7, 1, 1, '2025-08-10', 1, 'mmmm', '666', 'nnnn', 'pending_inspection', 160000.00, 0.00, 'nnn', '2025-08-10 06:34:44', '2025-08-10 06:34:44'),
(10, 'GRN-20250810-0009', 8, 1, 2, '2025-08-10', 1, 'bbbb', 'hhh', 'bbb', 'pending_inspection', 595000.00, 0.00, 'bgg', '2025-08-10 06:48:37', '2025-08-10 06:48:37');

-- --------------------------------------------------------

--
-- Table structure for table `incidents`
--

DROP TABLE IF EXISTS `incidents`;
CREATE TABLE IF NOT EXISTS `incidents` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED NOT NULL,
  `project_id` bigint UNSIGNED DEFAULT NULL,
  `category_id` bigint UNSIGNED DEFAULT NULL,
  `incident_number` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `incident_date` date NOT NULL,
  `incident_time` time DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `severity` enum('minor','moderate','major','critical','fatal') NOT NULL,
  `incident_type` enum('accident','near_miss','unsafe_condition','unsafe_act','environmental','security') NOT NULL,
  `reported_by` bigint UNSIGNED NOT NULL,
  `persons_involved` json DEFAULT NULL,
  `witnesses` json DEFAULT NULL,
  `injury_sustained` tinyint(1) DEFAULT '0',
  `property_damage` tinyint(1) DEFAULT '0',
  `environmental_impact` tinyint(1) DEFAULT '0',
  `work_disruption` tinyint(1) DEFAULT '0',
  `estimated_cost` decimal(12,2) DEFAULT '0.00',
  `immediate_action_taken` text,
  `investigation_required` tinyint(1) DEFAULT '0',
  `investigation_assigned_to` bigint UNSIGNED DEFAULT NULL,
  `investigation_deadline` date DEFAULT NULL,
  `root_cause_analysis` text,
  `corrective_actions` text,
  `preventive_actions` text,
  `status` enum('open','investigating','resolved','closed') DEFAULT 'open',
  `priority` enum('low','medium','high','urgent') DEFAULT 'medium',
  `resolved_date` date DEFAULT NULL,
  `resolved_by` bigint UNSIGNED DEFAULT NULL,
  `closure_notes` text,
  `authorities_notified` tinyint(1) DEFAULT '0',
  `notification_date` date DEFAULT NULL,
  `notification_reference` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_company_incident_number` (`company_id`,`incident_number`),
  KEY `category_id` (`category_id`),
  KEY `reported_by` (`reported_by`),
  KEY `investigation_assigned_to` (`investigation_assigned_to`),
  KEY `resolved_by` (`resolved_by`),
  KEY `idx_incident_company` (`company_id`),
  KEY `idx_incident_project` (`project_id`),
  KEY `idx_incident_date` (`incident_date`),
  KEY `idx_incident_status` (`status`),
  KEY `idx_incident_severity` (`severity`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `incident_attachments`
--

DROP TABLE IF EXISTS `incident_attachments`;
CREATE TABLE IF NOT EXISTS `incident_attachments` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `incident_id` bigint UNSIGNED NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` bigint UNSIGNED DEFAULT NULL,
  `file_type` varchar(100) DEFAULT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  `attachment_type` enum('photo','video','document','audio','other') DEFAULT 'photo',
  `description` text,
  `uploaded_by` bigint UNSIGNED DEFAULT NULL,
  `uploaded_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `uploaded_by` (`uploaded_by`),
  KEY `idx_incident_attachment_incident` (`incident_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `incident_categories`
--

DROP TABLE IF EXISTS `incident_categories`;
CREATE TABLE IF NOT EXISTS `incident_categories` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(20) DEFAULT NULL,
  `description` text,
  `severity_level` enum('low','medium','high','critical') DEFAULT 'medium',
  `requires_investigation` tinyint(1) DEFAULT '0',
  `notification_required` tinyint(1) DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_incident_category_company` (`company_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `integration_providers`
--

DROP TABLE IF EXISTS `integration_providers`;
CREATE TABLE IF NOT EXISTS `integration_providers` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `provider_code` varchar(50) NOT NULL,
  `description` text,
  `api_base_url` varchar(500) DEFAULT NULL,
  `documentation_url` varchar(500) DEFAULT NULL,
  `supported_features` json DEFAULT NULL,
  `authentication_type` enum('api_key','oauth','basic_auth','bearer_token') NOT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `provider_code` (`provider_code`),
  KEY `idx_integration_provider_code` (`provider_code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

DROP TABLE IF EXISTS `invoices`;
CREATE TABLE IF NOT EXISTS `invoices` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED NOT NULL,
  `client_id` bigint UNSIGNED NOT NULL,
  `project_id` bigint UNSIGNED DEFAULT NULL,
  `invoice_number` varchar(50) NOT NULL,
  `invoice_date` date NOT NULL,
  `due_date` date NOT NULL,
  `reference_number` varchar(100) DEFAULT NULL,
  `subtotal` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(15,2) DEFAULT '0.00',
  `discount_amount` decimal(15,2) DEFAULT '0.00',
  `total_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `paid_amount` decimal(15,2) DEFAULT '0.00',
  `balance_due` decimal(15,2) DEFAULT '0.00',
  `currency` varchar(3) DEFAULT 'MWK',
  `status` enum('draft','sent','viewed','partial','paid','overdue','cancelled') DEFAULT 'draft',
  `payment_terms` varchar(255) DEFAULT NULL,
  `notes` text,
  `terms_conditions` text,
  `sent_at` timestamp NULL DEFAULT NULL,
  `viewed_at` timestamp NULL DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_company_invoice_number` (`company_id`,`invoice_number`),
  KEY `created_by` (`created_by`),
  KEY `idx_invoice_company` (`company_id`),
  KEY `idx_invoice_client` (`client_id`),
  KEY `idx_invoice_project` (`project_id`),
  KEY `idx_invoice_status` (`status`),
  KEY `idx_invoice_dates` (`invoice_date`,`due_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_line_items`
--

DROP TABLE IF EXISTS `invoice_line_items`;
CREATE TABLE IF NOT EXISTS `invoice_line_items` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice_id` bigint UNSIGNED NOT NULL,
  `material_id` bigint UNSIGNED DEFAULT NULL,
  `description` text NOT NULL,
  `quantity` decimal(12,2) NOT NULL DEFAULT '1.00',
  `unit_price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `line_total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tax_rate` decimal(5,2) DEFAULT '0.00',
  `tax_amount` decimal(12,2) DEFAULT '0.00',
  `line_order` int DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `material_id` (`material_id`),
  KEY `idx_invoice_line_invoice` (`invoice_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_templates`
--

DROP TABLE IF EXISTS `invoice_templates`;
CREATE TABLE IF NOT EXISTS `invoice_templates` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `template_html` text,
  `is_default` tinyint(1) DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_invoice_template_company` (`company_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_positions`
--

DROP TABLE IF EXISTS `job_positions`;
CREATE TABLE IF NOT EXISTS `job_positions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED NOT NULL,
  `department_id` bigint UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `description` text,
  `requirements` text,
  `min_salary` decimal(12,2) DEFAULT NULL,
  `max_salary` decimal(12,2) DEFAULT NULL,
  `employment_type` enum('full_time','part_time','contract','temporary','intern') DEFAULT 'full_time',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_position_company` (`company_id`),
  KEY `idx_position_department` (`department_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `job_positions`
--

INSERT INTO `job_positions` (`id`, `company_id`, `department_id`, `title`, `code`, `description`, `requirements`, `min_salary`, `max_salary`, `employment_type`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'IT Officer', 'IT0', 'Something', 'something', 100000.00, 500000.00, 'full_time', 1, '2025-06-26 19:14:04', '2025-06-26 19:14:04'),
(2, 1, 2, 'Chief Executive Officer', 'CEO', 'This is Management top level', 'Masters in Business Management or Business Admin', 1000000.00, 10000000.00, 'full_time', 1, '2025-06-26 19:20:47', '2025-06-26 19:20:47');

-- --------------------------------------------------------

--
-- Table structure for table `journal_entries`
--

DROP TABLE IF EXISTS `journal_entries`;
CREATE TABLE IF NOT EXISTS `journal_entries` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED NOT NULL,
  `entry_number` varchar(50) NOT NULL,
  `reference_type` enum('manual','invoice','payment','payroll','adjustment','accrual','depreciation') DEFAULT 'manual',
  `reference_id` bigint UNSIGNED DEFAULT NULL,
  `entry_date` date NOT NULL,
  `description` text,
  `total_debit` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_credit` decimal(15,2) NOT NULL DEFAULT '0.00',
  `status` enum('draft','posted','reversed') DEFAULT 'draft',
  `posted_by` bigint UNSIGNED DEFAULT NULL,
  `posted_at` timestamp NULL DEFAULT NULL,
  `reversed_by` bigint UNSIGNED DEFAULT NULL,
  `reversed_at` timestamp NULL DEFAULT NULL,
  `reversal_reason` text,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_company_entry_number` (`company_id`,`entry_number`),
  KEY `posted_by` (`posted_by`),
  KEY `reversed_by` (`reversed_by`),
  KEY `created_by` (`created_by`),
  KEY `idx_journal_company` (`company_id`),
  KEY `idx_journal_date` (`entry_date`),
  KEY `idx_journal_status` (`status`),
  KEY `idx_journal_reference` (`reference_type`,`reference_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `journal_entry_lines`
--

DROP TABLE IF EXISTS `journal_entry_lines`;
CREATE TABLE IF NOT EXISTS `journal_entry_lines` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `journal_entry_id` bigint UNSIGNED NOT NULL,
  `account_id` bigint UNSIGNED NOT NULL,
  `project_id` bigint UNSIGNED DEFAULT NULL,
  `description` text,
  `debit_amount` decimal(15,2) DEFAULT '0.00',
  `credit_amount` decimal(15,2) DEFAULT '0.00',
  `line_order` int DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `idx_journal_line_entry` (`journal_entry_id`),
  KEY `idx_journal_line_account` (`account_id`),
  KEY `idx_journal_line_project` (`project_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leave_applications`
--

DROP TABLE IF EXISTS `leave_applications`;
CREATE TABLE IF NOT EXISTS `leave_applications` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `leave_type_id` bigint UNSIGNED NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `total_days` decimal(5,2) NOT NULL,
  `reason` text,
  `emergency_contact` varchar(255) DEFAULT NULL,
  `emergency_phone` varchar(20) DEFAULT NULL,
  `handover_notes` text,
  `status` enum('pending','approved','rejected','cancelled') DEFAULT 'pending',
  `applied_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `reviewed_by` bigint UNSIGNED DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `review_comments` text,
  PRIMARY KEY (`id`),
  KEY `reviewed_by` (`reviewed_by`),
  KEY `idx_leave_user` (`user_id`),
  KEY `idx_leave_type` (`leave_type_id`),
  KEY `idx_leave_dates` (`start_date`,`end_date`),
  KEY `idx_leave_status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leave_types`
--

DROP TABLE IF EXISTS `leave_types`;
CREATE TABLE IF NOT EXISTS `leave_types` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(20) NOT NULL,
  `description` text,
  `max_days_per_year` decimal(5,2) DEFAULT NULL,
  `is_paid` tinyint(1) DEFAULT '1',
  `requires_approval` tinyint(1) DEFAULT '1',
  `min_notice_days` int DEFAULT '1',
  `max_consecutive_days` int DEFAULT NULL,
  `carry_forward` tinyint(1) DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_company_leave_code` (`company_id`,`code`),
  KEY `idx_leave_type_company` (`company_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `leave_types`
--

INSERT INTO `leave_types` (`id`, `company_id`, `name`, `code`, `description`, `max_days_per_year`, `is_paid`, `requires_approval`, `min_notice_days`, `max_consecutive_days`, `carry_forward`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'Annual Leave', 'ANNUAL', 'Annual vacation leave', 21.00, 1, 1, 1, NULL, 0, 1, '2025-06-24 19:48:45', '2025-06-24 19:48:45'),
(2, 1, 'Sick Leave', 'SICK', 'Medical sick leave', 14.00, 1, 1, 1, NULL, 0, 1, '2025-06-24 19:48:45', '2025-06-24 19:48:45'),
(3, 1, 'Maternity Leave', 'MATERNITY', 'Maternity leave', 90.00, 1, 1, 1, NULL, 0, 1, '2025-06-24 19:48:45', '2025-06-24 19:48:45'),
(4, 1, 'Paternity Leave', 'PATERNITY', 'Paternity leave', 7.00, 1, 1, 1, NULL, 0, 1, '2025-06-24 19:48:45', '2025-06-24 19:48:45'),
(5, 1, 'Emergency Leave', 'EMERGENCY', 'Emergency family leave', 5.00, 1, 1, 1, NULL, 0, 1, '2025-06-24 19:48:45', '2025-06-24 19:48:45');

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_records`
--

DROP TABLE IF EXISTS `maintenance_records`;
CREATE TABLE IF NOT EXISTS `maintenance_records` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `asset_id` bigint UNSIGNED NOT NULL,
  `schedule_id` bigint UNSIGNED DEFAULT NULL,
  `maintenance_type` enum('preventive','corrective','inspection','calibration','emergency') NOT NULL,
  `work_order_number` varchar(50) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `maintenance_date` date NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `performed_by` bigint UNSIGNED DEFAULT NULL,
  `supervised_by` bigint UNSIGNED DEFAULT NULL,
  `labor_cost` decimal(12,2) DEFAULT '0.00',
  `parts_cost` decimal(12,2) DEFAULT '0.00',
  `other_cost` decimal(12,2) DEFAULT '0.00',
  `total_cost` decimal(12,2) DEFAULT '0.00',
  `status` enum('scheduled','in_progress','completed','cancelled') DEFAULT 'scheduled',
  `completion_notes` text,
  `next_maintenance_date` date DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `schedule_id` (`schedule_id`),
  KEY `performed_by` (`performed_by`),
  KEY `supervised_by` (`supervised_by`),
  KEY `created_by` (`created_by`),
  KEY `idx_maintenance_record_asset` (`asset_id`),
  KEY `idx_maintenance_record_date` (`maintenance_date`),
  KEY `idx_maintenance_record_status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_schedules`
--

DROP TABLE IF EXISTS `maintenance_schedules`;
CREATE TABLE IF NOT EXISTS `maintenance_schedules` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `asset_id` bigint UNSIGNED NOT NULL,
  `maintenance_type` enum('preventive','corrective','inspection','calibration') NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `frequency_type` enum('days','weeks','months','years','hours','kilometers') NOT NULL,
  `frequency_value` int NOT NULL,
  `next_due_date` date NOT NULL,
  `assigned_to` bigint UNSIGNED DEFAULT NULL,
  `estimated_cost` decimal(12,2) DEFAULT '0.00',
  `estimated_hours` decimal(6,2) DEFAULT '0.00',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_maintenance_asset` (`asset_id`),
  KEY `idx_maintenance_due_date` (`next_due_date`),
  KEY `idx_maintenance_assigned` (`assigned_to`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `materials`
--

DROP TABLE IF EXISTS `materials`;
CREATE TABLE IF NOT EXISTS `materials` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED NOT NULL,
  `category_id` bigint UNSIGNED DEFAULT NULL,
  `primary_supplier_id` bigint UNSIGNED DEFAULT NULL,
  `item_code` varchar(100) NOT NULL,
  `barcode` varchar(100) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `brand` varchar(100) DEFAULT NULL,
  `model` varchar(100) DEFAULT NULL,
  `specifications` text,
  `unit` varchar(50) NOT NULL DEFAULT 'pcs',
  `unit_cost` decimal(12,2) DEFAULT '0.00',
  `selling_price` decimal(12,2) DEFAULT '0.00',
  `gl_account_code` varchar(50) DEFAULT NULL,
  `expense_account_code` varchar(50) DEFAULT NULL,
  `current_stock` decimal(12,2) DEFAULT '0.00',
  `minimum_stock` decimal(12,2) DEFAULT '0.00',
  `maximum_stock` decimal(12,2) DEFAULT '0.00',
  `reorder_level` decimal(12,2) DEFAULT '0.00',
  `weight` decimal(10,3) DEFAULT NULL,
  `dimensions` varchar(100) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `material_type` enum('consumable','tool','equipment','raw_material','finished_good') DEFAULT 'consumable',
  `is_tracked` tinyint(1) DEFAULT '1',
  `is_serialized` tinyint(1) DEFAULT '0',
  `requires_inspection` tinyint(1) DEFAULT '0',
  `shelf_life_days` int DEFAULT NULL,
  `status` enum('active','inactive','discontinued') DEFAULT 'active',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_company_item_code` (`company_id`,`item_code`),
  KEY `created_by` (`created_by`),
  KEY `idx_material_company` (`company_id`),
  KEY `idx_material_category` (`category_id`),
  KEY `idx_material_barcode` (`barcode`),
  KEY `idx_material_stock` (`current_stock`,`minimum_stock`),
  KEY `idx_material_supplier` (`primary_supplier_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `materials`
--

INSERT INTO `materials` (`id`, `company_id`, `category_id`, `primary_supplier_id`, `item_code`, `barcode`, `name`, `description`, `brand`, `model`, `specifications`, `unit`, `unit_cost`, `selling_price`, `gl_account_code`, `expense_account_code`, `current_stock`, `minimum_stock`, `maximum_stock`, `reorder_level`, `weight`, `dimensions`, `color`, `material_type`, `is_tracked`, `is_serialized`, `requires_inspection`, `shelf_life_days`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 1, 7, 2, 'khasu8', '', 'khasu', '', NULL, NULL, NULL, 'piece', 20000.00, 0.00, NULL, NULL, 1100.00, 0.00, 0.00, 0.00, NULL, NULL, NULL, 'consumable', 0, 0, 0, NULL, 'active', 1, '2025-06-28 06:14:29', '2025-06-28 08:09:55'),
(2, 1, 7, 1, 'C562', '08877665', 'cement dangote', '', NULL, NULL, NULL, 'kg', 20000.00, 0.00, NULL, NULL, 1400.00, 0.00, 0.00, 0.00, NULL, NULL, NULL, 'consumable', 0, 0, 0, NULL, 'active', 1, '2025-06-28 06:16:39', '2025-06-28 08:08:59'),
(3, 1, 3, 1, 'C766', '887655444', 'Bidinding wire', 'ghgf', NULL, NULL, NULL, 'piece', 5000.00, 0.00, NULL, NULL, 600.00, 0.00, 0.00, 0.00, NULL, NULL, NULL, 'consumable', 0, 0, 0, NULL, 'active', 1, '2025-06-28 08:45:28', '2025-06-28 08:47:41'),
(4, 1, 3, 1, 'S916', '', 'zokhomera blandering', 'this is zookhomera', NULL, NULL, NULL, 'piece', 70000.00, 0.00, NULL, NULL, 70.00, 0.00, 0.00, 0.00, NULL, NULL, NULL, 'consumable', 0, 0, 0, NULL, 'active', 1, '2025-08-09 00:24:35', '2025-08-09 00:25:28');

-- --------------------------------------------------------

--
-- Table structure for table `material_categories`
--

DROP TABLE IF EXISTS `material_categories`;
CREATE TABLE IF NOT EXISTS `material_categories` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED NOT NULL,
  `parent_id` bigint UNSIGNED DEFAULT NULL,
  `code` varchar(50) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_material_category_company` (`company_id`),
  KEY `idx_material_category_parent` (`parent_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `material_categories`
--

INSERT INTO `material_categories` (`id`, `company_id`, `parent_id`, `code`, `name`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 'CEMENT', 'Cement & Concrete', 'Cement, concrete, and related materials', 1, '2025-06-24 19:48:45', '2025-06-24 19:48:45'),
(2, 1, NULL, 'STEEL', 'Steel & Metal', 'Steel bars, sheets, and metal materials', 1, '2025-06-24 19:48:45', '2025-06-24 19:48:45'),
(3, 1, NULL, 'WOOD', 'Timber & Wood', 'Lumber, plywood, and wood materials', 1, '2025-06-24 19:48:45', '2025-06-24 19:48:45'),
(4, 1, NULL, 'ELECT', 'Electrical', 'Electrical wires, fixtures, and components', 1, '2025-06-24 19:48:45', '2025-06-24 19:48:45'),
(5, 1, NULL, 'PLUMB', 'Plumbing', 'Pipes, fittings, and plumbing materials', 1, '2025-06-24 19:48:45', '2025-06-24 19:48:45'),
(6, 1, NULL, 'PAINT', 'Paint & Finishes', 'Paints, varnishes, and finishing materials', 1, '2025-06-24 19:48:45', '2025-06-24 19:48:45'),
(7, 1, NULL, 'TOOLS', 'Tools & Equipment', 'Hand tools and small equipment', 1, '2025-06-24 19:48:45', '2025-06-24 19:48:45'),
(8, 1, NULL, 'SAFETY', 'Safety Equipment', 'PPE and safety equipment', 1, '2025-06-24 19:48:45', '2025-06-24 19:48:45');

-- --------------------------------------------------------

--
-- Table structure for table `material_requests`
--

DROP TABLE IF EXISTS `material_requests`;
CREATE TABLE IF NOT EXISTS `material_requests` (
  `id` int NOT NULL AUTO_INCREMENT,
  `request_number` varchar(50) NOT NULL,
  `project_id` int DEFAULT NULL,
  `requested_by` int NOT NULL,
  `department_id` int DEFAULT NULL,
  `request_date` date NOT NULL,
  `required_date` date DEFAULT NULL,
  `status` enum('draft','pending_approval','approved','rejected','partially_fulfilled','completed') DEFAULT 'draft',
  `priority` enum('low','medium','high','urgent') DEFAULT 'medium',
  `total_estimated_cost` decimal(15,2) DEFAULT NULL,
  `approved_by` int DEFAULT NULL,
  `approved_date` datetime DEFAULT NULL,
  `rejection_reason` text,
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `request_number` (`request_number`),
  KEY `project_id` (`project_id`),
  KEY `requested_by` (`requested_by`),
  KEY `approved_by` (`approved_by`),
  KEY `department_id` (`department_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `material_requests`
--

INSERT INTO `material_requests` (`id`, `request_number`, `project_id`, `requested_by`, `department_id`, `request_date`, `required_date`, `status`, `priority`, `total_estimated_cost`, `approved_by`, `approved_date`, `rejection_reason`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'MR-20250809-0001', 2, 1, 1, '2025-08-09', '2025-08-15', 'draft', 'high', 140000.00, NULL, NULL, NULL, 'nmghmgm', '2025-08-09 03:01:06', '2025-08-09 03:01:06'),
(2, 'MR-20250809-0002', 1, 1, 2, '2025-08-09', '2025-08-09', 'draft', 'medium', 35000.00, NULL, NULL, NULL, 'hhh', '2025-08-09 03:10:07', '2025-08-09 03:10:07'),
(3, 'MR-20250809-0003', 1, 1, 1, '2025-08-09', '2025-08-09', 'approved', 'medium', 160000.00, 1, '2025-08-09 07:45:46', NULL, 'jhhhh', '2025-08-09 03:12:35', '2025-08-09 05:45:46'),
(4, 'MR-20250810-0004', 2, 1, 1, '2025-08-10', '2025-08-29', 'approved', 'medium', 140000.00, 1, '2025-08-10 06:37:56', NULL, 'nnnnnn', '2025-08-10 04:37:36', '2025-08-10 04:37:56'),
(5, 'MR-20250810-0005', 2, 1, 1, '2025-08-10', '2025-08-14', 'approved', 'medium', 595000.00, 1, '2025-08-10 06:39:50', NULL, 'nnnn', '2025-08-10 04:39:35', '2025-08-10 04:39:50'),
(6, 'MR-20250810-0006', 1, 1, 2, '2025-08-10', '2025-08-13', 'approved', 'medium', 30000.00, 1, '2025-08-10 06:43:06', NULL, 'bbbbb', '2025-08-10 04:42:28', '2025-08-10 04:43:06');

-- --------------------------------------------------------

--
-- Table structure for table `material_request_items`
--

DROP TABLE IF EXISTS `material_request_items`;
CREATE TABLE IF NOT EXISTS `material_request_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `material_request_id` int NOT NULL,
  `material_id` int NOT NULL,
  `quantity_requested` decimal(10,3) NOT NULL,
  `quantity_approved` decimal(10,3) DEFAULT NULL,
  `estimated_unit_cost` decimal(10,2) DEFAULT NULL,
  `estimated_total_cost` decimal(15,2) DEFAULT NULL,
  `specification_notes` text,
  `urgency_notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `material_request_id` (`material_request_id`),
  KEY `material_id` (`material_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `material_request_items`
--

INSERT INTO `material_request_items` (`id`, `material_request_id`, `material_id`, `quantity_requested`, `quantity_approved`, `estimated_unit_cost`, `estimated_total_cost`, `specification_notes`, `urgency_notes`, `created_at`) VALUES
(1, 3, 2, 8.000, 8.000, 20000.00, 160000.00, '', NULL, '2025-08-09 05:12:35'),
(2, 4, 2, 7.000, 7.000, 20000.00, 140000.00, 'jjjj', NULL, '2025-08-10 06:37:36'),
(3, 5, 3, 7.000, 7.000, 5000.00, 35000.00, 'jj', NULL, '2025-08-10 06:39:35'),
(4, 5, 4, 8.000, 8.000, 70000.00, 560000.00, 'hhhjh', NULL, '2025-08-10 06:39:35'),
(5, 6, 3, 6.000, NULL, 5000.00, 30000.00, 'jjj', NULL, '2025-08-10 06:42:28');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int NOT NULL,
  `batch` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(1, '2025-06-28-110000', 'App\\Database\\Migrations\\AddSupplierIdToMaterials', 'default', 'App', 1751095909, 1),
(2, '2025-06-29-100000', 'App\\Database\\Migrations\\CreateSupplierMaterialsTable', 'default', 'App', 1751096292, 2),
(3, '2025-06-30-120000', 'App\\Database\\Migrations\\CreateDeliveriesTable', 'default', 'App', 1751096575, 3);

-- --------------------------------------------------------

--
-- Table structure for table `mobile_app_versions`
--

DROP TABLE IF EXISTS `mobile_app_versions`;
CREATE TABLE IF NOT EXISTS `mobile_app_versions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `version_number` varchar(20) NOT NULL,
  `build_number` int NOT NULL,
  `platform` enum('android','ios') NOT NULL,
  `min_os_version` varchar(20) DEFAULT NULL,
  `release_date` date NOT NULL,
  `is_mandatory_update` tinyint(1) DEFAULT '0',
  `download_url` varchar(500) DEFAULT NULL,
  `release_notes` text,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_platform_version` (`platform`,`version_number`),
  KEY `idx_mobile_version_platform` (`platform`),
  KEY `idx_mobile_version_active` (`is_active`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `notification_type` enum('email','sms','push','in_app') NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `related_type` varchar(100) DEFAULT NULL,
  `related_id` bigint UNSIGNED DEFAULT NULL,
  `priority` enum('low','medium','high','urgent') DEFAULT 'medium',
  `status` enum('pending','sent','delivered','failed','read') DEFAULT 'pending',
  `is_read` tinyint(1) DEFAULT '0',
  `read_at` timestamp NULL DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `delivery_status` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_notification_company` (`company_id`),
  KEY `idx_notification_user` (`user_id`),
  KEY `idx_notification_status` (`status`),
  KEY `idx_notification_type` (`notification_type`),
  KEY `idx_notification_related` (`related_type`,`related_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification_templates`
--

DROP TABLE IF EXISTS `notification_templates`;
CREATE TABLE IF NOT EXISTS `notification_templates` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `notification_type` enum('email','sms','push','in_app') NOT NULL,
  `event_trigger` varchar(100) NOT NULL,
  `subject_template` varchar(500) DEFAULT NULL,
  `body_template` text,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_notification_template_company` (`company_id`),
  KEY `idx_notification_template_trigger` (`event_trigger`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `offline_sync_queue`
--

DROP TABLE IF EXISTS `offline_sync_queue`;
CREATE TABLE IF NOT EXISTS `offline_sync_queue` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `device_token` varchar(500) DEFAULT NULL,
  `table_name` varchar(100) NOT NULL,
  `record_id` bigint UNSIGNED NOT NULL,
  `action` enum('create','update','delete') NOT NULL,
  `data_payload` json DEFAULT NULL,
  `sync_status` enum('pending','synced','failed','conflict') DEFAULT 'pending',
  `conflict_resolution` json DEFAULT NULL,
  `attempted_at` timestamp NULL DEFAULT NULL,
  `synced_at` timestamp NULL DEFAULT NULL,
  `error_message` text,
  `retry_count` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_offline_sync_user` (`user_id`),
  KEY `idx_offline_sync_status` (`sync_status`),
  KEY `idx_offline_sync_table` (`table_name`),
  KEY `idx_offline_sync_created` (`created_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
CREATE TABLE IF NOT EXISTS `payments` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED NOT NULL,
  `invoice_id` bigint UNSIGNED DEFAULT NULL,
  `client_id` bigint UNSIGNED DEFAULT NULL,
  `supplier_id` bigint UNSIGNED DEFAULT NULL,
  `payment_method_id` bigint UNSIGNED DEFAULT NULL,
  `receipt_number` varchar(50) NOT NULL,
  `payment_date` date NOT NULL,
  `payment_type` enum('received','paid') NOT NULL,
  `amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `currency` varchar(3) DEFAULT 'MWK',
  `exchange_rate` decimal(10,4) DEFAULT '1.0000',
  `reference_number` varchar(100) DEFAULT NULL,
  `bank_reference` varchar(100) DEFAULT NULL,
  `notes` text,
  `status` enum('pending','cleared','bounced','cancelled') DEFAULT 'cleared',
  `recorded_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_company_receipt_number` (`company_id`,`receipt_number`),
  KEY `payment_method_id` (`payment_method_id`),
  KEY `recorded_by` (`recorded_by`),
  KEY `idx_payment_company` (`company_id`),
  KEY `idx_payment_invoice` (`invoice_id`),
  KEY `idx_payment_client` (`client_id`),
  KEY `idx_payment_supplier` (`supplier_id`),
  KEY `idx_payment_date` (`payment_date`),
  KEY `idx_payment_type` (`payment_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_methods`
--

DROP TABLE IF EXISTS `payment_methods`;
CREATE TABLE IF NOT EXISTS `payment_methods` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(20) DEFAULT NULL,
  `account_id` bigint UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `account_id` (`account_id`),
  KEY `idx_payment_method_company` (`company_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payroll_allowance_details`
--

DROP TABLE IF EXISTS `payroll_allowance_details`;
CREATE TABLE IF NOT EXISTS `payroll_allowance_details` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `payroll_record_id` bigint UNSIGNED NOT NULL,
  `allowance_type_id` bigint UNSIGNED NOT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `is_taxable` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `allowance_type_id` (`allowance_type_id`),
  KEY `idx_payroll_allowance_record` (`payroll_record_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payroll_deduction_details`
--

DROP TABLE IF EXISTS `payroll_deduction_details`;
CREATE TABLE IF NOT EXISTS `payroll_deduction_details` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `payroll_record_id` bigint UNSIGNED NOT NULL,
  `deduction_type_id` bigint UNSIGNED NOT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `deduction_type_id` (`deduction_type_id`),
  KEY `idx_payroll_deduction_record` (`payroll_record_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payroll_records`
--

DROP TABLE IF EXISTS `payroll_records`;
CREATE TABLE IF NOT EXISTS `payroll_records` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `pay_period_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `basic_salary` decimal(12,2) NOT NULL DEFAULT '0.00',
  `gross_salary` decimal(12,2) NOT NULL DEFAULT '0.00',
  `regular_hours` decimal(6,2) DEFAULT '0.00',
  `overtime_hours` decimal(6,2) DEFAULT '0.00',
  `total_hours` decimal(6,2) DEFAULT '0.00',
  `hourly_rate` decimal(10,2) DEFAULT '0.00',
  `overtime_rate` decimal(10,2) DEFAULT '0.00',
  `regular_pay` decimal(12,2) DEFAULT '0.00',
  `overtime_pay` decimal(12,2) DEFAULT '0.00',
  `total_allowances` decimal(12,2) DEFAULT '0.00',
  `bonus` decimal(12,2) DEFAULT '0.00',
  `commission` decimal(12,2) DEFAULT '0.00',
  `total_earnings` decimal(12,2) DEFAULT '0.00',
  `total_deductions` decimal(12,2) DEFAULT '0.00',
  `tax_deduction` decimal(12,2) DEFAULT '0.00',
  `social_security` decimal(12,2) DEFAULT '0.00',
  `other_deductions` decimal(12,2) DEFAULT '0.00',
  `net_pay` decimal(12,2) NOT NULL DEFAULT '0.00',
  `payment_method` enum('bank_transfer','cash','cheque','mobile_money') DEFAULT 'bank_transfer',
  `payment_reference` varchar(100) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `payment_status` enum('pending','processed','failed','cancelled') DEFAULT 'pending',
  `calculated_by` bigint UNSIGNED DEFAULT NULL,
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `paid_by` bigint UNSIGNED DEFAULT NULL,
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_payroll_period_user` (`pay_period_id`,`user_id`),
  KEY `calculated_by` (`calculated_by`),
  KEY `approved_by` (`approved_by`),
  KEY `paid_by` (`paid_by`),
  KEY `idx_payroll_period` (`pay_period_id`),
  KEY `idx_payroll_user` (`user_id`),
  KEY `idx_payroll_payment_status` (`payment_status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pay_periods`
--

DROP TABLE IF EXISTS `pay_periods`;
CREATE TABLE IF NOT EXISTS `pay_periods` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `pay_date` date NOT NULL,
  `status` enum('draft','processing','approved','paid','closed') DEFAULT 'draft',
  `total_gross_pay` decimal(15,2) DEFAULT '0.00',
  `total_deductions` decimal(15,2) DEFAULT '0.00',
  `total_net_pay` decimal(15,2) DEFAULT '0.00',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  KEY `idx_pay_period_company` (`company_id`),
  KEY `idx_pay_period_dates` (`start_date`,`end_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `procurement_accounting_entries`
--

DROP TABLE IF EXISTS `procurement_accounting_entries`;
CREATE TABLE IF NOT EXISTS `procurement_accounting_entries` (
  `id` int NOT NULL AUTO_INCREMENT,
  `transaction_type` enum('material_request','purchase_order','goods_receipt','stock_in','invoice_receipt','payment') NOT NULL,
  `reference_id` int NOT NULL,
  `reference_table` varchar(50) NOT NULL,
  `journal_entry_id` int NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `journal_entry_id` (`journal_entry_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
CREATE TABLE IF NOT EXISTS `projects` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED NOT NULL,
  `client_id` bigint UNSIGNED DEFAULT NULL,
  `category_id` bigint UNSIGNED DEFAULT NULL,
  `project_code` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `project_type` enum('residential','commercial','industrial','infrastructure','renovation') NOT NULL,
  `priority` enum('low','medium','high','critical') DEFAULT 'medium',
  `status` enum('planning','active','on_hold','completed','cancelled') DEFAULT 'planning',
  `progress_percentage` decimal(5,2) DEFAULT '0.00',
  `estimated_budget` decimal(15,2) NOT NULL DEFAULT '0.00',
  `actual_cost` decimal(15,2) DEFAULT '0.00',
  `contract_value` decimal(15,2) DEFAULT '0.00',
  `currency` varchar(3) DEFAULT 'MWK',
  `wip_account_code` varchar(50) DEFAULT NULL,
  `revenue_account_code` varchar(50) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `planned_end_date` date DEFAULT NULL,
  `actual_end_date` date DEFAULT NULL,
  `site_address` text,
  `site_city` varchar(100) DEFAULT NULL,
  `site_state` varchar(100) DEFAULT NULL,
  `site_coordinates` point DEFAULT NULL,
  `project_manager_id` bigint UNSIGNED DEFAULT NULL,
  `site_supervisor_id` bigint UNSIGNED DEFAULT NULL,
  `is_template` tinyint(1) DEFAULT '0',
  `is_archived` tinyint(1) DEFAULT '0',
  `requires_permit` tinyint(1) DEFAULT '0',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_code` (`project_code`),
  KEY `category_id` (`category_id`),
  KEY `site_supervisor_id` (`site_supervisor_id`),
  KEY `created_by` (`created_by`),
  KEY `idx_project_company` (`company_id`),
  KEY `idx_project_client` (`client_id`),
  KEY `idx_project_status` (`status`),
  KEY `idx_project_code` (`project_code`),
  KEY `idx_project_dates` (`start_date`,`planned_end_date`),
  KEY `idx_project_manager` (`project_manager_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `company_id`, `client_id`, `category_id`, `project_code`, `name`, `description`, `project_type`, `priority`, `status`, `progress_percentage`, `estimated_budget`, `actual_cost`, `contract_value`, `currency`, `wip_account_code`, `revenue_account_code`, `start_date`, `planned_end_date`, `actual_end_date`, `site_address`, `site_city`, `site_state`, `site_coordinates`, `project_manager_id`, `site_supervisor_id`, `is_template`, `is_archived`, `requires_permit`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 1, 0, 0, 'cthp98', 'chitipi church contsuction', 'bbbb', 'residential', 'medium', 'active', 0.00, 200000.00, 0.00, 20000000.00, 'USD', NULL, NULL, '2025-06-01', '2025-08-28', '2025-11-19', 'jhjhhhjhj', 'bhhggg', 'ggg', NULL, 1, 2, 0, 0, 0, 1, '2025-06-26 21:28:52', '2025-06-26 21:28:52'),
(2, 1, 1, 1, 'CHIWEMBESC', 'Chiwembe School construction', 'jhfghfhjgjhg vjshvhfdhfhdfhg', 'industrial', 'high', 'active', 3.00, 1000000.00, 0.00, 10000000.00, 'USD', NULL, NULL, '2025-06-28', '2025-07-10', '2025-07-12', 'CHimbe', 'Blantyre', 'Blantyre', NULL, 2, 1, 0, 0, 0, 1, '2025-06-28 08:29:03', '2025-06-28 08:32:33');

-- --------------------------------------------------------

--
-- Table structure for table `project_categories`
--

DROP TABLE IF EXISTS `project_categories`;
CREATE TABLE IF NOT EXISTS `project_categories` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text,
  `color_code` varchar(7) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_category_company` (`company_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `project_categories`
--

INSERT INTO `project_categories` (`id`, `company_id`, `name`, `description`, `color_code`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'Contsruction', 'pcontsruvtion project', '#6366f1', 1, '2025-06-26 21:33:53', '2025-06-26 21:33:53'),
(2, 1, 'Project Super', 'ksdvksfv', '#6366f1', 1, '2025-06-28 08:25:35', '2025-06-28 08:25:35');

-- --------------------------------------------------------

--
-- Table structure for table `project_team_members`
--

DROP TABLE IF EXISTS `project_team_members`;
CREATE TABLE IF NOT EXISTS `project_team_members` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `project_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `role` varchar(100) NOT NULL,
  `responsibilities` text,
  `hourly_rate` decimal(10,2) DEFAULT NULL,
  `assigned_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `removed_at` timestamp NULL DEFAULT NULL,
  `assigned_by` bigint UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_project_user_active` (`project_id`,`user_id`,`removed_at`),
  KEY `assigned_by` (`assigned_by`),
  KEY `idx_team_project` (`project_id`),
  KEY `idx_team_user` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_orders`
--

DROP TABLE IF EXISTS `purchase_orders`;
CREATE TABLE IF NOT EXISTS `purchase_orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `po_number` varchar(50) NOT NULL,
  `supplier_id` int NOT NULL,
  `material_request_id` int DEFAULT NULL,
  `project_id` int DEFAULT NULL,
  `po_date` date NOT NULL,
  `expected_delivery_date` date DEFAULT NULL,
  `status` enum('draft','sent','acknowledged','partially_received','completed','cancelled') DEFAULT 'draft',
  `payment_terms` varchar(100) DEFAULT NULL,
  `delivery_terms` varchar(100) DEFAULT NULL,
  `subtotal` decimal(15,2) NOT NULL,
  `tax_amount` decimal(15,2) DEFAULT '0.00',
  `freight_cost` decimal(15,2) DEFAULT '0.00',
  `total_amount` decimal(15,2) NOT NULL,
  `currency` varchar(3) DEFAULT 'USD',
  `created_by` int NOT NULL,
  `approved_by` int DEFAULT NULL,
  `approved_date` datetime DEFAULT NULL,
  `sent_date` datetime DEFAULT NULL,
  `notes` text,
  `terms_conditions` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `po_number` (`po_number`),
  KEY `supplier_id` (`supplier_id`),
  KEY `material_request_id` (`material_request_id`),
  KEY `project_id` (`project_id`),
  KEY `created_by` (`created_by`),
  KEY `approved_by` (`approved_by`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `purchase_orders`
--

INSERT INTO `purchase_orders` (`id`, `po_number`, `supplier_id`, `material_request_id`, `project_id`, `po_date`, `expected_delivery_date`, `status`, `payment_terms`, `delivery_terms`, `subtotal`, `tax_amount`, `freight_cost`, `total_amount`, `currency`, `created_by`, `approved_by`, `approved_date`, `sent_date`, `notes`, `terms_conditions`, `created_at`, `updated_at`) VALUES
(1, 'PO-2025-0001', 1, NULL, 1, '2025-08-09', '2025-08-30', 'draft', 'Net 30 days', 'FOB Destination', 40000.00, 0.00, 0.00, 40000.00, 'MWK', 1, NULL, NULL, NULL, 'nnhhh', 'All materials must meet specified standards and requirements.', '2025-08-09 02:53:09', '2025-08-09 02:53:09'),
(2, 'PO-2025-0002', 2, 3, 1, '2025-08-09', '2025-08-09', 'draft', 'Net 30 days', 'FOB Destination', 140000.00, 0.00, 0.00, 140000.00, 'MWK', 1, NULL, NULL, NULL, 'bbbb', 'All materials must meet specified standards and requirebbbbments.', '2025-08-09 03:47:39', '2025-08-09 03:47:39'),
(3, 'PO-2025-0003', 2, 3, 1, '2025-08-09', '2025-08-09', 'draft', 'Net 30 days', 'FOB Destination', 160000.00, 0.00, 0.00, 160000.00, 'MWK', 1, NULL, NULL, NULL, 'bbbb', 'All materials must meet specified standards and requirebbbbments.', '2025-08-09 03:52:47', '2025-08-09 03:52:47'),
(4, 'PO-2025-0004', 2, 3, 1, '2025-08-09', '2025-08-09', 'draft', 'Net 30 days', 'FOB Destination', 160000.00, 0.00, 0.00, 160000.00, 'MWK', 1, NULL, NULL, NULL, 'bbbb', 'All materials must meet specified standards and requirebbbbments.', '2025-08-09 03:53:10', '2025-08-09 03:53:10'),
(5, 'PO-2025-0005', 2, 3, 1, '2025-08-09', '2025-08-21', 'draft', 'Net 30 days', 'FOB Destination', 160000.00, 0.00, 0.00, 160000.00, 'MWK', 1, NULL, NULL, NULL, NULL, 'All materials must meet specified standards and requirements.', '2025-08-09 03:53:53', '2025-08-09 03:53:53'),
(6, 'PO-2025-0006', 2, 3, 1, '2025-08-09', '2025-08-21', 'sent', 'Net 30 days', 'FOB Destination', 160000.00, 0.00, 0.00, 160000.00, 'MWK', 1, 1, '2025-08-10 06:48:05', '2025-08-10 06:48:05', NULL, 'All materials must meet specified standards and requirements.', '2025-08-09 03:56:42', '2025-08-10 04:48:05'),
(7, 'PO-2025-0007', 1, 3, 1, '2025-08-10', '2025-08-11', 'completed', 'Net 30 days', 'FOB Destination', 160000.00, 0.00, 0.00, 160000.00, 'MWK', 1, 1, '2025-08-10 06:01:40', '2025-08-10 06:01:40', NULL, 'All materials must meet specified standards and requirements.', '2025-08-10 03:31:50', '2025-08-10 04:34:44'),
(8, 'PO-2025-0008', 1, 5, 2, '2025-08-10', '2025-08-13', 'completed', 'Net 30 days', 'FOB Destination', 595000.00, 0.00, 0.00, 595000.00, 'MWK', 1, 1, '2025-08-10 06:44:09', '2025-08-10 06:44:09', 'nnnn', 'All materials must meet specified standards and requirements.', '2025-08-10 04:43:58', '2025-08-10 04:48:37');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_items`
--

DROP TABLE IF EXISTS `purchase_order_items`;
CREATE TABLE IF NOT EXISTS `purchase_order_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `purchase_order_id` int NOT NULL,
  `material_id` int NOT NULL,
  `material_request_item_id` int DEFAULT NULL,
  `quantity_ordered` decimal(10,3) NOT NULL,
  `unit_cost` decimal(10,2) NOT NULL,
  `total_cost` decimal(15,2) NOT NULL,
  `quantity_received` decimal(10,3) DEFAULT '0.000',
  `quantity_pending` decimal(10,3) NOT NULL,
  `specification_notes` text,
  `delivery_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `purchase_order_id` (`purchase_order_id`),
  KEY `material_id` (`material_id`),
  KEY `material_request_item_id` (`material_request_item_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `purchase_order_items`
--

INSERT INTO `purchase_order_items` (`id`, `purchase_order_id`, `material_id`, `material_request_item_id`, `quantity_ordered`, `unit_cost`, `total_cost`, `quantity_received`, `quantity_pending`, `specification_notes`, `delivery_date`, `created_at`) VALUES
(1, 999999, 1, NULL, 5.000, 100.00, 500.00, 0.000, 0.000, 'Test insert', NULL, '2025-08-09 05:55:15'),
(2, 6, 2, NULL, 8.000, 20000.00, 160000.00, 0.000, 8.000, NULL, NULL, '2025-08-09 05:56:42'),
(3, 7, 2, NULL, 8.000, 20000.00, 160000.00, 8.000, 0.000, NULL, NULL, '2025-08-10 05:31:50'),
(4, 8, 3, NULL, 7.000, 5000.00, 35000.00, 7.000, 0.000, NULL, NULL, '2025-08-10 06:43:58'),
(5, 8, 4, NULL, 8.000, 70000.00, 560000.00, 8.000, 0.000, NULL, NULL, '2025-08-10 06:43:58'),
(6, 999999, 1, NULL, 5.000, 100.00, 500.00, 0.000, 0.000, 'Test insert', NULL, '2025-08-10 07:33:13');

-- --------------------------------------------------------

--
-- Table structure for table `quality_inspections`
--

DROP TABLE IF EXISTS `quality_inspections`;
CREATE TABLE IF NOT EXISTS `quality_inspections` (
  `id` int NOT NULL AUTO_INCREMENT,
  `inspection_number` varchar(50) NOT NULL,
  `grn_item_id` int NOT NULL,
  `material_id` int NOT NULL,
  `inspector_id` int NOT NULL,
  `inspection_date` datetime NOT NULL,
  `inspection_type` enum('incoming','random','complaint','audit') DEFAULT 'incoming',
  `status` enum('pending','in_progress','passed','failed','conditional') DEFAULT 'pending',
  `overall_grade` enum('A','B','C','D','F') DEFAULT NULL,
  `quantity_inspected` decimal(10,3) NOT NULL,
  `quantity_passed` decimal(10,3) DEFAULT '0.000',
  `quantity_failed` decimal(10,3) DEFAULT '0.000',
  `defect_description` text,
  `corrective_action` text,
  `inspector_notes` text,
  `attachments` json DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `inspection_number` (`inspection_number`),
  KEY `grn_item_id` (`grn_item_id`),
  KEY `material_id` (`material_id`),
  KEY `inspector_id` (`inspector_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `report_categories`
--

DROP TABLE IF EXISTS `report_categories`;
CREATE TABLE IF NOT EXISTS `report_categories` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `icon` varchar(50) DEFAULT NULL,
  `sort_order` int DEFAULT '1',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_report_category_company` (`company_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text,
  `is_system_role` tinyint(1) DEFAULT '0',
  `permissions` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_company_role_slug` (`company_id`,`slug`),
  KEY `idx_role_company` (`company_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `company_id`, `name`, `slug`, `description`, `is_system_role`, `permissions`, `created_at`, `updated_at`) VALUES
(1, 1, 'Super Admin', 'super_admin', 'Full system access', 1, '[\"*\"]', '2025-06-24 19:48:45', '2025-06-24 19:48:45'),
(2, 1, 'Admin', 'admin', 'Administrative access', 1, '[\"users.*\", \"projects.*\", \"inventory.*\", \"accounting.*\", \"hr.*\"]', '2025-06-24 19:48:45', '2025-06-24 19:48:45'),
(3, 1, 'Project Manager', 'project_manager', 'Project management access', 1, '[\"projects.*\", \"tasks.*\", \"timesheets.*\", \"files.*\"]', '2025-06-24 19:48:45', '2025-06-24 19:48:45'),
(4, 1, 'Site Supervisor', 'site_supervisor', 'On-site management access', 1, '[\"projects.view\", \"tasks.*\", \"attendance.*\", \"safety.*\"]', '2025-06-24 19:48:45', '2025-06-24 19:48:45'),
(5, 1, 'Employee', 'employee', 'Basic employee access', 1, '[\"profile.*\", \"timesheets.own\", \"attendance.own\"]', '2025-06-24 19:48:45', '2025-06-24 19:48:45');

-- --------------------------------------------------------

--
-- Table structure for table `safety_inspections`
--

DROP TABLE IF EXISTS `safety_inspections`;
CREATE TABLE IF NOT EXISTS `safety_inspections` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED NOT NULL,
  `project_id` bigint UNSIGNED DEFAULT NULL,
  `inspection_number` varchar(50) NOT NULL,
  `inspection_type` enum('routine','surprise','regulatory','post_incident','pre_work') NOT NULL,
  `inspection_date` date NOT NULL,
  `inspection_time` time DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `inspector_id` bigint UNSIGNED NOT NULL,
  `inspector_type` enum('internal','external','regulatory') DEFAULT 'internal',
  `external_inspector_name` varchar(255) DEFAULT NULL,
  `external_company` varchar(255) DEFAULT NULL,
  `overall_score` decimal(5,2) DEFAULT NULL,
  `max_possible_score` decimal(5,2) DEFAULT NULL,
  `compliance_percentage` decimal(5,2) DEFAULT NULL,
  `status` enum('scheduled','in_progress','completed','cancelled') DEFAULT 'scheduled',
  `findings_summary` text,
  `recommendations` text,
  `follow_up_required` tinyint(1) DEFAULT '0',
  `follow_up_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_company_inspection_number` (`company_id`,`inspection_number`),
  KEY `inspector_id` (`inspector_id`),
  KEY `idx_safety_inspection_company` (`company_id`),
  KEY `idx_safety_inspection_project` (`project_id`),
  KEY `idx_safety_inspection_date` (`inspection_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `safety_inspection_items`
--

DROP TABLE IF EXISTS `safety_inspection_items`;
CREATE TABLE IF NOT EXISTS `safety_inspection_items` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `inspection_id` bigint UNSIGNED NOT NULL,
  `category` varchar(255) NOT NULL,
  `item_description` text NOT NULL,
  `compliance_status` enum('compliant','non_compliant','partial','not_applicable') NOT NULL,
  `severity` enum('low','medium','high','critical') DEFAULT 'medium',
  `score` decimal(5,2) DEFAULT '0.00',
  `max_score` decimal(5,2) DEFAULT '0.00',
  `observations` text,
  `corrective_action_required` text,
  `target_completion_date` date DEFAULT NULL,
  `assigned_to` bigint UNSIGNED DEFAULT NULL,
  `item_order` int DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `assigned_to` (`assigned_to`),
  KEY `idx_safety_item_inspection` (`inspection_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_movements`
--

DROP TABLE IF EXISTS `stock_movements`;
CREATE TABLE IF NOT EXISTS `stock_movements` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED NOT NULL,
  `material_id` bigint UNSIGNED NOT NULL,
  `source_warehouse_id` bigint UNSIGNED DEFAULT NULL,
  `destination_warehouse_id` bigint UNSIGNED DEFAULT NULL,
  `project_id` bigint UNSIGNED DEFAULT NULL,
  `task_id` bigint UNSIGNED DEFAULT NULL,
  `reference_type` enum('purchase','sale','transfer','adjustment','return','consumption','production','project_usage','delivery','manual') NOT NULL,
  `reference_id` bigint UNSIGNED DEFAULT NULL,
  `movement_type` enum('in','out','transfer','adjustment','project_usage','return','consumption','production') NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `unit_cost` decimal(12,2) DEFAULT '0.00',
  `total_cost` decimal(12,2) DEFAULT '0.00',
  `previous_balance` decimal(12,2) DEFAULT '0.00',
  `new_balance` decimal(12,2) DEFAULT '0.00',
  `batch_number` varchar(100) DEFAULT NULL,
  `serial_numbers` json DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `notes` text,
  `moved_by` bigint UNSIGNED DEFAULT NULL,
  `approved_by` bigint DEFAULT NULL,
  `performed_by` bigint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `purchase_order_id` int DEFAULT NULL,
  `grn_id` int DEFAULT NULL,
  `material_request_id` int DEFAULT NULL,
  `quality_status` enum('pending','approved','rejected') DEFAULT 'approved',
  `inspection_id` int DEFAULT NULL,
  `delivery_note_number` varchar(100) DEFAULT NULL,
  `supplier_batch_number` varchar(100) DEFAULT NULL,
  `freight_cost` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `moved_by` (`moved_by`),
  KEY `idx_stock_company` (`company_id`),
  KEY `idx_stock_material` (`material_id`),
  KEY `idx_stock_project` (`project_id`),
  KEY `idx_stock_created` (`created_at`),
  KEY `idx_stock_task` (`task_id`),
  KEY `idx_stock_destination_warehouse` (`destination_warehouse_id`),
  KEY `idx_stock_source_warehouse` (`source_warehouse_id`),
  KEY `purchase_order_id` (`purchase_order_id`),
  KEY `grn_id` (`grn_id`),
  KEY `material_request_id` (`material_request_id`),
  KEY `inspection_id` (`inspection_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Stock movements tracking for materials across warehouses and projects';

--
-- Dumping data for table `stock_movements`
--

INSERT INTO `stock_movements` (`id`, `company_id`, `material_id`, `source_warehouse_id`, `destination_warehouse_id`, `project_id`, `task_id`, `reference_type`, `reference_id`, `movement_type`, `quantity`, `unit_cost`, `total_cost`, `previous_balance`, `new_balance`, `batch_number`, `serial_numbers`, `expiry_date`, `notes`, `moved_by`, `approved_by`, `performed_by`, `created_at`, `updated_at`, `purchase_order_id`, `grn_id`, `material_request_id`, `quality_status`, `inspection_id`, `delivery_note_number`, `supplier_batch_number`, `freight_cost`) VALUES
(1, 1, 2, NULL, 2, NULL, NULL, 'manual', NULL, 'in', 100.00, 20000.00, 2000000.00, 0.00, 0.00, NULL, NULL, NULL, 'jkdkjd', 1, NULL, 1, '2025-06-28 07:58:59', '2025-06-28 07:58:59', NULL, NULL, NULL, 'approved', NULL, NULL, NULL, 0.00),
(2, 1, 2, NULL, 2, NULL, NULL, 'manual', NULL, 'in', 200.00, 20000.00, 4000000.00, 0.00, 0.00, NULL, NULL, NULL, 'nmvsmvnsc', 1, NULL, 1, '2025-06-28 08:08:59', '2025-06-28 08:08:59', NULL, NULL, NULL, 'approved', NULL, NULL, NULL, 0.00),
(3, 1, 1, NULL, 2, NULL, NULL, 'manual', NULL, 'in', 100.00, 20000.00, 2000000.00, 0.00, 0.00, NULL, NULL, NULL, 'jdj', 1, NULL, 1, '2025-06-28 08:09:31', '2025-06-28 08:09:31', NULL, NULL, NULL, 'approved', NULL, NULL, NULL, 0.00),
(4, 1, 1, NULL, 1, NULL, NULL, 'manual', NULL, 'in', 1000.00, 20000.00, 20000000.00, 0.00, 0.00, NULL, NULL, NULL, 'kcxxk', 1, NULL, 1, '2025-06-28 08:09:55', '2025-06-28 08:09:55', NULL, NULL, NULL, 'approved', NULL, NULL, NULL, 0.00),
(5, 1, 3, NULL, 2, NULL, NULL, 'manual', NULL, 'in', 100.00, 5000.00, 500000.00, 0.00, 0.00, NULL, NULL, NULL, 'hghg', 1, NULL, 1, '2025-06-28 08:47:10', '2025-06-28 08:47:10', NULL, NULL, NULL, 'approved', NULL, NULL, NULL, 0.00),
(6, 1, 3, NULL, 1, NULL, NULL, 'manual', NULL, 'in', 500.00, 5000.00, 2500000.00, 0.00, 0.00, NULL, NULL, NULL, 'vhhg', 1, NULL, 1, '2025-06-28 08:47:41', '2025-06-28 08:47:41', NULL, NULL, NULL, 'approved', NULL, NULL, NULL, 0.00),
(7, 1, 4, NULL, 2, NULL, NULL, 'manual', NULL, 'in', 70.00, 70000.00, 4900000.00, 0.00, 0.00, NULL, NULL, NULL, 'abc in chawa', 1, NULL, 1, '2025-08-09 00:25:28', '2025-08-09 00:25:28', NULL, NULL, NULL, 'approved', NULL, NULL, NULL, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
CREATE TABLE IF NOT EXISTS `suppliers` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED NOT NULL,
  `supplier_code` varchar(50) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `address` text,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `tax_number` varchar(100) DEFAULT NULL,
  `payment_terms` varchar(100) DEFAULT NULL,
  `credit_limit` decimal(15,2) DEFAULT '0.00',
  `supplier_type` enum('materials','equipment','services','mixed') DEFAULT 'mixed',
  `rating` decimal(3,2) DEFAULT '0.00',
  `status` enum('active','inactive','blacklisted') DEFAULT 'active',
  `notes` text,
  `payable_account_code` varchar(50) DEFAULT NULL,
  `expense_account_code` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_supplier_company` (`company_id`),
  KEY `idx_supplier_code` (`supplier_code`),
  KEY `idx_supplier_status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `company_id`, `supplier_code`, `name`, `contact_person`, `email`, `phone`, `mobile`, `address`, `city`, `state`, `country`, `tax_number`, `payment_terms`, `credit_limit`, `supplier_type`, `rating`, `status`, `notes`, `payable_account_code`, `expense_account_code`, `created_at`, `updated_at`) VALUES
(1, 1, 'G320', 'GUEBE', 'hhhh', '', '0994099461', NULL, 'kaya', NULL, NULL, NULL, NULL, '', 0.00, 'mixed', 0.00, 'active', '', NULL, NULL, '2025-06-28 06:01:14', '2025-06-28 06:01:14'),
(2, 1, 'S472', 'steve shop', '0994099462', '', '0996099764', NULL, 'hhh', NULL, NULL, NULL, NULL, 'ne 30', 0.00, 'mixed', 0.00, 'active', '', NULL, NULL, '2025-06-28 06:03:47', '2025-06-28 06:03:47');

-- --------------------------------------------------------

--
-- Table structure for table `supplier_invoices`
--

DROP TABLE IF EXISTS `supplier_invoices`;
CREATE TABLE IF NOT EXISTS `supplier_invoices` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED NOT NULL,
  `supplier_id` bigint UNSIGNED NOT NULL,
  `purchase_order_id` bigint UNSIGNED DEFAULT NULL,
  `invoice_number` varchar(100) NOT NULL,
  `invoice_date` date NOT NULL,
  `due_date` date NOT NULL,
  `subtotal` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(15,2) DEFAULT '0.00',
  `total_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `paid_amount` decimal(15,2) DEFAULT '0.00',
  `balance_due` decimal(15,2) DEFAULT '0.00',
  `currency` varchar(3) DEFAULT 'MWK',
  `status` enum('received','matched','approved','paid','disputed') DEFAULT 'received',
  `three_way_match_status` enum('pending','matched','variance') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `supplier_materials`
--

DROP TABLE IF EXISTS `supplier_materials`;
CREATE TABLE IF NOT EXISTS `supplier_materials` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `supplier_id` int UNSIGNED NOT NULL,
  `material_id` int UNSIGNED NOT NULL,
  `unit_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `min_order_qty` decimal(15,2) DEFAULT NULL,
  `lead_time` int DEFAULT NULL COMMENT 'Lead time in days',
  `notes` text,
  `is_preferred` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1=preferred supplier for this material, 0=not preferred',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `supplier_id_material_id` (`supplier_id`,`material_id`),
  KEY `supplier_materials_material_id_foreign` (`material_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `supplier_payments`
--

DROP TABLE IF EXISTS `supplier_payments`;
CREATE TABLE IF NOT EXISTS `supplier_payments` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED NOT NULL,
  `supplier_id` bigint UNSIGNED NOT NULL,
  `supplier_invoice_id` bigint UNSIGNED DEFAULT NULL,
  `payment_number` varchar(50) NOT NULL,
  `payment_date` date NOT NULL,
  `payment_method_id` bigint UNSIGNED NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `currency` varchar(3) DEFAULT 'MWK',
  `reference_number` varchar(100) DEFAULT NULL,
  `notes` text,
  `status` enum('pending','processed','cleared','cancelled') DEFAULT 'pending',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_alerts`
--

DROP TABLE IF EXISTS `system_alerts`;
CREATE TABLE IF NOT EXISTS `system_alerts` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED NOT NULL,
  `alert_type` enum('low_stock','overdue_task','budget_exceeded','maintenance_due','safety_incident','payment_overdue','contract_expiry') NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `severity` enum('info','warning','error','critical') DEFAULT 'info',
  `related_type` varchar(100) DEFAULT NULL,
  `related_id` bigint UNSIGNED DEFAULT NULL,
  `threshold_value` decimal(15,2) DEFAULT NULL,
  `current_value` decimal(15,2) DEFAULT NULL,
  `is_acknowledged` tinyint(1) DEFAULT '0',
  `acknowledged_by` bigint UNSIGNED DEFAULT NULL,
  `acknowledged_at` timestamp NULL DEFAULT NULL,
  `is_resolved` tinyint(1) DEFAULT '0',
  `resolved_by` bigint UNSIGNED DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `resolution_notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `acknowledged_by` (`acknowledged_by`),
  KEY `resolved_by` (`resolved_by`),
  KEY `idx_system_alert_company` (`company_id`),
  KEY `idx_system_alert_type` (`alert_type`),
  KEY `idx_system_alert_severity` (`severity`),
  KEY `idx_system_alert_status` (`is_acknowledged`,`is_resolved`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

DROP TABLE IF EXISTS `system_settings`;
CREATE TABLE IF NOT EXISTS `system_settings` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text,
  `setting_type` enum('string','number','boolean','json','encrypted') DEFAULT 'string',
  `category` varchar(50) DEFAULT 'general',
  `description` text,
  `is_public` tinyint(1) DEFAULT '0',
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_company_setting` (`company_id`,`setting_key`),
  KEY `updated_by` (`updated_by`),
  KEY `idx_system_setting_company` (`company_id`),
  KEY `idx_system_setting_category` (`category`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

DROP TABLE IF EXISTS `tasks`;
CREATE TABLE IF NOT EXISTS `tasks` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `project_id` bigint UNSIGNED NOT NULL,
  `parent_task_id` bigint UNSIGNED DEFAULT NULL,
  `category_id` bigint UNSIGNED DEFAULT NULL,
  `task_code` varchar(50) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `task_type` enum('milestone','task','subtask') DEFAULT 'task',
  `priority` enum('low','medium','high','critical') DEFAULT 'medium',
  `status` enum('not_started','in_progress','review','completed','cancelled','on_hold') DEFAULT 'not_started',
  `progress_percentage` decimal(5,2) DEFAULT '0.00',
  `assigned_to` bigint UNSIGNED DEFAULT NULL,
  `assigned_by` bigint UNSIGNED DEFAULT NULL,
  `planned_start_date` date DEFAULT NULL,
  `planned_end_date` date DEFAULT NULL,
  `actual_start_date` date DEFAULT NULL,
  `actual_end_date` date DEFAULT NULL,
  `estimated_hours` decimal(8,2) DEFAULT NULL,
  `actual_hours` decimal(8,2) DEFAULT '0.00',
  `estimated_cost` decimal(12,2) DEFAULT '0.00',
  `actual_cost` decimal(12,2) DEFAULT '0.00',
  `depends_on` json DEFAULT NULL,
  `is_critical_path` tinyint(1) DEFAULT '0',
  `requires_approval` tinyint(1) DEFAULT '0',
  `is_billable` tinyint(1) DEFAULT '1',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `assigned_by` (`assigned_by`),
  KEY `created_by` (`created_by`),
  KEY `idx_task_project` (`project_id`),
  KEY `idx_task_assigned` (`assigned_to`),
  KEY `idx_task_status` (`status`),
  KEY `idx_task_parent` (`parent_task_id`),
  KEY `idx_task_dates` (`planned_start_date`,`planned_end_date`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `project_id`, `parent_task_id`, `category_id`, `task_code`, `title`, `description`, `task_type`, `priority`, `status`, `progress_percentage`, `assigned_to`, `assigned_by`, `planned_start_date`, `planned_end_date`, `actual_start_date`, `actual_end_date`, `estimated_hours`, `actual_hours`, `estimated_cost`, `actual_cost`, `depends_on`, `is_critical_path`, `requires_approval`, `is_billable`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, NULL, 'cthp98-TSK-001', 'kulima', 'gggg', 'task', 'medium', 'not_started', 0.00, 3, 1, '2025-06-27', '2025-06-28', NULL, NULL, 300.00, 0.00, 1000.00, 0.00, NULL, 1, 1, 1, 1, '2025-06-27 12:41:09', '2025-06-27 12:41:09'),
(2, 1, NULL, NULL, 'cthp98-MS-001', 'Foundation', 'foundation abc', 'milestone', 'high', 'not_started', 0.00, NULL, NULL, '2025-06-30', '2025-06-30', NULL, NULL, NULL, 0.00, 0.00, 0.00, NULL, 0, 0, 1, 1, '2025-06-27 19:26:33', '2025-06-27 19:26:33'),
(3, 2, NULL, NULL, 'CHIWEMBESC-TSK-001', 'Foundation', 'ghgghhfgdg', 'milestone', 'medium', 'not_started', 0.00, 3, 1, '2025-06-28', '2025-06-30', NULL, NULL, 6.00, 0.00, 1000.00, 0.00, NULL, 1, 1, 1, 1, '2025-06-28 08:38:43', '2025-06-28 08:38:43'),
(4, 2, NULL, NULL, 'CHIWEMBESC-TSK-002', 'Foundation', 'ghgghhfgdg', 'milestone', 'medium', 'not_started', 0.00, 3, 1, '2025-06-28', '2025-06-30', NULL, NULL, 6.00, 0.00, 1000.00, 0.00, NULL, 1, 1, 1, 1, '2025-06-28 08:38:49', '2025-06-28 08:38:49'),
(5, 2, NULL, NULL, 'CHIWEMBESC-TSK-003', 'Foundation', 'ghgghhfgdg', 'task', 'medium', 'not_started', 0.00, 3, 1, '2025-06-28', '2025-06-30', NULL, NULL, 6.00, 0.00, 1000.00, 0.00, NULL, 1, 1, 1, 1, '2025-06-28 08:38:58', '2025-06-28 08:38:58'),
(6, 1, NULL, NULL, 'cthp98-MS-002', 'jjjjjj', 'nnnm', 'milestone', 'medium', 'not_started', 0.00, NULL, NULL, '2025-10-17', '2025-10-17', NULL, NULL, NULL, 0.00, 0.00, 0.00, NULL, 0, 0, 1, 1, '2025-08-08 23:29:01', '2025-08-08 23:29:01'),
(7, 1, NULL, NULL, 'cthp98-MS-003', 'kuyesa', 'nnnn', 'milestone', 'medium', 'not_started', 0.00, NULL, NULL, '2025-08-30', '2025-08-30', NULL, NULL, NULL, 0.00, 0.00, 0.00, NULL, 0, 0, 1, 1, '2025-08-08 23:46:15', '2025-08-08 23:46:15'),
(8, 1, NULL, NULL, 'cthp98-MS-004', 'something', 'hbjjhhjhjhj', 'milestone', 'medium', '', 0.00, 1, NULL, '2025-08-01', '2025-08-23', NULL, NULL, NULL, 0.00, 0.00, 0.00, NULL, 0, 0, 1, 1, '2025-08-09 00:21:00', '2025-08-09 00:21:00'),
(9, 1, NULL, NULL, 'cthp98-MS-005', 'misheck web', 'jjjj', 'milestone', 'medium', '', 0.00, NULL, NULL, '2025-08-02', '2025-08-30', NULL, NULL, NULL, 0.00, 0.00, 0.00, NULL, 0, 0, 1, 1, '2025-08-09 00:21:59', '2025-08-09 00:21:59');

-- --------------------------------------------------------

--
-- Table structure for table `task_attachments`
--

DROP TABLE IF EXISTS `task_attachments`;
CREATE TABLE IF NOT EXISTS `task_attachments` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `task_id` bigint UNSIGNED NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` bigint UNSIGNED DEFAULT NULL,
  `file_type` varchar(100) DEFAULT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  `uploaded_by` bigint UNSIGNED DEFAULT NULL,
  `uploaded_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `uploaded_by` (`uploaded_by`),
  KEY `idx_attachment_task` (`task_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `task_categories`
--

DROP TABLE IF EXISTS `task_categories`;
CREATE TABLE IF NOT EXISTS `task_categories` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text,
  `color_code` varchar(7) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_task_category_company` (`company_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `task_comments`
--

DROP TABLE IF EXISTS `task_comments`;
CREATE TABLE IF NOT EXISTS `task_comments` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `task_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `comment` text NOT NULL,
  `is_internal` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_comment_task` (`task_id`),
  KEY `idx_comment_user` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `timesheets`
--

DROP TABLE IF EXISTS `timesheets`;
CREATE TABLE IF NOT EXISTS `timesheets` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `period_id` bigint UNSIGNED NOT NULL,
  `total_hours` decimal(6,2) DEFAULT '0.00',
  `regular_hours` decimal(6,2) DEFAULT '0.00',
  `overtime_hours` decimal(6,2) DEFAULT '0.00',
  `billable_hours` decimal(6,2) DEFAULT '0.00',
  `non_billable_hours` decimal(6,2) DEFAULT '0.00',
  `status` enum('draft','submitted','approved','rejected','processed') DEFAULT 'draft',
  `submitted_at` timestamp NULL DEFAULT NULL,
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approval_comments` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_period` (`user_id`,`period_id`),
  KEY `approved_by` (`approved_by`),
  KEY `idx_timesheet_user` (`user_id`),
  KEY `idx_timesheet_period` (`period_id`),
  KEY `idx_timesheet_status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `timesheet_entries`
--

DROP TABLE IF EXISTS `timesheet_entries`;
CREATE TABLE IF NOT EXISTS `timesheet_entries` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `timesheet_id` bigint UNSIGNED NOT NULL,
  `project_id` bigint UNSIGNED NOT NULL,
  `task_id` bigint UNSIGNED DEFAULT NULL,
  `work_date` date NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `break_time` decimal(4,2) DEFAULT '0.00',
  `total_hours` decimal(6,2) NOT NULL DEFAULT '0.00',
  `overtime_hours` decimal(6,2) DEFAULT '0.00',
  `work_type` enum('regular','overtime','holiday','weekend') DEFAULT 'regular',
  `is_billable` tinyint(1) DEFAULT '1',
  `hourly_rate` decimal(10,2) DEFAULT NULL,
  `description` text,
  `location` varchar(255) DEFAULT NULL,
  `approved_hours` decimal(6,2) DEFAULT NULL,
  `approval_comments` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_timesheet_entry_timesheet` (`timesheet_id`),
  KEY `idx_timesheet_entry_project` (`project_id`),
  KEY `idx_timesheet_entry_task` (`task_id`),
  KEY `idx_timesheet_entry_date` (`work_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `timesheet_periods`
--

DROP TABLE IF EXISTS `timesheet_periods`;
CREATE TABLE IF NOT EXISTS `timesheet_periods` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('open','locked','processed') DEFAULT 'open',
  `submission_deadline` date DEFAULT NULL,
  `approval_deadline` date DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  KEY `idx_timesheet_period_company` (`company_id`),
  KEY `idx_timesheet_period_dates` (`start_date`,`end_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_categories`
--

DROP TABLE IF EXISTS `transaction_categories`;
CREATE TABLE IF NOT EXISTS `transaction_categories` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(20) DEFAULT NULL,
  `transaction_type` enum('income','expense','transfer') NOT NULL,
  `description` text,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_transaction_category_company` (`company_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED NOT NULL,
  `employee_id` varchar(50) DEFAULT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(200) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(200) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `national_id` varchar(50) DEFAULT NULL,
  `passport_number` varchar(50) DEFAULT NULL,
  `address` text,
  `city` varchar(100) DEFAULT NULL,
  `emergency_contact_name` varchar(200) DEFAULT NULL,
  `emergency_contact_phone` varchar(20) DEFAULT NULL,
  `profile_photo_url` varchar(200) DEFAULT NULL,
  `status` enum('active','inactive','suspended','terminated') DEFAULT 'active',
  `is_verified` tinyint(1) DEFAULT '0',
  `last_login_at` timestamp NULL DEFAULT NULL,
  `password_changed_at` timestamp NULL DEFAULT NULL,
  `two_factor_enabled` tinyint(1) DEFAULT '0',
  `two_factor_secret` varchar(250) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_user_company` (`company_id`),
  KEY `idx_user_status` (`status`),
  KEY `idx_user_email` (`email`),
  KEY `idx_employee_id` (`employee_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `company_id`, `employee_id`, `username`, `email`, `email_verified_at`, `password`, `first_name`, `last_name`, `middle_name`, `phone`, `mobile`, `date_of_birth`, `gender`, `national_id`, `passport_number`, `address`, `city`, `emergency_contact_name`, `emergency_contact_phone`, `profile_photo_url`, `status`, `is_verified`, `last_login_at`, `password_changed_at`, `two_factor_enabled`, `two_factor_secret`, `created_at`, `updated_at`) VALUES
(1, 1, 'emp1', 'kamulonim', 'kamulonim@gmail.com', '2025-06-24 20:50:36', '$2y$10$Xk3NQzCZPRWF/DViTy9s2uNQM/BQS9UR1TXeYIFD1whthYIqwSG5S', 'Misheck', 'Kamuloni', 'Davis', '0994099461', '0994099461', '1992-06-03', 'male', 'TSY9netk', 'MA900', 'Private Bag B44 Lilongwe', 'Lilongwe', NULL, NULL, NULL, 'active', 1, '2025-08-10 11:00:59', NULL, 0, NULL, '2025-06-24 20:52:45', '2025-08-10 11:00:59'),
(2, 1, 'EMP-2025-0001', 'isabelak', 'isabelak@gmail.com', NULL, '$2y$10$CWzHdY6yK8Vl2XJE0s2jNehhumULO5mooPwEDwVEo.LcZSmxubD.2', 'Isabela', 'Kambuzi', '', '0994099461', '', '1992-06-18', 'male', 'TSY9NETK', NULL, '', '', 'Mwaii Nthyola', '099409444', NULL, 'active', 0, NULL, NULL, 0, NULL, '2025-06-26 19:33:46', '2025-06-26 19:33:46'),
(3, 1, 'EMP-2025-0002', 'msdvfv', 'vev@egdfbg.com', NULL, '$2y$10$16dRGrvA1L7LrIEdQVo0VOBnFQDvz9RJlvN8P2WeZzllE1XQLVxC6', 'mvdfbd', 'svfbve', '', '0994099461', '', '1990-06-27', 'male', 'hhshvf', NULL, '', '', 'svbdfb', '0994099471', NULL, 'active', 0, NULL, NULL, 0, NULL, '2025-06-26 20:13:32', '2025-06-26 20:13:32'),
(4, 1, 'EMP-2025-0003', 'kldffdbdfb', 'fgedfb2@GFDDF.COM', NULL, '$2y$10$h5Iwy1ecPG03Y7Z5703GJeUDWHXCtdOcY.4DS7lSzgZ5tFwIo.vVW', 'kwfjwh', 'nvnfdmv', '', '0994099461', '', '2025-06-26', 'male', 'FGSFBSFBFDBFD', NULL, '', '', 'BDFB1', '09494848484', NULL, 'active', 0, NULL, NULL, 0, NULL, '2025-06-26 20:19:15', '2025-06-26 20:19:15'),
(5, 1, 'EMP-2025-0004', 'mdmdmdmdmdmdm', 'msmsmsm@mfmf.com', NULL, '$2y$10$5l9t/6GAJHON.QrXp9KjK.ey.scDM4UBKybBjKXYFA1Dz0EPu9/BK', 'mfmfm', 'mfmfmf', '', '0994099461', '', '2025-06-26', 'male', 'sjsjvjsvjs', NULL, 'nsnvsdnv', '', 'sdvsj', 'sdvs', NULL, 'active', 0, NULL, NULL, 0, NULL, '2025-06-26 20:23:37', '2025-06-26 20:23:37'),
(6, 1, 'EMP-2025-0005', 'sgsfg333', 'mdmdmMM@GGG.com', NULL, '$2y$10$vLSkGtCUoKA1uTiqlq413ONpkqskMX/RteSYzOWxXxM6awDwq2jL.', 'svssf', 'svsdsvd', 'svsdv', '09940948484', NULL, '2025-06-27', 'female', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'active', 1, NULL, NULL, 0, NULL, '2025-06-26 20:33:52', '2025-06-26 20:33:52'),
(7, 1, 'EMP-2025-0006', 'sgsfg333444', 'fvdbdfb2ME@egrg.com', NULL, '$2y$10$DkjOKgwCf/A3dVgakGNS5eVE28MlawS3a83yBqFtLPvofQE06hWjS', 'dffbdfb', 'dfbdf', NULL, '0994099461', NULL, '2025-06-24', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'active', 1, NULL, NULL, 0, NULL, '2025-06-26 20:35:52', '2025-06-26 20:35:52');

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

DROP TABLE IF EXISTS `user_roles`;
CREATE TABLE IF NOT EXISTS `user_roles` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL,
  `assigned_by` bigint UNSIGNED DEFAULT NULL,
  `assigned_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_role` (`user_id`,`role_id`),
  KEY `assigned_by` (`assigned_by`),
  KEY `idx_user_roles_user` (`user_id`),
  KEY `idx_user_roles_role` (`role_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`id`, `user_id`, `role_id`, `assigned_by`, `assigned_at`, `expires_at`) VALUES
(1, 1, 1, 1, '2025-06-26 20:15:39', NULL),
(2, 2, 3, 1, '2025-06-26 19:33:46', NULL),
(3, 3, 3, 1, '2025-06-26 20:13:32', NULL),
(4, 4, 4, 1, '2025-06-26 20:19:15', NULL),
(5, 5, 3, 1, '2025-06-26 20:23:37', NULL),
(6, 6, 3, 1, '2025-06-26 20:33:52', NULL),
(7, 7, 4, 1, '2025-06-26 20:35:52', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_sessions`
--

DROP TABLE IF EXISTS `user_sessions`;
CREATE TABLE IF NOT EXISTS `user_sessions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `session_token` varchar(250) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `device_type` varchar(50) DEFAULT NULL,
  `is_mobile` tinyint(1) DEFAULT '0',
  `location_info` json DEFAULT NULL,
  `expires_at` timestamp NOT NULL,
  `last_activity` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `session_token` (`session_token`),
  KEY `idx_session_user` (`user_id`),
  KEY `idx_session_token` (`session_token`),
  KEY `idx_session_expires` (`expires_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_employee_summary`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `v_employee_summary`;
CREATE TABLE IF NOT EXISTS `v_employee_summary` (
`annual_leave_balance` decimal(5,2)
,`attendance_last_30_days` bigint
,`basic_salary` decimal(12,2)
,`company_id` bigint unsigned
,`department_name` varchar(255)
,`email` varchar(200)
,`employee_id` varchar(50)
,`employment_status` enum('active','resigned','terminated','retired','on_leave')
,`employment_type` enum('full_time','part_time','contract','temporary','intern')
,`full_name` varchar(201)
,`hire_date` date
,`id` bigint unsigned
,`phone` varchar(20)
,`position_title` varchar(255)
,`sick_leave_balance` decimal(5,2)
,`supervisor_name` varchar(201)
,`user_status` enum('active','inactive','suspended','terminated')
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_inventory_summary`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `v_inventory_summary`;
CREATE TABLE IF NOT EXISTS `v_inventory_summary` (
`brand` varchar(100)
,`category_name` varchar(255)
,`company_id` bigint unsigned
,`current_stock` decimal(12,2)
,`id` bigint unsigned
,`item_code` varchar(100)
,`maximum_stock` decimal(12,2)
,`minimum_stock` decimal(12,2)
,`movements_last_30_days` bigint
,`name` varchar(255)
,`status` enum('active','inactive','discontinued')
,`stock_status` varchar(9)
,`total_value` decimal(24,4)
,`unit` varchar(50)
,`unit_cost` decimal(12,2)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_project_summary`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `v_project_summary`;
CREATE TABLE IF NOT EXISTS `v_project_summary` (
`actual_cost` decimal(15,2)
,`actual_end_date` date
,`client_name` varchar(255)
,`company_id` bigint unsigned
,`completed_tasks` bigint
,`estimated_budget` decimal(15,2)
,`id` bigint unsigned
,`name` varchar(255)
,`planned_end_date` date
,`priority` enum('low','medium','high','critical')
,`progress_percentage` decimal(5,2)
,`project_code` varchar(50)
,`project_manager_name` varchar(201)
,`site_supervisor_name` varchar(201)
,`start_date` date
,`status` enum('planning','active','on_hold','completed','cancelled')
,`team_size` bigint
,`timeline_status` varchar(8)
,`total_tasks` bigint
);

-- --------------------------------------------------------

--
-- Table structure for table `warehouses`
--

DROP TABLE IF EXISTS `warehouses`;
CREATE TABLE IF NOT EXISTS `warehouses` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` text,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `manager_id` bigint UNSIGNED DEFAULT NULL,
  `warehouse_type` enum('main','site','temporary') DEFAULT 'main',
  `capacity` decimal(12,2) DEFAULT NULL,
  `status` enum('active','inactive','maintenance') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_company_warehouse_code` (`company_id`,`code`),
  KEY `manager_id` (`manager_id`),
  KEY `idx_warehouse_company` (`company_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `warehouses`
--

INSERT INTO `warehouses` (`id`, `company_id`, `code`, `name`, `address`, `city`, `state`, `manager_id`, `warehouse_type`, `capacity`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'WHK952', 'kaya', 'bbhfs', 'lilongwe', 'Lilongwe', 1, 'main', 20000.00, 'active', '2025-06-28 07:13:49', '2025-06-28 07:13:49'),
(2, 1, 'WHC131', 'CHawanga warehouse', 'This is another main sit', 'Lilongwe', 'Mnchinii', 2, 'main', 0.00, 'active', '2025-06-28 07:21:25', '2025-06-28 07:21:25');

-- --------------------------------------------------------

--
-- Table structure for table `warehouse_stock`
--

DROP TABLE IF EXISTS `warehouse_stock`;
CREATE TABLE IF NOT EXISTS `warehouse_stock` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED NOT NULL,
  `warehouse_id` bigint UNSIGNED NOT NULL,
  `material_id` bigint UNSIGNED NOT NULL,
  `current_quantity` decimal(12,2) DEFAULT '0.00',
  `minimum_quantity` decimal(12,2) DEFAULT '0.00',
  `shelf_location` varchar(100) DEFAULT NULL,
  `last_stock_update` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_material_warehouse` (`material_id`,`warehouse_id`),
  KEY `idx_warehouse_stock_company` (`company_id`),
  KEY `idx_warehouse_stock_warehouse` (`warehouse_id`),
  KEY `idx_warehouse_stock_material` (`material_id`),
  KEY `idx_warehouse_stock_quantity` (`current_quantity`,`minimum_quantity`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `warehouse_stock`
--

INSERT INTO `warehouse_stock` (`id`, `company_id`, `warehouse_id`, `material_id`, `current_quantity`, `minimum_quantity`, `shelf_location`, `last_stock_update`) VALUES
(1, 1, 1, 2, 1000.00, 0.00, NULL, '2025-06-28 07:52:38'),
(2, 1, 2, 2, 400.00, 0.00, NULL, '2025-06-28 08:08:59'),
(3, 1, 2, 1, 100.00, 0.00, NULL, '2025-06-28 08:09:31'),
(4, 1, 1, 1, 1000.00, 0.00, NULL, '2025-06-28 08:09:55'),
(5, 1, 2, 3, 100.00, 0.00, NULL, '2025-06-28 08:47:10'),
(6, 1, 1, 3, 500.00, 0.00, NULL, '2025-06-28 08:47:41'),
(7, 1, 2, 4, 70.00, 0.00, NULL, '2025-08-09 00:25:28');

-- --------------------------------------------------------

--
-- Structure for view `v_employee_summary`
--
DROP TABLE IF EXISTS `v_employee_summary`;

DROP VIEW IF EXISTS `v_employee_summary`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_employee_summary`  AS SELECT `u`.`id` AS `id`, `u`.`company_id` AS `company_id`, `u`.`employee_id` AS `employee_id`, concat(`u`.`first_name`,' ',`u`.`last_name`) AS `full_name`, `u`.`email` AS `email`, `u`.`phone` AS `phone`, `u`.`status` AS `user_status`, `ed`.`employment_status` AS `employment_status`, `ed`.`employment_type` AS `employment_type`, `ed`.`hire_date` AS `hire_date`, `ed`.`basic_salary` AS `basic_salary`, `d`.`name` AS `department_name`, `jp`.`title` AS `position_title`, concat(`supervisor`.`first_name`,' ',`supervisor`.`last_name`) AS `supervisor_name`, `ed`.`annual_leave_balance` AS `annual_leave_balance`, `ed`.`sick_leave_balance` AS `sick_leave_balance`, (select count(0) from `attendance_records` `ar` where ((`ar`.`user_id` = `u`.`id`) and (`ar`.`attendance_date` >= (curdate() - interval 30 day)) and (`ar`.`status` = 'present'))) AS `attendance_last_30_days` FROM ((((`users` `u` join `employee_details` `ed` on((`u`.`id` = `ed`.`user_id`))) left join `departments` `d` on((`ed`.`department_id` = `d`.`id`))) left join `job_positions` `jp` on((`ed`.`position_id` = `jp`.`id`))) left join `users` `supervisor` on((`ed`.`supervisor_id` = `supervisor`.`id`))) WHERE (`u`.`status` = 'active') ;

-- --------------------------------------------------------

--
-- Structure for view `v_inventory_summary`
--
DROP TABLE IF EXISTS `v_inventory_summary`;

DROP VIEW IF EXISTS `v_inventory_summary`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_inventory_summary`  AS SELECT `m`.`id` AS `id`, `m`.`company_id` AS `company_id`, `m`.`item_code` AS `item_code`, `m`.`name` AS `name`, `m`.`brand` AS `brand`, `m`.`unit` AS `unit`, `m`.`unit_cost` AS `unit_cost`, `m`.`current_stock` AS `current_stock`, `m`.`minimum_stock` AS `minimum_stock`, `m`.`maximum_stock` AS `maximum_stock`, `mc`.`name` AS `category_name`, `m`.`status` AS `status`, (case when (`m`.`current_stock` <= `m`.`minimum_stock`) then 'low_stock' when (`m`.`current_stock` <= (`m`.`minimum_stock` * 1.2)) then 'warning' else 'normal' end) AS `stock_status`, (`m`.`current_stock` * `m`.`unit_cost`) AS `total_value`, (select count(0) from `stock_movements` `sm` where ((`sm`.`material_id` = `m`.`id`) and (`sm`.`created_at` >= (curdate() - interval 30 day)))) AS `movements_last_30_days` FROM (`materials` `m` left join `material_categories` `mc` on((`m`.`category_id` = `mc`.`id`))) WHERE (`m`.`status` = 'active') ;

-- --------------------------------------------------------

--
-- Structure for view `v_project_summary`
--
DROP TABLE IF EXISTS `v_project_summary`;

DROP VIEW IF EXISTS `v_project_summary`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_project_summary`  AS SELECT `p`.`id` AS `id`, `p`.`company_id` AS `company_id`, `p`.`project_code` AS `project_code`, `p`.`name` AS `name`, `p`.`status` AS `status`, `p`.`priority` AS `priority`, `p`.`progress_percentage` AS `progress_percentage`, `p`.`estimated_budget` AS `estimated_budget`, `p`.`actual_cost` AS `actual_cost`, `p`.`start_date` AS `start_date`, `p`.`planned_end_date` AS `planned_end_date`, `p`.`actual_end_date` AS `actual_end_date`, `c`.`name` AS `client_name`, concat(`pm`.`first_name`,' ',`pm`.`last_name`) AS `project_manager_name`, concat(`ss`.`first_name`,' ',`ss`.`last_name`) AS `site_supervisor_name`, (select count(0) from `tasks` `t` where ((`t`.`project_id` = `p`.`id`) and (`t`.`status` <> 'cancelled'))) AS `total_tasks`, (select count(0) from `tasks` `t` where ((`t`.`project_id` = `p`.`id`) and (`t`.`status` = 'completed'))) AS `completed_tasks`, (select count(0) from `project_team_members` `ptm` where ((`ptm`.`project_id` = `p`.`id`) and (`ptm`.`removed_at` is null))) AS `team_size`, (case when ((`p`.`planned_end_date` < curdate()) and (`p`.`status` not in ('completed','cancelled'))) then 'overdue' when ((`p`.`planned_end_date` <= (curdate() + interval 7 day)) and (`p`.`status` not in ('completed','cancelled'))) then 'due_soon' else 'on_track' end) AS `timeline_status` FROM (((`projects` `p` left join `clients` `c` on((`p`.`client_id` = `c`.`id`))) left join `users` `pm` on((`p`.`project_manager_id` = `pm`.`id`))) left join `users` `ss` on((`p`.`site_supervisor_id` = `ss`.`id`))) WHERE (`p`.`is_archived` = false) ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
