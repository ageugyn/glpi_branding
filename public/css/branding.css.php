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

// Set content type
header('Content-Type: text/css; charset=utf-8');

// Define constants
if (!defined('GLPI_ROOT')) {
    define('GLPI_ROOT', realpath('../../../..'));
}
define('DO_NOT_CHECK_HTTP_REFERER', 1);

include_once(GLPI_ROOT . '/inc/includes.php');

use GlpiPlugin\Branding\Config;

// Get entity ID from parameter
$entities_id = isset($_GET['entities_id']) ? (int)$_GET['entities_id'] : 0;
$is_login = isset($_GET['login']) && $_GET['login'] == '1';

// Get configuration
$config = Config::getForEntity($entities_id);

if (!$config || !$config['enabled']) {
    // No branding configured
    exit;
}

// Get active colors based on schedule
$colors = Config::getActiveColors($config);

// Generate CSS
$css = "/* Branding Plugin - Generated CSS */\n\n";

// Logo expanded (sidebar expanded)
if (!empty($config['logo_expanded'])) {
    $logo_url = Config::getFileUrl($config['logo_expanded']);
    $css .= "
.navbar-brand-logo {
    background-image: url('{$logo_url}') !important;
    background-size: contain !important;
    background-repeat: no-repeat !important;
    background-position: center !important;
}
";
}

// Logo collapsed (sidebar collapsed)
if (!empty($config['logo_collapsed'])) {
    $logo_url = Config::getFileUrl($config['logo_collapsed']);
    $css .= "
.navbar-brand-logo-mini {
    background-image: url('{$logo_url}') !important;
    background-size: contain !important;
    background-repeat: no-repeat !important;
    background-position: center !important;
}
";
}

// Favicon
if (!empty($config['favicon'])) {
    $favicon_url = Config::getFileUrl($config['favicon']);
    $css .= "
/* Favicon is set via HTML, not CSS */
";
}

// Login page specific styles
if ($is_login) {
    if (!empty($config['logo_login'])) {
        $logo_url = Config::getFileUrl($config['logo_login']);
        $css .= "
.login-page .logo {
    background-image: url('{$logo_url}') !important;
    background-size: contain !important;
    background-repeat: no-repeat !important;
    background-position: center !important;
}
";
    }

    if (!empty($config['background_login'])) {
        $bg_url = Config::getFileUrl($config['background_login']);
        $css .= "
body.login-page {
    background-image: url('{$bg_url}') !important;
    background-size: cover !important;
    background-position: center !important;
    background-repeat: no-repeat !important;
    background-attachment: fixed !important;
}
";
    }
}

// Apply color scheme
if (!empty($colors)) {
    
    // Primary color
    if (!empty($colors['primary'])) {
        $primary = $colors['primary'];
        $css .= "
/* Primary Color */
.btn-primary,
.page-link.active,
.navbar,
.header-toolbar .btn:hover,
.nav-tabs .nav-link.active {
    background-color: {$primary} !important;
    border-color: {$primary} !important;
}

a {
    color: {$primary} !important;
}
";
    }

    // Secondary color
    if (!empty($colors['secondary'])) {
        $secondary = $colors['secondary'];
        $css .= "
/* Secondary Color */
.btn-secondary,
.sidebar,
.navbar-brand {
    background-color: {$secondary} !important;
}
";
    }

    // Background color
    if (!empty($colors['background'])) {
        $background = $colors['background'];
        $css .= "
/* Background Color */
body:not(.login-page),
.main-content {
    background-color: {$background} !important;
}
";
    }

    // Text color
    if (!empty($colors['text'])) {
        $text = $colors['text'];
        $css .= "
/* Text Color */
body,
.navbar,
.sidebar {
    color: {$text} !important;
}
";
    }

    // Sidebar background
    if (!empty($colors['sidebar_bg'])) {
        $sidebar_bg = $colors['sidebar_bg'];
        $css .= "
/* Sidebar Background */
.sidebar,
.navbar-nav {
    background-color: {$sidebar_bg} !important;
}
";
    }

    // Sidebar text
    if (!empty($colors['sidebar_text'])) {
        $sidebar_text = $colors['sidebar_text'];
        $css .= "
/* Sidebar Text */
.sidebar .nav-link,
.sidebar .menu-label {
    color: {$sidebar_text} !important;
}
";
    }
}

// Custom CSS
if (!empty($config['custom_css'])) {
    $css .= "\n/* Custom CSS */\n";
    $css .= $config['custom_css'];
}

// Output CSS
echo $css;
