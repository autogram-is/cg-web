<?php

/**
 * Given an item that is either tagged with a region, or has an `offices`
 * field, return its list of related offices.
 */
function cg_get_related_offices(int $post_id) {
  $offices = get_field('offices', $post_id);
  if (is_array($offices) && count($offices)) return $offices;

  $regions = wp_get_object_terms($post_id, 'region', array('fields' => 'ids'));
  if (is_error($regions) || count($regions) === 0) {
    return [];
  }

  $region_content_query = [
    'post_type'      => 'office',
    'post__not_in'   => [$post_id],
    'fields'         => 'id',
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
function cg_get_projects_for_portfolio_items(array $post_ids, int $limit = 10, array $ignore = [], string $mode = 'breadth') {
  $project_buckets = [];
  foreach ($post_ids as $post_id) {
    // This respects the order projects appear in on the item itself,
    // rather than the recency of the projects.
    $projects = get_field('projects', $post_ids);
    if (is_array($projects) && count($projects) > 0) {
      $project_buckets[$post_id] = $projects;
    }
  }

  return cg_balance_buckets($project_buckets, $limit, $ignore);
}

/**
 * Given an array of portfolio items (sectors, services, projects, or offices),
 * find associated news posts, events, and market intelligence reports.
 */
function cg_get_news_for_portfolio_items(array $post_ids, int $limit = 10, array $ignore = [], string $mode = 'breadth') {
  $project_buckets = [];
  foreach ($post_ids as $post_id) {
    // This respects the order projects appear in on the item itself,
    // rather than the recency of the projects.
    $projects = get_field('related_news', $post_ids);
    if (is_array($projects) && count($projects) > 0) {
      $project_buckets[$post_id] = $projects;
    }
  }

  return cg_balance_buckets($project_buckets, $limit, $ignore);
}

/**
 * Given a keyed array of arrays, output a flattened list of values
 * containing a mostly-equal number of values from each array.
 */
function cg_balance_buckets(array $lists = [], int $limit = 10, array $ignore = []) {
  $output = [];
  while (count($output) < $limit) {
    foreach(array_values($lists) as $list) {
      // This skips a bucket when a duplicate or ignored value is
      // found. Might want to revisit later.
      if (is_array($list) && (count($list) > 0)) {
        $val = array_shift($list);
        if (!in_array($val, $output) && !in_array($val, $ignore)) {
          $output[] = $val;
        }
      }
      if (count($output) >= $limit) {
        return $output;
      }
    }
  }
  return $output;
}

