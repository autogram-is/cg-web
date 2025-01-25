<?php

function cg_migrate_page($post, $dry_run = false) {
  $taxonomies = get_post_taxonomies($post->ID);
  $tags = wp_get_post_terms($post->ID, $taxonomies);
  $meta = get_post_meta($post->ID);

  // Fallback case — just grab titles and texts from Fusion
  $raw = $post->post_content;
  $dom = cg_get_cleaned_dom($raw);
  $cleaned = trim(join(PHP_EOL, _page_fusion_converter($post, $dom)));

  if (!$dry_run && !empty($cleaned)) {
    $post->post_content = $cleaned;

    wp_update_post($post);
    cg_save_migration_body($post->ID, $raw);
  }
  WP_CLI::log(($dry_run ? "Dry Run: " : "") . "Page #$post->ID ($post->post_title) processed");
}

function _page_fusion_converter($post, $dom, $node = null, &$chunks = []) {
 
  if ($node) {
    if ($node->nodeType === XML_ELEMENT_NODE) {
      // Fusion Titles get converted to H2s, Fusion Text gets converted to P tags.
      // This needs to be a bit more rigorous but for now it works.
      if ($node->tagName === 'fusion_text') {
        $text = $dom->saveHTML($node);
        $chunks[] = wp_kses($text, cg_allowed_markup());
      } else if ($node->tagName === 'fusion_title') {
        $text = trim($dom->saveHTML($node));
        if ($text !== $post->post_title) {
          $chunks[] = '<h2>' . str_replace('\n', '', wp_kses($text, 'plain')) . '</h2>';
        }
      } else if (str_starts_with($node->tagName, 'fusion_')) {
        $ignore = ['fusion_builder_container', 'fusion_builder_row', 'fusion_builder_column', 'fusion_slider', 'fusion_table', 'fusion_separator', 'fusion_slide'];
        if (!in_array($node->tagName, $ignore)) {
          WP_CLI::log("   Encountered '$node->tagName' Fusion Tag in " .  $post->post_type . ' ' . $post->ID);
        }
      }
    }
  } else {
    $node = $dom;
  }

  // Recursively traverse child nodes
  foreach ($node->childNodes as $child) {
    _page_fusion_converter($post, $dom, $child, $chunks);
  }
  return $chunks;
}
