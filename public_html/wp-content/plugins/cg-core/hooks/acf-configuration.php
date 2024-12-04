<?php
/**
 * @package CG Core
 * @version 1.0.0
 */

/**
 * ACF hook implementations
 */

// Store ACF post type and field group definitions in this directory.
function cg_core_acf_json_save_point( $path ) {
    return plugin_dir_path( __DIR__ ) . 'acf-json';
}
add_filter( 'acf/settings/save_json', 'cg_core_acf_json_save_point' );

// Load ACF post type and field group definitions from this directory.
function cg_core_acf_json_load_point( $paths ) {
  // Remove the original path
  unset($paths[0]);

  // Append the new path and return it.
  $paths[] = plugin_dir_path( __DIR__ ) . 'acf-json';

  return $paths;    
}
add_filter( 'acf/settings/load_json', 'cg_core_acf_json_load_point' );
