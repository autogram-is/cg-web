<?php
/**
 * The tag migration map takes existing tags and categories from
 * a number of taxonomies and does one of the following:
 * 
 * - Remaps an individual taxo term to a new one (RETAG)
 * - Remaps an individual taxo term to a relationship with another post (RELATE)
 * - Removes the taxo term from the post (UNTAG)
 * - Removes the taxo term and archives the post (ARCHIVE)
 */
function cg_map_old_tags($ids = [], $dry_run = false, $preserve = false) {
  $map = cg_get_tag_map();
  $taxonomies = ['category', 'post_tag', 'news_and_insights', 'news_region', 'news_topics', 'portfolio_category', 'portfolio_skills', 'portfolio_tags'];

  // If the post being processed is one of these types, the relationships that replace the
  // term links should be post-type-specific. ie, $post_meta['sectors'], $post_meta['services']
  // and so on. Other post types have an all-purpose $post_meta['related_portfolio_items'] relationship.
  $portfolio_types = ['project', 'sector', 'service', 'office'];

  foreach ($ids as $post_id) {
    $post = get_post($post_id);
    
    if ($post) {
      $to_add = [];
      $to_remove = [];

      // Retrieve all of the terms on the post for each taxonomy we're remapping
      $post_terms = wp_get_object_terms($post_id, $taxonomies);
      if (count($post_terms) === 0) {
        continue;
      }

      // For each term, see if we have a mapped action. If so, queue up the term to
      // be removed or replaced on this particular post.
      foreach ($post_terms as $term) {
        if (key_exists($term->term_id, $map)) {
          $record = $map[$term->term_id] ?? NULL;

          if ($record) {
            if ($record['action'] === 'RELATE') {
              $target_field_name = 'related_portfolio_items';
              if (in_array($post->post_type, $portfolio_types)) {
                $target_field_name = $record['type'] . 's';
              }
              $to_relate[$target_field_name][] = $record['id'];
              $to_remove[$record['old']][] = $term->term_id;
            } else if ($record['action'] === 'RETAG') {
              $to_add[$record['type']][] = $record['id'];
              $to_remove[$record['old']][] = $term->term_id;
            } else if ($record['action'] === 'UNTAG') {
              $to_remove[$record['old']][] = $term->term_id;
            }
          }
        }
      }
      
      $relationships_added = 0;
      $tags_added = 0;
      $tags_removed = 0;

      // Update relationships
      foreach($to_relate as $field => $ids) {
        $relationships_added += count($ids);
        if (!$dry_run) {
          // Get the existing field values and merge in the new ones
          // to ensure we don't blow stuff away by running this multiple
          // times after the tags are deleted.
          $new_ids = get_field($field, $post_id) ?? [];
          $new_ids = array_merge($new_ids, $ids);
          $new_ids = array_unique($new_ids);
          update_field($field, array_values($new_ids), $post_id);
        } else {
          WP_CLI::log("Dry Run: Add " . join(', ', array_values($ids)) . " to $field on '$post->post_title' ($post->post_type $post->ID)");
        }
      }

      // Apply new taxonomy tags
      foreach($to_relate as $taxonomy => $ids) {
        $tags_added += count($ids);
        if (!$dry_run) {
          // Get the existing field values
          wp_add_object_terms($post_id, $ids, $taxonomy);
        } else {
          WP_CLI::log("Dry Run: Add " . join(', ', array_values($ids)) . " to $field on '$post->post_title' ($post->post_type $post->ID)");
        }
      }
      
      // Remove old taxonomy tags
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

      WP_CLI::log($dry_run ? "Dry Run: " : '' . "Added $relationships_added relationships, removed $tags_removed of ". count($post_terms) ." tags from '$post->post_title' ($post->post_type $post->ID)");
    }
  }
}
