<?php

//  1. Build out Region, Office, Sector, and Service skeleton
function cg_cli_build_hierarchy($dry_run = false, $preserve = false) {
  _populate_regions($dry_run, $preserve);
  _populate_sectors($dry_run, $preserve);
  _populate_services($dry_run, $preserve);
  _populate_offices($dry_run, $preserve);
}

function _populate_regions($dry_run = false) {
  $items = load_content_csv('regions.csv');

  if ($dry_run) {
    WP_CLI::log("Dry-Run: ". count($items) . " regions defined");
  } else {
    foreach ($items as $item) {
      if (!$item['title']) continue;

      if (term_exists($item['slug'], 'region')) {
        WP_CLI::log("Region '" . $item['title'] . "' already exists");
        continue;
      }

      // May want to change this to a custom meta field in the future
      $parent = $item['zone'] ? term_exists($item['zone'], 'region') : NULL;
      $parent_id = $parent ? $parent['term_id'] : NULL;
  
      $term = wp_insert_term(
        $item['title'],
        'region',
        array(
          'slug' => $item['slug'],
          'parent' => $parent_id
        )
      );

      if (is_array($term)) {
        WP_CLI::log("Region '" . $term['title'] . "' created (#". $item['title'] . ")");
      } else {
        WP_CLI::error($term);
      }

    }
  }
}

function _populate_sectors($dry_run = false, $preserve = false) {
  // Load all the children of the existing 'Sectors' and 'EU Sectors' landing pages
  $old_sectors = get_all_child_pages(15041);
  $old_eu_sectors = get_all_child_pages(57642);

  if ($dry_run) {
    WP_CLI::log("Dry-Run: ". (count($old_sectors) + count($old_eu_sectors)) ." legacy Sectors found");
  } else if (!$preserve) {
    WP_CLI::log((count($old_sectors) + count($old_eu_sectors)) ." legacy Sectors found");
    foreach ($old_sectors as $post) {
      wp_delete_post($post->ID, true);
      WP_CLI::log("Deleted legacy Sector $post->ID");
    }
    foreach ($old_eu_sectors as $post) {
      wp_delete_post($post->ID, true);
      WP_CLI::log("Deleted legacy Sector $post->ID");
    }
  }

  $items = load_content_csv('sectors.csv');

  foreach($items as $item) {
    if (!$item['title']) continue;

    if ($dry_run) {
      WP_CLI::log("Dry-Run: Sector '". $item['title'] . "' created.");
    } else {
      $id = cg_save_sector($item, true, true);
    }
  }
}

function _populate_services($dry_run = false, $preserve = false) {
  // Load all the children of the existing 'Services' landing page
  $old_services = get_all_child_pages(14996);

  if ($dry_run) {
    WP_CLI::log("Dry-Run: ". count($old_services) ." legacy Services found");
  } else if (!$preserve) {
    WP_CLI::log(count($old_services) ." legacy Services found");
    foreach ($old_services as $post) {
      wp_delete_post($post->ID, true);
      WP_CLI::log("Deleted legacy Service $post->ID");
    }
  }

  $items = load_content_csv('services.csv');

  foreach($items as $item) {
    if (!$item['title']) continue;

    if ($dry_run) {
      WP_CLI::log("Dry-Run: Service '". $item['title'] . "' created.");
    } else {
      $id = cg_save_service($item, true, true);
    }
  }
}

function _populate_offices($dry_run = false, $preserve = false) {
  $old_offices = get_all_child_pages(62959);
  if ($dry_run) {
    WP_CLI::log("Dry-Run: ". count($old_offices) ." legacy Offices found");
  } else if (!$preserve) {
    foreach ($old_offices as $post) {
      wp_delete_post($post->ID, true);
      WP_CLI::log("Deleted legacy Office $post->ID");
    }
  }

  $items = load_content_csv('offices.csv');

  foreach($items as $item) {
    if (!$item['title']) continue;
    if ($dry_run) {
      WP_CLI::log("Dry-Run: Office ". $item['title'] . "' created.");
    } else {
      $id = cg_save_office($item, true, true);
    }
  }
}

function _static_pages() {

}

function _navigation_menus($dry_run = false) {
  $menus = [];

  $items = load_migration_csv('nav-menus.csv');

  if ($dry_run) {

  } else {
    $menu_id = wp_create_nav_menu($menu_name);

    wp_update_nav_menu_item($menu_id, 0, array(
      'menu-item-title' => 'About',
      'menu-item-object' => 'page',
      'menu-item-object-id' => get_page_by_path('about')->ID,
      'menu-item-type' => 'post_type',
      'menu-item-status' => 'publish'
    ));
  }
}

function get_all_child_pages($post_id) {
  $params = array('post_type' => 'page', 'posts_per_page' => -1);
  $query = new WP_Query();
  $all_wp_pages = $query->query($params);

  return get_page_children($post_id, $all_wp_pages);
}