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
  $primary = 0;
  $secondary = 0;
  $deleted = 0;

  $processed = [];

  if ($dry_run) {
    WP_CLI::log("Dry-Run: " . count($items) . " projects read");
  } else {
    foreach ($items as $item) {
      if ($item['migration_status'] === 'delete') {
        cg_delete_office($item['slug']);
        WP_CLI::log("office '" . $item['title'] . "' deleted. " . ($item['migration_notes'] ?? ''));
        $deleted++;
      } else {
        if (in_array($item['slug'], $processed)) {
          cg_add_loc_to_office($item);
          $secondary++;
        } else {
          $id = cg_save_office($item);
          $processed[] = $item['slug'];
          $primary++;
        }
      }
    }
    WP_CLI::log("$primary primary office locations updated, $secondary secondary; $deleted deleted");
  }
}

function cg_import_bios($dry_run = false) {
  $items = load_content_csv('people.csv');
  $updated = 0;
  $deleted = 0;

  if ($dry_run) {
    WP_CLI::log("Dry-Run: " . count($items) . " bios read");
  } else {
    foreach ($items as $item) {
      if ($item['migration_status'] === 'delete') {
        WP_CLI::log("person '" . $item['title'] . "' deleted " . $item['migration_notes']);
        $deleted++;
      } else {
        cg_save_person($item);
        $updated++;
      }
    }
    WP_CLI::log("$updated bios updated, $deleted deleted");
  }
}

function cg_import_news($dry_run = false) {
  $items = load_content_csv('news.csv');
  $deleted = 0;
  $updated = 0;

  if ($dry_run) {
    WP_CLI::log("Dry-Run: " . count($items) . " news posts read");
  } else {
    foreach ($items as $item) {
      if ($item['migration_status'] === 'delete') {
        wp_delete_post($item['id'], TRUE);
        WP_CLI::log("news '" . $item['title'] . "' deleted " . $item['migration notes']);
        $deleted++;
      } else {
        $item = cg_remap_news_import_fields($item);
        cg_save_news($item, false, false);
        $updated++;  
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