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
  // Remap old post types to new ones
  $post_type = isset($post['post_type']) ? $post['post_type'] : 'post';
  $post['post_type'] = _cgih_map_post_type($post);

  // Convert any fusion bracket-markup into XML-parsable tags for downstream processing
  $post['post_content'] = cgih_fusion_unbracket($post['post_content']);

  // If a dedicated preprocessing hook for this post type exists, call it.
  $func = 'cgih_preprocess_raw_' . $post['post_type'];
  if (function_exists($func)) {
    $post = $func($post);
  }

  return $post;
}
add_filter('wp_import_post_data_raw', 'cgih_preprocess_post_raw', 10, 2);

function _cgih_map_post_type($post) {
  $map = array(
    'avada_portfolio' => 'cg_project',  // Convert to new project format
    'tribe_events' => 'cg_event',       // Convert to new events; venues are merged as event properties
    'fusion_element' => 'SKIP',         // Skip for the time being.
    'slide' => 'SKIP',                  // Skip for the time being
  );

  foreach ($map as $old => $new) {
    if ($post['post_type'] == $old){
      return $new ? $new : 'SKIP';
    }
  }
  
  return $post['post_type'];
}
