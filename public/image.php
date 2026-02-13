<?php
/**
 * -------------------------------------------------------------------------
 * Branding plugin for GLPI - Image Server
 * -------------------------------------------------------------------------
 *
 * Serves branding images from the plugin's files directory
 * -------------------------------------------------------------------------
 */

// Get GLPI root - try multiple methods
$glpi_root = null;

// Method 1: Check common paths
$possible_roots = [
    '/var/www/glpi',
    '/var/www/html/glpi',
    dirname(__DIR__, 4), // Marketplace: plugins/branding/public -> glpi
    dirname(__DIR__, 3), // Alternative structure
];

foreach ($possible_roots as $path) {
    if (file_exists($path . '/inc/includes.php')) {
        $glpi_root = $path;
        break;
    }
}

if ($glpi_root === null) {
    http_response_code(500);
    die('GLPI root not found');
}

// Define GLPI_ROOT before including
if (!defined('GLPI_ROOT')) {
    define('GLPI_ROOT', $glpi_root);
}

// Include GLPI config to get GLPI_PLUGIN_DOC_DIR
if (file_exists($glpi_root . '/config/config_db.php')) {
    include_once($glpi_root . '/config/config_db.php');
}

// Define paths if not already defined
if (!defined('GLPI_VAR_DIR')) {
    // Try common locations
    if (is_dir('/var/glpi')) {
        define('GLPI_VAR_DIR', '/var/glpi');
    } else {
        define('GLPI_VAR_DIR', $glpi_root . '/files');
    }
}

if (!defined('GLPI_PLUGIN_DOC_DIR')) {
    define('GLPI_PLUGIN_DOC_DIR', GLPI_VAR_DIR . '/files/_plugins');
}

// Get the requested file
$file = $_GET['file'] ?? '';

// Security: only allow alphanumeric, underscore, dash, and dot
if (!preg_match('/^[A-Za-z0-9._-]+$/', $file)) {
    http_response_code(400);
    die('Invalid filename');
}

// Build the full path
$filepath = GLPI_PLUGIN_DOC_DIR . '/branding/' . $file;

// Security: ensure the file is within the branding directory
$realpath = realpath($filepath);
$basepath = realpath(GLPI_PLUGIN_DOC_DIR . '/branding');

if ($realpath === false || $basepath === false || strpos($realpath, $basepath) !== 0) {
    http_response_code(404);
    die('File not found');
}

if (!is_file($realpath)) {
    http_response_code(404);
    die('File not found');
}

// Get MIME type
$extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
$mime_types = [
    'png'  => 'image/png',
    'jpg'  => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'gif'  => 'image/gif',
    'webp' => 'image/webp',
    'ico'  => 'image/x-icon',
];

$mime = $mime_types[$extension] ?? 'application/octet-stream';

// Set cache headers (1 day)
$expires = 86400;
header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($realpath));
header('Cache-Control: public, max-age=' . $expires);
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expires) . ' GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($realpath)) . ' GMT');

// Output the file
readfile($realpath);
exit;
