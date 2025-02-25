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

global $params;

$context         = Timber::context();
$timber_post     = Timber::get_post();
$context['post'] = $timber_post;

$templates = cg_post_templates($timber_post, 'single');

// If the post is a person's bio, but their bio page has been deactivated,
// generate a 404 instead of the normal page.
if ($timber_post->post_type == 'person') {
	$generate = get_field('generate_bio_page', $timber_post->ID) ?? false;
	$ex = get_field('ex_employee', $timber_post->ID) ?? false;
	if (($ex === true) || ($generate === false)) {
		if (current_user_can( 'read_private_posts' )) {
			$context['bio_is_hidden'] = true;
		} else {
			global $wp_query;
			$wp_query->set_404();
			status_header(404);
			$context = Timber::context();
			Timber::render('404.twig', $context);
			return;	
		}
	}
}

// Allow different template for office, service, and sector pages when
// the sub-page /portfolio is visited.
if ($params['portfolio'] ?? false) {
	// Build a fresh wp-query with pagination support
	$ids = $timber_post->meta('projects');
	if (is_array($ids) && count($ids)) {
		$query = [
			'post__in' => $ids,
			'post_type' => 'project',
			'posts_per_page' => 18,
			'paged' => get_query_var('page') ? get_query_var('page') : 1,
    ];
		$context['projects'] = Timber::get_posts($query);
	}
	array_unshift($templates, 'single/' . $timber_post->post_type . '-portfolio.twig');
	array_unshift($templates, 'single/' . 'default-portfolio.twig');
	$context['portfolio'] = TRUE;	
}

if ( post_password_required($timber_post->ID) ) {
	Timber::render( 'single/password.twig', $context );
} else {
	Timber::render($templates, $context);
}
