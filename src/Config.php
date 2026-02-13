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

namespace GlpiPlugin\Branding;

use CommonDBTM;
use CommonGLPI;
use Entity;
use Glpi\Application\View\TemplateRenderer;
use Html;
use Session;

if (!defined('GLPI_ROOT')) {
    die("Sorry. You can't access this file directly");
}

/**
 * Config Class
 **/
class Config extends CommonDBTM
{
    public $dohistory = true;
    public static $rightname = 'entity';

    /**
     * Get name of this type
     *
     * @param integer $nb Number of items
     * @return string
     **/
    public static function getTypeName($nb = 0)
    {
        return __('Branding', 'branding');
    }

    /**
     * Get tab name for item
     *
     * @param CommonGLPI $item         Item instance
     * @param integer    $withtemplate Template usage flag
     * @return string|array
     **/
    public function getTabNameForItem(CommonGLPI $item, $withtemplate = 0)
    {
        if (!$withtemplate && $item->getType() == Entity::class) {
            return self::getTypeName(Session::getPluralNumber());
        }
        return '';
    }

    /**
     * Display tab content
     *
     * @param CommonGLPI $item         Item instance
     * @param integer    $tabnum       Tab number
     * @param integer    $withtemplate Template usage flag
     * @return boolean
     **/
    public static function displayTabContentForItem(CommonGLPI $item, $tabnum = 1, $withtemplate = 0)
    {
        if ($item->getType() == Entity::class) {
            $config = new self();
            $config->showForEntity($item);
        }
        return true;
    }

    /**
     * Get config for specific entity
     *
     * @param integer $entities_id Entity ID
     * @param boolean $recursive   Recursive search
     * @return array|false
     */
    public static function getForEntity($entities_id, $recursive = true, $from_child = false)
    {
        global $DB;

        $iterator = $DB->request([
            'SELECT' => ['*'],
            'FROM'   => self::getTable(),
            'WHERE'  => ['entities_id' => $entities_id],
            'LIMIT'  => 1
        ]);

        if (count($iterator)) {
            $config = $iterator->current();
            if (!empty($config['enabled'])) {
                if (!$from_child || !empty($config['is_recursive'])) {
                    return $config;
                }
                return false;
            }
            if (!$recursive || empty($config['is_recursive'])) {
                return false;
            }
        }

        // If recursive and not found, search in parent entities
        if ($recursive && $entities_id > 0) {
            $entity = new Entity();
            if ($entity->getFromDB($entities_id)) {
                return self::getForEntity($entity->fields['entities_id'], true, true);
            }
        }

        return false;
    }

    /**
     * Get active theme colors based on schedule
     *
     * @param array $config Configuration array
     * @return array|null
     */
    public static function getActiveColors($config)
    {
        if (!$config) {
            return [];
        }

        $theme = self::getActiveTheme($config);
        if ($theme === 'night') {
            return self::decodeColors($config['colors_night'] ?? []);
        }
        return self::decodeColors($config['colors_day'] ?? []);
    }

    /**
     * Show config form for entity
     *
     * @param Entity $entity Entity instance
     * @return void
     */
    public function showForEntity(Entity $entity)
    {
        global $DB;

        $entities_id = $entity->getID();
        
        if (!$entity->can($entities_id, READ)) {
            return false;
        }

        // Get existing config
        $iterator = $DB->request([
            'SELECT' => ['*'],
            'FROM'   => self::getTable(),
            'WHERE'  => ['entities_id' => $entities_id],
            'LIMIT'  => 1
        ]);

        $defaults = [
            'id'               => 0,
            'entities_id'      => $entities_id,
            'is_recursive'     => 0,
            'enabled'          => 0,
            'logo_login'       => null,
            'logo_expanded'    => null,
            'logo_collapsed'   => null,
            'favicon'          => null,
            'background_login' => null,
            'colors_day'       => '{}',
            'colors_night'     => '{}',
            'schedule_enabled' => 0,
            'day_start'        => '08:00:00',
            'night_start'      => '20:00:00',
            'custom_css'       => ''
        ];

        if (count($iterator)) {
            $this->fields = array_merge($defaults, $iterator->current());
        } else {
            $this->fields = $defaults;
        }

        // Ensure JSON fields are arrays for Twig templates
        $this->fields['colors_day'] = self::decodeColors($this->fields['colors_day'] ?? []);
        $this->fields['colors_night'] = self::decodeColors($this->fields['colors_night'] ?? []);
        $this->fields['custom_css'] = (string)($this->fields['custom_css'] ?? '');

        TemplateRenderer::getInstance()->display('@branding/config.html.twig', [
            'item'   => $this,
            'params' => [
                'candel' => false,
                'canedit' => $entity->can($entities_id, UPDATE)
            ]
        ]);
    }

