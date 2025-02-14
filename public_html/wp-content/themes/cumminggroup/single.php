<?php
/**
 * The Template for displaying all single posts
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

use Timber\Timber;

$context         = Timber::context();
$timber_post     = Timber::get_post();
$context['post'] = $timber_post;

$templates = array(
	'single-' . $timber_post->ID . '.twig',
	'single-' . $timber_post->post_type . '.twig',
	'single-' . $timber_post->slug . '.twig',
	'single.twig'
);

// Allow different news post templates depending on the news category;
// follows the pattern: `single-post-categoryslug.twig`
if ($timber_post->post_type === 'post') {
	$categories = get_the_terms($post->ID, 'news-category');
	if ($categories != NULL && !is_wp_error($categories) && count($categories) > 0) {
		$context['news-category'] = $categories[0];
		array_unshift($templates, 'single-' . $timber_post->post_type . '-' . $categories[0]->slug . '.twig');
	}
}

// Allow different template for office, service, and sector pages if the ?projects=all
// get parameter is set.
//
// Follows the pattern: `single-posttype-projects.twig`
if (in_array($timber_post->post_type, ['sector', 'service', 'office'])) {
	if (array_key_exists('projects', $_GET)) {
		array_unshift($templates, 'single-' . $timber_post->post_type . '-projects.twig');
		$context['show_all_projects'] = TRUE;
	}
}

if ( post_password_required( $timber_post->ID ) ) {
	Timber::render( 'single-password.twig', $context );
} else {
	Timber::render($templates, $context);
}
