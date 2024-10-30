<?php

// Incoming portfolio items have several key elements:
// - post_content in ugly fusion markup; we'll use a standard extractor to get it out.

function cgih_preprocess_raw_cg_project($postdata) {
  $body = $postdata['post_content'];
  $dom = new DOMDocument;

  $contentType = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';

  $dom->loadHTML($contentType . $body);

  $new_body = '';
  foreach ($dom->getElementsByTagName('fusion_text') as $node) {
    if ( $node->textContent ) {
      $new_body .= '<p>' . $node->textContent . '</p>' . PHP_EOL;
    }
  }

  // Iterate over $postdata['terms'] to find the sectors, services, and offices
  // for a given case study.

  // We need a lookup table of old service/sector/office IDs and the new incoming ones.
  if ($postdata['terms']) {
    foreach ($postdata['terms'] as $term) {
      if ($term['domain'] === 'portfolio_skills') {
        // Services
        $postdata['postmeta'][] = array(
          'key' => 'cg_import_services',
          'value' => $term['slug'],
        );
      } elseif ($term['domain'] === 'portfolio_category') {
        // Sectors
        $postdata['postmeta'][] = array(
          'key' => 'cg_import_sectors',
          'value' => $term['slug'],
        );
      } elseif ($term['domain'] === 'portfolio_tags') {
        // Offices
        $postdata['postmeta'][] = array(
          'key' => 'cg_import_offices',
          'value' => $term['slug'],
        );
      }
    }
  }

  $postdata['terms'] = [];
  $postdata['post_content'] = $new_body;

  var_dump($postdata);

  return $postdata;
};

function cleanKey($key) {
  $key = str_replace('(s)', '', $key);
  $key = str_replace(':', '', $key);
  return trim($key);
}

function strToKey($key) {
  $key = cleanKey($key);
  $key = strtolower($key);
  $key = str_replace(' ', '_', $key);
  return 'cg_import_project_'.$key;
}