    /**
     * Prepare input for add/update
     *
     * @param array $input Input data
     * @return array|false
     */
    public function prepareInputForAdd($input)
    {
        return $this->prepareInput($input);
    }

    /**
     * Prepare input for update
     *
     * @param array $input Input data
     * @return array|false
     */
    public function prepareInputForUpdate($input)
    {
        return $this->prepareInput($input);
    }

    /**
     * Prepare input (common for add/update)
     *
     * @param array $input Input data
     * @return array|false
     */
    private function prepareInput($input)
    {
        // Sanitize custom CSS
        if (isset($input['custom_css'])) {
            $input['custom_css'] = self::sanitizeCustomCss((string)$input['custom_css']);
        }

        // Handle JSON fields
        if (isset($input['colors_day']) && is_array($input['colors_day'])) {
            $input['colors_day'] = json_encode($input['colors_day']);
        }
        
        if (isset($input['colors_night']) && is_array($input['colors_night'])) {
            $input['colors_night'] = json_encode($input['colors_night']);
        }

        // Handle file uploads with validation
        // GLPI 11's fileField macro uses JavaScript uploader that prefixes field names with '_uploader_'
        // It also sends hidden fields with prefix like '_prefix_logo_login' and '_logo_login' for existing values
        $upload_dir = GLPI_PLUGIN_DOC_DIR . '/branding';
        $max_size = 5 * 1024 * 1024; // 5 MB
        $allowed_extensions = ['png', 'jpg', 'jpeg', 'gif', 'webp', 'ico'];
        $allowed_mime = [
            'image/png',
            'image/jpeg',
            'image/gif',
            'image/webp',
            'image/x-icon',
            'image/vnd.microsoft.icon'
        ];
        
        // Mapping: database field => possible $_FILES keys (try _uploader_ first, then raw)
        $file_fields = [
            'logo_login'       => ['_uploader_logo_login', 'logo_login'],
            'logo_expanded'    => ['_uploader_logo_expanded', 'logo_expanded'],
            'logo_collapsed'   => ['_uploader_logo_collapsed', 'logo_collapsed'],
            'favicon'          => ['_uploader_favicon', 'favicon'],
            'background_login' => ['_uploader_background_login', 'background_login'],
        ];
        
        foreach ($file_fields as $db_field => $possible_keys) {
            $file_data = null;
            
            // Try each possible key in $_FILES
            foreach ($possible_keys as $key) {
                if (isset($_FILES[$key]) && $_FILES[$key]['error'] === UPLOAD_ERR_OK) {
                    $file_data = $_FILES[$key];
                    break;
                }
            }
            
            // If no file uploaded, check for GLPI's _prefix_ pattern (for multi-file uploader)
            if ($file_data === null) {
                // GLPI may send files as _prefix_fieldname[0] or similar
                $prefix_key = '_prefix_' . $db_field;
                if (isset($input[$prefix_key]) && is_array($input[$prefix_key])) {
                    // The actual files might be in GLPI's temp upload dir
                    // Try to handle via GLPI's Document upload pattern
                    foreach ($input[$prefix_key] as $idx => $prefix) {
                        $tag_key = '_tag_' . $db_field;
                        if (isset($input['_' . $db_field]) && is_array($input['_' . $db_field])) {
                            $tmp_name = $input['_' . $db_field][$idx] ?? null;
                            if ($tmp_name && file_exists(GLPI_TMP_DIR . '/' . $tmp_name)) {
                                $file_data = [
                                    'name' => $tmp_name,
                                    'tmp_name' => GLPI_TMP_DIR . '/' . $tmp_name,
                                    'size' => filesize(GLPI_TMP_DIR . '/' . $tmp_name),
                                    'error' => UPLOAD_ERR_OK,
                                ];
                                break;
                            }
                        }
                    }
                }
            }
            
            if ($file_data === null) {
                continue;
            }
            
            $original = $file_data['name'];
            $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
            $size = (int)$file_data['size'];

            if (!in_array($ext, $allowed_extensions, true)) {
                Session::addMessageAfterRedirect(
                    __('Invalid file extension', 'branding') . ': ' . $db_field,
                    false,
                    ERROR
                );
                continue;
            }

            if ($size <= 0 || $size > $max_size) {
                Session::addMessageAfterRedirect(
                    __('Invalid file size', 'branding') . ': ' . $db_field,
                    false,
                    ERROR
                );
                continue;
            }

            $mime = self::detectMime($file_data['tmp_name']);
            if ($mime !== null && !in_array($mime, $allowed_mime, true)) {
                Session::addMessageAfterRedirect(
                    __('Invalid file type', 'branding') . ': ' . $db_field,
                    false,
                    ERROR
                );
                continue;
            }

            $safe_base = preg_replace('/[^A-Za-z0-9._-]/', '_', pathinfo($original, PATHINFO_FILENAME));
            if ($safe_base === '') {
                $safe_base = 'file';
            }
            $filename = uniqid() . '_' . $safe_base . '.' . $ext;
            $filepath = $upload_dir . '/' . $filename;
            
            // Use move_uploaded_file for $_FILES, copy for temp files
            $moved = false;
            if (is_uploaded_file($file_data['tmp_name'])) {
                $moved = move_uploaded_file($file_data['tmp_name'], $filepath);
            } else {
                // File from GLPI temp directory
                $moved = copy($file_data['tmp_name'], $filepath);
                if ($moved) {
                    @unlink($file_data['tmp_name']);
                }
            }
            
            if ($moved) {
                $input[$db_field] = $filename;
            }
        }

        return $input;
    }

