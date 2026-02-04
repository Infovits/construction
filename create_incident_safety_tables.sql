-- Incident & Safety Reporting Tables

-- ============================================================
-- Incident Severity Levels
-- ============================================================
DROP TABLE IF EXISTS `incident_severity_levels`;
CREATE TABLE IF NOT EXISTS `incident_severity_levels` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text,
  `color_code` varchar(7) DEFAULT NULL,
  `numeric_level` int DEFAULT '1',
  `requires_immediate_action` tinyint(1) DEFAULT '0',
  `requires_reporting` tinyint(1) DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_company_id` (`company_id`),
  KEY `idx_numeric_level` (`numeric_level`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Default severity levels
INSERT INTO `incident_severity_levels` (`company_id`, `name`, `description`, `color_code`, `numeric_level`, `requires_immediate_action`, `requires_reporting`, `is_active`) VALUES
(1, 'Critical', 'Life-threatening injuries or major safety hazards', '#FF0000', 4, 1, 1, 1),
(1, 'High', 'Serious injuries or significant safety concerns', '#FF9900', 3, 1, 1, 1),
(1, 'Medium', 'Minor injuries or safety hazards requiring attention', '#FFFF00', 2, 0, 1, 1),
(1, 'Low', 'Near-miss incidents or minor safety concerns', '#00CC00', 1, 0, 0, 1);

-- ============================================================
-- Incident Types Table
-- ============================================================
DROP TABLE IF EXISTS `incident_types`;
CREATE TABLE IF NOT EXISTS `incident_types` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text,
  `icon` varchar(50) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_company_id` (`company_id`),
  KEY `idx_active` (`is_active`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Default incident types
INSERT INTO `incident_types` (`company_id`, `name`, `description`, `icon`, `is_active`) VALUES
(1, 'Injury', 'Personal injury or occupational health incident', 'fa-user-injured', 1),
(1, 'Equipment Damage', 'Damage to equipment, machinery, or property', 'fa-tools', 1),
(1, 'Near Miss', 'Incident that could have resulted in injury but did not', 'fa-exclamation-circle', 1),
(1, 'Environmental Hazard', 'Environmental or spill incidents', 'fa-droplet', 1),
(1, 'Safety Violation', 'Non-compliance with safety procedures', 'fa-ban', 1),
(1, 'Vehicle Incident', 'Vehicle accident or traffic-related incident', 'fa-car-crash', 1);

-- ============================================================
-- Incidents Table
-- ============================================================
DROP TABLE IF EXISTS `incidents`;
CREATE TABLE IF NOT EXISTS `incidents` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED NOT NULL,
  `project_id` bigint UNSIGNED NOT NULL,
  `incident_code` varchar(50) NOT NULL,
  `incident_type_id` bigint UNSIGNED NOT NULL,
  `severity_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `incident_date` datetime NOT NULL,
  `reported_by` bigint UNSIGNED NOT NULL,
  `reported_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `location` varchar(255) DEFAULT NULL,
  `affected_people_count` int DEFAULT '0',
  `affected_people_names` text DEFAULT NULL,
  `witness_count` int DEFAULT '0',
  `witness_names` text DEFAULT NULL,
  `injuries_sustained` text DEFAULT NULL,
  `property_damage_description` text DEFAULT NULL,
  `immediate_actions_taken` text DEFAULT NULL,
  `status` enum('reported','investigating','under_review','resolved','closed','reopened') DEFAULT 'reported',
  `assigned_to` bigint UNSIGNED DEFAULT NULL,
  `investigation_findings` text DEFAULT NULL,
  `investigation_completed_date` date DEFAULT NULL,
  `investigation_completed_by` bigint UNSIGNED DEFAULT NULL,
  `is_safety_audit_required` tinyint(1) DEFAULT '0',
  `is_documented` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `incident_code` (`incident_code`),
  KEY `idx_company_id` (`company_id`),
  KEY `idx_project_id` (`project_id`),
  KEY `idx_incident_type` (`incident_type_id`),
  KEY `idx_severity` (`severity_id`),
  KEY `idx_reported_by` (`reported_by`),
  KEY `idx_assigned_to` (`assigned_to`),
  KEY `idx_status` (`status`),
  KEY `idx_incident_date` (`incident_date`),
  KEY `idx_severity_date` (`severity_id`, `incident_date`),
  FOREIGN KEY (`incident_type_id`) REFERENCES `incident_types` (`id`),
  FOREIGN KEY (`severity_id`) REFERENCES `incident_severity_levels` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ============================================================
-- Incident Photos Table
-- ============================================================
DROP TABLE IF EXISTS `incident_photos`;
CREATE TABLE IF NOT EXISTS `incident_photos` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `incident_id` bigint UNSIGNED NOT NULL,
  `photo_path` varchar(500) NOT NULL,
  `original_file_name` varchar(255) NOT NULL,
  `photo_type` enum('before','after','evidence','overview') DEFAULT 'evidence',
  `description` text DEFAULT NULL,
  `uploaded_by` bigint UNSIGNED NOT NULL,
  `uploaded_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_incident_id` (`incident_id`),
  KEY `idx_uploaded_by` (`uploaded_by`),
  FOREIGN KEY (`incident_id`) REFERENCES `incidents` (`id`) ON DELETE CASCADE
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ============================================================
-- Incident Action Steps Table
-- ============================================================
DROP TABLE IF EXISTS `incident_action_steps`;
CREATE TABLE IF NOT EXISTS `incident_action_steps` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `incident_id` bigint UNSIGNED NOT NULL,
  `action_number` int NOT NULL,
  `action_description` text NOT NULL,
  `responsible_person_id` bigint UNSIGNED DEFAULT NULL,
  `assigned_to` bigint UNSIGNED DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `completed_date` date DEFAULT NULL,
  `completion_status` enum('pending','in_progress','completed','overdue','cancelled') DEFAULT 'pending',
  `completion_notes` text DEFAULT NULL,
  `is_critical` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_incident_id` (`incident_id`),
  KEY `idx_assigned_to` (`assigned_to`),
  KEY `idx_status` (`completion_status`),
  KEY `idx_due_date` (`due_date`),
  FOREIGN KEY (`incident_id`) REFERENCES `incidents` (`id`) ON DELETE CASCADE
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ============================================================
-- Safety Audits Table
-- ============================================================
DROP TABLE IF EXISTS `safety_audits`;
CREATE TABLE IF NOT EXISTS `safety_audits` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED NOT NULL,
  `project_id` bigint UNSIGNED NOT NULL,
  `incident_id` bigint UNSIGNED DEFAULT NULL,
  `audit_code` varchar(50) NOT NULL,
  `audit_date` date NOT NULL,
  `audit_type` enum('routine','incident_related','compliance','follow_up') DEFAULT 'routine',
  `auditor_id` bigint UNSIGNED NOT NULL,
  `audit_scope` text DEFAULT NULL,
  `findings_summary` text DEFAULT NULL,
  `total_observations` int DEFAULT '0',
  `critical_findings` int DEFAULT '0',
  `major_findings` int DEFAULT '0',
  `minor_findings` int DEFAULT '0',
  `conformance_percentage` decimal(5,2) DEFAULT NULL,
  `status` enum('draft','completed','reported','addressed') DEFAULT 'draft',
  `document_path` varchar(500) DEFAULT NULL,
  `due_date_for_corrections` date DEFAULT NULL,
  `follow_up_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `audit_code` (`audit_code`),
  KEY `idx_company_id` (`company_id`),
  KEY `idx_project_id` (`project_id`),
  KEY `idx_auditor_id` (`auditor_id`),
  KEY `idx_audit_date` (`audit_date`),
  KEY `idx_status` (`status`),
  FOREIGN KEY (`incident_id`) REFERENCES `incidents` (`id`) ON DELETE SET NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ============================================================
-- Safety Audit Findings Table
-- ============================================================
DROP TABLE IF EXISTS `safety_audit_findings`;
CREATE TABLE IF NOT EXISTS `safety_audit_findings` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `safety_audit_id` bigint UNSIGNED NOT NULL,
  `finding_number` int NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `finding_description` text NOT NULL,
  `severity` enum('critical','major','minor') DEFAULT 'minor',
  `evidence_description` text DEFAULT NULL,
  `standard_reference` varchar(255) DEFAULT NULL,
  `corrective_action` text DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `responsible_person_id` bigint UNSIGNED DEFAULT NULL,
  `status` enum('open','in_progress','closed','verified') DEFAULT 'open',
  `closed_date` date DEFAULT NULL,
  `closed_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_audit_id` (`safety_audit_id`),
  KEY `idx_severity` (`severity`),
  KEY `idx_status` (`status`),
  FOREIGN KEY (`safety_audit_id`) REFERENCES `safety_audits` (`id`) ON DELETE CASCADE
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ============================================================
-- Safety Trends Analytics Table
-- ============================================================
DROP TABLE IF EXISTS `safety_analytics`;
CREATE TABLE IF NOT EXISTS `safety_analytics` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED NOT NULL,
  `project_id` bigint UNSIGNED DEFAULT NULL,
  `analytics_date` date NOT NULL,
  `total_incidents` int DEFAULT '0',
  `critical_incidents` int DEFAULT '0',
  `high_incidents` int DEFAULT '0',
  `medium_incidents` int DEFAULT '0',
  `low_incidents` int DEFAULT '0',
  `total_injured_people` int DEFAULT '0',
  `total_near_misses` int DEFAULT '0',
  `safety_audits_conducted` int DEFAULT '0',
  `audit_compliance_percentage` decimal(5,2) DEFAULT NULL,
  `average_resolution_days` int DEFAULT NULL,
  `incidents_this_month` int DEFAULT '0',
  `incidents_previous_month` int DEFAULT '0',
  `trend_direction` enum('improving','stable','declining') DEFAULT 'stable',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_company_id` (`company_id`),
  KEY `idx_project_id` (`project_id`),
  KEY `idx_analytics_date` (`analytics_date`),
  UNIQUE KEY `unique_company_date` (`company_id`, `analytics_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ============================================================
-- Safety Reports Table
-- ============================================================
DROP TABLE IF EXISTS `safety_reports`;
CREATE TABLE IF NOT EXISTS `safety_reports` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED NOT NULL,
  `project_id` bigint UNSIGNED NOT NULL,
  `report_code` varchar(50) NOT NULL,
  `report_type` enum('daily','weekly','monthly','quarterly','annual') DEFAULT 'monthly',
  `report_period_start` date NOT NULL,
  `report_period_end` date NOT NULL,
  `generated_by` bigint UNSIGNED NOT NULL,
  `report_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `total_incidents_reported` int DEFAULT '0',
  `total_near_misses` int DEFAULT '0',
  `total_injured_workers` int DEFAULT '0',
  `lost_time_incidents` int DEFAULT '0',
  `safety_audits_conducted` int DEFAULT '0',
  `training_sessions_held` int DEFAULT '0',
  `key_highlights` text DEFAULT NULL,
  `challenges_identified` text DEFAULT NULL,
  `recommendations` text DEFAULT NULL,
  `report_file_path` varchar(500) DEFAULT NULL,
  `status` enum('draft','pending_review','approved','published') DEFAULT 'draft',
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `approved_date` timestamp NULL DEFAULT NULL,
  `distribution_list` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `report_code` (`report_code`),
  KEY `idx_company_id` (`company_id`),
  KEY `idx_project_id` (`project_id`),
  KEY `idx_report_type` (`report_type`),
  KEY `idx_report_period` (`report_period_start`, `report_period_end`),
  KEY `idx_status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
