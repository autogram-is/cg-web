<?php

function cg_save_person(array $post_data = [], bool $use_slug = true, bool $create = true) {
  if (!array_key_exists('migration_status', $post_data)) $post_data['migration_status'] = 'needs-review';

  $post = cg_save_base('person', $post_data, true, true);

  if ($post) {
    update_field('role', trim($post_data['role']) ?? NULL, $post->ID);
    update_field('email', trim($post_data['email']) ?? NULL, $post->ID);
    update_field('phone', trim($post_data['phone']) ?? NULL, $post->ID);
    update_field('linkedin', trim($post_data['linkedin']) ?? NULL, $post->ID);

    update_field('show_contact', boolval($post_data['show_contact'] ?? NULL), $post->ID);
    update_field('ex_employee', boolval($post_data['ex_employee'] ?? NULL), $post->ID);
  } else {
    WP_CLI::log("Could not update person '". $post_data['title'] ."'");
  }

  return $post;
}

function cg_delete_person($slug) {
  $post = get_post_by_name($slug, 'person');
  if ($post) {
    wp_delete_post($post->ID, TRUE);
  }
}

function cg_update_person_headshot($slug, $filename) {
  // TODO
}

