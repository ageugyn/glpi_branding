#!/usr/bin/env php
<?php
/**
 * -------------------------------------------------------------------------
 * Branding plugin for GLPI - System Check
 * -------------------------------------------------------------------------
 *
 * This script checks if the system meets all requirements for the plugin
 * 
 * Usage: php tools/check_system.php
 * -------------------------------------------------------------------------
 */

echo "🔍 Branding Plugin - System Check\n";
echo str_repeat('=', 70) . "\n\n";

$errors = [];
$warnings = [];
$success = [];

// Check 1: PHP Version
echo "Checking PHP version... ";
$php_version = phpversion();
if (version_compare($php_version, '8.1', '>=')) {
    echo "✅ $php_version\n";
    $success[] = "PHP version is compatible ($php_version)";
} else {
    echo "❌ $php_version (requires 8.1+)\n";
    $errors[] = "PHP version $php_version is not compatible (requires 8.1+)";
}

// Check 2: Required PHP Extensions
echo "Checking PHP extensions...\n";
$required_extensions = ['json', 'gd', 'mysqli', 'mbstring', 'curl', 'fileinfo'];

foreach ($required_extensions as $ext) {
    echo "  - $ext: ";
    if (extension_loaded($ext)) {
        echo "✅\n";
        $success[] = "Extension $ext is loaded";
    } else {
        echo "❌\n";
        $errors[] = "Extension $ext is not loaded";
    }
}

// Check 3: GLPI Installation
echo "Checking GLPI installation... ";
$glpi_root = realpath(__DIR__ . '/../../../');
$glpi_config = $glpi_root . '/config/config_db.php';

if (file_exists($glpi_config)) {
    echo "✅\n";
    $success[] = "GLPI installation found at $glpi_root";
    
    // Check GLPI version
    define('GLPI_ROOT', $glpi_root);
    include_once($glpi_root . '/inc/define.php');
    
    if (defined('GLPI_VERSION')) {
        $glpi_version = GLPI_VERSION;
        echo "GLPI version: ";
        if (version_compare($glpi_version, '11.0', '>=')) {
            echo "✅ $glpi_version\n";
            $success[] = "GLPI version is compatible ($glpi_version)";
        } else {
            echo "❌ $glpi_version (requires 11.0+)\n";
            $errors[] = "GLPI version $glpi_version is not compatible (requires 11.0+)";
        }
    }
} else {
    echo "❌\n";
    $errors[] = "GLPI installation not found at $glpi_root";
}

// Check 4: Plugin Directory
echo "Checking plugin directory... ";
$plugin_dir = realpath(__DIR__ . '/..');

if (is_dir($plugin_dir)) {
    echo "✅\n";
    $success[] = "Plugin directory found at $plugin_dir";
    
    // Check required files
    $required_files = [
        'setup.php',
        'hook.php',
        'src/Config.php',
        'public/css/branding.css.php',
        'public/api.php',
        'templates/config.html.twig'
    ];
    
    echo "Checking required files...\n";
    foreach ($required_files as $file) {
        echo "  - $file: ";
        if (file_exists($plugin_dir . '/' . $file)) {
            echo "✅\n";
            $success[] = "Required file $file exists";
        } else {
            echo "❌\n";
            $errors[] = "Required file $file not found";
        }
    }
} else {
    echo "❌\n";
    $errors[] = "Plugin directory not found";
}

// Check 5: Files Directory
echo "Checking files directory... ";
$files_dir = $glpi_root . '/files/_plugins/branding';

if (is_dir($files_dir)) {
    echo "✅\n";
    $success[] = "Files directory exists at $files_dir";
    
    // Check permissions
    if (is_writable($files_dir)) {
        echo "  Permissions: ✅ Writable\n";
        $success[] = "Files directory is writable";
    } else {
        echo "  Permissions: ❌ Not writable\n";
        $warnings[] = "Files directory is not writable";
    }
} else {
    echo "⚠️  Not found (will be created on install)\n";
    $warnings[] = "Files directory doesn't exist yet";
}

// Check 6: Database (if GLPI is accessible)
if (defined('GLPI_ROOT') && file_exists($glpi_config)) {
    echo "Checking database connection... ";
    
    try {
        include_once($glpi_config);
        
        if (defined('DB_HOST') && defined('DB_NAME') && defined('DB_USER')) {
            $mysqli = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if (!$mysqli->connect_error) {
                echo "✅\n";
                $success[] = "Database connection successful";
                
                // Check if table exists
                $result = $mysqli->query("SHOW TABLES LIKE 'glpi_plugin_branding_configs'");
                if ($result && $result->num_rows > 0) {
                    echo "  Plugin table: ✅ Exists\n";
                    $success[] = "Plugin table exists (plugin is installed)";
                } else {
                    echo "  Plugin table: ⚠️  Not found (plugin not installed yet)\n";
                    $warnings[] = "Plugin table doesn't exist (not installed yet)";
                }
                
                $mysqli->close();
            } else {
                echo "❌ " . $mysqli->connect_error . "\n";
                $errors[] = "Database connection failed: " . $mysqli->connect_error;
            }
        }
    } catch (Exception $e) {
        echo "❌ " . $e->getMessage() . "\n";
        $errors[] = "Database check failed: " . $e->getMessage();
    }
}

// Check 7: Composer
echo "Checking Composer autoload... ";
$composer_autoload = $plugin_dir . '/vendor/autoload.php';

if (file_exists($composer_autoload)) {
    echo "✅\n";
    $success[] = "Composer autoload found";
} else {
    echo "⚠️  Not found (run: composer install)\n";
    $warnings[] = "Composer autoload not found - run 'composer install'";
}

// Summary
echo "\n" . str_repeat('=', 70) . "\n";
echo "📊 SUMMARY\n";
echo str_repeat('=', 70) . "\n\n";

echo "✅ Success: " . count($success) . "\n";
echo "⚠️  Warnings: " . count($warnings) . "\n";
echo "❌ Errors: " . count($errors) . "\n\n";

if (count($errors) > 0) {
    echo "❌ ERRORS:\n";
    foreach ($errors as $error) {
        echo "  - $error\n";
    }
    echo "\n";
}

if (count($warnings) > 0) {
    echo "⚠️  WARNINGS:\n";
    foreach ($warnings as $warning) {
        echo "  - $warning\n";
    }
    echo "\n";
}

if (count($errors) === 0) {
    echo "✅ System is ready for plugin installation!\n";
    exit(0);
} else {
    echo "❌ Please fix the errors before installing the plugin.\n";
    exit(1);
}
