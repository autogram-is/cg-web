<?php

function cg_save_project(array $post_data = [], bool $use_slug = false, bool $create = false) {
  if (!array_key_exists('migration_status', $post_data)) $post_data['migration_status'] = 'auto-migrated';

  $post = cg_save_base('project', $post_data, false, false);


  if ($post) {
    _map_project_relationships($post_data);

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

    // Key relationships
    if (array_key_exists('sectors', $post_data) && $post_data['sectors']) update_field('sectors', $post_data['sectors'] ?? NULL, $post->ID);
    if (array_key_exists('services', $post_data) && $post_data['services']) update_field('services', $post_data['services'] ?? NULL, $post->ID);
    if (array_key_exists('offices', $post_data) && $post_data['offices']) update_field('offices', $post_data['offices'] ?? NULL, $post->ID);
    if (array_key_exists('people', $post_data) && $post_data['people']) update_field('people', $post_data['people'] ?? NULL, $post->ID);
  
    // Case study PDF
    if (array_key_exists('case_study_pdf', $post_data) && $post_data['case_study_pdf']) update_field('case_study_pdf', $post_data['case_study_pdf'] ?? NULL, $post->ID);

  } else {
    WP_CLI::log("Could not update project '". $post_data['title'] ."'");
  }

  return $post;
}

function setKey($key, $source, &$target) {
  if (array_key_exists($key, $source) && $source[$key] !== NULL) {
    $target[$key] = $source[$key];
  }
}

function _map_project_relationships(&$post_data) {
  if ($post_data['title'] ?? false) $post_data['post_title'] = $post_data['title'];

  $service_ids = _cols_to_id_array($post_data, 'service', ['service1', 'service2', 'service3', 'service4']);
  if (count($service_ids) > 0) {
    $post_data['services'] = $service_ids;
  }

  $sector_ids = _cols_to_id_array($post_data, 'sector', ['sector1', 'sector2', 'sector3', 'sector4']);
  if (count($sector_ids) > 0) {
    $post_data['sectors'] = $sector_ids;
  }

  $office_ids = _cols_to_id_array($post_data, 'office', ['office1', 'office2', 'office3']);
  if (count($office_ids) > 0) {
    $post_data['offices'] = $office_ids;
  }

  if (key_exists('case_study_id', $post_data) && $post_data['case_study_id']) {
    // Case study processing goes here
  }
}