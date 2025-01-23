<?php

function cg_save_office(array $post_data = [], bool $use_slug = true, bool $create = true) {
  $id = cg_save_base('office', $post_data, true, true);

  if ($id) {
    update_field('location', $post_data['location'] ?? NULL, $id);
    update_field('email', $post_data['email'] ?? NULL, $id);
    update_field('phone', $post_data['phone'] ?? NULL, $id);
    update_field('address', $post_data['address'] ?? NULL, $id);
  }

  return $id;
}