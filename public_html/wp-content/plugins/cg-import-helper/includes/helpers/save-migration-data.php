<?php

function cg_save_migration_body($post_id, $html) {
  add_post_meta($post_id, '_cg_migration_body', $html, true);
}

function cg_save_migration_data($post_id, $data) {
  add_post_meta($post_id, '_cg_migration_data', $data, true);
}