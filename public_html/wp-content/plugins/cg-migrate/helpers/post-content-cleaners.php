<?php

/**
 * Default post processing function.
 *
 * @param string $post_content String content of the post in question;
 * if it contains `[fusion_thing]` style markup, it will be converted
 * into XML-parsable content.
 */

function cg_fusion_to_xml($post_content) {
  $output = preg_replace('/\[(\/?fusion.*?)\]/', '<\1>', $post_content);
  return $output;
}

function cgih_fusion_extract_slides($html) {
  $slides = findAllTagInstances('fusion_slide', $html);
  $output = [];
  foreach ($slides as $slide) {
    $tag = new SimpleXMLElement($slide);
    $output[] = array(
      'type' => (string) $tag['type'],
      'image' => explode('|', (string) $tag['image_id'])[0],
      'content' => (string) $tag[0],
    );
  }
  return $output;
}
