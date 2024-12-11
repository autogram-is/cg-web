<?php

/**
 * Map structure:
 * 
 * [
 *   $term_id => [
 *     'action'   => $action, // IGNORE, REMOVE, or REPLACE
 *     'taxonomy' => $taxonomy_name,
 *     'type'     => $post_type,
 *     'id'       => $post_id
 *   ],
 * ]
 */

function cg_get_tag_map() {
  // Load the tag / post map from the CSV file

  $map = get_transient('cg_tag_map');

  if ($map === false) {
    $map = [];
    $datalines = file(CG_MIGRATION_DATA_DIR . '/tag-map.csv');
    $header = str_getcsv($datalines[0]);
    
    $index = 0;
    foreach ($datalines as $line) {
      WP_CLI::log("Building tag map");

      $data = str_getcsv($line);
        
      // Skip empty lines and header row
      if (empty($data) || $index == 0) {
        $index++;
        continue;
      }
      
      $row = array_combine($header, $data);
  
      if ($row['term_id']) {
        if ($row['new_slug'] && $row['new_type']) {
          $replacement = get_post_by_name($row['new_slug'], $row['new_type']);
          if ($replacement) {
            $map[$row['term_id']] = array(
              'action' => 'REPLACE',
              'taxonomy' => $row['taxonomy'],
              'type' => $replacement->post_type,
              'id' => $replacement->ID,
            );
          }
        } else if ($row['action'] === 'REMOVE') {
          $map[$row['term_id']] = array(
            'taxonomy' => $row['taxonomy'],
            'action' => 'REMOVE'
          );
        } else if ($row['action'] === 'IGNORE') {
          $map[$row['term_id']] = array(
            'taxonomy' => $row['taxonomy'],
            'action' => 'IGNORE'
          );
        }
      }
    }

    set_transient('cg_tag_map', $map, 3600);
  }

  return $map;
}
