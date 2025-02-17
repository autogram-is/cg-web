<?php

function cg_remove_archived($force_delete = false, $dry_run = false) {
  /*
   $pages_to_delete = load_migration_csv('archive-list.csv');

  foreach ($pages_to_delete as $row) {
    // id,type,slug,action,notes
    if (!$dry_run) {
      if ($force_delete || $row['action'] === 'DELETE') {
        wp_delete_post($row['id'], true);
      } else if ($row['action'] === 'ARCHIVE') {
        if ($force_delete) {
          wp_trash_post($row['id']); 
        } else {
          wp_update_post(array(
            'ID'          =>  $row['id'],
            'post_status' =>  'private',
          ));
        }
      }
    }
    WP_CLI::log(($dry_run ? "Dry Run: " : "") . (($force_delete || $row['action'] === 'DELETE') ? "Deleted #" : "Archived #") . $row['id'] . ' (' . $row['slug'] .')');
  }
  */

  // Find posts with the migration_status field set to 'delete' or 'archive' and change their status as appropriate.
  $query = new WP_Query(array(
    'posts_per_page' => -1,
    'fields' => 'ids',
    'meta_query' => array(
      'state_clause' => array(
        'key' => 'migration_status',
        'value' => ['archive', 'delete'],
        'compare' => 'IN',
      )
    ),
  ));

  if (!$dry_run) {
    $posts = $query->posts;
    foreach ($posts as $post_id) {
      wp_trash_post($post_id);
    }
  }
  WP_CLI::log(($dry_run ? "Dry Run: " : "") . "$query->post_count posts trashed");
}