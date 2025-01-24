<?php

function cg_export_projects($dry_run = false) {
  $filename = 'projects.csv';
  $items = [];

  // Execute the query
  $query = new WP_Query(array(
    'post_type'      => 'project',
    'fields'         => 'ids',
    'posts_per_page' => -1,
  ));
  $ids = $query->posts;

  foreach ($ids as $id) {
    $post = _load_post($id);
    if ($post) {
      $sectors = _get_rel_slugs('sectors', $post->ID);
      $services = _get_rel_slugs('services', $post->ID);
      $offices = _get_rel_slugs('offices', $post->ID);

      $item = array(
        'id' => $post->ID,
        'date' => get_the_date("Y-m-d", $post),
        'title' => $post->post_title,
        'slug' => $post->post_name,
    
        'client' => get_field('client', $post->ID, false),
        'facility' => get_field('facility', $post->ID, false),
        'location' => get_field('location', $post->ID, false),
        'start_date' => get_field('start_date', $post->ID, false),
        'end_date' => get_field('end_date', $post->ID, false),
    
        'owner' => get_field('owner', $post->ID, false),
        'architect' => get_field('architect', $post->ID, false),
        'vendors' => get_field('vendors', $post->ID, false),
        'contractors' => get_field('contractors', $post->ID, false),
    
        'case_study_id' => get_field('migration_case_study', $post->ID, false),

        'sector1' => $sectors[0] ?? '',
        'sector2' => $sectors[1] ?? '',
        'sector3' => $sectors[2] ?? '',
        'sector4' => $sectors[3] ?? '',
        'service1' => $services[0] ?? '',
        'service2' => $services[1] ?? '',
        'service3' => $services[2] ?? '',
        'service4' => $services[3] ?? '',
        'office1' => $offices[0] ?? '',
        'office2' => $offices[1] ?? '',
        'office3' => $offices[2] ?? '',

        'migration_status' => get_field('migration_status', $post->ID, false),
        'migration_note' => get_field('migration_note', $post->ID, false),
      );

      $items[] = $item;
    }
  }

  if (count($items) > 1) {
    write_content_csv($filename, $items);
  }
}

function cg_export_offices($dry_run = false) {
  $filename = 'offices.csv';
  $items = [];

  // Execute the query
  $query = new WP_Query(array(
    'post_type'      => 'office',
    'fields'         => 'ids',
    'posts_per_page' => -1,
  ));
  $ids = $query->posts;

  foreach ($ids as $id) {
    $post = _load_post($id);
    if ($post) {
      $regions = get_the_terms($id, 'region');
      $region = $regions[0]->slug ?? '';

      $item = array(
        'id' => $post->ID,
        'title' => $post->post_title,
        'slug' => $post->post_name,
    
        'location' => get_field('location', $post->ID, false),
        'email' => get_field('email', $post->ID, false),
        'phone' => get_field('phone', $post->ID, false),
        'address' => get_field('address', $post->ID, false),
        'region' => $region,

        'migration_status' => get_field('migration_status', $post->ID, false),
        'migration_note' => get_field('migration_note', $post->ID, false),
      );

      $items[] = $item;
    }
  }

  if (count($items) > 1) {
    write_content_csv($filename, $items);
  }
}

function cg_export_bios($dry_run = false) {
  $filename = 'people.csv';
  $items = [];

  // Execute the query
  $query = new WP_Query(array(
    'post_type'      => 'person',
    'fields'         => 'ids',
    'posts_per_page' => -1,
  ));
  $ids = $query->posts;

  foreach ($ids as $id) {
    $post = _load_post($id);
    if ($post) {
      $sectors = _get_rel_slugs('sectors', $post->ID);
      $services = _get_rel_slugs('services', $post->ID);
      $offices = _get_rel_slugs('offices', $post->ID);

      $item = array(
        'id' => $post->ID,
        'title' => $post->post_title,
        'slug' => $post->post_name,
    
        'role' => get_field('role', $post->ID),
        'show_contact' => get_field('show_contact', $post->ID),
        'email' => get_field('email', $post->ID),
        'phone' => get_field('phone', $post->ID),
        'linkedin' => get_field('linkedin', $post->ID),

        'sector1' => $sectors[0] ?? '',
        'sector2' => $sectors[1] ?? '',
        'sector3' => $sectors[2] ?? '',
        'service1' => $services[0] ?? '',
        'service2' => $services[1] ?? '',
        'service3' => $services[2] ?? '',
        'office' => $offices[0] ?? '',

        'migration_status' => get_field('migration_status', $post->ID, false),
        'migration_note' => get_field('migration_note', $post->ID, false),
      );

      $items[] = $item;
    }
  }

  if (count($items) > 1) {
    write_content_csv($filename, $items);
  }
}

