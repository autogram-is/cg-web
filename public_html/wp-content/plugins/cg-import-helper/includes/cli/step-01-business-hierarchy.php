<?php

use joshtronic\LoremIpsum;

//  1. Build out Region, Office, Sector, and Service skeleton
function cg_cli_build_hierarchy($dry_run = false, $preserve = false, $lipsum = false) {
  // Import the portfolio-hierarchy.csv data
  $lorem = new LoremIpsum();
  $datalines = file(CG_MIGRATION_DATA_DIR . '/portfolio-hierarchy.csv');
  $header = str_getcsv($datalines[0]);

  // We accumulate ready-to-save posts into this array. It's less efficient than
  // doing them one at a time, but this dataset isn't that large and it lets us
  // sort them for dependencies if necessary.
  $posts = array();

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

    // Prepare post data
    $post_data = array(
      'ID'            => $row['id'],
      'post_type'     => $row['type'],
      'post_title'    => sanitize_text_field($row['title']),
      'post_name'     => sanitize_text_field($row['slug']),
      'post_parent'   => $row['parent'], // If this is a title, we search for parent ID
      'region'        => empty($row['region']) ? NULL : $row['region'],
      'locale'        => empty($row['locale']) ? NULL : $row['locale'],
      'post_status'   => 'publish',
      'post_author'   => get_current_user_id(),
      'post_date'     => current_time('mysql'),
      'post_content'  => $lipsum ? $lorem->paragraphs(1, 'p') : NULL,
    );

    $posts[$index] = $post_data;
    $index++;
  }

  foreach ($posts as $post) {
    if ($post['post_parent']) {
      $post['post_parent'] = post_exists($post['post_parent'], '', '', $post['post_type']);
    }

    $region = $post['region'];
    $locale = $post['locale'];
    unset($post['region']);
    unset($post['locale']);

    $post_data = sanitize_post($post, 'db');

    if ($dry_run) {
      WP_CLI::log("Dry-Run: ". $post['post_type'] ." '". $post['post_title'] . "' built.");
    } else {
      $post_id = wp_insert_post($post, true);

      // If locales or regions were set for the post, update them here.
      // Doing this explicitly ensures ACF keeps keeps track of field associations.
      if ($post_id) {
        WP_CLI::log($post['post_type'] ." '". $post['post_title'] . "' ($post_id) created.");

        if ($region) {
          $rid = post_exists($region, '', '', 'cg_region');
          if ($rid) {
            update_field('regions', [$rid], $post_id);
          }
        }
        if ($locale) {
          update_field('locale', $locale, $post_id);
        }
      }
    }
  }

  // Delete old sector, service, and office posts
  if (!$preserve) {
    $sectors = get_all_child_pages(15041);
    $services = get_all_child_pages(14996);
    $eu_sectors = get_all_child_pages(57642);
    $offices = get_all_child_pages(62959);

    if ($dry_run) {
      WP_CLI::log("Dry-Run: ". count($sectors) ." legacy Sectors found");
      WP_CLI::log("Dry-Run: ". count($services) + count($eu_sectors) ." legacy Sectors found");
      WP_CLI::log("Dry-Run: ". count($offices) ." legacy Offices found");

    } else {
      foreach ($sectors as $post) {
        wp_delete_post($post->ID, true);
        WP_CLI::log("Deleted legacy Sector $post->ID");
      }
      foreach ($eu_sectors as $post) {
        wp_delete_post($post->ID, true);
        WP_CLI::log("Deleted legacy Sector $post->ID");
      }
      foreach ($services as $post) {
        wp_delete_post($post->ID, true);
        WP_CLI::log("Deleted legacy Service $post->ID");
      }
      foreach ($offices as $post) {
        wp_delete_post($post->ID, true);
        WP_CLI::log("Deleted legacy Office $post->ID");
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