<?php
if (!class_exists('WP_CLI_Command')) {
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
   * [--fusion]
   * : Parse and display Fusion Builder markup in the post body.
   *
   * [--blocks]
   * : Parse and display block structures in the post body.
   * 
   * [--details]
   * : Parse and display the full post data structure.
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
      $fusion = isset($assoc_args['fusion']);
      $blocks = isset($assoc_args['blocks']);
      $details = isset($assoc_args['details']);

      // Get the post
      $post = get_post($post_id);
      if ($post) {
        if ($fusion) {
          $output = cg_default_process_markup($post);
          $body = wp_kses($output['processed'], cg_extended_markup());
          WP_CLI::log(trim($body));  
        }
        
        if ($blocks) {
          $data = parse_blocks($post->post_content);
          WP_CLI::log(var_dump($data, true));  
        } 
        
        if ($details) {
          $taxonomies = get_post_taxonomies($post_id);
          $post->meta = get_post_meta($post_id);
          $post->taxonomy = wp_get_post_terms($post_id, $taxonomies);
          WP_CLI::log(print_r($post, true));  
        }
      } else {
        WP_CLI::log("Post #$post_id not found");
      }
    }
  }

  /**
   * Import the portfolio organization hierarchy
   *
   * ## EXAMPLES
   *
   *     wp cg stub
   *
   * @subcommand do
   */
  public function do($args) {
    // WP_CLI::log(var_export($data, true));
    // update_field('social', $social, 'cg_options');

    $args = array( 
      'posts_per_page' => -1,
      'post_type'      => 'post',
    );
    $posts = get_posts($args);
    foreach ($posts as $post) {
      cg_postprocess_news($post);
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
   * ## EXAMPLES
   *
   *     wp cg hierarchy
   *
   * @param array $args
   * @param array $assoc_args
   * 
   * @subcommand hierarchy
   */
  public function hierarchy($args, $assoc_args) {
    $dry_run = isset($assoc_args['dry-run']);
    $preserve = isset($assoc_args['preserve']);
    cg_cli_build_hierarchy($dry_run, $preserve);
  }

  /**
   * Create NA and EU navigation menus.
   *
   * ## OPTIONS
   * 
   * [--dry-run]
   * : If set, the command will only simulate the updates without saving them.
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
    cg_cli_build_nav_menus($dry_run);
  }

  /**
   * Convert porfolio posts to projects.
   *
   * ## OPTIONS
   * 
   * [--post-ids]
   * : If set, only the specified posts will be processed.
   *
   * [--reprocess]
   * : If set, reprocess previously-imported posts.
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
      $post_ids = $this->ids_for_types(['avada_portfolio', 'project']);
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
   * [--post-ids]
   * : If set, only the specified posts will be processed.
   *
   * [--reprocess]
   * : If set, reprocess previously-imported posts.
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
      $post_ids = $this->ids_for_types(['tribe_events', 'events']);
    }

    foreach ($post_ids as $post_id) {
      $post = get_post($post_id);
      $post = cg_migrate_event($post, $dry_run);
    }
  }

  /**
   * Migrate press releases, podcasts, and other posts to new structures.
   *
   * ## OPTIONS
   * 
   * [--post-ids]
   * : If set, only the specified posts will be processed.
   *
   * [--reprocess]
   * : If set, reprocess previously-imported posts.
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
    $reprocess = isset($assoc_args['reprocess']);
    $post_ids = isset($assoc_args['post-ids']) ? explode(",", $assoc_args['post-ids']) : [];

    if (count($post_ids) === 0) {
      $post_ids = $this->ids_for_types('post');
    }

    WP_CLI::log(($dry_run ? "Dry Run: " : "") . "Posts " . join(', ', $post_ids). " found");

    foreach ($post_ids as $post_id) {
      $post = get_post($post_id);
      $post = cg_migrate_post($post, $dry_run, $reprocess);
    }
  }

    /**
   * Migrate standalone pages.
   *
   * ## OPTIONS
   * 
   * [--post-ids]
   * : If set, only the specified pages will be processed.
   *
   * [--reprocess]
   * : If set, reprocess previously-imported pages.
   * 
   * [--dry-run]
   * : If set, the command will only simulate the updates without saving them.
   * 
   * ## EXAMPLES
   *
   *     wp cg pages
   *
   * @param array $args
   * @param array $assoc_args
   * 
   * @subcommand pages
   */
  public function pages($args, $assoc_args) {
    $dry_run = isset($assoc_args['dry-run']);
    $reprocess = isset($assoc_args['reprocess']);
    $post_ids = isset($assoc_args['post-ids']) ? explode(",", $assoc_args['post-ids']) : [];

    if (count($post_ids) === 0) {
      $post_ids = $this->ids_for_types('page');
    }

    WP_CLI::log(($dry_run ? "Dry Run: " : "") . "Pages " . join(', ', $post_ids). " found");

    foreach ($post_ids as $post_id) {
      $post = get_post($post_id);
      $post = cg_migrate_page($post, $dry_run, $reprocess);
    }
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
    $post_types = $assoc_args['post-types'] ?? ['post', 'page', 'project', 'event'];

    if ($dry_run) {
      cg_get_tag_map(true);
    }

    if (count($post_ids) === 0) {
      $post_ids = $this->ids_for_types($post_types);
    }
    WP_CLI::log(($dry_run ? "Dry Run: " : "") . "Mapping tags for ".count($post_ids)." posts");
    cg_map_old_tags($post_ids, $dry_run, $preserve);
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
    cg_remove_archived($force_delete, $dry_run);
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

    // Kill meta fields associated with nonexistant post IDs

    // kill all termmeta fields
    
    $meta_key_patterns = array(
      "_tribe_%" => "Tribe Events post meta",
      "_Event%" => "Legacy event data",
      "_Venue%" => "Legacy venue data",
      "_tec_%" => "Tribe calendar post meta",
      "pyre_%" => "Pyre post metadata",
      "fusion_%" => "Fusion post metadata",
      "_fusion_%" => "Fusion post metadata",
      "caf_%" => "CAF post metadata",
      "kd_featured-image%" => "Fusion multi-featured-image post metadata",
      "avada_%" => "Avada slider post metadata",
    );
    
    foreach($meta_key_patterns as $pattern => $note) {
      WP_CLI::log("DRY RUN: Deleted $note ('$pattern')");
    }
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
   *     wp cg migrate
   *
   * @param array $args
   * @param array $assoc_args
   * 
   * @subcommand migrate
   * @alias migrate
   */
  public function migrate() {
    WP_CLI::log("*** Starting migration\n");

    WP_CLI::log("\n*** Building portfolio hierarchy...\n");
    $this->hierarchy([], []);

    WP_CLI::log("\n*** Updating project portfolio...\n");
    $this->projects([], []);

    WP_CLI::log("\n*** Updating news posts...\n");
    $this->posts([], []);

    WP_CLI::log("\n*** Updating events...\n");
    $this->events([], []);

    WP_CLI::log("\n*** Updating static pages...\n");
    $this->pages([], []);

    WP_CLI::log("\n*** Re-mapping old tags...\n");
    $this->tags([], []);

    WP_CLI::log("\n*** Building navigation menus...\n");
    $this->navigation([], []);

    WP_CLI::log("\n*** Applying manual content updates...\n");
    $this->import([], []);

    WP_CLI::log("\n*** Updating site options...\n");
    cg_populate_site_defaults();

    WP_CLI::log("\n*** Updating site options...\n");
    $this->archive([], []);

    WP_CLI::log("\n*** Migration complete...\n");
  }


  /**
   * Exports tracking spreadsheets for content updates.
   *
   * ## OPTIONS
   * 
   * ## EXAMPLES
   *
   *     wp cg export
   *
   * @param array $args
   * @param array $assoc_args
   * 
   * @subcommand export
   * @alias export
   */
  public function export() {
    cg_export_projects();
    // cg_export_offices();
    cg_export_bios();
    cg_export_news();
    cg_export_events();
    cg_export_pages();
  }

  /**
   * Imports content updates from tracking spreadsheets.
   *
   * ## OPTIONS
   * 
   * [--dry-run]
   * : If set, the command will only simulate the updates without saving them.
   * 
   * ## EXAMPLES
   *
   *     wp cg import
   *
   * @param array $args
   * @param array $assoc_args
   * 
   * @subcommand import
   * @alias import
   */
  public function import() {
    cg_import_offices();
    cg_import_bios();
    cg_import_projects();
    cg_import_news();
    cg_apply_fixed_pages();
  }

  /**
   * Saves a post's core content to an export file for later reloading.
   *
   * ## OPTIONS
   * 
	 * <post_ids>...
	 * : The ID(s) of the post(s) to check.
   * 
   * [--slugs]
   * : If set, use slugs to identify the posts rather than IDs.
   * 
   * ## EXAMPLES
   *
   *     wp cg save 1 2 3 4
   *
   * @param array $args
   * @param array $assoc_args
   * 
   * @subcommand save
   * @alias save
   */
  public function save($args, $assoc_args) {
    $use_slugs = $assoc_args['slugs'] ?? false;

    foreach ($args as $id) {
      $post = get_post($id);
      if ($post) {
        if ($use_slugs) {
          $filename = CG_MIGRATE_CONTENT_DIR . '/' . $post->post_type . '.' . $post->post_name . '.html';
        } else {
          $filename = CG_MIGRATE_CONTENT_DIR . '/' . $post->ID . '.html';
        }
        file_put_contents($filename, $post->post_content);
        WP_CLI::log("Wrote $post->post_type '$post->post_title' to $post->ID.html.");
      } else {
        WP_CLI::log("Could not load post $id; skipping.");
      }
    }
  }

  private function ids_for_types($post_types, $reprocess = false) {
    $args = [
      'post_type'      => $post_types,
      'fields'          => 'ids',
      'posts_per_page' => -1,
    ];
  
    if (!$reprocess) {
      // TODO
    }
  
    // Execute the query
    $query = new WP_Query($args);
  
    return $query->posts;
  }
}

WP_CLI::add_command('cg', 'CG_CLI_Import_Commands');

