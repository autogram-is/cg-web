<?php

function cg_save_event(array $post_data = [], bool $use_slug = false, bool $create = false) {
  $id = cg_save_base('event', $post_data, false, false);

  if ($id) {
  }

  return $id;
}