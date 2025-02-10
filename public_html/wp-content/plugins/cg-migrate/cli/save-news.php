<?php

function cg_save_news(array $post_data = [], bool $use_slug = false, bool $create = false) {
  if (!array_key_exists('migration_status', $post_data)) $post_data['migration_status'] = 'auto-migrated';

  $post = cg_save_base('post', $post_data, false, false);

  if ($post) {
    update_field('podcast_name', $post_data['podcast_name'] ?? NULL, $post->ID);
    update_field('podcast_season', $post_data['podcast_season'] ?? NULL, $post->ID);
    update_field('podcast_episode', $post_data['podcast_episode'] ?? NULL, $post->ID);
    update_field('podcast_youtube_url', $post_data['podcast_youtube_url'] ?? NULL, $post->ID);
    update_field('podcast_mp3_url', $post_data['podcast_mp3_url'] ?? NULL, $post->ID);

    update_field('external_byline', $post_data['external_byline'] ?? NULL, $post->ID);

    update_field('reprint_url', $post_data['reprint_url'] ?? NULL, $post->ID);
    update_field('reprint_publication', $post_data['reprint_publication'] ?? NULL, $post->ID);
    update_field('reprint_logo', $post_data['reprint_logo'] ?? NULL, $post->ID);

    // People mentioned in posts
    update_field('people', $post_data['people'] ?? NULL, $post->ID);

    // Authors of posts
    update_field('internal_byline', $post_data['internal_byline'] ?? NULL, $post->ID);

    // Offices, Sectors, Services, and Projects
    update_field('related_portfolio_items', $post_data['related_portfolio_items'] ?? NULL, $post->ID);

    // Handle 
  } else {
    WP_CLI::log("Could not update post '". $post_data['title'] ."'");
  }

  return $post;
}
