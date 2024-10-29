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
 * Validate a meta key before it's actually saved. This gives an opportunity
 * to rename incoming meta keys.
 *
 * @param mixed $postmeta Meta property array.
 * @param mixed $post_id Post ID, if one has been assigned.
 * @param mixed $post Raw post object before insertion.
 */
function cgih_preprocess_post_meta($postmeta, $post_id, $post = null) {
  /** For example…
  foreach($postmeta as $i => $meta) {
		if($meta['key'] == 'some_key') {
			$value = $meta['value'];
      // Do stuff here
			$post_meta[$i]['value'] = $value;
		}
	}

	return $postmeta;
  */
}
add_filter('wp_import_post_meta', 'cgih_preprocess_post_meta', 10, 2);


/**
 * Validate a meta key before it's actually saved. This gives an opportunity
 * to rename incoming meta keys.
 *
 * @param string $key Meta property key.
 * @param mixed $post_id Post ID, if one has been assigned.
 * @param mixed $post Raw post object before insertion.
 */
function cgih_preprocess_post_meta_key($key, $post_id, $post) {
  // return FALSE to ignore metadata
  // return a new string to remap the key to a new one
  // return unmodified to pass through.
  return $key;
}
add_filter('import_post_meta_key', 'cgih_preprocess_post_meta_key', 10, 2);
