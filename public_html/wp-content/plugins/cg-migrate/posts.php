<?php
/**
 * @package CG Migrate
 * @version 1.0.0
 */

add_filter( 'wp_import_post_data_raw', 'cg_migrate_post' );

function cg_migrate_post($post) {
  if ($post->post_type == 'avada_portfolio') {
    return cg_migrate_portfolio_item($post);
  }

  return $post;
}

function cg_migrate_portfolio_item($post) {
  $post->type = 'cg_portfolio';
  return $post;
}