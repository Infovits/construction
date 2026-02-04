-- File Management Tables

-- ============================================================
-- File Categories Table
-- ============================================================
DROP TABLE IF EXISTS `file_categories`;
CREATE TABLE IF NOT EXISTS `file_categories` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text,
  `color_code` varchar(7) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_company_id` (`company_id`),
  KEY `idx_active` (`is_active`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ============================================================
-- Files Table (Main Files)
-- ============================================================
DROP TABLE IF EXISTS `files`;
CREATE TABLE IF NOT EXISTS `files` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED NOT NULL,
  `project_id` bigint UNSIGNED NOT NULL,
  `category_id` bigint UNSIGNED DEFAULT NULL,
  `file_name` varchar(255) NOT NULL,
  `original_file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_type` varchar(50) NOT NULL,
  `file_size` bigint DEFAULT '0',
  `mime_type` varchar(100) DEFAULT NULL,
  `description` text,
  `uploaded_by` bigint UNSIGNED NOT NULL,
  `version_number` int DEFAULT '1',
  `is_latest_version` tinyint(1) DEFAULT '1',
  `is_archived` tinyint(1) DEFAULT '0',
  `is_public` tinyint(1) DEFAULT '0',
  `document_date` date DEFAULT NULL,
  `expires_at` date DEFAULT NULL,
  `storage_location` enum('local','cloud','external') DEFAULT 'local',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_company_id` (`company_id`),
  KEY `idx_project_id` (`project_id`),
  KEY `idx_category_id` (`category_id`),
  KEY `idx_uploaded_by` (`uploaded_by`),
  KEY `idx_file_type` (`file_type`),
  KEY `idx_created_date` (`created_at`),
  KEY `idx_version` (`version_number`, `is_latest_version`),
  KEY `idx_archived` (`is_archived`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ============================================================
-- File Versions Table (Track Version History)
-- ============================================================
DROP TABLE IF EXISTS `file_versions`;
CREATE TABLE IF NOT EXISTS `file_versions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `file_id` bigint UNSIGNED NOT NULL,
  `version_number` int NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` bigint DEFAULT '0',
  `uploaded_by` bigint UNSIGNED NOT NULL,
  `change_description` text,
  `change_log` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_file_id` (`file_id`),
  KEY `idx_version` (`version_number`),
  KEY `idx_uploaded_by` (`uploaded_by`),
  FOREIGN KEY (`file_id`) REFERENCES `files` (`id`) ON DELETE CASCADE
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ============================================================
-- File Access Controls Table
-- ============================================================
DROP TABLE IF EXISTS `file_access_controls`;
CREATE TABLE IF NOT EXISTS `file_access_controls` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `file_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `role_id` bigint UNSIGNED DEFAULT NULL,
  `access_type` enum('view','edit','delete','manage') NOT NULL DEFAULT 'view',
  `granted_by` bigint UNSIGNED DEFAULT NULL,
  `granted_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` timestamp NULL DEFAULT NULL,
  `is_revoked` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_file_id` (`file_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_role_id` (`role_id`),
  KEY `idx_access_type` (`access_type`),
  FOREIGN KEY (`file_id`) REFERENCES `files` (`id`) ON DELETE CASCADE
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ============================================================
-- File Tags Table
-- ============================================================
DROP TABLE IF EXISTS `file_tags`;
CREATE TABLE IF NOT EXISTS `file_tags` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `file_id` bigint UNSIGNED NOT NULL,
  `tag_name` varchar(100) NOT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_file_id` (`file_id`),
  KEY `idx_tag_name` (`tag_name`),
  FOREIGN KEY (`file_id`) REFERENCES `files` (`id`) ON DELETE CASCADE
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ============================================================
-- File Comments Table
-- ============================================================
DROP TABLE IF EXISTS `file_comments`;
CREATE TABLE IF NOT EXISTS `file_comments` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `file_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `comment_text` text NOT NULL,
  `mentions` json DEFAULT NULL,
  `is_resolved` tinyint(1) DEFAULT '0',
  `resolved_by` bigint UNSIGNED DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_file_id` (`file_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_resolved` (`is_resolved`),
  FOREIGN KEY (`file_id`) REFERENCES `files` (`id`) ON DELETE CASCADE
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ============================================================
-- File Change Tracking Table
-- ============================================================
DROP TABLE IF EXISTS `file_change_logs`;
CREATE TABLE IF NOT EXISTS `file_change_logs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `file_id` bigint UNSIGNED NOT NULL,
  `action_type` enum('uploaded','updated','deleted','commented','shared','tagged','renamed') NOT NULL,
  `action_by` bigint UNSIGNED NOT NULL,
  `action_description` text,
  `old_values` json DEFAULT NULL,
  `new_values` json DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_file_id` (`file_id`),
  KEY `idx_action_type` (`action_type`),
  KEY `idx_action_by` (`action_by`),
  KEY `idx_created_date` (`created_at`),
  FOREIGN KEY (`file_id`) REFERENCES `files` (`id`) ON DELETE CASCADE
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
