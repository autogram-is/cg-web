<?php

/**
 * Given the ID of a post, find any directly attached projects.
 * If there aren't enough to fill the `limit` parameter, search for
 * offices related to the post and retrieve projects related to 
 * those offices.
 */
function cg_projects_with_fallback(int $post_id, int $limit = 6) {
  $items = get_field('projects', $post_id) ?? [];
  if ($items && is_array($items)) {
    $directCount = count($items);
  } else {
    $items = [];
    $directCount = 0;
  }

  if ($limit > $directCount) {
    $offices = cg_get_related_offices($post_id);
    $nearby = cg_get_projects_for_portfolio_items($offices, ($limit - $directCount), $items) ?? [];
    if ($nearby) {
      $items = array_merge($items, $nearby);
    }
  }
  $items = array_slice($items, 0, $limit);
  return $items;
}

/**
 * Given the ID of a post, find any directly attached projects.
 * If there aren't enough to fill the `limit` parameter, search for
 * offices related to the post and retrieve projects related to 
 * those offices.
 */
function cg_news_with_fallback(int $post_id, int $limit = 6) {
  $output = [];
  $events = [];
  $news = [];

  $items = get_field('related_news', $post_id) ?? [];

  if (is_array($items) && count($items) > 0) {
    $events = cg_events_upcoming_and_current($items);
    $news = cg_news_no_older_than('-6 months', $items);
    $output = array_slice(array_merge($events, $news), 0, $limit);
  }

  if (count($output) < $limit) {
    $offices = cg_get_related_offices($post_id);
    $nearby_items = cg_get_news_for_portfolio_items($offices);

    if (is_array($nearby_items) && count($nearby_items) > 0) {
      $nearby_events = cg_events_upcoming_and_current($nearby_items);
      $nearby_news = cg_news_no_older_than('-6 months', $nearby_items);
    
      $events = array_unique(array_merge($events, $nearby_events));
      $news = array_unique(array_merge($news, $nearby_news));
      $final = array_merge($events, $news);
    
      $output = array_slice($final, 0, $limit);
    }
  }

  return $output;
}

function _remove_items($list, $items_to_remove) {
  $output = [];
  foreach ($list as $item) {
    if (!in_array($item, $items_to_remove)) {
      $output[] = $item;
    }
  }
  return $output;
}

/**
 * Given an item that is either tagged with a region, or has an `offices`
 * field, return its list of related offices.
 */
function cg_get_related_offices(int $post_id) {
  $offices = get_field('offices', $post_id);
  if (is_array($offices) && count($offices)) return $offices;

  $regions = wp_get_object_terms($post_id, 'region', array('fields' => 'ids'));
  if (is_wp_error($regions) || count($regions) === 0) {
    return [];
  }

  $region_content_query = [
    'post_type'      => 'office',
    'post__not_in'   => [$post_id],
    'fields'         => 'ids',
    'tax_query'      => [
      [
        'taxonomy' => 'region',
        'field'    => 'term_id',
        'terms'    => $regions,
      ],
    ],
  ];
  return (new WP_Query($region_content_query))->posts;
}

/**
 * Given an array of portfolio items (sectors, services, projects, or offices),
 * find associated projects.
 */
function cg_get_projects_for_portfolio_items(array $post_ids, int $limit = 6, array $ignore = [], string $mode = 'breadth') {
  $project_buckets = [];
  foreach ($post_ids as $post_id) {
    // This respects the order projects appear in on the item itself,
    // rather than the recency of the projects.
    $projects = get_field('projects', $post_id);
    if (is_array($projects) && count($projects) > 0) {
      $project_buckets[$post_id] = $projects;
    }
  }

  return cg_balance_buckets($project_buckets, $limit, $ignore);
}

/**
 * Given an array of portfolio items (sectors, services, projects, or offices),
 * find all associated news posts, events, and market intelligence reports.
 */
