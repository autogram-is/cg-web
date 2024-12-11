<?php

add_action( 'init', 'cg_register_migration_taxonomies', 0 );

function cg_register_migration_taxonomies() {
  register_taxonomy('news_and_insights', ['post', 'page']);
  register_taxonomy('news_region', ['post', 'page']);
  register_taxonomy('news_topics', ['post', 'page']);

  register_taxonomy('portfolio_category', ['cg_project']);
  register_taxonomy('portfolio_skills', ['cg_project']);
  register_taxonomy('portfolio_tags', ['cg_project']);
}
