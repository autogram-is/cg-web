<?php

use Timber\Timber;

/**
 * Forcibly remove 'block patterns' that define custom page layouts.
 */
add_action( 'init', function() {
  if (!class_exists('WP_Block_Patterns_Registry')) {
    return;
  }
  $patterns = (array) WP_Block_Patterns_Registry::get_instance()->get_all_registered();
  foreach ( $patterns as $pattern ) {
    unregister_block_pattern($pattern['name']);
  }
});


// Disable remotely loaded patterns from wordpress.org
add_filter( 'should_load_remote_block_patterns', '__return_false' );

// Add our custom block categories
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

// An opportunity to edit existing block metadata.
// This fires for every individual block.
add_filter( 'block_type_metadata_settings', function( $settings, $metadata = null ) {
  return $settings;
});

/**
 * Register Cumming Group styles for Gutenberg blocks.
 */
function cg_register_block_styles() {
  /**
   * Additional Styles for the core heading block
   */
  register_block_style('core/heading',
    array('name' => 'hed-accent', 'label' => __( 'Underline Accent', 'textdomain' ), 'is_default' => false)
  );
  register_block_style('core/heading',
    array('name' => 'hed-hatch','label' => __( 'Hatched Accent', 'textdomain' ), 'is_default' => false)
  );

  /**
   * Backdrop styles for CG quote blocks
   */
  register_block_style('acf/cg-pullquote',
    array('name' => 'bg-none','label' => __( 'No Backdrop', 'textdomain' ), 'is_default' => false)
  );
  register_block_style('acf/cg-pullquote',
    array('name' => 'bg-timeless-backdrop','label' => __( 'Timeless Backdrop', 'textdomain' ), 'is_default' => true)
  );
  register_block_style('acf/cg-pullquote',
    array('name' => 'bg-timeless-highlight','label' => __( 'Timeless Highlight', 'textdomain' ), 'is_default' => false)
  );
  register_block_style('acf/cg-pullquote',
    array('name' => 'bg-timeless-gold','label' => __( 'Timeless Gold', 'textdomain' ), 'is_default' => false)
  );
  register_block_style('acf/cg-pullquote',
    array('name' => 'bg-steel-shade','label' => __( 'Steel Shade', 'textdomain' ), 'is_default' => false)
  );
  register_block_style('acf/cg-pullquote',
    array('name' => 'bg-deep-steel','label' => __( 'Deep Steel', 'textdomain' ), 'is_default' => false)
  );
  register_block_style('acf/cg-pullquote',
    array('name' => 'bg-regal-purple','label' => __( 'Regal Purple', 'textdomain' ), 'is_default' => false)
  );
  register_block_style('acf/cg-pullquote',
    array('name' => 'bg-legacy-red','label' => __( 'Legacy Red', 'textdomain' ), 'is_default' => false)
  );

  /**
   * Backdrop styles for CG statistic blocks
   */
  register_block_style('acf/cg-statistics',
    array('name' => 'bg-none','label' => __( 'No Backdrop', 'textdomain' ), 'is_default' => false)
  );
  register_block_style('acf/cg-statistics',
    array('name' => 'bg-timeless-backdrop','label' => __( 'Timeless Backdrop', 'textdomain' ), 'is_default' => true)
  );
  register_block_style('acf/cg-statistics',
    array('name' => 'bg-timeless-highlight','label' => __( 'Timeless Highlight', 'textdomain' ), 'is_default' => false)
  );
  register_block_style('acf/cg-statistics',
    array('name' => 'bg-timeless-gold','label' => __( 'Timeless Gold', 'textdomain' ), 'is_default' => false)
  );
  register_block_style('acf/cg-statistics',
    array('name' => 'bg-steel-shade','label' => __( 'Steel Shade', 'textdomain' ), 'is_default' => false)
  );
  register_block_style('acf/cg-statistics',
    array('name' => 'bg-deep-steel','label' => __( 'Deep Steel', 'textdomain' ), 'is_default' => false)
  );
  register_block_style('acf/cg-statistics',
    array('name' => 'bg-regal-purple','label' => __( 'Regal Purple', 'textdomain' ), 'is_default' => false)
  );
  register_block_style('acf/cg-statistics',
    array('name' => 'bg-legacy-red','label' => __( 'Legacy Red', 'textdomain' ), 'is_default' => false)
  );

  /**
   * Backdrop styles for CG news blocks
   */
  register_block_style('acf/cg-list-news',
    array('name' => 'bg-none','label' => __( 'No Backdrop', 'textdomain' ), 'is_default' => true)
  );
  register_block_style('acf/cg-list-news',
    array('name' => 'bg-timeless-backdrop','label' => __( 'Timeless Backdrop', 'textdomain' ), 'is_default' => false)
  );
  register_block_style('acf/cg-list-news',
    array('name' => 'bg-timeless-highlight','label' => __( 'Timeless Highlight', 'textdomain' ), 'is_default' => false)
  );
  register_block_style('acf/cg-list-news',
    array('name' => 'bg-timeless-gold','label' => __( 'Timeless Gold', 'textdomain' ), 'is_default' => false)
  );
  register_block_style('acf/cg-list-news',
    array('name' => 'bg-steel-shade','label' => __( 'Steel Shade', 'textdomain' ), 'is_default' => false)
  );
  register_block_style('acf/cg-list-news',
    array('name' => 'bg-deep-steel','label' => __( 'Deep Steel', 'textdomain' ), 'is_default' => false)
  );
  register_block_style('acf/cg-list-news',
    array('name' => 'bg-regal-purple','label' => __( 'Regal Purple', 'textdomain' ), 'is_default' => false)
  );
  register_block_style('acf/cg-list-news',
    array('name' => 'bg-legacy-red','label' => __( 'Legacy Red', 'textdomain' ), 'is_default' => false)
  );

  /**
   * Backdrop styles for CG gallery blocks
   */
  register_block_style('acf/cg-gallery',
    array('name' => 'bg-none','label' => __( 'No Backdrop', 'textdomain' ), 'is_default' => false)
  );
  register_block_style('acf/cg-gallery',
    array('name' => 'bg-timeless-backdrop','label' => __( 'Timeless Backdrop', 'textdomain' ), 'is_default' => true)
  );
  register_block_style('acf/cg-gallery',
    array('name' => 'bg-timeless-highlight','label' => __( 'Timeless Highlight', 'textdomain' ), 'is_default' => false)
  );
  register_block_style('acf/cg-gallery',
    array('name' => 'bg-timeless-gold','label' => __( 'Timeless Gold', 'textdomain' ), 'is_default' => false)
  );

  /**
   * Backdrop styles for CG project gallery blocks
   */
  register_block_style('acf/cg-list-projects',
    array('name' => 'bg-none','label' => __( 'No Backdrop', 'textdomain' ), 'is_default' => false)
  );
  register_block_style('acf/cg-list-projects',
    array('name' => 'bg-timeless-backdrop','label' => __( 'Timeless Backdrop', 'textdomain' ), 'is_default' => true)
  );
  register_block_style('acf/cg-list-projects',
    array('name' => 'bg-timeless-highlight','label' => __( 'Timeless Highlight', 'textdomain' ), 'is_default' => false)
  );
  register_block_style('acf/cg-list-projects',
    array('name' => 'bg-timeless-gold','label' => __( 'Timeless Gold', 'textdomain' ), 'is_default' => false)
  );

  /**
   * Backdrop styles for featured Cards
   */
  register_block_style('acf/cg-featured-post',
    array('name' => 'bg-none','label' => __( 'No Backdrop', 'textdomain' ), 'is_default' => true)
  );
  register_block_style('acf/cg-featured-post',
    array('name' => 'bg-timeless-backdrop','label' => __( 'Timeless Backdrop', 'textdomain' ), 'is_default' => false)
  );
  register_block_style('acf/cg-featured-post',
    array('name' => 'bg-timeless-highlight','label' => __( 'Timeless Highlight', 'textdomain' ), 'is_default' => false)
  );
  register_block_style('acf/cg-featured-post',
    array('name' => 'bg-timeless-gold','label' => __( 'Timeless Gold', 'textdomain' ), 'is_default' => false)
  );
  register_block_style('acf/cg-featured-post',
    array('name' => 'bg-steel-shade','label' => __( 'Steel Shade', 'textdomain' ), 'is_default' => false)
  );
  register_block_style('acf/cg-featured-post',
    array('name' => 'bg-deep-steel','label' => __( 'Deep Steel', 'textdomain' ), 'is_default' => false)
  );
  register_block_style('acf/cg-featured-post',
    array('name' => 'bg-regal-purple','label' => __( 'Regal Purple', 'textdomain' ), 'is_default' => false)
  );
  register_block_style('acf/cg-featured-post',
    array('name' => 'bg-legacy-red','label' => __( 'Legacy Red', 'textdomain' ), 'is_default' => false)
  );


  /**
   * List treatments
   */

  register_block_style('core/list',
    array('name' => 'default','label' => __( 'Default', 'textdomain' ), 'is_default' => true)
  );
  register_block_style('core/list',
    array('name' => 'multicol','label' => __( 'Default (Multicol)', 'textdomain' ), 'is_default' => false)
  );
  register_block_style('core/list',
    array('name' => 'client-list','label' => __( 'Plus Markers', 'textdomain' ), 'is_default' => false)
  );
  register_block_style('core/list',
    array('name' => 'client-list-multicol','label' => __( 'Plus (Multicol)', 'textdomain' ), 'is_default' => false)
  );
  /**
   * Paragraphs
   */
  register_block_style('core/paragraph',
    array('name' => 'lede-header','label' => __( 'Lede', 'textdomain' ), 'is_default' => false)
  );
  register_block_style('core/paragraph',
    array('name' => 'lede-standout','label' => __( 'Standout Lede', 'textdomain' ), 'is_default' => false)
  );

    /**
   * Paragraphs
   */
  register_block_style('core/columns',
    array('name' => 'bg-none','label' => __( 'No Backdrop', 'textdomain' ), 'is_default' => true)
  );
  register_block_style('core/columns',
    array('name' => 'bg-timeless-backdrop','label' => __( 'Timeless Backdrop', 'textdomain' ), 'is_default' => false)
  );
  register_block_style('core/columns',
    array('name' => 'bg-timeless-highlight','label' => __( 'Timeless Highlight', 'textdomain' ), 'is_default' => false)
  );
  register_block_style('core/columns',
    array('name' => 'bg-timeless-gold','label' => __( 'Timeless Gold', 'textdomain' ), 'is_default' => false)
  );
  register_block_style('core/columns',
    array('name' => 'bg-steel-shade','label' => __( 'Steel Shade', 'textdomain' ), 'is_default' => false)
  );
  register_block_style('core/columns',
    array('name' => 'bg-deep-steel','label' => __( 'Deep Steel', 'textdomain' ), 'is_default' => false)
  );
  register_block_style('core/columns',
    array('name' => 'bg-regal-purple','label' => __( 'Regal Purple', 'textdomain' ), 'is_default' => false)
  );
  register_block_style('core/columns',
    array('name' => 'bg-legacy-red','label' => __( 'Legacy Red', 'textdomain' ), 'is_default' => false)
  );


}

