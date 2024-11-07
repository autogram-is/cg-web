<?php

// Incoming portfolio items have several key elements:
// - post_content in ugly fusion markup; we'll use a standard extractor to get it out.

function cgih_preprocess_raw_cg_project($postdata) {
  $gallery = cgih_fusion_extract_slides($postdata['post_content']);
  $facts = cgih_fusion_extract_facts($postdata['post_content']);

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

  // This extracts sector, service, office, and region relationships.
  $relationships = cgih_extract_project_relationships_from_tags($postdata);
  foreach($relationships as $key => $val) {
    if ($val) {
      $postdata['postmeta'][] = array(
        'key' => $key,
        'value' => $val,
      );
    }
  }

  $postdata['terms'] = [];
  $postdata['post_content'] = $new_body;

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

function cgih_extract_project_relationships_from_tags($postdata) {
  $rel = [];
  if (array_key_exists('terms', $postdata)) {
    foreach ($postdata['terms'] as $term) {
      if ($term['domain'] === 'portfolio_skills') {
        // Services
        $t = tag_to_service($term['slug']);

        if ($t) {
          $rel['services'][] = $t->ID;
        }
      } elseif ($term['domain'] === 'portfolio_category') {
        // Sectors
        $t = tag_to_sector($term['slug']);
        if ($t) {
          $rel['sectors'][] = $t->ID;
        }
      } elseif ($term['domain'] === 'portfolio_tags') {
        // Offices, also region
        $t = tag_to_office($term['slug']);
        if ($t) {
          $rel['offices'][] = $t->ID;
          $regions = get_post_meta($t->ID, 'region');
          if ($regions[0]) {
            $rel['regions'][] = $regions[0];
          }
        }
      }
    }
  }
  return $rel;
}