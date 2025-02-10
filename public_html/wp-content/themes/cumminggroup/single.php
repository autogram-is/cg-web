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

$context         = Timber::context();
$timber_post     = Timber::get_post();
$context['post'] = $timber_post;

$templates = array(
	'single-' . $timber_post->ID . '.twig',
	'single-' . $timber_post->post_type . '.twig',
	'single-' . $timber_post->slug . '.twig',
	'single.twig'
);

// Allow category-specific templates, just in case.
$categories = get_the_terms($post->ID, 'news_category');
if (!is_wp_error($categories) && count($categories) > 0) {
	$context['news_category'] = $categories[0];
	array_unshift($templates, 'single-' . $timber_post->post_type . '-' . $categories[0]->slug . '.twig');
}

if ( post_password_required( $timber_post->ID ) ) {
	Timber::render( 'single-password.twig', $context );
} else {
	Timber::render($templates, $context);
}
