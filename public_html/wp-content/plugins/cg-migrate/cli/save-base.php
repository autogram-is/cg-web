<?php

function cg_save_base(string $post_type, array $post_data = [], bool $use_slug = false, bool $create = false) {
  $id = $post_data['id'] ?? null;
  $post = null;

  // If an ID was passed in, try to load the post. Otherwise, *as long as the 'use_slug' flag
  // is true, try to load it by type and slug.
  if ($id) {
    $post = get_post($id);
  }
  if (!$post && $use_slug) {
    $post = get_post_by_name($post_data['slug'], $post_type);
  }

  // If we found a post, update its name and title; otherwise (as long as the 'create' flag is true),
  // create a new post.
  if ($post !== null) {
    if (array_key_exists('slug', $post_data) && $post_data['slug']) $post->post_name = $post_data['slug'];
    if (array_key_exists('title', $post_data) && $post_data['title']) $post->post_title = $post_data['title'];
    $id = wp_update_post($post);  
  } elseif ($create) {
    $post_data = array(
      'post_type' => 'project',
      'post_title' => sanitize_text_field($post_data['title']),
      'post_name' => sanitize_text_field($post_data['slug']),
      'post_status'   => 'publish',
      'post_author'   => get_current_user_id(),
      'post_date'     => current_time('mysql'),    
    );
    $id = wp_update_post($post);
  }

  // Either update or creation was successful, handle common properties shared across post types.
  if ($id) {
    // handle region
    if(array_key_exists('region', $post_data) && $post_data['region']) {
      $region = $post_data['region'] ? term_exists($post_data['region'], 'region') : NULL;
      if ($region) {
        wp_set_object_terms($id, (int)$region['term_id'], 'region');
      }
    }

    // Handle core business relationships (offices, projects, sectors, services, people)

  }

  // Return the ID of the post (or an error, if it couldn't be created or updated successfully)
  return $id;
}