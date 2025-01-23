<?php

function cg_save_news(array $post_data = [], bool $use_slug = false, bool $create = false) {
  $id = cg_save_base('post', $post_data, false, false);
  $post = null;

  if ($id) {
    update_field('podcast_name', $post_data['podcast'] ?? NULL, $id);
    update_field('podcast_season', $post_data['season'] ?? NULL, $id);
    update_field('podcast_episode', $post_data['episode'] ?? NULL, $id);
    update_field('podcast_youtube_url', $post_data['youtube_url'] ?? NULL, $id);
    update_field('podcast_buzzsprout_id', $post_data['buzzsprout_id'] ?? NULL, $id);

    update_field('byline', $post_data['reprint_byline'] ?? NULL, $id);
    update_field('reprint_url', $post_data['reprint_url'] ?? NULL, $id);
    update_field('reprint_publication', $post_data['reprint_publication'] ?? NULL, $id);
    update_field('reprint_logo', $post_data['reprint_logo'] ?? NULL, $id);
  }

  return $id;
}