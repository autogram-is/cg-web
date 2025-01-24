<?php

function cg_save_service(array $post_data = [], bool $use_slug = true, bool $create = true) {
  $post = cg_save_base('service', $post_data, true, true);

  if ($post) {
    if ($post_data['locale']) {
      update_field('locale', $post_data['locale'] ?? NULL, $post->ID);
    }
  }

  return $post;
}