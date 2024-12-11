<?php

function cg_migrate_event($post, $dry_run = false) {
  $messages = [];

  // Switch to the new post type
  if ($post->post_type === 'tribe_events') {
    $messages[] = 'updated type';
    if (!$dry_run) {
      set_post_type($post->ID, 'cg_event');
      $post = get_post($post->ID);
    }
  }

  // Extract the event description and attendee bios
  $extracted = _fusion_event_details($post->post_content);

  if (!$dry_run) {
    $post->post_content = $extracted['body'];
    wp_update_post($post);
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
    if ($meta['_EventAllDay']) { 
      update_field('all_day', $meta['_EventAllDay'][0], $post->ID);
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
  $dom = new DOMDocument();
  $contentType = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';

  @$dom->loadHTML($contentType . $html); // Suppress warnings for malformed HTML

  $output = array(
    'email' => '',
    'attendees' => [],
    'html' => $html,
  );

  // Create a new DOMXPath instance
  $xpath = new DOMXPath($dom);

  // Find "schedule a meeting" buttons
  // a button
  $button = $xpath->query('//a[button]')->item(0);
  if ($button) {
    @$output['email'] = str_replace('mailto:', '', $button->attributes['href']->value);
    $button->parentNode->removeChild($button);
  }

  // Bios of CG staff in attendance. Structure:
  // h4 Attendees
  // div
  //   p img
  //   div
  //     p name
  //     p title
  //     p a email
  @$h4 = $xpath->query('//h4')->item(0);
  if ($h4 && $h4->textContent === 'Attendees') {
      @$bios = $xpath->query('//h4/following-sibling::div/div');
      foreach ($bios as $bio) {
          @$img = $xpath->query('img', $bio)->item(0);
          @$props = $xpath->query('div/p', $bio);
          @$email = $xpath->query('div//a', $bio)->item(0);
          $attendee = array(
              'headshot' => @$img->attributes['src']->value,
              'name' => @$props->item(0)->textContent,
              'role' => @$props->item(1)->textContent,
              'email' => @$email->attributes['href']->value,
          );
          if ($attendee['name']) {
              $output['attendees'][] = $attendee;
          }
          $bio->parentNode->removeChild($bio);
      }
      $h4->parentNode->removeChild($h4);
  }

  $output['body'] = str_replace($contentType, '', $dom->saveHTML());

  $allowed_html = array(
    'a' => array('href' => array()),
    'p' => array(),
    'ol' => array(),
    'ul' => array(),
    'li' => array(),
    'em' => array(),
    'strong' => array(),
    'h1' => array(),
    'h2' => array(),
    'h3' => array(),
    'h4' => array(),
    'h5' => array(),
  );
  $output['body'] = trim(wp_kses($output['body'], $allowed_html));

  return $output;
}

function _person_from_event_attendee($bio, $dry_run) {
  $slug = trim(sanitize_title($bio['name']));
  $headshot_id = attachment_url_to_postid($bio['headshot']);

  $post = get_post_by_name($slug, 'cg_person');

  if (!$post)  {
    $post_data = array(
      'post_title' => $bio['name'],
      'post_type' => 'cg_person',
      'post_status' => 'publish',
      'post_name' => $slug,
      'meta_input' => array(
        'email' => $bio['email'] ? str_replace('mailto:', '', $bio['email']) : null,
        'role' => $bio['role'],
      ),
    );

    if (!$dry_run) { 
      $post_id = wp_insert_post($post_data);
      if ($headshot_id) {
        set_post_thumbnail($post_id, $headshot_id);
      }
      $post = get_post($post_id);
    }

    WP_CLI::log(($dry_run ? "Dry Run: " : "") . "Bio for '{$post_data['post_title']}' ({$post_data['meta_input']['role']})");
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
