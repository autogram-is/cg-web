<?php

function cg_map_old_tags($ids = [], $dry_run = false, $preserve = false) {
  $map = cg_get_tag_map();
  $taxonomies = ['category', 'post_tag', 'news_and_insights', 'news_region', 'news_topics', 'portfolio_category', 'portfolio_skills', 'portfolio_tags'];

  // If the post being processed is one of these types, the relationships that replace the
  // term links should be post-type-specific. ie, $post_meta['sectors'], $post_meta['services']
  // and so on. Other post types have an all-purpose $post_meta['related'] relationship.
  $portfolio_types = ['cg_project', 'cg_sector', 'cg_service', 'cg_region', 'cg_office'];

  foreach ($ids as $post_id) {
    $post = get_post($post_id);
    
    if ($post) {
      $to_add = [];
      $to_remove = [];

      // Retrieve all of the terms on the post for each taxonomy we're remapping
      $post_terms = wp_get_object_terms($post_id, $taxonomies);

      // For each term, see if we have a mapped action. If so, queue up the term to
      // be removed or replaced on this particular post.
      foreach ($post_terms as $term) {
        if (key_exists($term->term_id, $map)) {
          $record = $map[$term->term_id];

          if ($record && $record['action'] === 'REPLACE') {
            $target_field_name = 'related';

            if (in_array($post->post_type, $portfolio_types)) {
              // A little ugly; these relational field names are just un-prefixed, pluralized versions of the post types
              $target_field_name = str_replace('cg_', '', $map[$term->term_id]['type']) . 's';
            }
            $to_add[$target_field_name][$map[$term->term_id]['id']] = $map[$term->term_id]['id'];
            $to_remove[$term->taxonomy][$term->term_id] = $term->term_id;
          } else if ($record && $record['action'] === 'REMOVE') {
            $to_remove[$term->taxonomy][$term->term_id] = $term->term_id;
          }
        }
      }
      
      $relationships_added = 0;
      $tags_removed = 0;

      // Now, make the actual updates for this particular post
      foreach($to_add as $field => $ids) {
        $relationships_added += count($ids);
        if (!$dry_run) {
          update_field($field, array_values($ids), $post_id);
        } else {
          WP_CLI::log("Dry Run: Add " . join(', ', array_values($ids)) . " to $field on '$post->post_title' ($post->post_type $post->ID)");
        }
      }

      if (!$preserve) {
        foreach($to_remove as $taxonomy => $ids) {
          $tags_removed += count($ids);
          if (!$dry_run) {
            wp_remove_object_terms($post_id, array_values($ids), $taxonomy);
          } else {
            WP_CLI::log("Dry Run: Remove " . join(', ', array_values($ids)) . " from '$post->post_title' ($post->post_type $post->ID)");
          }
        }    
      }

      if ($dry_run) {
        WP_CLI::log("Dry Run: Added $relationships_added relationships, removed $tags_removed tags from '$post->post_title' ($post->post_type $post->ID)");
      } else {
        WP_CLI::log("Added $relationships_added relationships, removed $tags_removed tags from '$post->post_title' ($post->post_type $post->ID)");
      }
    }
  }
}
