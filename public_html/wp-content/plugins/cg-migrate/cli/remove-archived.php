<?php

function cg_remove_archived($force_delete = false, $dry_run = false) {
  $pages_to_delete = load_migration_csv('archive-list.csv');

  foreach ($pages_to_delete as $row) {
    // id,type,slug,action,notes
    if (!$dry_run) {
      if ($force_delete || $row['action'] === 'DELETE') {
        wp_delete_post($row['id'], true);
      } else if ($row['action'] === 'ARCHIVE') {
        if ($force_delete) {
          wp_delete_post($row['id'], true); 
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
}