    /**
     * Get file URL
     *
     * @param string $filename Filename
     * @return string
     */
    public static function getFileUrl($filename)
    {
        if (empty($filename)) {
            return '';
        }
        
        global $CFG_GLPI;
        
        // Use the image.php endpoint to serve files from outside web root
        return $CFG_GLPI['root_doc'] . '/plugins/branding/public/image.php?file=' . urlencode($filename);
    }

    /**
     * Compute active theme based on schedule
     *
     * @param array $config
     * @param string|null $time
     * @return string
     */
    public static function getActiveTheme(array $config, $time = null)
    {
        if (empty($config['schedule_enabled'])) {
            return 'day';
        }

        $current_time = $time ?? date('H:i:s');
        $day_start = $config['day_start'] ?? '08:00:00';
        $night_start = $config['night_start'] ?? '20:00:00';

        if ($day_start === $night_start) {
            return 'day';
        }

        if ($day_start < $night_start) {
            return ($current_time >= $day_start && $current_time < $night_start) ? 'day' : 'night';
        }

        // Overnight schedule (day spans midnight)
        return ($current_time >= $day_start || $current_time < $night_start) ? 'day' : 'night';
    }

    private static function decodeColors($value)
    {
        if (is_array($value)) {
            return $value;
        }
        if (!is_string($value) || $value === '') {
            return [];
        }
        $decoded = json_decode($value, true);
        return is_array($decoded) ? $decoded : [];
    }

    private static function detectMime($filepath)
    {
        if (!is_file($filepath)) {
            return null;
        }

        if (!function_exists('finfo_open')) {
            return null;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if (!$finfo) {
            return null;
        }

        $mime = finfo_file($finfo, $filepath);
        finfo_close($finfo);
        return $mime ?: null;
    }

    private static function sanitizeCustomCss($css)
    {
        // Remove @import statements and CSS expressions
        $css = preg_replace('/@import\\s+[^;]+;?/i', '', $css);
        $css = preg_replace('/expression\\s*\\(/i', '', $css);
        // Neutralize javascript: in url()
        $css = preg_replace('/url\\s*\\(\\s*[\"\\\']?\\s*javascript:/i', 'url(', $css);
        return $css;
    }
}
