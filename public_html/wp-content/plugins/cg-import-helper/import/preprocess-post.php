<?php

// After the post has been assembled and is ready to import, we have a chance to modify it.
function cgih_preprocess_post($postdata, $post) {
  // Filter this list, removing items from the list we don't want to import.
}

add_action('wp_import_post_data_processed', 'cgih_preprocess_post', 10, 2);
