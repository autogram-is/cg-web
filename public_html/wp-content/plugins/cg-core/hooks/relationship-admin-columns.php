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

add_filter( 'manage_post_posts_columns', function($columns) {
  $columns['related_portfolio_items'] = 'Related Items';
  return $columns;
});

add_filter( 'manage_report_posts_columns', function($columns) {
  $columns['related_portfolio_items'] = 'Related Items';
  return $columns;
});

add_filter( 'manage_event_posts_columns', function($columns) {
  $columns['related_portfolio_items'] = 'Related Items';
  $columns['attendees'] = 'Attendees';
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
    case 'attendees':
      $ids = get_field('people', $post_id);
      if (is_array($ids)) echo count($ids);
      break;
    }
}, 10, 2 );


add_filter( 'gettext', function( $translated_text, $untranslated_text, $domain ) {
	if ( 'acf-quickedit-fields' !== $domain ) {
		return $translated_text;
	}
  if ('(No value)' === $untranslated_text) {
    return "";
  }
  return $translated_text;
}, 10, 3 );
