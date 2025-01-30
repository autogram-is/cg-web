<?php

function cg_save_office(array $post_data = [], bool $use_slug = true, bool $create = true) {
  if (!array_key_exists('migration_status', $post_data)) $post_data['migration_status'] = 'needs-review';

  $post = cg_save_base('office', $post_data, true, true);

  if ($post) {
    update_field('location', $post_data['location'] ?? NULL, $post->ID);
    update_field('office_details', $post_data['office_details'] ?? NULL, $post->ID);
  }

  return $post;
}