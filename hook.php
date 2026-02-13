<?php
/**
 * -------------------------------------------------------------------------
 * Branding plugin for GLPI
 * -------------------------------------------------------------------------
 *
 * LICENSE
 *
 * This file is part of Branding.
 *
 * Branding is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Branding is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Branding. If not, see <http://www.gnu.org/licenses/>.
 * -------------------------------------------------------------------------
 * @copyright Copyright (C) 2025 by Branding plugin team.
 * @license   GPLv2 https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/pluginsGLPI/branding
 * -------------------------------------------------------------------------
 */

/**
 * Plugin install process
 *
 * @return boolean
 */
function plugin_branding_install()
{
    global $DB;

    $migration = new Migration(PLUGIN_BRANDING_VERSION);
    $table = 'glpi_plugin_branding_configs';

    if (!$DB->tableExists($table)) {
        $query = "CREATE TABLE IF NOT EXISTS `$table` (
            `id` int unsigned NOT NULL AUTO_INCREMENT,
            `entities_id` int unsigned NOT NULL DEFAULT '0',
            `is_recursive` tinyint NOT NULL DEFAULT '0',
            `enabled` tinyint NOT NULL DEFAULT '0',
            `logo_login` varchar(255) DEFAULT NULL,
            `logo_expanded` varchar(255) DEFAULT NULL,
            `logo_collapsed` varchar(255) DEFAULT NULL,
            `favicon` varchar(255) DEFAULT NULL,
            `background_login` varchar(255) DEFAULT NULL,
            `colors_day` json DEFAULT NULL,
            `colors_night` json DEFAULT NULL,
            `schedule_enabled` tinyint NOT NULL DEFAULT '0',
            `day_start` time DEFAULT '08:00:00',
            `night_start` time DEFAULT '20:00:00',
            `custom_css` text,
            `date_creation` timestamp NULL DEFAULT NULL,
            `date_mod` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `entities_id` (`entities_id`),
            KEY `is_recursive` (`is_recursive`),
            KEY `date_mod` (`date_mod`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC";

        // GLPI 11 blocks direct queries. Use Migration to run DDL safely.
        $migration->addPostQuery($query);
    }

    // Create upload directory
    $upload_dir = GLPI_PLUGIN_DOC_DIR . '/branding';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $migration->executeMigration();

    return true;
}

/**
 * Plugin uninstall process
 *
 * @return boolean
 */
function plugin_branding_uninstall()
{
    $migration = new Migration(PLUGIN_BRANDING_VERSION);

    $tables = [
        'glpi_plugin_branding_configs'
    ];

    foreach ($tables as $table) {
        $migration->addPostQuery("DROP TABLE IF EXISTS `$table`");
    }

    $migration->executeMigration();

    // Remove upload directory (optional - uncomment to delete files on uninstall)
    // $upload_dir = GLPI_PLUGIN_DOC_DIR . '/branding';
    // if (is_dir($upload_dir)) {
    //     Toolbox::deleteDir($upload_dir);
    // }

    return true;
}

/**
 * Display CSS on login page
 *
 * @return void
 */
function plugin_branding_display_login()
{
    global $CFG_GLPI;
    
    if (isset($_GET['disable_branding'])) {
        return;
    }

    // Get root entity config for login page
    $entity_id = 0;
    $url = $CFG_GLPI['root_doc'] . '/plugins/branding/public/css/branding.css.php?entities_id=' . $entity_id . '&login=1';
    
    echo '<link rel="stylesheet" type="text/css" href="' . $url . '">';

    $config = \GlpiPlugin\Branding\Config::getForEntity($entity_id);
    if ($config && !empty($config['favicon'])) {
        $favicon_url = \GlpiPlugin\Branding\Config::getFileUrl($config['favicon']);
        echo '<link rel="icon" type="image/x-icon" href="' . $favicon_url . '">';
    }
}

/**
 * Add favicon on authenticated pages
 *
 * @return void
 */
function plugin_branding_add_header()
{
    if (!Session::getLoginUserID() || isset($_GET['disable_branding'])) {
        return;
    }

    $entity_id = $_SESSION['glpiactive_entity'] ?? 0;
    $config = \GlpiPlugin\Branding\Config::getForEntity($entity_id);
    if ($config && !empty($config['favicon'])) {
        $favicon_url = \GlpiPlugin\Branding\Config::getFileUrl($config['favicon']);
        echo '<link rel="icon" type="image/x-icon" href="' . $favicon_url . '">';
    }
}
