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
  // In addition, if there are multiple tags we need to collapse them into a stringified
  // array.
  if ($postdata['terms']) {
    foreach ($postdata['terms'] as $term) {
      if ($term['domain'] === 'portfolio_skills') {
        // Services
        $service = get_page_by_name($term['slug'], 'cg_service');
        if ($service) {
          $_services[] = $service['post_id'];
        }
      } elseif ($term['domain'] === 'portfolio_category') {
        // Sectors
        $sector = get_page_by_name($term['slug'], 'cg_sector');
        if ($sector) {
          $_sectors[] = $sector['post_id'];
        }
      } elseif ($term['domain'] === 'portfolio_tags') {
        // Offices
        $office = get_page_by_name($term['slug'], 'cg_office');
        if ($office) {
          $_offices[] = $office['post_id'];
        }
      }
    }
  }

  if ($_sectors) {
    $postdata['postmeta'][] = array(
      'key' => 'sectors',
      'value' => $_sectors,
    );
  }
  if ($_services) {
    $postdata['postmeta'][] = array(
      'key' => 'services',
      'value' => $_services,
    );
  }
  if ($_offices) {
    $postdata['postmeta'][] = array(
      'key' => 'offices',
      'value' => $_offices,
    );
  }

  $postdata['terms'] = [];
  $postdata['post_content'] = $new_body;

  return $postdata;
};

function cgih_preprocess_post_import_cg_project($postdata, $post = null) {
  // After the post itself has been assembled, do this.
}

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
