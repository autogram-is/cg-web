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
      if ($post) {
        $post->meta = get_post_meta($post_id);
        WP_CLI::log(print_r($post, true));  
      } else {
        WP_CLI::log("Post #$post_id not found");
      }
    }
  }

  /**
   * Import the portfolio organization hierarchy
   *
   * ## OPTIONS
   * 
   * [--dry-run]
   * : If set, the command will only simulate the updates without saving them.
   * 
   * [--preserve]
   * : If set, preserves old business hierarchy pages even if new ones are created.
   *
   * [--lipsum]
   * : If set, populates post bodies with one paragraph of Lorem Ipsum text.
   *
   * ## EXAMPLES
   *
   *     wp cg portfolio
   *
   * @param array $args
   * @param array $assoc_args
   * 
   * @subcommand hierarchy
   */
  public function hierarchy($args, $assoc_args) {
    $dry_run = isset($assoc_args['dry-run']);
    $preserve = isset($assoc_args['preserve']);
    $lipsum = isset($assoc_args['lipsum']);

    cg_cli_build_hierarchy($dry_run, $preserve, $lipsum);
  }

  /**
   * Create stubbed informational pages and navigation menus.
   *
   * ## OPTIONS
   * 
   * [--dry-run]
   * : If set, the command will only simulate the updates without saving them.
   * 
   * [--preserve]
   * : If set, preserves old pages even if new ones are created.
   * 
   * ## EXAMPLES
   *
   *     wp cg navigation
   *
   * @param array $args
   * @param array $assoc_args
   * 
   * @subcommand navigation
   * @alias nav
   */
  public function navigation($args, $assoc_args) {
    $dry_run = isset($assoc_args['dry-run']);
    $preserve = isset($assoc_args['preserve']);
  }

  /**
   * Convert porfolio posts to projects.
   *
   * ## OPTIONS
   * 
   * [--post-ids]
   * : If set, only the specified posts will be processed.
   * 
   * [--dry-run]
   * : If set, the command will only simulate the updates without saving them.
   * 
   * ## EXAMPLES
   *
   *     wp cg projects
   *
   * @param array $args
   * @param array $assoc_args
   * 
   * @subcommand projects
   */
  public function projects($args, $assoc_args) {
    $dry_run = isset($assoc_args['dry-run']);
    $post_ids = isset($assoc_args['post-ids']) ? explode(",", $assoc_args['post-ids']) : [];

    if (count($post_ids) === 0) {
      $args = [
        'post_type'      => ['avada_portfolio', 'cg_project'],
        'fields'          => 'ids',
        'posts_per_page' => -1,
      ];

      // Execute the query
      $query = new WP_Query($args);
      $post_ids = $query->posts;
    }

    foreach ($post_ids as $post_id) {
      $post = get_post($post_id);
      $post = cg_migrate_project($post, $dry_run);
    }
  }

  /**
   * Convert old events to new, merging venues.
   *
   * ## OPTIONS
   * 
   * [--dry-run]
   * : If set, the command will only simulate the updates without saving them.
   * 
   * [--dry-run]
   * : If set, the command will only simulate the updates without saving them.
   * 
   * ## EXAMPLES
   *
   *     wp cg events
   *
   * @param array $args
   * @param array $assoc_args
   * 
   * @subcommand events
   */
  public function events($args, $assoc_args) {
    $dry_run = isset($assoc_args['dry-run']);
    $post_ids = isset($assoc_args['post-ids']) ? explode(",", $assoc_args['post-ids']) : [];

    if (count($post_ids) === 0) {
      $args = [
        'post_type'      => ['tribe_events', 'cg_events'],
        'fields'          => 'ids',
        'posts_per_page' => -1,
      ];

      // Execute the query
      $query = new WP_Query($args);
      $post_ids = $query->posts;
    }

    foreach ($post_ids as $post_id) {
      $post = get_post($post_id);
      $post = cg_migrate_event($post, $dry_run);
    }
  }

  /**
   * Convert press releases, podcasts, and other posts to new types.
   *
   * ## OPTIONS
   * 
   * [--dry-run]
   * : If set, the command will only simulate the updates without saving them.
   * 
   * ## EXAMPLES
   *
   *     wp cg posts
   *
   * @param array $args
   * @param array $assoc_args
   * 
   * @subcommand posts
   */
  public function posts($args, $assoc_args) {
    $dry_run = isset($assoc_args['dry-run']);
    $post_ids = isset($assoc_args['post-ids']) ? explode(",", $assoc_args['post-ids']) : [];

    if (count($post_ids) === 0) {
      $args = [
        'post_type'      => ['post', 'page'],
        'fields'          => 'ids',
        'posts_per_page' => -1,
      ];

      // Execute the query
      $query = new WP_Query($args);
      $post_ids = $query->posts;
    }

    foreach ($post_ids as $post_id) {
      $post = get_post($post_id);
      $post = cg_migrate_post($post, $dry_run);
    }
  }

  /**
   * Convert Fusion markup to scrubbed HTML.
   *
   * ## OPTIONS
   * 
   * [--dry-run]
   * : If set, the command will only simulate the updates without saving them.
   * 
   * [--preserve-meta]
   * : If set, the fusion meta properties will be preserved.
   *
   * ## EXAMPLES
   *
   *     wp cg markup
   *
   * @param array $args
   * @param array $assoc_args
   * 
   * @subcommand markup
   */
  public function markup($args, $assoc_args) {
    $dry_run = isset($assoc_args['dry-run']);
  }

  /**
   * Remap old tags to new relationships.
   *
   * ## OPTIONS
   *
   * [--post-types]
   * : If set, only the specified post types will be processed.
   *
   * [--post-ids]
   * : If set, only the specified posts will be processed.
   * 
   * [--dry-run]
   * : If set, the command will only simulate the updates without saving them.
   *
   * [--preserve]
   * : If set, the command will add relationships but not remove old tags.
   *
   * ## EXAMPLES
   *
   *     wp cg delete-old
   *
   * @param array $args
   * @param array $assoc_args
   * 
   * @subcommand tags
   */
  public function tags($args, $assoc_args) {
    $dry_run = isset($assoc_args['dry-run']);
    $preserve = isset($assoc_args['preserve']);
    $post_ids = isset($assoc_args['post-ids']) ? explode(",", $assoc_args['post-ids']) : [];

    // While other post types exist, these are the ones that were extensively tagged and need shuffling.
    $post_types = $assoc_args['post-types'] ?? ['post', 'page', 'cg_project', 'cg_event', 'cg_episode'];

    if ($dry_run) {
      cg_get_tag_map(true);
    }

    if (count($post_ids) > 0) {
      WP_CLI::log(($dry_run ? "Dry Run: " : "") . "Mapping tags for posts " . join(', ', $post_ids));
      cg_map_old_tags($post_ids, $dry_run, $preserve);
    } else {
      // Get the list of post ids
      $args = [
        'post_type'      => $post_types,       // Change to specific post type if needed
        'fields'         => 'ids',        // Return only post IDs
        'posts_per_page' => -1,          // Get all posts
      ];

      // Execute the query
      $query = new WP_Query($args);
      $post_ids = $query->posts;

      WP_CLI::log(($dry_run ? "Dry Run: " : "") . "Mapping tags for ".count($post_ids)." posts");
      cg_map_old_tags($post_ids, $dry_run, $preserve);
    }
  }

  /**
   * Delete uneeded posts, pages, tags and categories.
   *
   * ## OPTIONS
   * 
   * [--force-delete]
   * : If set, the command always delete content instead of archiving.
   *
   * [--dry-run]
   * : If set, the command will only simulate the updates without saving them.
   *
   * ## EXAMPLES
   *
   *     wp cg archive
   *
   * @param array $args
   * @param array $assoc_args
   * 
   * @subcommand archive
   */
  public function archive($args, $assoc_args) {
    $dry_run = isset($assoc_args['dry-run']);
    $force_delete = isset($assoc_args['force-delete']);
    cg_archive_content($force_delete, $dry_run);
  }

  /**
   * Deletes old meta properties for migrated posts.
   *
   * ## OPTIONS
   * 
   * [--dry-run]
   * : If set, the command will only simulate the updates without saving them.
   *
   * ## EXAMPLES
   *
   *     wp cg clean-meta
   *
   * @param array $args
   * @param array $assoc_args
   * 
   * @subcommand clean-meta
   * @alias clean_meta
   * @alias meta
   */
  public function clean_meta($args, $assoc_args) {
    $dry_run = isset($assoc_args['dry-run']);

  }


  /**
   * Runs all steps of the Cumming Group migration process.
   *
   * ## OPTIONS
   *
   * [--dry-run]
   * : If set, the command will only simulate the updates without saving them.
   * 
   * [--regnerate-images]
   * : Regenerate thumbnails for attached images after the migration is complete.
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
    $dry_run = isset($assoc_args['dry-run']);

    //  1. Build out Region, Office, Sector, and Service skeleton
    // cg_cli_build_hierarchy($dry_run);

    //  2. Build out the new page hierarchy

    //  3. Migrate avada_portfolio posts to projects
    cg_cli_migrate_projects($dry_run);

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
