<?php

function get_post_by_meta($key, $value = null) {
  $args = array(
    'meta_key' => $key,
  );

  if ($value) {
    $args['meta_query'] = array(
      array(
          'key' => 'cp_annonceur',
          'value' => 'professionnel',
          'compare' => '=',
      )
    );
  }
  $query = new WP_Query($args); 
}