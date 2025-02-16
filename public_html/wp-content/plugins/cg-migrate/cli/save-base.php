<?php

/**
 * Update an existing post or save a new one.
 * 
 * @param string $post_type The type of the post to create or update.
 * @param array $post_data  The raw post data; will be populated with defaults if they're not specified.
 * @param bool $use_slug    If no ID is present in $post_data, search for a post with the same slug.
 * @param bool $create      If no matching post is found, create it from scratch.
 */
function cg_save_base(string $post_type, array $post_data = [], bool $use_slug = false, bool $create = false) {
  $id = $post_data['id'] ?? null;
  $post = null;

  if ($id) {
    $post = get_post($id);
  }
  if (!$post) {
    if ($use_slug) {
      $post = get_post_by_name($post_data['slug'], $post_type);
    }
    if (!$post) {
      WP_CLI::log($post_type . " '". $post_data['title'] ."' (" . ($post_data['id'] ?? $post_data['slug']) . ") doesn't exist");
    }
  }

  // If we found a post, update its name and title; otherwise (as long as the 'create' flag is true),
  // create a new post.
  if ($post) {
    if (array_key_exists('slug', $post_data) && $post_data['slug']) $post->post_name = $post_data['slug'];
    if (array_key_exists('title', $post_data) && $post_data['title']) $post->post_title = $post_data['title'];
    $id = wp_update_post($post);
    WP_CLI::log($post->post_type . " '". $post->post_title ."' (" . $post->ID . ") updated");
  } elseif ($create) {
    $input = array(
      'post_type' => $post_type,
      'post_title' => sanitize_text_field($post_data['title']),
      'post_name' => sanitize_text_field($post_data['slug']),
      'post_status'   => 'publish',
      'post_author'   => get_current_user_id(),
      'post_date'     => current_time('mysql'),    
    );
    $id = wp_insert_post($input);
    $post = get_post($id);
    if ($id) {
      WP_CLI::log($post->post_type . " '". $post->post_title ."' (" . $post->ID . ") created");
    }
  }

  // Either update or creation was successful, handle common properties shared across post types.
  if ($id) {
    // handle region
    if(array_key_exists('region', $post_data)) {
      $region = term_exists($post_data['region'], 'region');
      if ($region) {
        wp_set_object_terms($id, [(int)$region['term_id']], 'region');
      }
    }

    // Handle core business relationships (offices, projects, sectors, services, people)

    // Handle migration note fields
    if(array_key_exists('migration_status', $post_data)) {
      update_field('migration_status', $post_data['migration_status'] ?? NULL, $post->ID);
    }
    if(array_key_exists('migration_note', $post_data)) {
      update_field('migration_note', $post_data['migration_note'] ?? NULL, $post->ID);
    }

    if (array_key_exists('migration_status', $post_data)) {
      $action = strtolower(trim($post_data['migration_status']));
      if (($action === 'delete') || ($action === 'archive')) {
        wp_trash_post( $id );
        WP_CLI::log($post_type . " '". $post_data['title'] ."' (" . ($post_data['id'] ?? $post_data['slug']) . ") trashed");
      }
    }
  }


  // Return the ID of the post (or an error, if it couldn't be created or updated successfully)
  return get_post($id);
}