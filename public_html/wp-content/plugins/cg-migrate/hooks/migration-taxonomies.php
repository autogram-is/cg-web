<?php

add_action( 'init', 'cg_register_migration_taxonomies', 0 );

function cg_register_migration_taxonomies() {
  register_taxonomy('news_and_insights', ['post', 'page'], array('labels' => array('name' => 'News and Insights (old)')));
  register_taxonomy('news_region', ['post', 'page'], array('labels' => array('name' => 'News Regions (old)')));
  register_taxonomy('news_topics', ['post', 'page'], array('labels' => array('name' => 'News Topics (old)')));

  register_taxonomy('portfolio_category', ['project'], array('labels' => array('name' => 'Portfolio Categories (old)')));
  register_taxonomy('portfolio_skills', ['project'], array('labels' => array('name' => 'Portfolio Skills (old)')));
  register_taxonomy('portfolio_tags', ['project'], array('labels' => array('name' => 'Portfolio Tags (old)')));
}
