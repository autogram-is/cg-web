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


function cg_extended_markup() {
  $allowed_html = cg_allowed_markup();
  $allowed_html['p'] = array('class' => []);
  $allowed_html['ol'] = array('class' => []);
  $allowed_html['ul'] = array('class' => []);
  $allowed_html['a'] = array('href' => [], 'class' => [], '_target' => []);
  $allowed_html['img'] = array('src' => [], 'title' => [], 'class' => []);
  $allowed_html['figure'] = array('class' => []);
  $allowed_html['figcaption'] = array('class' => []);
  $allowed_html['details'] = array();
  $allowed_html['summary'] = array();
  $allowed_html['mark'] = array();
  
  return $allowed_html;
}

function cg_extended_markup_with_tables() {
  $allowed_html = cg_extended_markup();
  $allowed_html['table'] = array();
  $allowed_html['th'] = array();
  $allowed_html['th'] = array();
  $allowed_html['td'] = array();

  return $allowed_html;
}