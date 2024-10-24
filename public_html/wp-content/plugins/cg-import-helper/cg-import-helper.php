<?php
/**
 * Plugin Name: CG Import Helper
 * Description: Cleans and tidies incoming imports 
 * Version: 1.0
 * Author: Autogram
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

define('CG_IMPORT_PLUGIN_DIR', WP_PLUGIN_DIR . '/cg-import-helper');

$supported_extras = ['meta-keys', 'post-types', 'helpers', 'hooks'];
foreach ($supported_extras as $dir) {
    foreach (glob(join([CG_IMPORT_PLUGIN_DIR, 'includes', $dir, '*.php'])) as $inc) {
        $inc = basename($inc);
        require_once CG_IMPORT_PLUGIN_DIR . '/includes/' . $dir . '/' . $inc;
    }    
}

require_once CG_IMPORT_PLUGIN_DIR . '/csv-importer.php';
