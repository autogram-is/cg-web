<?php

function cgih_preprocess_raw_cg_event($postdata) {
  // Incoming events. Event-specific meta tags are handled in
  // cgih_preprocess_post_meta_key(); this is just responsible
  // for preprocessing the terrible fusion markup and extracting
  // the meeting link and attendee headshots.
  return $postdata; 
}