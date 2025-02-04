<?php

/**
 * Cumming Group custom theme
 * 
 * This file is primarily responsible for pulling in other includes that
 * do the heavy lifting of defining content types, etc.
 */

 use Timber\Timber;
 use Timber\PostQuery;

// Load Composer dependencies.
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/CGSite.php';
require_once __DIR__ . '/src/CGPost.php';
require_once __DIR__ . '/src/CGProject.php';
require_once __DIR__ . '/src/blocks.php';

Timber::init();

// Sets the directories (inside your theme) to find .twig files.
Timber::$dirname = [ 'templates', 'views' ];

new CGSite();
// use CGPost;

// Set the directory Timber-generated blocks are stored in
add_filter('timber/acf-gutenberg-blocks-templates', function () {
  return ['views/blocks']; // default: ['views/blocks']
});

add_filter('timber/post/classmap', function ($classmap) {
  $custom_classmap = [
    'page' => CGPost::class,
    'post' => CGPost::class,

    'event' => CGPost::class,

    'person' => CGPost::class,
    'office' => CGPost::class,
    'sector' => CGPost::class,
    'service' => CGPost::class,
    'project' => CGProject::class,
  ];

  return array_merge($classmap, $custom_classmap);
});

function pluralize(int | array | PostQuery $items, string $singular, string $plural=null) {
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

add_filter('timber/acf-gutenberg-blocks-templates', function () {
  return ['blocks']; // default: ['views/blocks']
});