function columns_block_classes( $block_content, $block ) {
    return preg_replace('/wp-block-columns/', 'wp-block-columns region', $block_content);
}
add_filter( 'render_block_core/columns', 'columns_block_classes', 10, 2 );

function column_block_classes( $block_content, $block ) {
    return preg_replace('/wp-block-column/', 'wp-block-column prose flow', $block_content);
}
add_filter( 'render_block_core/column', 'column_block_classes', 10, 2 );

/**
 * Disable jank typography features on core blocks.
 */
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

/**
 * Register custom block type variations.
 *
 * @param array    $variations Array of block type variations.
 * @param WP_Block $block_type  The block type.
 * @return array
 */
function cg_block_type_variations($variations, $block_type ) {
	if ( 'namespace/block-a' === $block_type->name ) {
		// Add variations to the `$variations` array.
	} elseif ( 'namespace/block-b' === $block_type->name ) {
		// Add more variations to the `$variations` array.
	}
	return $variations;
}


/**
 * Intercept data population for custom Cumming Group listing blocks,
 * populating them with the content selected in the block.
 * 
 * Context variables include:
 * 
 * - post_id:    The ID of the WP post whose content includes the block.
 * - slug:       The block's internal identifier
 * - is_preview: TRUE if the block is being previewed in the content editor
 * - fields:     The ACF fields attached to the block in question
 * - block:      The Wordpress Block instance, including internal variables etc.
 * - classes:    CSS classes to be attached to the block's wrapper element.
 *
 * @param array    $context array of Twig context variables.
 * @return array
 */
