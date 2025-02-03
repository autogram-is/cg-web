<?php
// Turn off theme support for block patterns
add_action('after_setup_theme', function() {
  remove_theme_support( 'core-block-patterns' );
});

// Disable remotely loaded patterns from wordpress.org
add_filter( 'should_load_remote_block_patterns', '__return_false' );

// Loop through and disable every block pattern; conditionals can be added here.
add_action( 'init', function() {
  if (!class_exists('WP_Block_Patterns_Registry')) {
    return;
  }
  $patterns = (array) WP_Block_Patterns_Registry::get_instance()->get_all_registered();
  foreach ( $patterns as $pattern ) {
    unregister_block_pattern($pattern['name']);
  }
});

// Add a custom category
add_filter( 'block_categories_all', function($categories) {
  $text = array_slice($categories, 0, 1);  // Get the first item
  $others = array_slice($categories, 1);   // Get the rest of the array

  $custom[] = array(
    'slug'  => 'cg-index',
    'title' => __( 'Cumming Group Content Lists', 'cumminggroup' ),
  );

  $custom[] = array(
    'slug'  => 'cg-component',
    'title' => __( 'Cumming Group Page Components', 'cumminggroup' ),
  );

  // Merge them with the new entry in between
  $categories = array_merge($text, $custom, $others);
  return $categories;
});

// See https://developer.wordpress.org/block-editor/reference-guides/filters/block-filters/

// An opportunity to edit existing block metadata.
// This fires for every individual block.
add_filter('block_type_metadata', function($metadata) {
  // Check if 'supports' key exists.
  if (isset( $metadata['supports'] ) && isset( $metadata['supports']['color'] ) ) {
    // Remove Background color and Gradients support.
    $metadata['supports']['color']['background'] = false;
    $metadata['supports']['color']['gradients']  = false;
    $metadata['supports']['color']['text']  = false;
  }
  if (isset( $metadata['supports'] ) && isset( $metadata['supports']['typography'] ) ) {
    $metadata['supports']['typography']['fontSize'] = false;
    $metadata['supports']['typography']['dropCap'] = false;
    $metadata['supports']['typography']['fontStyle'] = false;
    $metadata['supports']['typography']['fontWeight'] = false;
    $metadata['supports']['typography']['letterSpacing'] = false;
    $metadata['supports']['typography']['lineHeight'] = false;
    $metadata['supports']['typography']['textColumns'] = false;
    $metadata['supports']['typography']['textDecoration'] = false;
    $metadata['supports']['typography']['textTransform'] = false;
    $metadata['supports']['typography']['writingMode'] = false;
    $metadata['supports']['typography']['fontSizes'] = false;
  }

  return $metadata;
});

// An opportunity to edit existing block metadata.
// This fires for every individual block.
add_filter( 'block_type_metadata_settings', function( $settings, $metadata = null ) {
  return $settings;
});
