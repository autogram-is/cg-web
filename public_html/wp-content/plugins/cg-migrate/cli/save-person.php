<?php

function cg_save_person(array $post_data = [], bool $use_slug = true, bool $create = true) {
  $id = $post_data['id'];
  if ($id) {
    $post = get_post($id);
  }
  if (!$post && $use_slug) {
    $post = get_post_by_name($post_data['slug'], 'person');
  }
  if ($post) {
    $post->post_name = $post_data['slug'];
    $post->post_title = $post_data['title'];
    $id = wp_update_post($post);  
  } elseif ($create) {
    $post_data = array(
      'post_type' => 'person',
      'post_title' => sanitize_text_field($post_data['title']),
      'post_name' => sanitize_text_field($post_data['slug']),
      'post_status'   => 'publish',
      'post_author'   => get_current_user_id(),
      'post_date'     => current_time('mysql'),    
    );
    $id = wp_update_post($post);
  }

  return $id;
}