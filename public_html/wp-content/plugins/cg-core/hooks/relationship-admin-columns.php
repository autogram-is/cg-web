<?php

/**
 * Hard-coded custom admin columns for the post relationships
 * that often have too many values to display meaningfully.
 * 
 * Instead, each of these values is used to produce a *count* of
 * linked items that can be used for display or sorting.
 */

add_filter( 'manage_project_posts_columns', function($columns) {
  $columns['gallery'] = 'Photos';
  $columns['related_news'] = 'News';
  return $columns;
});

add_filter( 'manage_office_posts_columns', function($columns) {
  $columns['projects'] = 'Projects';
  $columns['related_news'] = 'News';
  return $columns;
});

add_filter( 'manage_sector_posts_columns', function($columns) {
  $columns['projects'] = 'Projects';
  $columns['related_news'] = 'News';
  return $columns;
});

add_filter( 'manage_service_posts_columns', function($columns) {
  $columns['projects'] = 'Projects';
  $columns['related_news'] = 'News';
  return $columns;
});

add_filter( 'manage_news_posts_columns', function($columns) {
  $columns['related_portfolio_items'] = 'Related Items';
  return $columns;
});

add_filter( 'manage_report_posts_columns', function($columns) {
  $columns['related_portfolio_items'] = 'Related Items';
  return $columns;
});

add_filter( 'manage_event_posts_columns', function($columns) {
  $columns['related_portfolio_items'] = 'Related Items';
  return $columns;
});

add_action( 'manage_posts_custom_column' , function( $column, $post_id ) {
  switch ( $column ) {
  // in this example, a Product has custom fields called 'product_number' and 'product_name'
    case 'projects':
    case 'gallery':
    case 'related_news':
    case 'related_portfolio_items':
      $ids = get_field($column, $post_id);
      if (is_array($ids)) echo count($ids);
      break;
    }
}, 10, 2 );
