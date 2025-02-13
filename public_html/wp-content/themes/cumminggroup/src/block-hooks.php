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
   * List treatments
   */
  register_block_style('core/list',
    array('name' => 'client-list','label' => __( 'Plus', 'textdomain' ), 'is_default' => false)
  );
  register_block_style('core/list',
    array('name' => 'cta-list','label' => __( 'CTAs', 'textdomain' ), 'is_default' => false)
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

}

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
 * Register custom block type variations.
 *
 * @param array    $context array of Twig context variables.
 * @return array
 */
function cg_populate_custom_block_data($context) {
  $post_id =  $context['post_id'];
  $slug =  $context['slug'];
  $is_preview =  $context['is_preview'];
  $fields =  $context['fields'];
  $block =  $context['block'];
  $classes = $context['classes'];

  $posts = [];

  if ('cg-list-events' === $slug) {
    // Build past or future event list, populate $context['posts']
    if ($fields['window'] === 'upcoming') {
      $query_options = array(
        'meta_query' => array(
          array(
            'key' => 'start_date',
            'value' => current_time('mysql'),
            'compare' => '>=',
            'type' => 'DATE'
          )
        ),
        'post_type'     => 'event',
        'posts_per_page' => $fields['limit'] ?? 10,
        'orderby' => 'start_date'
      );
    } else {
      $query_options = array(
        'meta_query' => array(
          array(
            'key' => 'start_date',
            'value' => current_time('mysql'),
            'compare' => '<=',
            'type' => 'DATE'
          )
        ),
        'post_type'     => 'event',
        'posts_per_page' => $fields['limit'] ?? 20,
        'orderby' => 'start_date'
      );
      if ($filds['pagination'] ?? false) {
        $query_options['paged'] = get_query_var('paged') ? get_query_var('paged') : 1;
      }
    }
    $context['posts'] = Timber::get_posts($query_options);
    
  } else if ('cg-list-news' === $slug) {
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

  } else if ('cg-list-team' === $slug) {
    // Build people, falling back on current post's related people, to populate $context['posts']
    $context['posts'] = $posts;

  } else if ('cg-list-projects' === $slug) {
    // Build projects to populate $context['posts']
    $context['posts'] = $posts;

  } else if ('cg-list-regions' === $slug) {
    // Build office list for the selected region and its zub-regions
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
  }

  return $context;
}