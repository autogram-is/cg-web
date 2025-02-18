<?php

function cg_migrate_post($post, $dry_run = false) {
  $data = cg_process_news_markup($post);
  $content = wp_kses($data['processed'], cg_extended_markup());
  $post->post_content = trim($content);

  $people = [];
  foreach ($data['people'] as $bio) {
    $bioPost = _person_from_post_markup($bio, $dry_run);
    if ($bioPost) {
      $people[] = $bioPost->ID;
    }
  }
  if (count($people)) {
    $messages[] = count($people) . ' people';
    if (!$dry_run) {
      update_field('internal_bylines', $people, $post->ID);
    }
  }

  if (!$dry_run) {
    wp_update_post($post);
    clean_post_cache($post->ID);
    WP_CLI::log(($dry_run ? "Dry Run: " : "")  . $post->post_type . " #$post->ID ($post->post_title) processed");
  }
}


function cg_postprocess_news($post, $dry_run = false) {
  $taxonomies = get_post_taxonomies($post->ID);
  $tags = wp_get_post_terms($post->ID, $taxonomies);
  $meta = get_post_meta($post->ID);

  foreach($tags as $tag) {
    if ($tag->slug === 'podcast') {
      _process_podcast($post, $dry_run);

    } else if ($tag->slug === 'news') {
      _process_news($post, $dry_run);
      
    } else if ($tag->slug === 'press-releases') {
      // Press Release. Extract CTA form. Possibly convert About Cumming and CTA.
      _process_press_release($post, $dry_run);

    } else if ($tag->slug === 'blog') {
      // Blog / Thought Leadership. 
    }
  }

  if (!$dry_run) {
    wp_update_post($post);
  }
}
function _process_news(&$post, $dry_run) {
  // Find attached images; non-featured image is the masthead on article reprints
  // Split attribution to byline; move publication name and link to separate fields


}

function _process_press_release(&$post, $dry_run) {
  // Press releases in particular have a PDF download and a contact form at the bottom.
  $content = $post->post_content;
  $parts = explode('<!-- wp:image -->', $content);
  if (count($parts) > 1) {
    $post->post_content = $parts[0];
  }
}

function _process_podcast(&$post) {
  // Strip duplicative heading and buzzsprout shortcode; migration spreadsheet has podcast IDs.
  $text = $post->post_content;

  $text = preg_replace("/<h\d>\s*(The )?Construction Insiders[\d\w\s\:,]+<\/h\d>/", '', $text);
  $text = preg_replace("/<h\d>\s*Overview\s*<\/h\d>/", '', $text);
  $text = preg_replace("/<h\d>\s*Podcast Transcript\s*<\/h\d>/", '<h2>Episode Transcript</h2>', $text);
  $text = preg_replace("/\[buzzsprout [^]]*\]/", '', $text);

  $text = preg_replace("/<h\d>\s*(The )?Construction Insiders[\d\w\s\:,]+<\/h\d>/", '', $text);

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
  $remove_entirely = ['fusion_portfolio', 'fusion_blog', 'fusion_slider', 'fusion_slide', 'fusion_code', 'fusion_global', 'fusion_separator', 'fusion_table'];
  $simple_tags = ['fusion_checklist', 'fusion_li_item', 'fusion_highlight', 'fusion_button', 'fusion_toggle'];
  $media_tags = ['fusion_imageframe', 'fusion_image', 'fusion_gallery', 'fusion_youtube', 'fusion_testimonial'];

  $headings_to_ignore = [$post->post_title, 'Overview'];

  cg_dom_remove_tags($dom, $preserve_children, true);
  cg_dom_remove_tags($dom, $remove_entirely, false);
  cg_dom_process_fusion_tags($dom, $simple_tags);
  cg_dom_process_fusion_tags($dom, $media_tags);
  cg_dom_process_fusion_tags($dom, ['fusion_text']); // Ignore paragraphs that are a parseable date

  cg_dom_process_fusion_titles($dom, $headings_to_ignore);

  $output['people'] = cg_event_person_components($dom);

  $output['processed'] = $dom->saveHTML();
  
  cg_log_remaining_fusion_tags($dom);

  return $output;  
}

function cg_extract_bylines(&$post) {
  
}