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
   * Convert legacy tags to portfolio post references.
   *
   * ## OPTIONS
   *
   * [--dry-run]
   * : If set, the command will only simulate the updates without saving them.
   * 
   * [--delete-tags]
   * : If true, tags and categories will be deleted once their references are migrated.
   * 
   * ## EXAMPLES
   *
   *     wp convert-tags
   *
   * @param array $args
   * @param array $assoc_args
   * 
   * @subcommand convert-tags
   * @alias convert_tags
   */
  public function convert_tags($args, $assoc_args) {
    $dry_run = isset($assoc_args['dry-run']);
    $delete_tags = isset($assoc_args['delete']);

    $query_args = [
      'posts_per_page' => 1,
      'post_status'    => 'any',
      'fields'         => 'ids',
    ];

    $posts = get_posts($query_args);

    if (empty($posts)) {
      WP_CLI::success("No matching posts found");
      return;
    }

    WP_CLI::log(sprintf("Found %d matching posts", count($posts)));

    foreach ($posts as $post_id) {
      // Get the post
      $post = get_post($post_id);

      // Apply a change (example: append ' - Updated' to the title)
      $new_title = $post->post_title . ' - Updated';

      if ($dry_run) {
        WP_CLI::log("Dry run: Post ID $post_id would be updated to: $new_title");
      } else {
        wp_update_post([
          'ID'         => $post_id,
          'post_title' => $new_title,
        ]);
        WP_CLI::log("Updated post ID $post_id to: $new_title");
        WP_CLI::log(print_r($post, true));
      }
    }

    if ($dry_run) {
      WP_CLI::success("Dry run complete. No posts were updated.");
    } else {
      WP_CLI::success("All posts have been updated.");
    }
  }


  /**
   * Convert legacy tags to portfolio post references.
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
      WP_CLI::log(print_r($post, true));
    }
  }
}

WP_CLI::add_command('cg', 'CG_CLI_Import_Commands');
