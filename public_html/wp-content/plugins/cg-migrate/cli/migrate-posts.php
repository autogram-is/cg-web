<?php

function cg_migrate_post($post, $dry_run = false) {
  // First, clean up the Fusion Text cruft
  $raw = $post->post_content;
  $dom = cg_get_cleaned_dom($raw);
  $cleaned = trim(join(PHP_EOL, _post_fusion_converter($post, $dom)));
  $post->post_content = $cleaned;

  $taxonomies = get_post_taxonomies($post->ID);
  $tags = wp_get_post_terms($post->ID, $taxonomies);
  $meta = get_post_meta($post->ID);

  foreach($tags as $tag) {
    if (in_array($tag->term_taxonomy_id, [335, 461])) {
      // Podcast episodes. Extract ID, episode number, guests.
      _process_podcast($post, $dry_run);

    } else if (in_array($tag->term_taxonomy_id, [340, 463])) {
      // Case study. Hide and remap to project metadata manually.
      

    } else if (in_array($tag->term_taxonomy_id, [355, 469])) {
      // Events. Extract time, attendees, location. Convert to event.
      $post->post_type = 'event';

    } else if (in_array($tag->term_taxonomy_id, [3, 464])) {
      // Press Release. Extract CTA form. Possibly convert About Cumming and CTA.

    } else if (in_array($tag->term_taxonomy_id, [2, 459, 220])) {
      // News. Extract byline, publication, publication date.

    } else if (in_array($tag->term_taxonomy_id, [336, 369, 499, 460, 462])) {
      // Blog / Thought Leadership.
      
    }
  }

  if (!$dry_run && !empty($cleaned)) {
    wp_update_post($post);
    cg_save_migration_body($post->ID, $raw);
    clean_post_cache($post->ID);
    WP_CLI::log(($dry_run ? "Dry Run: " : "")  . $post->post_type . " #$post->ID ($post->post_title) processed");
  }
}

function _post_fusion_converter($post, $dom, $node = null, &$chunks = []) {
  if ($node) {
    if ($node->nodeType === XML_ELEMENT_NODE) {
      // Fusion Titles get converted to H2s, Fusion Text gets converted to P tags.
      // This needs to be a bit more rigorous but for now it works.
      if ($node->tagName === 'fusion_text') {
        $text = $dom->saveHTML($node);
        $chunks[] = wp_kses($text, cg_allowed_markup());
      } else if ($node->tagName === 'fusion_title') {
        $raw = trim($dom->saveHTML($node));
        if (wp_strip_all_tags($raw) !== $post->post_title) {
          $chunks[] = '<h2>' . str_replace('\n', '', wp_kses($raw, 'plain')) . '</h2>';
        }
      }
    }
  } else {
    $node = $dom;
  }

  // Recursively traverse child nodes
  foreach ($node->childNodes as $child) {
    _post_fusion_converter($post, $dom, $child, $chunks);
  }
  return $chunks;
}


function _process_news($post, $dry_run) {
  // Find attached images; non-featured image is the masthead on article reprints
  // Split attribution to byline; move publication name and link to separate fields
}

function _process_podcast($post, $dry_run) {
  // Strip duplicative heading and buzzsprout shortcode; migration spreadsheet has podcast IDs.
  $text = $post->post_content;

  $text = preg_replace("/<h2>\s+(The )?Construction Insiders[\d\w\s\:,]+Episode \d+\s+<\/h2>/", '', $text);
  $text = preg_replace("/<h2>\s+Overview\s+<\/h2>/", '', $text);
  $text = preg_replace("/<h2>\s+Podcast Transcript\s+<\/h2>/", '<h2>Episode Transcript</h2>', $text);
  $text = preg_replace("/\[buzzsprout [^]]*\]/", '', $text);

  if (strlen(trim($text)) > 0) {
    $post->post_content = $text;
  } else {
    WP_CLI::log($post->ID ." regex broke");
  }
}

function _process_case_study($post, $dry_run) {
  // 
}