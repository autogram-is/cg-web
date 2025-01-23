<?php
function cg_import_projects($dry_run = false) {
  $items = load_content_csv('projects.csv');
  $updated = 0;
  $created = 0;

  if ($dry_run) {
    WP_CLI::log("Dry-Run: " . count($items) . " projects read");
  } else {
    foreach ($items as $item) {
      $id = cg_save_project($item);
      if ($item['id'] && $item['id'] === $id) {
        $updated++;
      } elseif (!$item['id'] && $id) {
        $created++;
      }
    }
    WP_CLI::log("$updated projects updated, $created created");
  }
}

function cg_import_offices($dry_run = false) {
  $items = load_content_csv('offices.csv');
  $updated = 0;
  $created = 0;

  if ($dry_run) {
    WP_CLI::log("Dry-Run: " . count($items) . " projects read");
  } else {
    foreach ($items as $item) {
      $id = cg_save_office($item);
      if ($item['id'] && $item['id'] === $id) {
        $updated++;
      } elseif (!$item['id'] && $id) {
        $created++;
      }
    }
    WP_CLI::log("$updated offices updated, $created created");
  }
}

function cg_import_bios($dry_run = false) {
  $items = load_content_csv('bios.csv');
  $updated = 0;
  $created = 0;

  if ($dry_run) {
    WP_CLI::log("Dry-Run: " . count($items) . " bios read");
  } else {
    foreach ($items as $item) {
      $id = cg_save_person($item);
      if ($item['id'] && $item['id'] === $id) {
        $updated++;
      } elseif (!$item['id'] && $id) {
        $created++;
      }
    }
    WP_CLI::log("$updated bios updated, $created created");
  }
}

function cg_import_news($dry_run = false) {
  $items = load_content_csv('news.csv');
  $updated = 0;
  $created = 0;

  if ($dry_run) {
    WP_CLI::log("Dry-Run: " . count($items) . " news posts read");
  } else {
    foreach ($items as $item) {
      $id = cg_save_news($item, false, false);
      if ($item['id'] && $item['id'] === $id) {
        $updated++;
      } elseif (!$item['id'] && $id) {
        $created++;
      }
    }
    WP_CLI::log(count($items) . " news posts updated");
  }

}
