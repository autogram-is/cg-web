<?php

use Timber\Timber;

/**
 * Register Cumming Group styles for Gutenberg blocks.
 */
function cg_register_block_styles() {
  /**
   * Additional Styles for the core heading block
   */
  register_block_style('core/heading',
    array('name' => 'type-display', 'label' => __( 'Display', 'textdomain' ), 'is_default' => false)
  );
  register_block_style('core/heading',
    array('name' => 'hed-accent', 'label' => __( 'Accented', 'textdomain' ), 'is_default' => false)
  );

  /**
   * Additional Styles for the core heading block
   */
  register_block_style('acf/cg-pullquote',
    array('name' => 'bg-deep-steel','label' => __( 'Dark Background', 'textdomain' ), 'is_default' => false)
  );

  /**
   * Additional Styles for the Cumming Group statistics block
   */
  register_block_style('acf/cg-statistics',
    array('name' => 'horizontal','label' => __( 'Horizontal', 'textdomain' ), 'is_default' => false)
  );
  register_block_style('acf/cg-statistics',
    array('name' => 'hero','label' => __( 'Hero', 'textdomain' ), 'is_default' => false)
  );
  register_block_style('acf/cg-statistics',
    array('name' => 'standout','label' => __( 'Standout', 'textdomain' ), 'is_default' => false)
  );

  /**
   * List treatments
   */
  register_block_style('core/list',
    array('name' => 'client-list','label' => __( 'Clients', 'textdomain' ), 'is_default' => false)
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
}

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
      'post_type' => 'event',
      'posts_per_page' => $fields['limit'] ?? 10,
      'category_name' => $fields['category'] ?? NULL,
    );
    if ($fields['paged']) {
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