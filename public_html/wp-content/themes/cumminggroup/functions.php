<?php

/**
 * Cumming Group custom theme
 * 
 * This file is primarily responsible for pulling in other includes that
 * do the heavy lifting of defining content types, etc.
 */

// Load Composer dependencies.
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/CGSite.php';
require_once __DIR__ . '/src/CGPost.php';

Timber\Timber::init();

// Sets the directories (inside your theme) to find .twig files.
Timber::$dirname = [ 'templates', 'views' ];

new CGSite();
use CGPost;

// Set the directory Timber-generated blocks are stored in
add_filter('timber/acf-gutenberg-blocks-templates', function () {
  return ['views/blocks']; // default: ['views/blocks']
});

add_filter('timber/post/classmap', function ($classmap) {
  $custom_classmap = [
      'page' => CGPost::class,
      'post' => CGPost::class,

      'cg_region' => CGPost::class,
      'cg_person' => CGPost::class,
      'cg_office' => CGPost::class,
      'cg_sector' => CGPost::class,
      'cg_service' => CGPost::class,
      //'cg_project' => CGPost::class,

      'cg_event' => CGPost::class,
      'cg_report' => CGPost::class,
      'cg_episode' => CGPost::class,
    ];

  return array_merge($classmap, $custom_classmap);
});

function pluralize(int | array | Timber\PostQuery $items, string $singular, string $plural=null) {
  if (is_int($items) && $items < 2) return $singular;
  if(count($items)==1 || !strlen($singular)) return $singular;
  if($plural!==null) return $plural;

  $last_letter = strtolower($singular[strlen($singular)-1]);
  switch($last_letter) {
      case 'y':
          return substr($singular,0,-1).'ies';
      case 's':
          return $singular.'es';
      default:
          return $singular.'s';
  }
}

function get_post_a_tag($post) {
  $href = get_permalink($post);
  $title = get_the_title($post);
  return '<a href="'.$href.'">'.$title.'</a>';
}

function snake_to_title_case(string $input) {
  return mb_convert_case(str_replace('_', " ", $input), MB_CASE_TITLE_SIMPLE);
}