<?php

/**
 * Make the 'news-category' taxonomy available as a permalink tag
 */
add_filter("available_permalink_structure_tags", function($available_tags) {
  $available_tags['news-category'] = 'News Category';
  return $available_tags;
}, 10, 1);

/**
 * Add a cluster of rewrite rules for several "magic" listing URLs.
 * These are highly dependent on /news-insights, /events, and /market-analysis pages existing.
 * 
 * The same may be necessary for /sectors, /services, and /portfolio (aka the project list)
 * if indexes are desired for those pages.
 * 
 * Ideally, this would be configurable using an Options page.
 */
add_action('init', function() {
  // Ensure pagination works for post types with hand-crafted index pages
  add_rewrite_rule('^(events|market-analysis|news-insights)/page/([0-9]+)?/?','index.php?pagename=$matches[1]&paged=$matches[2]','top');

  // Ensure taxonomy term pages work correctly for news category indexes
  add_rewrite_rule('^news-insights/([^/]*)/page/([0-9]+)?/?','index.php?news-category=$matches[1]&paged=$matches[2]','top');

  // Finally, supply a hard-coded rewrite rule for news URLs that avoids triggering on either top or mid level indexes.
  add_rewrite_rule('^news-insights/((?!page)[^/]*)/((?!page)[^/]*)/?','index.php?news-category=$matches[1]&post_type=post&name=$matches[2]','top');
});


/**
 * Rewrite news post URLs to replace the %news-category% placeholder
 */
add_filter('post_link', function($permalink, $post) {
  if ( false === strpos( $permalink, '%news-category%' ) ) {
    return $permalink;
  }
  $terms = wp_get_post_terms( $post->ID, 'news-category' );
  if ( 0 < count( $terms ) ) {
    $newscat = $terms[0]->slug;
  } else {
    $newscat = 'unknown';
    $newscat = urlencode( $newscat );
  }
  $permalink = str_replace('%news-category%', $newscat , $permalink );
  return $permalink;
}, 10, 2 );
