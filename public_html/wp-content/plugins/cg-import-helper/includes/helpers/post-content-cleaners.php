<?php

/**
 * Default post processing function.
 *
 * @param string $post_content String content of the post in question;
 * if it contains `[fusion_thing]` style markup, it will be converted
 * into XML-parsable content.
 */

function cgih_fusion_unbracket($post_content) {
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

function findAllTagInstances($tagName, $html = '') {
	// Use a regex pattern to capture the full tag including attributes and content
	$pattern = "/(<$tagName\b[^>]*>.*?<\/$tagName>)/is";
	
	// Use preg_match_all to find all matches
	preg_match_all($pattern, $html, $matches);
	
	// Return all matches, which include the full HTML of each tag instance
	return $matches[1];
}
