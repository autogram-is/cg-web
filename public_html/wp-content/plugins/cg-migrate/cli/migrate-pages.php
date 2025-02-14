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

function cg_apply_fixed_pages($dry_run = false) {
  $matches = glob(join("/", [CG_MIGRATE_CONTENT_DIR, '*.html']));
  WP_CLI::log(($dry_run ? "Dry Run: " : "") . count($matches) . " content override files found");

  foreach ($matches as $match) {
    $file = basename($match);
    $path = CG_MIGRATE_CONTENT_DIR . '/' . $file;

    $slug = str_replace('.html', '', $file);

    if (ctype_digit($slug)) {
      $post = get_post(intval($slug));
    } else {
      $post = get_post_by_name($slug, 'page');
    }

    if ($post && file_exists($path)) {
      $body = file_get_contents($path);
      $post->post_content = $body;

      if (!$dry_run) {
        wp_update_post($post);
      }
      WP_CLI::log(($dry_run ? "Dry Run: " : "") . "Page #$post->ID updated from '$file'");
    } 
  }    
}