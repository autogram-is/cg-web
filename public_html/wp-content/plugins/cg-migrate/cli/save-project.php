<?php

function cg_save_project(array $post_data = [], bool $use_slug = false, bool $create = false) {
  if (!array_key_exists('migration_status', $post_data)) $post_data['migration_status'] = 'auto-migrated';

  $post = cg_save_base('project', $post_data, false, false);

  if ($post) {
    $facts = [];
    setKey('client', $post_data, $facts);
    setKey('facility', $post_data, $facts);
    setKey('location', $post_data, $facts);
    setKey('start_date', $post_data, $facts);
    setKey('end_date', $post_data, $facts);

    setKey('owner', $post_data, $facts);
    setKey('architect', $post_data, $facts);
    setKey('vendors', $post_data, $facts);
    setKey('contractors', $post_data, $facts);

    update_field('facts', $facts, $post->ID);

    // Important relationships
    update_field('sectors', $post_data['sectors'] ?? NULL, $post->ID);
    update_field('services', $post_data['services'] ?? NULL, $post->ID);
    update_field('offices', $post_data['offices'] ?? NULL, $post->ID);
    update_field('people', $post_data['people'] ?? NULL, $post->ID);
  } else {
    WP_CLI::log("Could not update project '". $post_data['title'] ."'");
  }

  function setKey($key, $source, &$target) {
    if (array_key_exists($key, $source) && $source[$key] !== NULL) {
      $target[$key] = $source[$key];
    }
  }

  return $post;
}