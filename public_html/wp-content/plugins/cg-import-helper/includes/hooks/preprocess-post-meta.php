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
 * Modify meta keys and values before the post is saved.
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
			$postmeta[$i]['value'] = $value;
		}
	}
  */

  return $postmeta;
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
function cgih_preprocess_post_meta_key($key, $post_id, $post = null) {
  // return FALSE to ignore metadata
  // return a new string to remap the key to a new one
  // return unmodified to pass through.

  // These keys are vestigial remnants of old plugins; we're ignoring them
  // for the time being.
  if (str_starts_with($key, 'pyre_')) return false;
  if (str_starts_with($key, 'sbg_')) return false;

  // _Event meta keys are from Tribal Events; we pull in start and end
  // dates, links, etc. 
  if ($key === '_EventVenueID') { return 'cg_import_venue_id'; }
  if ($key === '_EventStartDate') { return 'start_date'; }
  if ($key === '_EventEndDate') { return 'end_date'; }
  if ($key === '_EventURL') { return 'link'; }
  if (str_starts_with($key, '_Event')) { return false; }
  if (str_starts_with($key, '_tribe')) { return false; }

  return $key;
}
add_filter('import_post_meta_key', 'cgih_preprocess_post_meta_key', 10, 2);
