<?php

// Remove items from the post list if we've marked them
function cgih_preprocess_post_list(&$posts) {
  // Filter this list, removing items from the list we don't want to import.
}
add_action('wp_import_posts', 'cgih_preprocess_post_list', 10, 2);
