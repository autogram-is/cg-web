<?php
if (!defined('ABSPATH')) {
    exit;
}

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
