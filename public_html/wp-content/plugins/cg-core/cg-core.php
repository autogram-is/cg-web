<?php
/**
 * @package CG Core
 * @version 1.0.0
 */

/*
Plugin Name: CG Core
Plugin URI: http://github.com/autogram-is/cg-web
Description: Defines custom post types, core site settings, and reusable logic.
Author: Autogram
Version: 1.0.0
Author URI: http://autogram.is/
*/

define('CG_CORE_PLUGIN_DIR', plugin_dir_path(__FILE__));

$inc_dirs = ['util', 'hooks'];
foreach ($inc_dirs as $dir) {
    foreach (glob(join("/", [CG_CORE_PLUGIN_DIR, $dir, '*.php'])) as $inc) {
        $inc = basename($inc);
        require_once CG_CORE_PLUGIN_DIR . '/' . $dir . '/' . $inc;
    }    
}
