<?php

/**
 * Output map structure:
 * 
 * [
 *   $term_id => [
 *     'action'   => // RETAG, RELATE, UNTAG, ARCHIVE
 *     'new_type' => // post-type or taxonomy name
 *     'new_slug' => // slug of post or taxonomy item
 *   ],
 * ]
 * 
 * Depending on the type of post being processed, different actions may
 * need to be taken (ie, populating the 'related_portfolio_items' relationship
 * rather than the 'services' relationship when processing news items)
 */

function cg_get_tag_map($rebuild = false) {
  // Load the tag / post map from the CSV file
  $map = get_transient('cg_tag_map');

  if ($map === false || $rebuild) {
    WP_CLI::log("Building tag/relationship map");

    $map = [];
    $tags = load_migration_csv('tags.csv');

    foreach ($tags as $row) {
      if ($row['term_id']) {
        if ($row['action'] === 'RETAG') {
          $replacement = term_exists($row['new_slug'], $row['new_type']);
          if ($replacement) {
            $map[intval($row['term_id'])] = array(
              'action' => $row['action'],
              'old' => $row['taxonomy'],
              'type'   => $row['new_type'],
              'id'     => intval($replacement['term_id']),
            );  
          }
        } elseif ($row['action'] === 'RELATE') {
          $replacement = get_post_by_name($row['new_slug'], $row['new_type']);
          if ($replacement) {
            $map[intval($row['term_id'])] = array(
              'action' => $row['action'],
              'old' => $row['taxonomy'],
              'type'   => $row['new_type'],
              'id'     => intval($replacement->ID),
            );  
          }
        } else {
          $map[intval($row['term_id'])] = array(
            'old' => $row['taxonomy'],
            'action' => $row['action'],
          );
        }
      }
    }

    set_transient('cg_tag_map', $map, 3600);
  }

  return $map;
}
