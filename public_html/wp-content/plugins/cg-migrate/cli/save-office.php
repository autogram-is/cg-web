<?php

function cg_save_office(array $post_data = [], bool $use_slug = true, bool $create = true) {
  if (!array_key_exists('migration_status', $post_data)) $post_data['migration_status'] = 'needs-review';

  $post = cg_save_base('office', $post_data, true, true);

  if ($post) {
    if ($post_data['name'] || $post_data['email'] || $post_data['phone'] || $post_data['address']) {
      $item['office_details'] = [];
      $item['office_details'][] = array(
        'name' => $post_data['name'] ?? '',
        'email' => $post_data['email'] ?? '',
        'phone' => $post_data['phone'] ?? '',
        'address' => $post_data['address'] ?? '',
      );
    }

    update_field('location', $post_data['location'] ?? NULL, $post->ID);
    update_field('office_details', $post_data['office_details'] ?? NULL, $post->ID);

    if (key_exists('people', $post_data) && $post_data['people']) {
      update_field('people', $post_data['people'] ?? NULL, $post->ID);
    }

  } else {
    WP_CLI::log("Could not update office '". $post_data['title'] ."'");
  }

  return $post;
}