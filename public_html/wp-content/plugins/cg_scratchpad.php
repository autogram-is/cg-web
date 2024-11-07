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

  $posts = get_post_by_meta('cg_sector', 'locale', null, 1);
  $meta = get_post_meta($posts[0]->ID, 'locale', true);
  var_dump($meta);

  echo('</pre>');
  echo('</div>');
}
