<?php

function get_posts_by_taxonomy($taxonomy, $term, $fields = NULL) {
    // Query arguments
    $args = [
        'post_type'      => 'any',       // Change to specific post type if needed
        'posts_per_page' => -1,          // Get all posts
        'fields'         => $fields,        // Return only post IDs
        'tax_query'      => [
            [
                'taxonomy' => $taxonomy, // taxonomy slug
                'field'    => 'slug',     // Can also use 'term_id' or 'name'
                'terms'    => $term,     // Single taxonomy term slug
            ],
        ],
    ];

    // Execute the query
    $query = new WP_Query($args);

    return $query->posts; // Array of post IDs
}
