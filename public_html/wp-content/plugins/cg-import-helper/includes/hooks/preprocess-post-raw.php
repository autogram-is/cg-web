<?php

/**
 * Plugin Name: CG Import Helper
 * Description: Cleans and tidies incoming imports 
 * Version: 1.0
 * Author: Autogram
 */

if (!defined('ABSPATH')) {
  exit;
}

/**
 * Make modifications to the post object before it's been processed or inserted.
 *
 * @param array $post Raw post information.
 */
function cgih_preprocess_post_raw($post) {
  // Remap old post types to new ones.
  $post_type = isset($post['post_type']) ? $post['post_type'] : 'post';
  $post['post_type'] = _cgih_map_post_type($post_type);

  // First we convert the fusion bracket-markup into XML-parsable tags
  $post = _cgih_clean_fusion_markup($post);

  $func = 'cgih_preprocess_raw_' . $post['post_type'];
  if (function_exists($func)) {
    $post = $func($post);
  }

  // To skip a post entirely, set $postdata['post_status'] to 'auto-draft';
  return $post;
}
add_filter('wp_import_post_data_raw', 'cgih_preprocess_post_raw', 10, 2);

function _cgih_map_post_type($type) {
  $map = array(
    'avada_portfolio' => 'cg_project', // Convert to new project format
    'slide' => 'SKIP',                 // Convert to project gallery images
    'tribe_events' => 'cg_event',      // Merge these into the events when they appear
    'tribe_venue' => 'SKIP',           // Merge these into the events when they appear
    'tribe_organizer' => 'SKIP',       // Merge these into the events when they appear
    'fusion _element' => 'SKIP',       // Edge cases; dragons.
  );

  foreach ($map as $old => $new) {
    if ($type == $old){
      return $new ? $new : 'SKIP';
    }
  }
  return $type;
}

function _cgih_clean_fusion_markup($postdata) {
  $postdata['post_content'] = cgih_fusion_unbracket($postdata['post_content']);
  return $postdata;
}