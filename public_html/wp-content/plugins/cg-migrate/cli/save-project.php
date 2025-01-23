<?php

function cg_save_project(array $post_data = [], bool $use_slug = false, bool $create = false) {
  $id = cg_save_base('project', $post_data, false, false);

  if ($id) {
    update_field('client', $post_data['client'] ?? NULL, $id);
    update_field('facility', $post_data['facility'] ?? NULL, $id);
    update_field('location', $post_data['location'] ?? NULL, $id);
    update_field('start_date', $post_data['start_date'] ?? NULL, $id);
    update_field('end_date', $post_data['end_date'] ?? NULL, $id);

    update_field('owner', $post_data['owner'] ?? NULL, $id);
    update_field('architect', $post_data['architect'] ?? NULL, $id);
    update_field('vendors', $post_data['vendors'] ?? NULL, $id);
    update_field('contractors', $post_data['contractors'] ?? NULL, $id);
  }

  return $id;
}