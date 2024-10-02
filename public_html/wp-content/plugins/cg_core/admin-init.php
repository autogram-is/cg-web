<?php
/**
 * @package CG Core
 * @version 1.0.0
 */

/**
 * Init hooks that should only fire for administrators, users with ,
 * dashboard acccess etc.
 */

add_action('admin_init', 'cg_core_admin_init' );

function cg_core_admin_init() {
  cg_core_admin_menu_alterations();
}

function cg_core_admin_menu_alterations() {
  global $user_ID;

  if ( $user_ID != 1 ) {
   remove_menu_page('edit-comments.php'); // Comments
  }
}