<?php

function get_post_by_meta($type = 'post', $key, $value = null, $limit = null) {
  $args = array(
    'post_type' => $type,
  );

  if ($value) {
    $args['meta_query'] = array(
      array(
        'key' => $key,
        'value' => $value,
        'compare' => '=',
      )
    );
  } else {
    $args['meta_query'] = array(
      array(
        'key' => $key,
        'compare' => 'IS NOT NULL',
      )
    );
  }
  if ($limit) {
    $args['posts_per_page'] = $limit;
  }

  $query = new WP_Query($args);
  return $query->get_posts();
}
