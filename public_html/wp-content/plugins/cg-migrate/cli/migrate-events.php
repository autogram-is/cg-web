<?php

function cg_migrate_event($post, $dry_run = false) {
  $messages = [];

  // Switch to the new post type
  if ($post->post_type === 'tribe_events') {
    $messages[] = 'updated type';
    if (!$dry_run) {
      set_post_type($post->ID, 'event');
      $post = get_post($post->ID);
    }
  }

  // Extract the event description and attendee bios
  $extracted = _fusion_event_details($post->post_content);

  if (!$dry_run) {
    $raw = $post->post_content;
    $post->post_content = $extracted['processed'];
    wp_update_post($post);
    cg_save_migration_data($post->ID, array('raw' => $raw));
    cg_mark_migrated($post->ID);
  }
  // WP_CLI::log(print_r($details));

  // Process the attendees
  $attendees = [];
  foreach ($extracted['attendees'] as $bio) {
    $bioPost = _person_from_event_attendee($bio, $dry_run);
    if ($bioPost) {
      $attendees[] = $bioPost->ID;
    }
  }
  if (count($extracted['attendees']) > 0) {
    $messages[] = count($extracted['attendees']) . ' attendees';
    if (!$dry_run) {
      // 'people' from the 'related_people' group
      // for some reason using the 'people' fieldname saves this data in the 'offices' relationship
      update_field('field_6750c99c34f00', $attendees, $post->ID);
    }
  }

  // Load the event meta properties
  $meta = get_post_meta($post->ID) ?? [];
  if (!$dry_run) {
    if ($meta['_EventStartDate']) {
      update_field('start_date', $meta['_EventStartDate'][0], $post->ID);
    }
    if ($meta['_EventEndDate']) { 
      update_field('end_date', $meta['_EventEndDate'][0], $post->ID);
    }
    if (key_exists('_EventAllDay', $meta) && $meta['_EventAllDay']) { 
      update_field('all_day', true, $post->ID);
    }
    if ($meta['_EventURL']) { 
      update_field('event_url', $meta['_EventURL'][0], $post->ID);
    }
  }

  if (array_key_exists('_EventVenueID', $meta)) {
    $messages[] = "venue #{$meta['_EventVenueID'][0]}";
    _absorb_venue($post->ID, $meta['_EventVenueID'][0], $dry_run);
  }


  WP_CLI::log(($dry_run ? "Dry Run: " : "") . "Event #$post->ID ($post->post_title): " . join(', ', $messages));
}

function _fusion_event_details($html) {
  $output = [];

  $dom = cg_get_dom($html);

  $markup_people = cg_event_attendee_markup($dom);
  $fusion_people = cg_event_person_components($dom);
  $output['attendees'] = array_merge($fusion_people, $markup_people);
  
  $preserve_children = ['fusion_accordion', 'fusion_builder_column', 'fusion_builder_row', 'fusion_builder_container', 'fusion_testimonials'];
  $remove_entirely = ['fusion_portfolio', 'fusion_blog', 'fusion_slider', 'fusion_slide', 'fusion_code', 'fusion_global', 'fusion_separator'];
  $simple_tags = ['fusion_checklist', 'fusion_li_item', 'fusion_highlight', 'fusion_button', 'fusion_toggle'];
  $media_tags = ['fusion_imageframe', 'fusion_image', 'fusion_gallery', 'fusion_youtube', 'fusion_testimonial'];

  $headings_to_ignore = ['Overview'];

  cg_dom_remove_tags($dom, $preserve_children, true);
  cg_dom_remove_tags($dom, $remove_entirely, false);
  cg_dom_process_fusion_tags($dom, $simple_tags);
  cg_dom_process_fusion_tags($dom, $media_tags);

  cg_dom_process_fusion_tags($dom, ['fusion_text']);
  cg_dom_process_fusion_titles($dom, $headings_to_ignore);
  
  cg_log_remaining_fusion_tags($dom);

  // Create a new DOMXPath instance
  $xpath = new DOMXPath($dom);
  // Find "schedule a meeting" buttons
  // a button
  $button = $xpath->query('//a[button]')->item(0);
  if ($button) {
    @$output['email'] = str_replace('mailto:', '', $button->attributes['href']->value);
    $button->parentNode->removeChild($button);
  }

  $html = trim($dom->saveHTML());

  $output['processed'] = wp_kses($html, cg_allowed_markup());

  return $output;
}

function _person_from_event_attendee($bio, $dry_run) {
  $known_duplicates = array(
    'Eugenie LeRoux' => 'Eugenie LaRoux',
    'Kimberly McHugh' => 'Kim McHugh',
    'William Foulkes' => 'Bill Foulkes',
    'Chris Whitley' => 'Chris Whitley, Jr.'
  );
  foreach ($known_duplicates as $dupe => $correct) {
    $bio['name'] = str_replace($dupe, $correct, trim($bio['name']));
  }

  $slug = trim(sanitize_title($bio['name']));
  $headshot_id = attachment_url_to_postid($bio['headshot']);

  $post = get_post_by_name($slug, 'person');

  if (!$post)  {
    $post_data = array(
      'post_title' => $bio['name'],
      'post_type' => 'person',
      'post_status' => 'publish',
      'post_name' => $slug,
    );

    if (!$dry_run) { 
      $post_id = wp_insert_post($post_data);
      if ($headshot_id) {
        set_post_thumbnail($post_id, $headshot_id);
      }
      $post = get_post($post_id);
      if ($bio['email']) {
        update_field('email', str_replace('mailto:', '', trim($bio['email'])), $post_id);
      }
      if ($bio['role']) {
        update_field('role', trim($bio['role']), $post_id);
      }
      if ($bio['role']) {
        update_field('role', trim($bio['role']), $post_id);
      }
    }

    WP_CLI::log(($dry_run ? "Dry Run: " : "") . "Bio for '{$post_data['post_title']}' ({$bio['role']})");
  }

  return $post;
}

function _absorb_venue($event_id, $venue_id, $dry_run) {
  $venue = get_post($venue_id);
  if ($venue) {
    $meta = get_post_meta($venue->ID) ?? [];
    if (!$dry_run) {
      update_field('venue_name', $venue->post_title, $event_id);
      if ($meta['_VenueURL']) {
        update_field('venue_url', $meta['_VenueURL'][0], $event_id);
      }
      if ($meta['_VenueAddress']) {
        update_field('venue_address', $meta['_VenueAddress'][0], $event_id);
      }
      if ($meta['_VenueCity']) {
        update_field('venue_city', $meta['_VenueCity'][0], $event_id);
      }
      if ($meta['_VenueStateProvince']) {
        update_field('venue_state', $meta['_VenueStateProvince'][0], $event_id);
      }
      if ($meta['_VenueZip']) {
        update_field('venue_postcode', $meta['_VenueZip'][0], $event_id);
      }
      if ($meta['_VenueCountry']) {
        update_field('venue_country', $meta['_VenueCountry'][0], $event_id);
      }
      if ($meta['_VenuePhone']) {
        update_field('venue_phone', $meta['_VenuePhone'][0], $event_id);
      }
      wp_delete_post($venue_id);
    }
  }
}
