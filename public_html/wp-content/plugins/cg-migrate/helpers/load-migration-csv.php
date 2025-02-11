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

      if (count($data) !== count($header)) {
      }

      try {
        $results[] = array_combine($header, $data);
      } catch (Exception $ex) {
        WP_CLI::log(var_export($header, true));
        WP_CLI::log(var_export($data, true));
        return [];
      }
    }
  }

  return $results;
}

function write_csv(string $filename, array $items = [], ?array $headers = NULL) {
  if (!$headers) {
    // Construct headers from the incoming items
    $headers = [];
    foreach ($items as $item) {
      $headers = array_merge($headers, array_keys($item));
      $headers = array_keys(array_flip($headers));
    }
  }

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


