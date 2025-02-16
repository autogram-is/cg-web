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

    // People mentioned in news posts
    if (key_exists('people', $post_data) && $post_data['people']) {
      update_field('people', $post_data['people'] ?? NULL, $post->ID);
    }

    // Byline'd authors of posts
    if (key_exists('internal_byline', $post_data) && $post_data['internal_byline']) {
      update_field('internal_byline', $post_data['internal_byline'] ?? NULL, $post->ID);
    }

    // Offices, Sectors, Services, and Projects
    if (key_exists('related_portfolio_items', $post_data) && $post_data['related_portfolio_items']) {
      update_field('related_portfolio_items', $post_data['related_portfolio_items'] ?? NULL, $post->ID);
    }

    // Handle 
  } else {
    WP_CLI::log("Could not update post '". $post_data['title'] ."'");
  }

  return $post;
}


function cg_remap_news_import_fields($data) {
  if ($data['title'] ?? false) $data['post_title'] = $data['title'];
  if ($data['podcast'] ?? false) $data['podcast_name'] = $data['podcast'];
  if ($data['season'] ?? false) $data['podcast_season'] = $data['season'];
  if ($data['episode'] ?? false) $data['podcast_episode'] = $data['episode'];
  if ($data['youtube_url'] ?? false) $data['podcast_youtube_url'] = $data['youtube_url'];
  if ($data['mp3_url'] ?? false) $data['podcast_mp3_url'] = $data['mp3_url'];

  $service_ids = _cols_to_id_array($data, 'service', ['service1', 'service2', 'service3']);
  if (count($service_ids) > 0) {
    $post_data['services'] = $service_ids;
  }

  $sector_ids = _cols_to_id_array($data, 'sector', ['sector1', 'sector2', 'sector3']);
  if (count($sector_ids) > 0) {
    $post_data['sectors'] = $sector_ids;
  }

  $office_ids = _cols_to_id_array($data, 'office', ['office1', 'office2', 'office3', 'office4']);
  if (count($office_ids) > 0) {
    $post_data['offices'] = $office_ids;
  }

  $office_ids = _cols_to_id_array($data, 'internal_byline', ['internal_byline1', 'internal_byline2', 'internal_byline3']);
  if (count($office_ids) > 0) {
    $post_data['offices'] = $office_ids;
  }

  return $data;
}
