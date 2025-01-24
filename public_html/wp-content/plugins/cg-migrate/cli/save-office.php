<?php

function cg_save_office(array $post_data = [], bool $use_slug = true, bool $create = true) {
  $post = cg_save_base('office', $post_data, true, true);

  if ($post) {
    update_field('location', $post_data['location'] ?? NULL, $post->ID);
    update_field('email', $post_data['email'] ?? NULL, $post->ID);
    update_field('phone', $post_data['phone'] ?? NULL, $post->ID);
    update_field('address', $post_data['address'] ?? NULL, $post->ID);
  }

  return $post;
}