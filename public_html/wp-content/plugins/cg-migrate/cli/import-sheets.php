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
      if (($item['id'] ?? false) && $item['id'] === $id) {
        $updated++;
      } elseif (!($item['id'] ?? false) && $id) {
        $created++;
      }
    }
    WP_CLI::log("$updated offices updated, $created created");
  }
}

function cg_import_bios($dry_run = false) {
  $items = load_content_csv('people.csv');
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


function _cols_to_id_array($post_data, $type, $cols) {
  $slugs = [];
  foreach($cols as $col) {
    if (array_key_exists($col, $post_data) && $post_data[$col]) {
      $slugs[] = trim($post_data[$col]);
    }
  }

  $ids = [];
  foreach (array_unique($slugs) as $slug) {
    $post = get_post_by_name($slug, $type);
    if ($post) $ids[] = $post->ID;
  };

  return $ids;
}