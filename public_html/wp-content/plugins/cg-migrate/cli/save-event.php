<?php

function cg_save_event(array $post_data = [], bool $use_slug = false, bool $create = false) {
  if (!array_key_exists('migration_status', $post_data)) $post_data['migration_status'] = 'auto-migrated';

  $post = cg_save_base('event', $post_data, false, false);

  if ($post) {
    // Update additional fields here
    update_field('event_url', $post_data['event_url'] ?? NULL, $post->ID);
    update_field('start_date', $post_data['start_date'] ?? NULL, $post->ID);
    update_field('end_date', $post_data['end_date'] ?? NULL, $post->ID);
    update_field('all_day', $post_data['all_day'] ?? NULL, $post->ID);
    update_field('event_email', $post_data['event_email'] ?? NULL, $post->ID);

    update_field('venue_name', $post_data['venue_name'] ?? NULL, $post->ID);
    update_field('venue_url', $post_data['venue_url'] ?? NULL, $post->ID);
    update_field('venue_phone', $post_data['venue_phone'] ?? NULL, $post->ID);
    update_field('venue_address', $post_data['venue_address'] ?? NULL, $post->ID);
    update_field('venue_city', $post_data['venue_city'] ?? NULL, $post->ID);
    update_field('venue_state', $post_data['venue_state'] ?? NULL, $post->ID);
    update_field('venue_postcode', $post_data['venue_postcode'] ?? NULL, $post->ID);
    update_field('venue_country', $post_data['venue_country'] ?? NULL, $post->ID);

    // Event attendees
    if (key_exists('people', $post_data) && $post_data['people']) {
      update_field('people', $post_data['people'] ?? NULL, $post->ID);
    }

    // Offices, Sectors, Services, and Projects
    if (key_exists('related_portfolio_items', $post_data) && $post_data['related_portfolio_items']) {
      update_field('related_portfolio_items', $post_data['related_portfolio_items'] ?? NULL, $post->ID);
    }
  } else {
    WP_CLI::log("Could not update event '". $post_data['title'] ."'");
  }

  return $post;
}