function cg_populate_custom_block_data($context) {
  $func = str_replace(['/', '-'], '_', $context['slug']) . "_block_data";
  if (function_exists($func)) {
    return $func($context);
  } else {
    return $context;
  }
}

/**
 * Our most complex listing block.
 * 
 * It's capable of listing events in three ways:
 * 
 * - All events
 * - Upcoming events
 * - Past events
 */
function cg_list_events_block_data($context) {
  extract($context, EXTR_SKIP);

  $all_query = array(
    'meta_query' => array(
      array('key'     => 'start_date')
    ),
    'post_type'      => 'event',
    'posts_per_page' => $fields['limit'] ?? 10,
    'orderby'        => 'start_date'
  );

  $upcoming_query = array(
    'meta_query' => array(
      array(
        'key'     => 'start_date',
        'value'   => current_time('mysql'),
        'compare' => '>=',
        'type'    => 'DATE'
      )
    ),
    'post_type'      => 'event',
    'posts_per_page' => $fields['limit'] ?? 10,
    'orderby'        => 'start_date'
  );

  $past_query = array(
    'meta_query' => array(
      array(
        'key'     => 'start_date',
        'value'   => current_time('mysql'),
        'compare' => '<=',
        'type'    => 'DATE'
      )
    ),
    'post_type'      => 'event',
    'posts_per_page' => $fields['limit'] ?? 20,
    'orderby'        => 'start_date'
  );

  if ($fields['pagination'] ?? false) {
    $past_query['paged'] = get_query_var('paged') ? get_query_var('paged') : 1;
    $all_query['paged'] = get_query_var('paged') ? get_query_var('paged') : 1;
  }

  if ($fields['window'] == 'past') {
    $context['posts'] = Timber::get_posts($past_query);

  } else if ($fields['window'] == 'upcoming') {
    $context['posts'] = Timber::get_posts($upcoming_query);

  } else  if ($fields['window'] == 'all') {
    $context['posts'] = Timber::get_posts($all_query);

  } else if ($fields['window'] == 'auto') {
    $posts = Timber::get_posts($upcoming_query);
    if (count($posts)) {
      $context['posts'] = $posts;
      $context['fields']['window'] = 'upcoming';
    } else {
      $context['posts'] = Timber::get_posts($past_query);
      $context['fields']['window'] = 'past';  
    }
  }

  return $context;
}


