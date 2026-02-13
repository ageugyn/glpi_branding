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
 * REST API endpoint for Branding plugin
 * 
 * Usage: GET /plugins/branding/api.php?entity_id=0
 * 
 * Returns JSON with branding configuration for the specified entity
 */

// Set as stateless (no session required)
if (!defined('GLPI_ROOT')) {
    define('GLPI_ROOT', realpath('../../..'));
}
define('DO_NOT_CHECK_HTTP_REFERER', 1);

include_once(GLPI_ROOT . '/inc/includes.php');

use GlpiPlugin\Branding\Config;

// Set JSON header
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle OPTIONS for CORS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Only allow GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        'error' => 'Method not allowed',
        'message' => 'Only GET requests are supported'
    ]);
    exit;
}

try {
    // Get entity ID from query parameter
    $entity_id = isset($_GET['entity_id']) ? (int)$_GET['entity_id'] : 0;
    
    // Get configuration
    $config = Config::getForEntity($entity_id);
    
    if (!$config || !$config['enabled']) {
        http_response_code(404);
        echo json_encode([
            'error' => 'Not found',
            'message' => 'No branding configuration found for entity ' . $entity_id
        ]);
        exit;
    }

    // Get active theme and colors
    $active_theme = Config::getActiveTheme($config);
    $colors = Config::getActiveColors($config);

    // Prepare response
    $response = [
        'success' => true,
        'entity_id' => $entity_id,
        'enabled' => (bool)$config['enabled'],
        'is_recursive' => (bool)$config['is_recursive'],
        'schedule_enabled' => (bool)$config['schedule_enabled'],
        'day_start' => $config['day_start'],
        'night_start' => $config['night_start'],
        'active_theme' => $active_theme,
        'colors' => $colors,
        'assets' => [
            'logo_login' => Config::getFileUrl($config['logo_login']),
            'logo_expanded' => Config::getFileUrl($config['logo_expanded']),
            'logo_collapsed' => Config::getFileUrl($config['logo_collapsed']),
            'favicon' => Config::getFileUrl($config['favicon']),
            'background_login' => Config::getFileUrl($config['background_login'])
        ],
        'css_url' => Plugin::getWebDir('branding', true) . '/css/branding.css.php?entities_id=' . $entity_id
    ];

    http_response_code(200);
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Internal server error',
        'message' => $e->getMessage()
    ]);
}
