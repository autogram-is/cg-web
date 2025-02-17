<?php

function cg_populate_site_defaults() {

  update_field('copyright', 'Copyright © [YEAR] Cumming Group. All Rights Reserved.', 'cg_options');
  update_field('heading', 'Lorem Ipsum', 'cg_options');
  update_field('notice', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'cg_options');

  $social = array (
    array (
      'icon' => array ('type' => 'dashicons', 'value' => 'dashicons-linkedin'),
      'label' => 'LinkedIn',
      'url' => 'https://www.linkedin.com/company/cumming-group/mycompany/',
    ),
    array (
      'icon' => array ('type' => 'dashicons', 'value' => 'dashicons-instagram'),
      'label' => 'Instagram',
      'url' => 'https://www.instagram.com/cumminggroup/',
    ),
    array (
      'icon' => array ('type' => 'dashicons', 'value' => 'dashicons-youtube'),
      'label' => 'YouTube',
      'url' => 'https://www.youtube.com/channel/UCXH7eWXn38qH7r9AYBpmRAQ',
    ),
  );

  update_field('social', $social, 'cg_options');

  WP_CLI::log('Footer defaults set');

  // Image presets
  // add_image_size( string $name, int $width, int $height, bool|array $crop = false )
  // update_option( 'thumbnail_size_w', 160 );
  // update_option( 'thumbnail_size_h', 160 );
  // update_option( 'thumbnail_crop', 1 );

  // Set date/time format
}