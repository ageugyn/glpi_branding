-- -------------------------------------------------------------------------
-- Branding plugin for GLPI
-- Migration from v1.x to v2.0
-- -------------------------------------------------------------------------

SET @db := DATABASE();

-- Rename old logo column to logo_login (idempotent)
SET @has_logo := (
    SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'glpi_plugin_branding_configs' AND COLUMN_NAME = 'logo'
);
SET @has_logo_login := (
    SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'glpi_plugin_branding_configs' AND COLUMN_NAME = 'logo_login'
);
SET @sql := IF(@has_logo = 1 AND @has_logo_login = 0,
    'ALTER TABLE `glpi_plugin_branding_configs` CHANGE COLUMN `logo` `logo_login` varchar(255) DEFAULT NULL',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Add missing columns (idempotent)
SET @sql := IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'glpi_plugin_branding_configs' AND COLUMN_NAME = 'logo_expanded') = 0,
    'ALTER TABLE `glpi_plugin_branding_configs` ADD COLUMN `logo_expanded` varchar(255) DEFAULT NULL',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'glpi_plugin_branding_configs' AND COLUMN_NAME = 'logo_collapsed') = 0,
    'ALTER TABLE `glpi_plugin_branding_configs` ADD COLUMN `logo_collapsed` varchar(255) DEFAULT NULL',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'glpi_plugin_branding_configs' AND COLUMN_NAME = 'favicon') = 0,
    'ALTER TABLE `glpi_plugin_branding_configs` ADD COLUMN `favicon` varchar(255) DEFAULT NULL',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'glpi_plugin_branding_configs' AND COLUMN_NAME = 'background_login') = 0,
    'ALTER TABLE `glpi_plugin_branding_configs` ADD COLUMN `background_login` varchar(255) DEFAULT NULL',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'glpi_plugin_branding_configs' AND COLUMN_NAME = 'colors_day') = 0,
    'ALTER TABLE `glpi_plugin_branding_configs` ADD COLUMN `colors_day` json DEFAULT NULL',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'glpi_plugin_branding_configs' AND COLUMN_NAME = 'colors_night') = 0,
    'ALTER TABLE `glpi_plugin_branding_configs` ADD COLUMN `colors_night` json DEFAULT NULL',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'glpi_plugin_branding_configs' AND COLUMN_NAME = 'schedule_enabled') = 0,
    'ALTER TABLE `glpi_plugin_branding_configs` ADD COLUMN `schedule_enabled` tinyint NOT NULL DEFAULT 0',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'glpi_plugin_branding_configs' AND COLUMN_NAME = 'day_start') = 0,
    'ALTER TABLE `glpi_plugin_branding_configs` ADD COLUMN `day_start` time DEFAULT ''08:00:00''',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'glpi_plugin_branding_configs' AND COLUMN_NAME = 'night_start') = 0,
    'ALTER TABLE `glpi_plugin_branding_configs` ADD COLUMN `night_start` time DEFAULT ''20:00:00''',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'glpi_plugin_branding_configs' AND COLUMN_NAME = 'custom_css') = 0,
    'ALTER TABLE `glpi_plugin_branding_configs` ADD COLUMN `custom_css` text',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'glpi_plugin_branding_configs' AND COLUMN_NAME = 'is_recursive') = 0,
    'ALTER TABLE `glpi_plugin_branding_configs` ADD COLUMN `is_recursive` tinyint NOT NULL DEFAULT 0',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'glpi_plugin_branding_configs' AND COLUMN_NAME = 'enabled') = 0,
    'ALTER TABLE `glpi_plugin_branding_configs` ADD COLUMN `enabled` tinyint NOT NULL DEFAULT 0',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'glpi_plugin_branding_configs' AND COLUMN_NAME = 'date_creation') = 0,
    'ALTER TABLE `glpi_plugin_branding_configs` ADD COLUMN `date_creation` timestamp NULL DEFAULT NULL',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'glpi_plugin_branding_configs' AND COLUMN_NAME = 'date_mod') = 0,
    'ALTER TABLE `glpi_plugin_branding_configs` ADD COLUMN `date_mod` timestamp NULL DEFAULT NULL',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Update table structure to match GLPI 11 standards
ALTER TABLE `glpi_plugin_branding_configs`
    MODIFY COLUMN `id` int unsigned NOT NULL AUTO_INCREMENT,
    MODIFY COLUMN `entities_id` int unsigned NOT NULL DEFAULT '0',
    MODIFY COLUMN `is_recursive` tinyint NOT NULL DEFAULT '0',
    MODIFY COLUMN `enabled` tinyint NOT NULL DEFAULT '0';

-- Add indexes for performance (idempotent)
SET @sql := IF(
    (SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'glpi_plugin_branding_configs' AND INDEX_NAME = 'entities_id') = 0,
    'ALTER TABLE `glpi_plugin_branding_configs` ADD INDEX `entities_id` (`entities_id`)',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
    (SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'glpi_plugin_branding_configs' AND INDEX_NAME = 'is_recursive') = 0,
    'ALTER TABLE `glpi_plugin_branding_configs` ADD INDEX `is_recursive` (`is_recursive`)',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
    (SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'glpi_plugin_branding_configs' AND INDEX_NAME = 'date_mod') = 0,
    'ALTER TABLE `glpi_plugin_branding_configs` ADD INDEX `date_mod` (`date_mod`)',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Convert charset if needed
ALTER TABLE `glpi_plugin_branding_configs`
    CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Set row format for better performance
ALTER TABLE `glpi_plugin_branding_configs`
    ROW_FORMAT=DYNAMIC;
