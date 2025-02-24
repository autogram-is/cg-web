<?php

function cg_save_office(array $post_data = [], bool $use_slug = true, bool $create = true) {
  if (!array_key_exists('migration_status', $post_data)) $post_data['migration_status'] = 'needs-review';

  $post = cg_save_base('office', $post_data, true, true);

  if ($post) {
    if ($post_data['name'] || $post_data['email'] || $post_data['phone'] || $post_data['address']) {
      $post_data['office_details'] = [];
      $address = $post_data['address'] ?? '';
      $address = str_replace(";", "\n", ($address ?? ''));
      $post_data['office_details'][] = array(
        'name' => trim($post_data['name'] ?? ''),
        'email' => trim($post_data['email'] ?? ''),
        'phone' => trim($post_data['phone'] ?? ''),
        'address' => trim($address ?? ''),
      );
    }

    update_field('location', trim($post_data['location']) ?? NULL, $post->ID);

    if (key_exists('office_details', $post_data) && count($post_data['office_details']) > 0) {
      update_field('office_details', $post_data['office_details'] ?? NULL, $post->ID);
    }

    if (key_exists('people', $post_data) && $post_data['people']) {
      update_field('people', $post_data['people'] ?? NULL, $post->ID);
    }

  } else {
    WP_CLI::log("Could not update office '". $post_data['title'] ."'");
  }

  return $post;
}

function cg_add_loc_to_office(array $data = []) {
  $address = $data['address'] ?? '';
  $address = str_replace(";", "\n", ($address ?? ''));

  $new_location = array(
    'name' => trim($data['name'] ?? ''),
    'email' => trim($data['email'] ?? ''),
    'phone' => trim($data['phone'] ?? ''),
    'address' => $address ?? '',
  );

  $post = get_post_by_name($data['slug'], 'office');
  $locations = get_field('office_details', $post->ID);

  $already_exists = FALSE;
  if ($locations) {
    foreach ($locations as $key => $location) {
      if ($location['name'] === $new_location['name']) {
        $locations[$key] = $new_location;
        $already_exists = TRUE;
      }
    }  
  }

  if (!$already_exists) {
    if ($locations) {
      $locations[] = $new_location;
    } else {
      $locations = [$new_location];
    }
  }

  update_field('office_details', $locations, $post->ID);
}

function cg_delete_office($slug) {
  $post = get_post_by_name($slug, 'office');
  if ($post) {
    wp_delete_post($post->ID, TRUE);
  }
}

