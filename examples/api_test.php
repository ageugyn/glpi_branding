#!/usr/bin/env php
<?php
/**
 * -------------------------------------------------------------------------
 * Branding plugin for GLPI - API Test Example
 * -------------------------------------------------------------------------
 *
 * This script demonstrates how to use the Branding API
 * 
 * Usage: php examples/api_test.php
 * -------------------------------------------------------------------------
 */

// Configuration
$glpi_url = 'http://localhost/glpi';
$entity_id = 0;

echo "🎨 Branding API Test\n";
echo str_repeat('=', 50) . "\n\n";

// Test 1: Get branding configuration
echo "Test 1: Getting branding configuration for entity $entity_id\n";
echo str_repeat('-', 50) . "\n";

$url = "$glpi_url/plugins/branding/api.php?entity_id=$entity_id";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For local testing

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $http_code\n";

if ($http_code === 200) {
    echo "✅ Success!\n\n";
    
    $data = json_decode($response, true);
    
    echo "Entity ID: " . $data['entity_id'] . "\n";
    echo "Enabled: " . ($data['enabled'] ? 'Yes' : 'No') . "\n";
    echo "Active Theme: " . $data['active_theme'] . "\n";
    echo "Schedule Enabled: " . ($data['schedule_enabled'] ? 'Yes' : 'No') . "\n";
    
    if ($data['schedule_enabled']) {
        echo "Day starts at: " . $data['day_start'] . "\n";
        echo "Night starts at: " . $data['night_start'] . "\n";
    }
    
    echo "\nColors:\n";
    if (!empty($data['colors'])) {
        foreach ($data['colors'] as $key => $value) {
            echo "  - $key: $value\n";
        }
    } else {
        echo "  (no colors configured)\n";
    }
    
    echo "\nAssets:\n";
    foreach ($data['assets'] as $key => $value) {
        if (!empty($value)) {
            echo "  - $key: $value\n";
        }
    }
    
    echo "\nCSS URL: " . $data['css_url'] . "\n";
    
} else {
    echo "❌ Error!\n\n";
    echo $response . "\n";
}

echo "\n" . str_repeat('=', 50) . "\n";

// Test 2: Integration example
echo "\n📝 Integration Examples\n";
echo str_repeat('=', 50) . "\n\n";

echo "JavaScript/Fetch:\n";
echo <<<JS
fetch('$url')
    .then(response => response.json())
    .then(data => {
        console.log('Theme:', data.active_theme);
        document.body.style.backgroundColor = data.colors.background;
    });
JS;

echo "\n\n";

echo "cURL:\n";
echo "curl -X GET \"$url\"\n";

echo "\n\n";

echo "PHP:\n";
echo <<<PHP
\$ch = curl_init();
curl_setopt(\$ch, CURLOPT_URL, "$url");
curl_setopt(\$ch, CURLOPT_RETURNTRANSFER, true);
\$response = curl_exec(\$ch);
curl_close(\$ch);

\$branding = json_decode(\$response, true);
echo "Active theme: " . \$branding['active_theme'];
PHP;

echo "\n\n" . str_repeat('=', 50) . "\n";
echo "✅ Tests completed!\n";
