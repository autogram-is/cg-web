<?php

function cg_save_event(array $post_data = [], bool $use_slug = false, bool $create = false) {
  $post = cg_save_base('event', $post_data, false, false);

  if ($post) {
    // Update additional fields here
  }

  return $post;
}