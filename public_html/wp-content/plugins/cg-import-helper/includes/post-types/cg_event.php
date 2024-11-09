<?php

function cgih_preprocess_raw_cg_event($postdata) {
  // Incoming events. Event-specific meta tags are handled in
  // cgih_preprocess_post_meta_key(); this is just responsible
  // for preprocessing the terrible fusion markup and extracting
  // the meeting link and attendee headshots.

  $extract = cgih_fusion_extract_event_details($postdata['post_content']);

  $postdata['post_content'] = $extract['html'];
  if ($extract['email']) {
    $postdata['postmeta'][] = array(
      'key' => 'email',
      'value' => $extract['email'],
    );  
  }

  // Create and attach attendee bios
  $attendees = [];
  foreach ($extract['attendees'] as $bio) {
    $bioPost = cgih_create_person_from_event_bio($bio);
    if ($bioPost) {
      $bioPost = get_post($bioPost);
    }
    if ($bioPost) {
      $attendees[] = $bioPost->ID;
    }
  }
  if (count($attendees) > 0) {
    $postdata['postmeta'][] = array(
      'key' => 'attendees',
      'value' => $attendees,
    );  
  }

  return $postdata; 
}

function cgih_fusion_extract_event_details($html) {
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

  $output['html'] = str_replace($contentType, '', $dom->saveHTML());
  return $output;
}

function cgih_create_person_from_event_bio($bio) {
  $slug = trim(sanitize_title($bio['name']));
  $post = get_post_by_name($slug, 'cg_person');
  $headshot_id = attachment_url_to_postid($bio['headshot']);

  if (!$post)  {
    $post = wp_insert_post(array(
      'post_title' => $bio['name'],
      'post_type' => 'cg_person',
      'post_status' => 'publish',
      'post_name' => $slug,
      'meta_input' => array(
        'email' => $bio['email'] ? str_replace('mailto:', '', $bio['email']) : null,
        'role' => $bio['role'],
        '_import_featured_image' => $headshot_id || $bio['headshot'],
      ),
    ));  
    $post = get_post($post);
  }

  if ($headshot_id) {
    set_post_thumbnail($post, $headshot_id);
  }

  return $post;
}