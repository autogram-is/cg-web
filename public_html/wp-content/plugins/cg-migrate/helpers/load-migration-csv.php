<?php

function load_migration_csv($filename = NULL) {
  $results = array();
  if ($filename) {
    $datalines = file(CG_MIGRATE_DATA_DIR . '/' . $filename);
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

      $results[] = array_combine($header, $data);		 
    }
  }

  return $results;
}