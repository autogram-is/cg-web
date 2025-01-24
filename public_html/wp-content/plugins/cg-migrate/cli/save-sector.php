<?php

function cg_save_sector(array $post_data = [], bool $use_slug = true, bool $create = true) {

  $id = cg_save_base('sector', $post_data, true, true);


  if ($id) {
    if ($post_data['locale']) {
      update_field('locale', $post_data['locale'] ?? NULL, $id);
    }
  } else {
    WP_CLI::log("Could not create sector '". $post_data['title'] ."'");
  }


  return $id;
}