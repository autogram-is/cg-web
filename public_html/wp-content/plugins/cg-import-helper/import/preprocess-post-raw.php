<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Make modifications to the raw post object before it's inserted.
 *
 * @param array $postdata Data of the post being imported.
 * @param array $postmeta Meta data of the post being imported.
 */
function cgih_preprocess_post_raw($postdata, $postmeta) {
  // Remap old post types to new ones.
  $post_type = isset($postdata['post_type']) ? $postdata['post_type'] : 'post';
  $postdata['post_type'] = _cgih_map_post_type($post_type);

  // To skip a post entirely, set $postdata['post_status'] to 'auto-draft';
}
add_action('wp_import_post_data_raw', 'cgih_preprocess_post_raw', 10, 2);


function cgih_preprocess_meta_key($key, $post_id, $post) {
  // return FALSE to ignore metadata
  // return a new string to remap the key to a new one
  // return unmodified to pass through.
  return $key;
}
add_action('import_post_meta_key', 'cgih_preprocess_meta_key', 10, 2);


function _cgih_map_post_type($type) {
  $map = array(
    'avada_portfolio' => 'cg_project',
    'tribe_events' => 'cg_event'
  );

  foreach ($map as $old => $new) {
    if ($type == $old){
      return $new ? $new : 'SKIP';
    }
  }
  return $type;
}

function _cgih_clean_fusion_markup(&$postdata) {
  $postdata['post_content'] = cgih_fusion_unbracket($postdata['post_content']);
}