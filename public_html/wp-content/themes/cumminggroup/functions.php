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


Timber\Timber::init();

// Sets the directories (inside your theme) to find .twig files.
Timber::$dirname = [ 'templates', 'views' ];

new CGSite();

// Set the directory Timber-generated blocks are stored in
add_filter('timber/acf-gutenberg-blocks-templates', function () {
  return ['views/blocks']; // default: ['views/blocks']
});
