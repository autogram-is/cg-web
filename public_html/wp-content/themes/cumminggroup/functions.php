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

require_once __DIR__ . '/src/CGContent.php';
require_once __DIR__ . '/src/CGNews.php';
require_once __DIR__ . '/src/CGPortfolio.php';
require_once __DIR__ . '/src/CGProject.php';
require_once __DIR__ . '/src/CGOffice.php';
require_once __DIR__ . '/src/CGPerson.php';

require_once __DIR__ . '/src/CGTwigFilters.php';
require_once __DIR__ . '/src/block-hooks.php';
require_once __DIR__ . '/src/embed-hooks.php';

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
    'page' => CGContent::class,
  
    'sector' => CGPortfolio::class,
    'service' => CGPortfolio::class,
    'project' => CGProject::class,

    'person' => CGPerson::class,
    'office' => CGOffice::class,

    'post' => CGNews::class,
    'event' => CGNews::class,
    'report' => CGNews::class,
  ];

  return array_merge($classmap, $custom_classmap);
});

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
  if (is_int($post)) {
    $psot = get_post($post);
  }
  $templates = [];
  if ($directory && !str_ends_with($directory, '/')) $directory .= '/';
  if (is_null($post)) {
    // Something has gone wrong here; we should never try to get the template hierarchy for NULL.
    return [$directory . 'default.twig'];
  }
  
  $templates[] = $directory . $post->post_type . '-' . $post->ID . '.twig';

  if ($post->post_type === 'post') {
    $categories = get_the_terms($post->ID, 'news-category');
    if ($categories != NULL && !is_wp_error($categories) && count($categories) > 0) {
      $context['news_category'] = $categories[0];
      $templates[] = $directory . $post->post_type . '-' . $categories[0]->slug . '.twig';
    }
  }

  $templates[] = $directory . $post->post_type . '.twig';
  $templates[] = $directory . $post->post_type . '-' . $post->post_name . '.twig';
  $templates[] = $directory . 'default.twig';

  return $templates;
}

Routes::map(':type/:slug/portfolio', 'cg_project_portfolio_router');
Routes::map(':type/:slug/portfolio/page/:pg', 'cg_project_portfolio_router');

function cg_project_portfolio_router($params) {
  $types = array(
    'locations' => 'office',
    'sectors'   => 'sector',
    'services'  => 'service',
  );
  $type = $types[$params['type']] ?? false;
  $slug = $params['slug'];
  $pg = isset($params['pg']) ? $params['pg'] : 1;
  if ($type) {
    $query = "&post_type=$type&name=$slug&page=$pg";
    $data = array('portfolio' => TRUE);
    Routes::load('single.php', $data, $query, 200);  
  } else {
    Routes::load('404.php', null, false, 404);  
  }
}

function cg_archive_count( $query ) {
if ( $query->is_archive() && $query->is_main_query() && !is_admin() ) {
        $query->set( 'posts_per_page', 15 );
    }
}
add_action( 'pre_get_posts', 'cg_archive_count' );
