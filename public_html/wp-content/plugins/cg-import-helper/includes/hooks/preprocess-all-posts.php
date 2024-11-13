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

// Remove items from the post list if we've marked them
function cgih_preprocess_post_list($posts) {
  // Filter this list, removing items from the list we don't want to import.

  // Nothing yet, but we're ready if it's needed.
  return $posts;
}
add_filter('wp_import_posts', 'cgih_preprocess_post_list', 10, 2);
