# Cumming Group Wordpress Theme

## Timber

This theme is built on Timber, a library that maps Wordpress's PHP templates and "page loop" to more HTML-like Twig templates. See [Timber's documentation](https://timber.github.io/docs/v2/) for details on using or extending its features.

## The CG-Core Plugin

This theme relies on helper functions and post type definitions contained in the `CG Core` plugin.

## What Lives Where

- `assets/`: static CSS, JS, fonts, and images compiled from the modular SCSS files in the design project.
- `blocks/`: twig templates that define Cumming-Group specific blocks for use in Wordpress's Gutenberg editor.
- `src/`: PHP helper classes that mangae sitewide and post-type specific behaviors
- `views/`: twig templates to render standard Wordpress elements.
  - `views/single/`: twig templates to display specific post types in full-page mode.
  - `views/list-item/`: twig templates to display specific post types as items in a list.
  - `views/card/`: twig templates to display specific post types as featured cards.
