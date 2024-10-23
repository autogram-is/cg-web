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

// Load dependencies
require_once plugin_dir_path(__FILE__) . 'import/preprocess-all-posts.php';
require_once plugin_dir_path(__FILE__) . 'import/preprocess-post-raw.php';
require_once plugin_dir_path(__FILE__) . 'import/preprocess-post.php';

foreach (glob(CG_IMPORT_PLUGIN_DIR . "/includes/meta-keys/*.php") as $inc) {
    $inc = basename($inc);
    require CG_IMPORT_PLUGIN_DIR . '/includes/meta-keys/' . $inc;
}

foreach (glob(CG_IMPORT_PLUGIN_DIR . "/includes/post-types/*.php") as $inc) {
    $inc = basename($inc);
    require CG_IMPORT_PLUGIN_DIR . '/includes/post-types/' . $inc;
}

foreach (glob(CG_IMPORT_PLUGIN_DIR . "/includes/*.php") as $inc) {
    $inc = basename($inc);
    require CG_IMPORT_PLUGIN_DIR . '/includes/' . $inc;
}