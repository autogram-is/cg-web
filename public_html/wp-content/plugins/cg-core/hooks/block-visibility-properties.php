<?php

/**
 * Adds custom attributes to Gutenberg blocks
 * 
 * @see /block-visibility-extension.js
 * 
 * @param mixed $block_content
 * @param mixed $block
 * @return mixed
 */
function cg_custom_add_custom_attributes($block_content, $block) {
    // Check if there are custom attributes 'dataHideIn' e 'dataShowIn'
    if ( isset($block['attrs']['dataHideIn']) && !empty($block['attrs']['dataHideIn']) ) {
        $block_content = add_custom_attribute($block_content, 'data-hide-in', $block['attrs']['dataHideIn']);
    }
    if ( isset($block['attrs']['dataShowIn']) && !empty($block['attrs']['dataShowIn']) ) {
        $block_content = add_custom_attribute($block_content, 'data-show-in', $block['attrs']['dataShowIn']);
    }

    return $block_content;
}

/**
 * Functions to add data attributes to HTML blocks
 * 
 * @param mixed $block_content
 * @param mixed $attribute_name
 * @param mixed $attribute_value
 * @return mixed
 * 
 * @since 1.5.5
 */
function add_custom_attribute($block_content, $attribute_name, $attribute_value) {
    // Search the first tag HTML in the block content
    $pos = strpos($block_content, '>');

    if ($pos !== false) {
        // Insert the custom attribute after the first HTML tag
        $block_content = substr_replace($block_content, ' ' . $attribute_name . '="' . esc_attr($attribute_value) . '"', $pos, 0);
    }

    return $block_content;
}
add_filter('render_block', 'cg_custom_add_custom_attributes', 10, 2);

/**
 * Enqueue the script when the Gutenberg editor is loaded.
 */
add_action( 'enqueue_block_editor_assets', function() {
  if (get_field('allow_regional_blocks', 'cg_options')) {
    $scriptPath = '/block-visibility-extension.js';
    wp_enqueue_script(
      'cg-block-visibility', // unique handle
      CG_CORE_PLUGIN_URL . $scriptPath,
      [ 'wp-blocks', 'wp-element', 'wp-i18n' ], // required dependencies for blocks
      filemtime( CG_CORE_PLUGIN_URL . $scriptPath )
    );  
  }
});
