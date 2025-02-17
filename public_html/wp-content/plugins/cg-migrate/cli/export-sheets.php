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
      $facts = get_field('facts', $post->ID) ?? [];

      $item = array(
        'id' => $post->ID,
        'date' => get_the_date("Y-m-d", $post),
        'title' => $post->post_title,
        'slug' => $post->post_name,
    
        'client' => $facts['client'] ?? '',
        'facility' => $facts['facility'] ?? '',
        'location' => $facts['facts'] ?? '',
        'start_date' => $facts['start_date'] ?? '',
        'end_date' => $facts['end_date'] ?? '',
    
        'owner' => $facts['owner'] ?? '',
        'architects' => str_replace("\n", "; ", ($facts['architects'] ?? '')),
        'vendors' => str_replace("\n", "; ", ($facts['vendors'] ?? '')),
        'contractors' => str_replace("\n", "; ", ($facts['contractors'] ?? '')),
    
        'case_study_pdf' => get_field('case_study_pdf', $post->ID, false),

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

      // We're only handling the first office in this import;
      // A handfull of offices are actually 2-3 offices in one city.

      $details = get_field('office_details', $post->ID);
      if ($details) {
        $name = $details[0]['name'] ?? '';
        $email = $details[0]['email'] ?? '';
        $phone = $details[0]['phone'] ?? '';
        $address = $details[0]['address'] ?? '';
      } else {
        $name = '';
        $email = '';
        $phone = '';
        $address = '';
      }

      $item = array(
        // 'id' => $post->ID,
        'title' => $post->post_title,
        'slug' => $post->post_name,
    
        'location' => get_field('location', $post->ID, false),

        'name' => $name,
        'email' => $email,
        'phone' => wp_kses($phone, 'strip'),
        'address' => $address,

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
        'title' => $post->post_title,
        'slug' => $post->post_name,
    
        'role' => get_field('role', $post->ID),
        'generate_bio_page' => get_field('generate_bio_page', $post->ID),
        'hide_contact' => get_field('hide_contact', $post->ID),
        'ex_employee' => get_field('ex_employee', $post->ID),
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

        'headshot' =>  attachment_url_to_postid(get_the_post_thumbnail_url($post->ID)),
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
      $related = _get_rel_slugs('related_portfolio_items', $post->ID, TRUE);
      $internal_bylines = _get_rel_slugs('internal_byline', $post->ID);
      $categories = get_the_terms($id, 'news_category');
      if (is_wp_error($categories)) {
        $category = '';
      } else {
        $category = $categories[0]->slug ?? '';
      }

      $item = array(
        'id' => $post->ID,
        'date' => get_the_date("Y-m-d", $post),
        'title' => $post->post_title,
        'slug' => $post->post_name,
        'news_category' => $category,
    
        'location' => get_field('location', $post->ID, false),
        'byline' => get_field('external_byline', $post->ID, false),

        'reprint_url' => get_field('reprint_url', $post->ID, false),
        'reprint_publication' => get_field('reprint_publication', $post->ID, false),
        'reprint_logo' => get_field('reprint_logo', $post->ID),

        'podcast' => get_field('podcast_name', $post->ID, false),
        'season' => get_field('podcast_season', $post->ID, false),
        'episode' => get_field('podcast_episode', $post->ID, false),
        'youtube_url' => get_field('podcast_youtube_url', $post->ID, false),
        'mp3_url' => get_field('podcast_mp3_id', $post->ID, false),

        'sector1' => $related['sector'][0] ?? '',
        'sector2' => $related['sector'][1] ?? '',
        'sector3' => $related['sector'][2] ?? '',

        'service1' => $related['service'][0] ?? '',
        'service2' => $related['service'][1] ?? '',
        'service3' => $related['service'][2] ?? '',

        'office1' => $related['office'][0] ?? '',
        'office2' => $related['office'][1] ?? '',
        'office3' => $related['office'][2] ?? '',
        'office4' => $related['office'][3] ?? '',

        'internal_byline1' => $internal_bylines[0] ?? '',
        'internal_byline2' => $internal_bylines[1] ?? '',

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
        'created' => date("Y-m-d", strtotime($post->post_date)),
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
      $related = _get_rel_slugs('related_portfolio_items', $post, true);

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

function _get_rel_slugs($relationship, $post, $nest_by_type = false) {
  $output = [];
  $ids = get_field($relationship, $post);
  if ($ids) {
    foreach ($ids as $id) {
      $related = get_post($id);
      if ($nest_by_type) {
        if (!array_key_exists($related->post_type, $output)) {
          $output[$related->post_type] = [];
        }
        $output[$related->post_type][] = $related->post_name;
      } else {
        $output[] = $related->post_name;
      }
    }
  }
  return $output;
}
