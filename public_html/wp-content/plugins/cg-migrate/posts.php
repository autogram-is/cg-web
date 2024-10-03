<?php
/**
 * @package CG Migrate
 * @version 1.0.0
 */

add_filter( 'wp_import_post_data_raw', 'cg_migrate_post' );

function cg_migrate_post($post) {
  return $post;
}
