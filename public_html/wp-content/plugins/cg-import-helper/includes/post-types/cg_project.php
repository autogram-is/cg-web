<?php

// Incoming portfolio items have several key elements:
// - post_content in ugly fusion markup; we'll use a standard extractor to get it out.

function cgih_preprocess_raw_cg_project($postdata) {
  $body = $postdata['post_content'];
  $dom = new DOMDocument;
  $dom->loadHTML($body);

  $new_body = '';
  foreach ($dom->getElementsByTagName('fusion_text') as $node) {
    if ( $node->textContent ) {
      $new_body .= '<p>' . $node->textContent . '</p>' . PHP_EOL;
    }
  }

  // In each TR, the first TD should be treated as a key and the second
  // should be treated as a value. Most will need to be matched to the
  // Sector, Service, Office, and Region relationships while others will
  // be shuffled to the extra data properties.

  foreach ($dom->getElementsByTagName('tr') as $node) {
    if ( $node->textContent ) {
      $segments = explode(PHP_EOL, $node->textContent);
      $key = trim($segments[0]);
      $value = join(', ', array_slice($segments, 1));
      $new_body .= '<p><strong>'.$key.'</strong> '.$value.'</p>' . PHP_EOL;
    }
  }

  $postdata['post_content'] = $new_body;

  return $postdata;
};