function cg_list_news_block_data($context) {
  extract($context, EXTR_SKIP);

  // Build news, optionally filtered by category to, to populate $context['posts']
  $query_options = array(
    'post_type' => 'post',
    'posts_per_page' => isset( $fields['limit'] ) && $fields['limit'] > 0 ? $fields['limit'] : 10,
    'cat' => $fields['categories'] ?? NULL
  );
  if ($fields['pagination']) {
    $query_options['paged'] = get_query_var('paged') ? get_query_var('paged') : 1;
  }
  $context['posts'] = Timber::get_posts($query_options);
  return $context;
}

function cg_list_reports_block_data($context) {
  extract($context, EXTR_SKIP);

  // Build a list of recent reports and return it in $context['posts']
  $query_options = array(
    'post_type' => 'report',
    'posts_per_page' => isset( $fields['limit'] ) && $fields['limit'] > 0 ? $fields['limit'] : 10,
  );
  if ($fields['pagination']) {
    $query_options['paged'] = get_query_var('paged') ? get_query_var('paged') : 1;
  }
  $context['posts'] = Timber::get_posts($query_options);
  return $context;
}

function cg_list_regions_block_data($context) {
  extract($context, EXTR_SKIP);
  $posts = [];

  foreach ($fields['regions'] as $item) {
    $region_ids = get_term_children($item['region'], 'region');
    $region_ids[] = $item['region'];

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

    $context['regions'][] = array(
      'heading' => $item['heading'] ?? '',
      'posts' => Timber::get_posts($query_options),
    );
  }
  return $context;
}
