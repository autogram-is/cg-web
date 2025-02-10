<?php

function cg_save_service(array $post_data = [], bool $use_slug = true, bool $create = true) {
  if (!array_key_exists('migration_status', $post_data)) $post_data['migration_status'] = 'needs-content';

  $post = cg_save_base('service', $post_data, true, true);

  if ($post) {
    if ($post_data['locale']) {
      update_field('locale', $post_data['locale'] ?? NULL, $post->ID);
    }
    // We use the project as the linchpin piece of content; updating it in both
    // places during our imports would cause a lot of cross-talk.
    // The same applies to the related_news field.
    if ($post_data['people']) {
      update_field('people', $post_data['people'] ?? NULL, $post->ID);
    }
  } else {
    WP_CLI::log("Could not update service '". $post_data['title'] ."'");
  }

  return $post;
}