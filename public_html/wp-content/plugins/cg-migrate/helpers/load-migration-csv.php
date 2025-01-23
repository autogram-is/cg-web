<?php

function load_migration_csv($filename = NULL) {
  if ($filename) {
    return load_csv(CG_MIGRATE_DATA_DIR . '/' . $filename);
  }
  return [];
}

function write_migration_csv($filename, $items = [], $headers = NULL) {
  if ($filename) {
    write_csv(CG_MIGRATE_DATA_DIR . '/' . $filename, $items, $headers);
  }
}

function load_content_csv($filename = NULL) {
  if ($filename) {
    return load_csv(CG_MIGRATE_CONTENT_DIR . '/' . $filename);
  }
  return [];
}

function write_content_csv($filename, $items = [], $headers = NULL) {
  if ($filename) {
    write_csv(CG_MIGRATE_CONTENT_DIR . '/' . $filename, $items, $headers);
  }
}


function load_csv($filename = NULL) {
  $results = array();
  if ($filename) {
    $datalines = file($filename);
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

function write_csv($filename, $items = [], $headers = NULL) {
  if ($filename) {
    $fp = fopen($filename, 'w');

    if ($headers) {
      fputcsv($fp, $headers, ',', '"', '');
    }
    foreach ($items as $fields) {
      fputcsv($fp, $fields, ',', '"', '');
    }

    fclose($fp);
  }
}


