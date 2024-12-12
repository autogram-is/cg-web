<?php
function cg_allowed_markup() {
  $allowed_html = array(
    'p' => array(),

    'h1' => array(),
    'h2' => array(),
    'h3' => array(),
    'h4' => array(),
    'h5' => array(),

    'ol' => array(),
    'ul' => array(),
    'li' => array(),

    'a' => array('href' => array()),
    'em' => array(),
    'strong' => array(),
    'blockquote' => array(),
  );
  return $allowed_html;
}