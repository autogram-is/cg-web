<?php

//  1. Build out Region, Office, Sector, and Service skeleton
function cg_cli_build_hierarchy($dry_run = false, $preserve = false) {
  _populate_regions($dry_run, $preserve);
  _populate_sectors($dry_run, $preserve);
  _populate_services($dry_run, $preserve);
  _populate_offices($dry_run, $preserve);
}

function _populate_regions($dry_run = false) {
  $items = load_migration_csv('regions.csv');

  if ($dry_run) {
    WP_CLI::log("Dry-Run: ". count($items) . " regions defined");
  } else {
    foreach ($items as $item) {
      if (!$item['title']) continue;

      $parent = $item['zone'] ? term_exists($item['zone'], 'region') : NULL;
      $parent_id = $parent ? $parent['term_id'] : NULL;
  
      $term_id = wp_insert_term(
        $item['title'],
        'region',
        array(
          'slug' => $item['slug'],
          'parent' => $parent_id
        )
      );

      WP_CLI::log("Created region tag $term_id (". $item['title'] . ")");
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
    foreach ($old_sectors as $post) {
      wp_delete_post($post->ID, true);
      WP_CLI::log("Deleted legacy Sector $post->ID");
    }
    foreach ($old_eu_sectors as $post) {
      wp_delete_post($post->ID, true);
      WP_CLI::log("Deleted legacy Sector $post->ID");
    }
  }

  $items = load_migration_csv('sectors.csv');

  foreach($items as $item) {
    if (!$item['title']) continue;

    $post_data = array(
      'post_type'     => 'cg_sector',
      'post_title'    => sanitize_text_field($item['title']),
      'post_name'     => sanitize_text_field($item['slug']),
      'locale'        => empty($item['locale']) ? NULL : $item['locale'],
      'post_status'   => 'publish',
      'post_author'   => get_current_user_id(),
      'post_date'     => current_time('mysql'),
    );

    if ($dry_run) {
      WP_CLI::log("Dry-Run: ". $post_data['post_type'] ." '". $post_data['post_title'] . "' created.");
    } else {
      $post_id = wp_insert_post($post_data, true);
      if ($post_id) {
        // Check $item['region'] and connect to appropriate taxonomy tag

        if ($item['locale']) {
          update_field('locale', $item['locale'], $post_id);
        }
        WP_CLI::log($post_data['post_type'] ." '". $post_data['post_title'] . "' ($post_id) created.");
      }
    }
  }
}

function _populate_services($dry_run = false, $preserve = false) {
  // Load all the children of the existing 'Services' landing page
  $old_services = get_all_child_pages(14996);

  if ($dry_run) {
    WP_CLI::log("Dry-Run: ". count($old_services) ." legacy Sectors found");
  } else if (!$preserve) {
    foreach ($old_services as $post) {
      wp_delete_post($post->ID, true);
      WP_CLI::log("Deleted legacy Service $post->ID");
    }
  }

  $items = load_migration_csv('services.csv');

  foreach($items as $item) {
    if (!$item['title']) continue;
    
    $post_data = array(
      'post_type'     => 'cg_service',
      'post_title'    => sanitize_text_field($item['title']),
      'post_name'     => sanitize_text_field($item['slug']),
      'locale'        => empty($item['locale']) ? NULL : $item['locale'],
      'post_status'   => 'publish',
      'post_author'   => get_current_user_id(),
      'post_date'     => current_time('mysql'),
    );

    if ($dry_run) {
      WP_CLI::log("Dry-Run: ". $post_data['post_type'] ." '". $post_data['post_title'] . "' created.");
    } else {
      $post_id = wp_insert_post($post_data, true);
      if ($post_id) {
        // Check $item['region'] and connect to appropriate taxonomy tag

        if ($item['locale']) {
          update_field('locale', $item['locale'], $post_id);
        }
        WP_CLI::log($post_data['post_type'] ." '". $post_data['post_title'] . "' ($post_id) created.");
      }
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

  $items = load_migration_csv('offices.csv');

  foreach($items as $item) {
    if (!$item['title']) continue;

    $post_data = array(
      'post_type'     => 'cg_office',
      'post_title'    => sanitize_text_field($item['title']),
      'post_name'     => sanitize_text_field($item['slug']),
      'post_status'   => 'publish',
      'post_author'   => get_current_user_id(),
      'post_date'     => current_time('mysql'),
    );

    if ($dry_run) {
      WP_CLI::log("Dry-Run: ". $post_data['post_type'] ." '". $post_data['post_title'] . "' created.");
    } else {
      $post_id = wp_insert_post($post_data, true);
      if ($post_id) {
        if ($item['location']) {
          update_field('locale', $item['location'], $post_id);
        }
        if ($item['email']) {
          update_field('locale', $item['email'], $post_id);
        }
        if ($item['phone']) {
          update_field('locale', $item['phone'], $post_id);
        }
        if ($item['address']) {
          update_field('locale', $item['address'], $post_id);
        }

        if($item['region']) {
          $region_id = $item['region'] ? term_exists($item['region'], 'region') : NULL;
          if ($region_id) {
            wp_set_object_terms($post_id, $region_id, 'region');
          }
        }

        WP_CLI::log($post_data['post_type'] ." '". $post_data['post_title'] . "' ($post_id) created.");
      }
    }
  }
}

function get_all_child_pages($post_id) {
  $params = array('post_type' => 'page', 'posts_per_page' => -1);
  $query = new WP_Query();
  $all_wp_pages = $query->query($params);

  return get_page_children($post_id, $all_wp_pages);
}