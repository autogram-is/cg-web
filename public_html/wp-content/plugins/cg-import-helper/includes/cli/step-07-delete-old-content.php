<?php

function cg_archive_content($force_delete = false, $dry_run = false) {
  $datalines = file(CG_MIGRATION_DATA_DIR . '/archive-list.csv');
  $header = str_getcsv($datalines[0]);

  $index = 0;
  foreach ($datalines as $line) {
    // Parse each line using str_getcsv
    $data = str_getcsv($line);
      
    // Skip empty lines and header row
    if (empty($data) || $index == 0) {
      $index++;
      continue;
    }
    
    $row = array_combine($header, $data);

    if (!$dry_run) {
      if ($force_delete || $row['action'] === 'DELETE') {
        wp_delete_post($row['id'], true);
      } else if ($row['action'] === 'ARCHIVE') {
        wp_update_post(array(
          'ID'          =>  $row['id'],
          'post_status' =>  'private',
        ));
      }
    }
    WP_CLI::log(($dry_run ? "Dry Run: " : "") . (($force_delete || $row['action'] === 'DELETE') ? "Deleted #" : "Archived #") . $row['id'] . ' (' . $row['slug'] .')');
  }
}