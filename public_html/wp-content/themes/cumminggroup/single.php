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

if ($timber_post->post_type === 'project') {
	$relationships = array();
	$facts = array();

	// Project relationships
	if ($timber_post->sectors) {
		$relationships[] = array(
			'key' => pluralize($timber_post->sectors, 'Sector'),
			'value' => join(', ', array_map('get_post_a_tag', $timber_post->sectors)),
		);
	}

	if ($timber_post->services) {
		$relationships[] = array(
			'key' => pluralize($timber_post->services, 'Service'),
			'value' => join(', ', array_map('get_post_a_tag', $timber_post->services)),
		);
	}

	if ($timber_post->offices) {
		$relationships[] = array(
			'key' => pluralize($timber_post->offices, 'Office'),
			'value' => join(', ', array_map('get_post_a_tag', $timber_post->offices)),
		);
	}

	// Project facts
	$fact_fields = ['facility', 'client', 'location', 'start_date', 'completion_date', 'budget', 'capacity', 'owner', 'architect', 'vendors', 'contractors'];
	foreach ($fact_fields as $fact_field) {
		if ($timber_post->$fact_field) {
			$facts[] = array(
				'key' => snake_to_title_case($fact_field),
				'value' => $timber_post->$fact_field,
			);
		}
	}

	// Assemble project facts
	if (count($facts) > 0) {
		$context['facts'] = $facts;
	}

	if (count($relationships) > 0) {
		$context['relationships'] = $relationships;
	}

}

if ( post_password_required( $timber_post->ID ) ) {
	Timber::render( 'single-password.twig', $context );
} else {
	Timber::render( array( 'single-' . $timber_post->ID . '.twig', 'single-' . $timber_post->post_type . '.twig', 'single-' . $timber_post->slug . '.twig', 'single.twig' ), $context );
}
