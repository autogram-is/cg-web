<?php

function cg_migrate_post($post, $dry_run = false) {
  $taxonomies = get_post_taxonomies($post->ID);
  $tags = wp_get_post_terms($post->ID, $taxonomies);
  $meta = get_post_meta($post->ID);

  // 
  foreach($tags as $tag) {
    if (in_array($tag->term_taxonomy_id, [335, 461])) {
      // Podcast episodes. Extract ID, episode number, guests.

    } else if (in_array($tag->term_taxonomy_id, [340, 463])) {
      // Case study. Hide and remap to project metadata manually.

    } else if (in_array($tag->term_taxonomy_id, [35, 469])) {
      // Events. Extract time, attendees, location. Convert to event.

    } else if (in_array($tag->term_taxonomy_id, [3, 464])) {
      // Presss Release. Extract CTA form. Possibly convert About Cumming and CTA.

    } else if (in_array($tag->term_taxonomy_id, [2, 459, 220])) {
      // News. Extract byline, publication, publication date.

    } else if (in_array($tag->term_taxonomy_id, [336, 369, 499, 460, 462])) {
      // Blog / Thought Leadership.
      
    }
  }

  // 'Events Hosted By Others' (map to events, or delete?)
  // Adapt these to the event template

  // Webinars
  //

  // Press Releases
  // Nothing fancy, just make the Fusion markup not terrible

  // News / Other News (articles reprinted from third-party sources)
  // Find attached images; non-featured image is the masthead on article reprints
  
  // Podcasts
  // Convert to 'podcast' posts, remap meta tags and extract data from fusion markup
  // Attach people

  // Blog

  // Fallback case — just grab titles and texts from Fusion
  $raw = $post->post_content;
  $dom = cg_get_cleaned_dom($raw);
  $cleaned = trim(join(PHP_EOL, _post_fusion_converter($post, $dom)));

  if (!$dry_run && !empty($cleaned)) {
    $post->post_content = $cleaned;

    wp_update_post($post);
    cg_save_migration_body($post->ID, $raw);
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
        $text = trim($dom->saveHTML($node));
        if ($text !== $post->post_title) {
          $chunks[] = '<h2>' . wp_kses($text, 'plain') . '</h2>';
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
  
}


function _process_podcast($post, $dry_run) {
  
}


function _process_event_post($post, $dry_run) {
  
}


function _process_case_study($post, $dry_run) {
  
}