<?php

function cg_shortcode_index($atts) {
  // Lists all items of a given type, with assorted options.

  $query_options = array(
    'post_type'     => _attr($atts, 'type', 'post'),
    'posts_per_page' => _attr($atts, 'limit', 20),
    'category_name' => _attr($atts, 'category'),
  );

  if (_attr($atts, 'paged', true)) {
    $query_options['paged'] = get_query_var('paged') ? get_query_var('paged') : 1;
  }

  $data = array(
    'posts' => Timber::get_posts($query_options),
    'style' => _attr($atts, 'style')
  );

  // This time we use Timber::compile since shortcodes should return the code
  return Timber::compile('shortcodes/posts-index.twig', $data);
}

function cg_shortcode_events_upcoming($atts) {
  // Lists all events whose start_date is in the future.

  $query_options = array(
    'post_type'     => 'event',
    'posts_per_page' => _attr($atts, 'limit', 10),
  );

  $data = array(
    'posts' => Timber::get_posts($query_options),
  );

  // This time we use Timber::compile since shortcodes should return the code
  return Timber::compile('shortcodes/events-upcoming.twig', $data);
}

function cg_shortcode_events_past($atts) {
  // Lists all events whose end_date is in the past.
  $query_options = array(
    'post_type'     => 'event',
    'posts_per_page' => _attr($atts, 'limit', 20),
    'paged' => get_query_var('paged') ? get_query_var('paged') : 1,
  );

  $data = array(
    'posts' => Timber::get_posts($query_options),
  );

  // This time we use Timber::compile since shortcodes should return the code
  return Timber::compile('shortcodes/events-past.twig', $data);
}

function cg_shortcode_region_offices($atts) {
  // Gets all child taxonomies of the given tag, then gets all offices tagged with those taxonomies.
  // Loads those offices and passes them to the template.

  $zone_slug = _attr($atts, 'zone');
  $zone = term_exists($zone_slug, 'region');
  $zone_id = $zone['term_id'];
  $region_ids = get_term_children($zone_id, 'region');

  $query_options = array(
    'post_type' => 'office',
    'posts_per_page' => -1,
    'tax_query' => array(
      array(
        'taxonomy' => 'region',
        'field' => 'id',
        'terms' => $region_ids,
        'operator' => 'IN',
      )
    )
  );
    
  $data = array(
    'posts' => Timber::get_posts($query_options),
    'heading' => _attr($atts, 'heading'),
  );

  // This time we use Timber::compile since shortcodes should return the code
  return Timber::compile('shortcodes/region-offices.twig', $data);
}

function _attr($attr = [], $prop = NULL, $default = null) {
  if (array_key_exists($prop, $attr)) {
    $value = sanitize_text_field($attr[$prop]);
  }
  return $value ?? $default;
}