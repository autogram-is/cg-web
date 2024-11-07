<?php

require_once CG_IMPORT_PLUGIN_DIR . '/libraries/htmlpurifier/HTMLPurifier.auto.php';

function  cgih_purify_html($dirty_html) {
  $config = HTMLPurifier_Config::createDefault();
  $purifier = new HTMLPurifier($config);
  $clean_html = $purifier->purify($dirty_html);
  return $clean_html . '<!-- purified -->';
}
