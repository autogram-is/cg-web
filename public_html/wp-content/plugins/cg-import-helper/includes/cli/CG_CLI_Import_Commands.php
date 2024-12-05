<?php

// use WP_CLI;
// use WP_CLI_Command;

if (!\class_exists('WP_CLI_Command')) {
  return;
}

/**
 * Updates all posts of a specific post type.
 */
class CG_CLI_Import_Commands extends WP_CLI_Command {
  /**
   * Inspect one or more posts from the command line.
   *
   * ## OPTIONS
   * 
	 * <post_ids>...
	 * : The ID(s) of the post(s) to check.
   * 
   * ## EXAMPLES
   *
   *     wp inspect-post
   *
   * @param array $args
   * @param array $assoc_args
   * 
   * @subcommand inspect-post
   * @alias inspect
   * @alias inspect_post
   */
  public function inspect_post($args, $assoc_args) {
    foreach ($args as $post_id) {
      // Get the post
      $post = get_post($post_id);
      $post->meta = get_post_meta($post_id);
      WP_CLI::log(print_r($post, true));
    }
  }

  /**
   * Convert project attachments to gallery photos.
   *
   * ## OPTIONS
   *
   * [--dry-run]
   * : If set, the command will only simulate the updates without saving them.
   * 
   * [--ignore-thumb]
   * : If set, the featured image of the post will not be added to its gallery.
   * 
   * ## EXAMPLES
   *
   *     wp attach-galleries
   *
   * @param array $args
   * @param array $assoc_args
   * 
   * @subcommand attach-galleries
   * @alias attach_galleries
   */
  public function attach_galleries($args, $assoc_args) {
    $dry_run = isset($assoc_args['dry-run']);
    $ignore_thumb = isset($assoc_args['ignore-thumb']);

    $query_args = [
      'posts_per_page' => -1,
      'post_type'      => 'cg_project',
      'post_status'    => 'any',
      'fields'         => 'ids',
    ];

    $posts = get_posts($query_args);

    WP_CLI::log($posts);

    if (empty($posts)) {
      WP_CLI::success("No projects found");
      return;
    }

    WP_CLI::log(sprintf("Found %d projects", count($posts)));

    foreach ($posts as $post_id) {
      // Get the post
      $post = get_post($post_id);

      // …And its attachments
      $args = array(
        'order' => 'ASC',
        'post_type' => 'attachment',
        'post_parent' => $post_id,
        'post_mime_type' => 'image',
        'post_status' => null,
        'fields' => 'ids',
      );
      $attachments = get_posts($args);
      $featured = get_post_thumbnail_id($post_id);

      if ($ignore_thumb) {
        unset($attachments[array_search($featured, $attachments)]);
      }

      $count = count($attachments);
    
      $affected_projects = 0;
      $affected_images = 0;

      if ($count > 1) {
        $affected_projects++;
        $affected_images += $count;
        if ($dry_run) {
          WP_CLI::log("Dry run: Project $post_id ($post->post_title) has $count attached images.");
        } else {
          update_field('gallery', $attachments, $post_id);
          WP_CLI::log("Galleried $count attachments for project $post_id ($post->post_title).");
        }
      }
    }

    if ($dry_run) {
      WP_CLI::success("Dry run complete. $affected_projects projects with gallery images found, no posts updated.");
    } else {
      WP_CLI::success("Images converted to galleries for $affected_projects projects.");
    }
  }

    /**
   * Convert project attachments to gallery photos.
   *
   * ## OPTIONS
   *
	 * <post_types>...
	 * : The post types to migrate.
   *
   * [--dry-run]
   * : If set, the command will only simulate the updates without saving them.
   * 
   * ## EXAMPLES
   *
   *     wp migrate
   *
   * @param array $args
   * @param array $assoc_args
   * 
   * @subcommand migrate
   * @alias migrate
   */
  public function migrate() {
    //  1. Build out Region, Office, Sector, and Service skeleton
    //  2. Build out the new page hierarchy
    //  3. Migrate avada_portfolio posts to projects
    //    3a. Add non-featured image attachments to project gallery
    //    3b. Convert portfolio tags to relationships
    //  4. Migrate tribe_events posts to events
    //    4a. Fold tribe_venues into events and delete
    //    4b. Create Person records from event attendees
    //  5. Handle existing `post` types
    //    5a. Convert podcast-tagged posts to episodes
    //    5b. Extract and reformat logo'd and byline'd news
    //    5c. Convert portfolio/city/etc tags to portfolio relationships
    //  6. Sanitize remaining fusion builder markup
    //  7. Delete old posts
    //  8. Delete old taxonomy terms and categories
    //  9. Delete supporting legacy posts (fusion element, slides, avado, etc)
  }
}

WP_CLI::add_command('cg', 'CG_CLI_Import_Commands');
