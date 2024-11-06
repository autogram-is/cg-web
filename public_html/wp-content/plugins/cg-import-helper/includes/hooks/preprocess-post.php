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
 * Default post processing function.
 *
 * @param string $postdata The raw data used to produce the post.
 * @param string $post The resulting post, not yet saved.
 */
function cgih_preprocess_post($postdata, $post) {
  // Most of what we care about will be done in the raw stage;
  // This gives us one more chance to modify things before
  // saving, though.
  $func = 'cgih_preprocess_post_import_' . $postdata['post_type'];

  if (function_exists($func)) {
    $postdata = $func($postdata, $post);
  }

  return $postdata;
}

add_filter('wp_import_post_data_processed', 'cgih_preprocess_post', 10, 2);
