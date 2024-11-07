<?php

function cgih_preprocess_raw_tribe_venue($postdata) {
  // Find the events the venue is used by, and move its metadata there.
  // Then, mark this post as DO NOT IMPORT
  return $postdata;
}

function cgih_preprocess_raw_tribe_organizer($postdata) {
  // Find the event this organizer appears on and move its metadata there.
  // Then, mark this post as DO NOT IMPORT
  return $postdata;
}