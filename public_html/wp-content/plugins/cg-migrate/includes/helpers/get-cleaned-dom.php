<?php

function cg_get_cleaned_dom($html) {
  $html = cg_fusion_to_xml($html);
  $dom = new DOMDocument;
  libxml_use_internal_errors(true); // Suppress warnings for malformed HTML

  $contentType = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
  $dom->loadHTML($contentType . $html);

  libxml_clear_errors();
  return $dom;
}