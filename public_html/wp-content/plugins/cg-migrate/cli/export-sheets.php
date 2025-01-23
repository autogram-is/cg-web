<?php

function cg_export_projects($dry_run = false) {
  $filename = 'projects.csv';
  $headers = [
    'id',
    'date',
    'title',
    'slug',

    'client',
    'facility',
    'location',
    'start_date',
    'end_date',

    'owner',
    'architect',
    'vendors',
    'contractors',

    'case_study_id',

    'sector1',
    'sector2',
    'sector3',
    'sector4',
    'service1',
    'service2',
    'service3',
    'service4',
    'office1',
    'office2',
    'office3',
  ];
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
        'date' => get_the_date("Y-n-j", $post),
        'title' => $post->post_title,
        'slug' => $post->post_name,
    
        'client' => get_field('client', $post->ID),
        'facility' => get_field('facility', $post->ID),
        'location' => get_field('location', $post->ID),
        'start_date' => get_field('start_date', $post->ID),
        'end_date' => get_field('end_date', $post->ID),
    
        'owner' => get_field('owner', $post->ID),
        'architect' => get_field('architect', $post->ID),
        'vendors' => get_field('vendors', $post->ID),
        'contractors' => get_field('contractors', $post->ID),
    
        'case_study_id' => '',

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
      );

      $items[] = array_values($item);
    }
  }

  if (count($items) > 1) {
    write_content_csv($filename, $items, $headers);
  }
}

function cg_export_offices($dry_run = false) {
  $filename = 'offices.csv';
  $headers = [
    'id',
    'title',
    'slug',

    'location',
    'email',
    'phone',
    'address',
    'region',
  ];
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
    
        'location' => get_field('location', $post->ID),
        'email' => get_field('email', $post->ID),
        'phone' => get_field('phone', $post->ID),
        'address' => get_field('address', $post->ID),
        'region' => $region,
      );

      $items[] = array_values($item);
    }
  }

  if (count($items) > 1) {
    write_content_csv($filename, $items, $headers);
  }
}

function cg_export_bios($dry_run = false) {
  $filename = 'bios.csv';
  $headers = [
    'id',
    'title',
    'slug',
    
    'role',
    'show_contact',
    'email',
    'phone',
    'linkedin',

    'sector1',
    'sector2',
    'sector3',
    'service1',
    'service2',
    'service3',
    'office',
  ];
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
      );

      $items[] = array_values($item);
    }
  }

  if (count($items) > 1) {
    write_content_csv($filename, $items, $headers);
  }
}

// Exports ids/titles/statuses of all posts and pages
function cg_export_news($post_ids = [], $dry_run = false) {
  $filename = 'news.csv';
  $headers = [
    'id',
    'date',
    'title',
    'slug',
    'category',

    'location',
    'byline',

    'reprint_url',
    'reprint_publication',
    'reprint_logo',

    'podcast',
    'season',
    'episode',
    'youtube_url',
    'buzzsprout_id',

    'sector1',
    'sector2',
    'sector3',
    'service1',
    'service2',
    'service3',
    'bio1',
    'bio2',
    'bio3',
    'office',
  ];
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
        'date' => get_the_date("Y-n-j", $post),
        'title' => $post->post_title,
        'slug' => $post->post_name,
        'category' => $category,
    
        'location' => get_field('location', $post->ID),
        'byline' => get_field('reprint_author', $post->ID),

        'reprint_url' => get_field('reprint_url', $post->ID),
        'reprint_publication' => get_field('reprint_publication', $post->ID),
        'reprint_logo' => get_field('reprint_logo', $post->ID),

        'podcast' => get_field('podcast_name', $post->ID),
        'season' => get_field('podcast_season', $post->ID),
        'episode' => get_field('podcast_episode', $post->ID),
        'youtube_url' => get_field('podcast_youtube_url', $post->ID),
        'buzzsprout_id' => get_field('podcast_buzzsprout_id', $post->ID),

        'sector1' => $sectors[0] ?? '',
        'sector2' => $sectors[1] ?? '',
        'sector3' => $sectors[2] ?? '',
        'service1' => $services[0] ?? '',
        'service2' => $services[1] ?? '',
        'service3' => $services[2] ?? '',
        'bio1' => $bios[0] ?? '',
        'bio2' => $bios[1] ?? '',
        'bio3' => $bios[2] ?? '',
        'office' => $offices[2] ?? '',
      );

      $items[] = array_values($item);
    }
  }

  if (count($items) > 1) {
    write_content_csv($filename, $items, $headers);
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