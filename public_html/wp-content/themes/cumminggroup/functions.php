<?php

/**
 * Cumming Group custom theme
 * 
 * This file is primarily responsible for pulling in other includes that
 * do the heavy lifting of defining content types, etc.
 */

use Timber\Timber;
use Timber\PostQuery;

// Load Composer dependencies.
require_once __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/src/CGSite.php';

require_once __DIR__ . '/src/CGPost.php';
require_once __DIR__ . '/src/CGOffice.php';
require_once __DIR__ . '/src/CGPerson.php';
require_once __DIR__ . '/src/CGProject.php';

require_once __DIR__ . '/src/CGTwigFilters.php';
require_once __DIR__ . '/src/block-hooks.php';

Timber::init();

// Sets the directories (inside your theme) to find .twig files.
Timber::$dirname = [ 'templates', 'views' ];

new CGSite();

// Set the directory Timber-generated blocks are stored in
add_filter('timber/acf-gutenberg-blocks-templates', function () {
  return ['views/blocks']; // default: ['views/blocks']
});

add_filter('timber/post/classmap', function ($classmap) {
  $custom_classmap = [
    'page' => CGPost::class,
    'post' => CGPost::class,
    'event' => CGPost::class,
    'sector' => CGPost::class,
    'service' => CGPost::class,
    'report' => CGPost::class,

    'person' => CGPerson::class,
    'office' => CGOffice::class,
    'project' => CGProject::class,
  ];

  return array_merge($classmap, $custom_classmap);
});

function pluralize(int | array | PostQuery $items, string $singular, string $plural=null) {
  if (is_int($items) && $items < 2) return $singular;
  if(count($items)==1 || !strlen($singular)) return $singular;
  if($plural!==null) return $plural;

  $last_letter = strtolower($singular[strlen($singular)-1]);
  switch($last_letter) {
      case 'y':
          return substr($singular,0,-1).'ies';
      case 's':
          return $singular.'es';
      default:
          return $singular.'s';
  }
}

function get_post_a_tag($post) {
  $href = get_permalink($post);
  $title = get_the_title($post);
  return '<a href="'.$href.'">'.$title.'</a>';
}

function snake_to_title_case(string $input) {
  return mb_convert_case(str_replace('_', " ", $input), MB_CASE_TITLE_SIMPLE);
}

add_filter('timber/acf-gutenberg-blocks-templates', function () {
  return ['blocks']; // default: ['views/blocks']
});

// Add style overrides for Gutenberg.
add_action('enqueue_block_editor_assets', 'gutenberg_editor_assets');

function gutenberg_editor_assets() {
  wp_enqueue_style('my-gutenberg-editor-styles', get_theme_file_uri('editor-override.css'), FALSE);
}

add_filter('use_block_editor_for_post_type', 'cg_disable_gutenberg', 10, 2);
function cg_disable_gutenberg($current_status, $post_type) {
  // Use your post type key instead of 'product'
  if ($post_type === 'office') return false;
  if ($post_type === 'person') return false;
  if ($post_type === 'event') return false;
  return $current_status;
}

function is_news_category() {
  global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '3.1.0' );
		return false;
	}

	return get_query_var('news-category') ?? false;
}


/**
 * Returns an array of potential templates for a post, optionally in a given subdirectory.
 * 
 * For post ID 1 with a title 'My Post', the following array will be returned:
 * 
 * - post-1.twig
 * - post-category-slug.twig
 * - post-my-post.twig
 * - post.twig
 * - default.twig
 * 
 * @return array
 */
function cg_post_templates($post, $directory = '') {
  $templates = [];
  if ($directory && !str_ends_with($directory, '/')) $directory .= '/';
  
  $templates[] = $directory . $post->post_type . '-' . $post->ID . '.twig';

  if ($post->post_type === 'post') {
    $categories = get_the_terms($post->ID, 'news-category');
    if ($categories != NULL && !is_wp_error($categories) && count($categories) > 0) {
      $context['news_category'] = $categories[0];
      $templates[] = $post->post_type . '-' . $categories[0]->slug . '.twig';
    }
  }

  $templates[] = $directory . $post->post_type . '.twig';
  $templates[] = $directory . $post->post_type . '-' . $post->post_name . '.twig';
  $templates[] = $directory . 'default.twig';

  return $templates;
}
