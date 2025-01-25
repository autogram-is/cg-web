<?php

function cg_save_sector(array $post_data = [], bool $use_slug = true, bool $create = true) {
  if (!array_key_exists('migration_status', $post_data)) $post_data['migration_status'] = 'needs-content';

  $post = cg_save_base('sector', $post_data, true, true);

  if ($post) {
    if ($post_data['locale']) {
      update_field('locale', $post_data['locale'] ?? NULL, $post->ID);
    }
  } else {
    WP_CLI::log("Could not create sector '". $post_data['title'] ."'");
  }


  return $post;
}