function cg_get_news_for_portfolio_items(array $post_ids, int $limit = 6, array $ignore = []) {
  $news_items = [];
  foreach ($post_ids as $post_id) {
    // No date cutoff; build a balanced mix of news from as many offices as possible.
    $items = get_field('related_news', $post_id);
    if (is_array($items) && count($items) > 0) {
      $news_items = array_merge($news_items, $items);
      $news_items = array_unique($news_items);
    }
  }
  return $news_items;
}

function cg_news_no_older_than(string $offset = '-6 months', ?array $ids = NULL) {
  $args = array(
    'post_type' => 'post',
    'posts_per_page' => -1,
    'perm' => 'readable',
    'date_query' => array(
      'after' => date('Y-m-d', strtotime($offset))
    ),
    'orderby'        => 'post_date',
    'order'          => 'DESC',
    'fields'         => 'ids'
  );
  if (is_array($ids)) {
    $args['post__in'] = $ids;
  }

  $posts = get_posts($args);
  return $posts;
}

function cg_events_upcoming_and_current(?array $ids = NULL) {
  $args = array(
    'post_type'      => 'event',
    'posts_per_page' => -1,
    'perm' => 'readable',
    'meta_query' => array(
      array(
        'key'     => 'end_date',
        'value'   => current_time('mysql'),
        'compare' => '>=',
        'type'    => 'DATE'
      )
    ),
    'orderby'        => 'start_date',
    'order'          => 'DESC',
    'fields'         => 'ids'
  );
  if (is_array($ids)) {
    $args['post__in'] = $ids;
  }
  $posts = get_posts($args);
  return $posts;
}

/**
 * Given a keyed array of arrays, output a flattened list of values
 * containing a mostly-equal number of values from each array.
 */
function cg_balance_buckets(array $lists = [], int $limit = 6, array $ignore = []) {
  $result = [];
  if (count($lists) > 0) {
    $maxLength = max(array_map('count', $lists));
    $count = 0;
    
    for ($i = 0; $i < $maxLength; $i++) {
      foreach ($lists as $list) {
        if (isset($list[$i]) && !in_array($list[$i], $ignore, true)) {
          $result[] = $list[$i];
          $ignore[] = $list[$i]; // Once it's in the list, never put it in again.
          $count++;
          if ($limit !== null && $count >= $limit) {
            return $result;
          }
        }
      }
    }
  }
  
  return $result;
}

/**
 * Uses Wordpress's Transient system to return a cached list of event IDs
 * that have already passed; thise can be used to exclude past events from
 * related content listings.
 */
function cg_past_event_ids($flush = FALSE) {
  if ($flush) delete_transient('cg_past_events');
  $ids = get_transient('cg_past_events');
  if ($ids === FALSE) {
    $args = array(
      'meta_query' => array(
        array(
          'key'     => 'end_date',
          'value'   => current_time('mysql'),
          'compare' => '<',
          'type'    => 'DATE'
        )
      ),
      'post_type'      => 'event',
      'posts_per_page' => -1,
      'orderby'        => 'end_date',
      'fields'         => 'ids'
    );
  
    $query = $query = new WP_Query($args);
    $ids = $query->posts;
    set_transient('cg_past_events', $ids, 60);  
  }
  return $ids;
}

/**
 * Uses Wordpress's Transient system to return a cached list of event IDs
 * that are not yet over; this includes events that .
 */
function cg_upcoming_event_ids($flush = FALSE) {
  if ($flush) delete_transient('cg_upcoming_events');
  $ids = get_transient('cg_upcoming_events');
  if ($ids === FALSE) {
    $args = array(
      'meta_query' => array(
        array(
          'key'     => 'start_date',
          'value'   => current_time('mysql'),
          'compare' => '>=',
          'type'    => 'DATE'
        )
      ),
      'post_type'      => 'event',
      'posts_per_page' => -1,
      'orderby'        => 'start_date',
      'fields'        => 'ids'
    );
  
    $query = $query = new WP_Query($args);
    $ids = $query->posts;
    set_transient('cg_upcoming_events', $ids, 60);
  }
  return $ids;
}