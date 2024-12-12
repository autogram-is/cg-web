<?php
/**
 * Plugin Name: CG Migration Helper
 * Description: Content and data migration for the CG web site redesign 
 * Version: 1.0
 * Author: Autogram
 */

require_once __DIR__ . '/vendor/autoload.php';

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

define('CG_MIGRATE_PLUGIN_DIR', WP_PLUGIN_DIR . '/cg-migrate');
define('CG_MIGRATE_DATA_DIR', CG_MIGRATE_PLUGIN_DIR . '/data');

$inc_dirs = ['post-types', 'helpers', 'hooks', 'cli'];
foreach ($inc_dirs as $dir) {
    foreach (glob(join("/", [CG_MIGRATE_PLUGIN_DIR, 'includes', $dir, '*.php'])) as $inc) {
        $inc = basename($inc);
        require_once CG_MIGRATE_PLUGIN_DIR . '/includes/' . $dir . '/' . $inc;
    }    
}
