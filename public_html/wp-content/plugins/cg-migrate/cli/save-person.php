<?php

function cg_save_person(array $post_data = [], bool $use_slug = true, bool $create = true) {
  if (!array_key_exists('migration_status', $post_data)) $post_data['migration_status'] = 'needs-review';

  $post = cg_save_base('person', $post_data, true, true);

  if ($post) {
    update_field('role', $post_data['role'] ?? NULL, $post->ID);
    update_field('show_contact', $post_data['show_contact'] ?? NULL, $post->ID);
    update_field('email', $post_data['email'] ?? NULL, $post->ID);
    update_field('phone', $post_data['phone'] ?? NULL, $post->ID);
    update_field('linkedin', $post_data['linkedin'] ?? NULL, $post->ID);
  }

  return $post;
}
