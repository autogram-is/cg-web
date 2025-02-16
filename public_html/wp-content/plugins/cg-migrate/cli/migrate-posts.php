<?php

function cg_migrate_post($post, $dry_run = false) {
  $data = cg_process_news_markup($post);
  $content = wp_kses($data['processed'], cg_extended_markup());
  $post->post_content = trim($content);

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

  if (!$dry_run) {
    wp_update_post($post);
    clean_post_cache($post->ID);
    WP_CLI::log(($dry_run ? "Dry Run: " : "")  . $post->post_type . " #$post->ID ($post->post_title) processed");
  }
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

function cg_process_news_markup(object $post) {
  $output = [];
  $output['original'] = $post->post_content;

  $dom = cg_get_dom($post->post_content);

  $preserve_children = ['fusion_accordion', 'fusion_builder_column', 'fusion_builder_row', 'fusion_builder_container', 'fusion_testimonials'];
  $remove_entirely = ['fusion_portfolio', 'fusion_blog', 'fusion_slider', 'fusion_slide', 'fusion_code', 'fusion_global', 'fusion_separator'];
  $simple_tags = ['fusion_checklist', 'fusion_li_item', 'fusion_highlight', 'fusion_button', 'fusion_toggle'];
  $media_tags = ['fusion_imageframe', 'fusion_image', 'fusion_gallery', 'fusion_youtube', 'fusion_testimonial'];

  $headings_to_ignore = [$post->post_title, 'Overview'];

  cg_dom_remove_tags($dom, $preserve_children, true);
  cg_dom_remove_tags($dom, $remove_entirely, false);
  cg_dom_process_fusion_tags($dom, $simple_tags);
  cg_dom_process_fusion_tags($dom, $media_tags);
  cg_dom_process_fusion_tags($dom, ['fusion_text']); // Ignore paragraphs that are a parseable date

  cg_dom_process_fusion_titles($dom, $headings_to_ignore);

  $output['processed'] = $dom->saveHTML();
  
  cg_log_remaining_fusion_tags($dom);

  return $output;  
}
