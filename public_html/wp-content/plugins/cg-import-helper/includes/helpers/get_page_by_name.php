<?php

function get_page_by_name($post_name, $post_type = 'post', $output = OBJECT) {
  global $wpdb;
  $post = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_name = %s AND post_type= %s", $post_name, $post_type ));
  if ( $post ) return get_post($post, $output);
  return null;
}
