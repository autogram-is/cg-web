<?php

function cg_save_project(array $post_data = [], bool $use_slug = false, bool $create = false) {
  $post = cg_save_base('project', $post_data, false, false);

  if ($post) {
    update_field('client', $post_data['client'] ?? NULL, $post->ID);
    update_field('facility', $post_data['facility'] ?? NULL, $post->ID);
    update_field('location', $post_data['location'] ?? NULL, $post->ID);
    update_field('start_date', $post_data['start_date'] ?? NULL, $post->ID);
    update_field('end_date', $post_data['end_date'] ?? NULL, $post->ID);

    update_field('owner', $post_data['owner'] ?? NULL, $post->ID);
    update_field('architect', $post_data['architect'] ?? NULL, $post->ID);
    update_field('vendors', $post_data['vendors'] ?? NULL, $post->ID);
    update_field('contractors', $post_data['contractors'] ?? NULL, $post->ID);
  }

  return $post;
}