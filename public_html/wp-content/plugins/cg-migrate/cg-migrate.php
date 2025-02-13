<?php
/**
 * Plugin Name: CG Migration Helper
 * Description: Content and data migration for the CG web site redesign 
 * Version: 1.0
 * Author: Autogram
 */

// require_once __DIR__ . '/vendor/autoload.php';

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

define('CG_MIGRATE_LEGACY_SECTOR_LANDING', 15041);
define('CG_MIGRATE_LEGACY_EU_SECTOR_LANDING', 57642);
define('CG_MIGRATE_LEGACY_SERVICE_LANDING', 14996);
define('CG_MIGRATE_LEGACY_OFFICE_LANDING', 62959);

define('CG_MIGRATE_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CG_MIGRATE_DATA_DIR', CG_MIGRATE_PLUGIN_DIR . '/data');
define('CG_MIGRATE_CONTENT_DIR', CG_MIGRATE_PLUGIN_DIR . '/content');

$inc_dirs = ['cli', 'helpers', 'hooks', 'markup'];
foreach ($inc_dirs as $dir) {
    foreach (glob(join("/", [CG_MIGRATE_PLUGIN_DIR, $dir, '*.php'])) as $inc) {
        $inc = basename($inc);
        require_once CG_MIGRATE_PLUGIN_DIR . '/' . $dir . '/' . $inc;
    }    
}