// Exports ids/titles/statuses of all posts and pages
function cg_export_news($post_ids = [], $dry_run = false) {
  $filename = 'news.csv';
  $items = [];

  // Execute the query
  $query = new WP_Query(array(
    'post_type'      => 'post',
    'fields'         => 'ids',
    'posts_per_page' => -1,
  ));
  $ids = $query->posts;

  foreach ($ids as $id) {
    $post = _load_post($id);
    if ($post) {
      $sectors = _get_rel_slugs('sectors', $post->ID);
      $services = _get_rel_slugs('services', $post->ID);
      $offices = _get_rel_slugs('offices', $post->ID);
      $bios = _get_rel_slugs('people', $post->ID);
      $categories = get_the_terms($id, 'category');
      $category = $categories[0]->slug ?? '';

      $item = array(
        'id' => $post->ID,
        'date' => get_the_date("Y-m-d", $post),
        'title' => $post->post_title,
        'slug' => $post->post_name,
        'category' => $category,
    
        'location' => get_field('location', $post->ID, false),
        'byline' => get_field('reprint_author', $post->ID, false),

        'reprint_url' => get_field('reprint_url', $post->ID, false),
        'reprint_publication' => get_field('reprint_publication', $post->ID, false),
        'reprint_logo' => get_field('reprint_logo', $post->ID),

        'podcast' => get_field('podcast_name', $post->ID, false),
        'season' => get_field('podcast_season', $post->ID, false),
        'episode' => get_field('podcast_episode', $post->ID, false),
        'youtube_url' => get_field('podcast_youtube_url', $post->ID, false),
        'buzzsprout_id' => get_field('podcast_buzzsprout_id', $post->ID, false),

        'sector1' => $sectors[0] ?? '',
        'sector2' => $sectors[1] ?? '',
        'sector3' => $sectors[2] ?? '',
        'service1' => $services[0] ?? '',
        'service2' => $services[1] ?? '',
        'service3' => $services[2] ?? '',
        'bio1' => $bios[0] ?? '',
        'bio2' => $bios[1] ?? '',
        'bio3' => $bios[2] ?? '',
        'office' => $offices[0] ?? '',

        'migration_status' => get_field('migration_status', $post->ID, false),
        'migration_note' => get_field('migration_note', $post->ID, false),
      );

      $items[] = $item;
    }
  }

  if (count($items) > 1) {
    write_content_csv($filename, $items);
  }
}

function cg_export_pages($dry_run = false) {
  $filename = 'pages.csv';
  $items = [];

  // Execute the query
  $query = new WP_Query(array(
    'post_type'      => 'page',
    'fields'         => 'ids',
    'posts_per_page' => -1,
  ));
  $ids = $query->posts;

  foreach ($ids as $id) {
    $post = _load_post($id);
    if ($post) {
      $item = array(
        'id' => $post->ID,
        'modified' => date("Y-m-d", strtotime($post->post_modified)),
        'title' => $post->post_title,
        'slug' => $post->post_name,
        'parent' => $post->post_parent ? $post->post_parent : '',
        'status' => $post->post_status ?? '',

        'migration_status' => get_field('migration_status', $post->ID, false),
        'migration_note' => get_field('migration_note', $post->ID, false),
      );
      $items[] = $item;
    }
  }

  if (count($items) > 1) {
    write_content_csv($filename, $items);
  }
}

function cg_export_events($dry_run = false) {
  $filename = 'events.csv';
  $items = [];

  // Execute the query
  $query = new WP_Query(array(
    'post_type'      => 'event',
    'fields'         => 'ids',
    'posts_per_page' => -1,
  ));
  $ids = $query->posts;

  foreach ($ids as $id) {
    $post = _load_post($id);
    if ($post) {
      $people = _get_rel_slugs('people', $post);

      $item = array(
        'id' => $post->ID,
        'title' => $post->post_title,
        'slug' => $post->post_name,

        'event_url' => get_field('event_url', $post->ID, false),
        'start_date' => get_field('start_date', $post->ID, false),
        'end_date' => get_field('end_date', $post->ID, false),
        'all_day' => get_field('all_day', $post->ID, false),
        'event_email' => get_field('event_email', $post->ID, false),
        'venue_name' => get_field('venue_name', $post->ID, false),
        'venue_url' => get_field('venue_url', $post->ID, false),
        'venue_phone' => get_field('venue_phone', $post->ID, false),
        'venue_address' => get_field('venue_address', $post->ID, false),
        'venue_city' => get_field('venue_city', $post->ID, false),
        'venue_state' => get_field('venue_state', $post->ID, false),
        'venue_postcode' => get_field('venue_postcode', $post->ID, false),
        'venue_country' => get_field('venue_country', $post->ID, false),
        'person1'  => $people[0] ?? '',
        'person2'  => $people[1] ?? '',
        'person3'  => $people[2] ?? '',
        'person4'  => $people[3] ?? '',
        'person5'  => $people[4] ?? '',

        'migration_status' => get_field('migration_status', $post->ID, false),
        'migration_note' => get_field('migration_note', $post->ID, false),
      );
      $items[] = $item;
    }
  }

  if (count($items) > 1) {
    write_content_csv($filename, $items);
  }
}


function _load_post($id) {
  $post = get_post($id);
  if ($post) {
    $taxonomies = get_post_taxonomies($id);      
    $post->meta = get_post_meta($id);
    $post->taxonomy = wp_get_post_terms($id, $taxonomies);  
  }
  return $post;
}

function _get_rel_slugs($relationship, $post) {
  $output = [];
  $ids = get_field($relationship, $post);
  if ($ids) {
    foreach ($ids as $id) {
      $related = get_post($id);
      $output[] = $related->post_name;
    }
  }
  return $output;
}