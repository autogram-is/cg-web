<?php

function cgih_preprocess_raw_tribe_venue($postdata) {
  // Find the events the venue is used by, and move its metadata there.
  $meta = postmeta_keyed($postdata['postmeta']);
  $events = get_post_by_meta('cg_event', 'cg_import_venue_id', $postdata['post_id']);

  if ($events[0]) {
    $id = $events[0]->ID;
    update_field('venue_name', $postdata['post_title'], $id);
    update_field('venue_url', $meta['_VenueURL'], $id);
    update_field('venue_address', $meta['_VenueAddress'], $id);
    update_field('venue_city', $meta['_VenueCity'], $id);
    update_field('venue_state', $meta['_VenueStateProvince'], $id);
    update_field('venue_country', $meta['_VenueCountry'], $id);
    update_field('venue_postcode', $meta['_VenueZip'], $id);
    update_field('venue_phone', $meta['_VenuePhone'], $id);
  }

  // This bypasses importing the actual venue once we've attached its data to the event.
  $postdata['post_status'] = 'auto-draft';
  return $postdata;
}

function cgih_preprocess_raw_tribe_organizer($postdata) {
  // Find the event this organizer appears on and move its metadata there.
  // Then, mark this post as DO NOT IMPORT

  // To skip a post entirely, set $postdata['post_status'] to 'auto-draft';
  $postdata['post_status'] = 'auto-draft';
  return $postdata;
}

function postmeta_keyed($postmeta) {
  $output = [];
  foreach ($postmeta as $entry) {
    $output[$entry['key']] = $entry['value'];
  }
  return $output;
}