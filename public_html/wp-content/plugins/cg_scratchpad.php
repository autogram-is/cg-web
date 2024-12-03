<?php
/*
Plugin Name: CG Scratchpad
Description: Temporary code for the deveplopment process..
Version: 1.0
Author: Your Name
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Hook to add the custom menu page
add_action('admin_menu', 'cgs_add_tools_menu_page');

function cgs_add_tools_menu_page() {
    add_management_page(
        'Code Scratchpad',           // Page title
        'Code Scratchpad',           // Menu title
        'manage_options',            // Capability required to access this page
        'custom-tools-page',         // Menu slug
        'cgs_tools_page_content'     // Callback function to render page content
    );
}

// Callback function to render the content of the page
function cgs_tools_page_content() {
  echo('<div class="wrap"><h1>Code Scratchpad</h1>');
  echo('<pre>');
  echo('</pre>');

  $query = new WP_Query(array(
    'post_type' => 'cg_event',
    'posts_per_page' => 100
  ));
  $posts = $query->get_posts();

  echo(count($posts). ' posts found</br>');
  echo('<ol>');
  foreach ($posts as $post) {
    $data = cgih_fusion_extract_event_details($post->post_content);
    foreach($data['attendees'] as $bio) {
        cgih_create_person_from_event_bio($bio);
    }
  }
  echo('</ol>');
  echo('</div>');
}

