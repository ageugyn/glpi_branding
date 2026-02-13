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

use GlpiPlugin\Branding\Config;

// Include GLPI - works for both plugins/ and marketplace/ locations
$glpi_root = null;
$possible_roots = [
    dirname(__DIR__, 4),           // marketplace/branding/front -> /var/glpi -> needs /var/www/glpi
    dirname(__DIR__, 3),           // plugins/branding/front -> glpi
    '/var/www/glpi',               // Docker common path
    '/var/www/html/glpi',          // Alternative path
];

foreach ($possible_roots as $path) {
    if (file_exists($path . '/inc/includes.php')) {
        $glpi_root = $path;
        break;
    }
}

if ($glpi_root === null) {
    die('GLPI not found');
}

include($glpi_root . '/inc/includes.php');

Session::checkRight("entity", UPDATE);

$config = new Config();

if (isset($_POST["update"])) {
    $config->check($_POST['id'], UPDATE);
    $config->update($_POST);
    Html::back();
    
} else if (isset($_POST["add"])) {
    $config->check(-1, CREATE);
    
    if ($newID = $config->add($_POST)) {
        if ($_SESSION['glpibackcreated']) {
            Html::redirect($config->getLinkURL());
        }
    }
    Html::back();
    
} else if (isset($_POST["purge"])) {
    $config->check($_POST['id'], PURGE);
    $config->delete($_POST, 1);
    Html::redirect(Entity::getFormURLWithID($_POST['entities_id']));
    
} else {
    Html::header(
        __('Branding', 'branding'),
        $_SERVER['PHP_SELF'],
        "admin",
        "entity"
    );
    
    $config->display([
        'id' => $_GET["id"] ?? 0
    ]);
    
    Html::footer();
}
