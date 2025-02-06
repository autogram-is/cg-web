<?php

use Timber\Site;
use Timber\Timber;

/**
 * Class CGRelatedContentHelper
 */
class CGRelatedContentHelper {
  /**
	 * For any region-tagged entity, find other items tagged with the same region(s).
	 */
	public function nearby(int $post_id, string $mode = 'projects', int $limit = -1) {
		$output = [];

    switch($mode) {
      case 'news': 
        $content_types = ['post', 'event'];
      case 'projects': 
        $content_types = ['project'];
        break;
      default:
        $content_types = ['project', 'post', 'event'];
        break;
    }

		$regions = wp_get_post_terms($post_id, 'region');
		if (is_error($regions) || count($regions) === 0) {
			return $output;
		}

		// Find content that's in the same region as the current post; if that's enough
    // to meet the limit, return the post IDs.
		$region_query = [
			'post_type'      => $content_types,
			'posts_per_page' => $limit,
			'fields'         => 'id',
			'tax_query'      => [
					[
							'taxonomy' => 'region',
							'field'    => 'term_id',
							'terms'    => $regions,
					],
			],
		];
		$nearby_content = (new WP_Query($region_query))->posts;
		if (count($nearby_content) > $limit) {
			$output = array_merge($output, $nearby_content);
		}

		// If there aren't enough, find offices in the regions and pull them.
		$office_query = [
			'post_type'      => 'office',
			'posts_per_page' => $limit,
			'fields'         => 'id',
			'tax_query'      => [
					[
							'taxonomy' => 'region',
							'field'    => 'term_id',
							'terms'    => $regions,
					],
			],
		];
		$nearby_offices = (new WP_Query($office_query))->posts;

    // Caveat: If one local office has a lot of items, it will pull
    // from that office before any of the others rather than using a mix
    // of them. There's no great way to randomize this without blindly
    // loading all related projects and news for all nearby offices, then
    // getting a mix per-office.
    // We might want to do that.

    foreach ($nearby_offices as $office_id) {
      if (in_array('project', $content_types)) {
        $office_projects = get_field('projects', $office_id);
        if (is_array($office_projects)) {
          $output = array_merge($output, $office_projects);
        }
      }
      if (in_array('event', $content_types) || in_array('post', $content_types)) {
        $office_news = get_field('related', $office_id);
        if (is_array($office_news)) {
          $output = array_merge($output, $office_news);
        }
      }

      $output = array_unique($output);
      if (count($output) > $limit) {
        return $output;
      }
    }

    return $output;
	}

  /**
	 * For any post id, return a list of directly-related offices.
   * If there are none, but the post is tagged with region(s),
   * return list of offices in the same region.
	 */
  function nearbyOffices(int $post_id) {
    $output = get_field('offices', $post_id);

    if (!$output) {
      $regions = wp_get_post_terms($post_id, 'region');
      if (is_error($regions) || count($regions) === 0) {
        return $output;
      }
      $office_query = [
        'post_type'      => 'office',
        'posts_per_page' => -1,
        'fields'         => 'id',
        'tax_query'      => [
            [
                'taxonomy' => 'region',
                'field'    => 'term_id',
                'terms'    => array_map(function($a) { return $a->term_id; }, $regions),
            ],
        ],
      ];
      $output = (new WP_Query($office_query))->posts;
    }

    return $output;
  }
}