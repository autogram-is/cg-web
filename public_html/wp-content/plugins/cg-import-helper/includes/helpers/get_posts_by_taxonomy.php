<?php

function get_post_ids_by_taxonomy($taxonomy, $term) {
    // Query arguments
    $args = [
        'post_type'      => 'any',       // Change to specific post type if needed
        'posts_per_page' => -1,          // Get all posts
        'fields'         => 'ids',      // Return only post IDs
        'tax_query'      => [
            [
                'taxonomy' => $taxonomy,
                'field'    => 'slug',   // Can also use 'term_id' or 'name'
                'terms'    => $term,
            ],
        ],
    ];

    // Execute the query
    $query = new WP_Query($args);

    return $query->posts; // Array of post IDs
}
