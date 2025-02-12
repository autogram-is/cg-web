<?php

function cg_migrate_page($post, $dry_run = false) {
  $taxonomies = get_post_taxonomies($post->ID);
  $tags = wp_get_post_terms($post->ID, $taxonomies);
  $meta = get_post_meta($post->ID);

  $data = cg_default_process_markup($post); 

  if (!$dry_run) {
    $content = wp_kses($data['processed'], cg_extended_markup_with_tables());
    $post->post_content = trim($content);
    wp_update_post($post);
  }

  WP_CLI::log(($dry_run ? "Dry Run: " : "") . "Page #$post->ID ($post->post_title) processed");
}

