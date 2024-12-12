<?php

function cg_save_migration_body($post_id, $html) {
  add_post_meta($post_id, '_cg_migration_body', $html, true);
}

function cg_save_migration_data($post_id, $data) {
  add_post_meta($post_id, '_cg_migration_data', $data, true);
}

function cg_mark_migrated($post_id, $status = true) {
  add_post_meta($post_id, '_cg_migration_flag', $status, true);
}

function cg_get_migration_data($post_id) {
  return get_post_meta($post_id, '_cg_migration_data', true) ?? [];
}

function cg_get_migration_status($post_id) {
  return get_post_meta($post_id, '_cg_migration_flag', true) ?? [];
}