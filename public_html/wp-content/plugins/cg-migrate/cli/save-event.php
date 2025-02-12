<?php

function cg_save_event(array $post_data = [], bool $use_slug = false, bool $create = false) {
  if (!array_key_exists('migration_status', $post_data)) $post_data['migration_status'] = 'auto-migrated';

  $post = cg_save_base('event', $post_data, false, false);

  if ($post) {
    // Update additional fields here
    update_field('event_url', $post_data['event_url'] ?? NULL, $post->ID);
    update_field('start_date', $post_data['start_date'] ?? NULL, $post->ID);
    update_field('end_date', $post_data['end_date'] ?? NULL, $post->ID);
    update_field('all_day', $post_data['all_day'] ?? NULL, $post->ID);
    update_field('event_email', $post_data['event_email'] ?? NULL, $post->ID);

    update_field('venue_name', $post_data['venue_name'] ?? NULL, $post->ID);
    update_field('venue_url', $post_data['venue_url'] ?? NULL, $post->ID);
    update_field('venue_phone', $post_data['venue_phone'] ?? NULL, $post->ID);
    update_field('venue_address', $post_data['venue_address'] ?? NULL, $post->ID);
    update_field('venue_city', $post_data['venue_city'] ?? NULL, $post->ID);
    update_field('venue_state', $post_data['venue_state'] ?? NULL, $post->ID);
    update_field('venue_postcode', $post_data['venue_postcode'] ?? NULL, $post->ID);
    update_field('venue_country', $post_data['venue_country'] ?? NULL, $post->ID);

    // Event attendees
    if (key_exists('people', $post_data) && $post_data['people']) {
      update_field('people', $post_data['people'] ?? NULL, $post->ID);
    }

    // Offices, Sectors, Services, and Projects
    if (key_exists('related_portfolio_items', $post_data) && $post_data['related_portfolio_items']) {
      update_field('related_portfolio_items', $post_data['related_portfolio_items'] ?? NULL, $post->ID);
    }
  } else {
    WP_CLI::log("Could not update event '". $post_data['title'] ."'");
  }

  return $post;
}


function cg_clean_event_markup($post) {
  $output = [];

  $dom = cg_get_dom($post->post_content);

  $preserve_children = ['fusion_accordion', 'fusion_builder_column', 'fusion_builder_row', 'fusion_builder_container'];
  $remove_entirely = ['fusion_portfolio', 'fusion_blog', 'fusion_slider', 'fusion_slide', 'fusion_code', 'fusion_global', 'fusion_separator'];
  $simple_tags = ['fusion_checklist', 'fusion_li_item', 'fusion_highlight', 'fusion_button', 'fusion_toggle'];
  $media_tags = ['fusion_imageframe', 'fusion_image', 'fusion_gallery', 'fusion_youtube'];

  cg_dom_remove_tags($dom, $preserve_children, true);
  cg_dom_remove_tags($dom, $remove_entirely, false);
  cg_dom_process_fusion_tags($dom, $simple_tags);
  cg_dom_process_fusion_tags($dom, $media_tags);
  cg_dom_process_fusion_tags($dom, ['fusion_text']);

  cg_dom_process_fusion_titles($dom, [$post->post_title], 2);
  $output['facts'] = projectFactTable($dom);

  cg_dom_remove_tags($dom, ['table']);

  $remaining_fusion_tags = cg_count_fusion_tags($dom);
  WP_CLI::log("$post->post_type #$post->ID ($post->post_title)");
  foreach ($remaining_fusion_tags as $tag => $count) {
    WP_CLI::log("  Encountered tag '$tag': $count");
  }

  $output['raw'] = $post->post_content;
  $output['body'] = $dom->saveHTML();

  return $output;
}

/**

$nodes = iterator_to_array($dom->getElementsByTagName('fusion_person'));
foreach ($nodes as $node) {
  $found_people[] = cg_fusion_person($node);
}



*/
