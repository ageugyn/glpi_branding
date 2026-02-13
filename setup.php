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

use Glpi\Http\Firewall;

define('PLUGIN_BRANDING_VERSION', '2.0.1');
define('PLUGIN_BRANDING_MIN_GLPI', '11.0.0');
define('PLUGIN_BRANDING_MAX_GLPI', '11.0.99');

/**
 * Init hooks of the plugin.
 * REQUIRED
 *
 * @return void
 */
function plugin_init_branding()
{
    global $PLUGIN_HOOKS;

    $PLUGIN_HOOKS['csrf_compliant']['branding'] = true;

    $plugin = new Plugin();
    
    if ($plugin->isInstalled('branding') && $plugin->isActivated('branding')) {
        
        Plugin::registerClass(
            'GlpiPlugin\\Branding\\Config',
            [
                'addtabon' => 'Entity'
            ]
        );

        // Add CSS dynamically (for authenticated users)
        if (Session::getLoginUserID()) {
            $entity_id = $_SESSION['glpiactive_entity'] ?? 0;
            
            // Bypass if disable_branding is set
            if (!isset($_GET['disable_branding'])) {
                $PLUGIN_HOOKS['add_css']['branding'][] = 'public/css/branding.css.php?entities_id=' . $entity_id;
            }
        }

        // Add CSS to login page
        $PLUGIN_HOOKS['display_login']['branding'] = 'plugin_branding_display_login';
        // Add favicon to authenticated pages
        $PLUGIN_HOOKS['add_header']['branding'] = 'plugin_branding_add_header';

        // Register API endpoints (stateless)
        if (class_exists('Glpi\\Http\\SessionManager')) {
            \Glpi\Http\SessionManager::registerPluginStatelessPath('branding', '#^/public/api\.php#');
        }

        // Register public CSS endpoint as stateless
        Firewall::addPluginStrategyForLegacyScripts(
            'branding',
            '#^/public/css/branding\\.css\\.php$#',
            Firewall::STRATEGY_NO_CHECK
        );

        // Register public API endpoint as stateless
        Firewall::addPluginStrategyForLegacyScripts(
            'branding',
            '#^/public/api\\.php$#',
            Firewall::STRATEGY_NO_CHECK
        );

        // Register public image endpoint as stateless
        Firewall::addPluginStrategyForLegacyScripts(
            'branding',
            '#^/public/image\\.php$#',
            Firewall::STRATEGY_NO_CHECK
        );
    }
}

/**
 * Get the name and the version of the plugin
 * REQUIRED
 *
 * @return array
 */
function plugin_version_branding()
{
    return [
        'name'           => __('Branding', 'branding'),
        'version'        => PLUGIN_BRANDING_VERSION,
        'author'         => '<a href="https://github.com/pluginsGLPI">Branding Team</a>',
        'license'        => 'GPLv2+',
        'homepage'       => 'https://github.com/pluginsGLPI/branding',
        'requirements'   => [
            'glpi' => [
                'min' => PLUGIN_BRANDING_MIN_GLPI,
                'max' => PLUGIN_BRANDING_MAX_GLPI,
            ],
            'php'  => [
                'min' => '8.1',
            ]
        ]
    ];
}
