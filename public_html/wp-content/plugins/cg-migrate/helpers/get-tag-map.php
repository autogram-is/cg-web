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

function cg_get_tag_map($rebuild = false) {
  // Load the tag / post map from the CSV file

  $map = get_transient('cg_tag_map');

  if ($map === false || $rebuild) {
    WP_CLI::log("Building tag/relationship map");

    $map = [];
    $news = load_migration_csv('news-tags.csv');
    $projects = load_migration_csv('project-tags.csv');
    $items = array_merge($news, $projects);

    foreach ($items as $row) {
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
