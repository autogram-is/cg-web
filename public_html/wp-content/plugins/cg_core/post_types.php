<?php

// Defines custom post types for the Cumming Group site
function cumminggroup_post_type() {
  $args = array();
  register_post_type( 'custom_post', $args ); 
}

add_action( 'init', 'cumminggroup_post_type' );