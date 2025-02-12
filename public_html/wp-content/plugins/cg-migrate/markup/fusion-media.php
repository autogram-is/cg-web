<?php

function cg_fusion_image(DOMElement &$el) {
  // convert to standalone wordpress image block
  $fig_classes = ['wp-block-image', 'size-large'];
  $img_url = $el->getAttribute('image');
  $img_id = $el->getAttribute('image_id');

  $html  = '<figure class="' . join(' ', $fig_classes) . '">';
  $html .= '<img src="' . $img_url . '" alt="" class="wp-image-' . $img_id . '">';
  $html .= '</figure>';

  $output = serialize_block(array(
    'blockName' => 'core/image',
    'attrs' => NULL,
    'innerBlocks' => [],
    'innerHTML' => $html,
    'innerContent' => [$html],
  ));

  cg_node_replace_with_html($el, $output);
}

function cg_fusion_imageframe(DOMElement &$el) {
  // convert to standalone image

  $fig_classes = ['wp-block-image', 'size-large'];
  $img_url = $el->textContent;
  $img_id = explode('|', $el->getAttribute('image_id'))[0];

  $html  = '<figure class="' . join(' ', $fig_classes) . '">';
  $html .= '<img src="' . $img_url . '" alt="" class="wp-image-' . $img_id . '">';
  $html .= '</figure>';

  $output = serialize_block(array(
    'blockName' => 'core/image',
    'attrs' => NULL,
    'innerHTML' => $html,
    'innerContent' => [$html],
  ));

  cg_node_replace_with_html($el, $output);
}

function cg_fusion_gallery(DOMElement &$el) {
  // Convert to embedded gallery with image ids

  $ids = [];
  $images = iterator_to_array($el->childNodes);
  foreach ($images as $image) {
    $id = intval($image->getAttribute('image_id')) ?? NULL;
    if ($id) $ids[] = $id;
  }

  if (count($ids)) {
    $output = serialize_block(array(
      'blockName' => 'acf/cg-gallery',
      'attrs' => array(
        'name' => 'acf/cg-gallery',
        'data' => array('gallery' => $ids, '_gallery' => 'field_67a4d28895546'),
        'align' => 'false',
        'mode' => 'preview',
      ),
      'innerBlocks' => [],
      'innerHTML' => '',
      'innerContent' => [],
    ));
    cg_node_replace_with_html($el, $output);
  } else {
    cg_delete_node($el);
  }
}

function cg_fusion_youtube(DOMElement &$el) {
  $fig_classes = ['wp-block-embed', 'is-type-video', 'is-provider-youtube', 'wp-block-embed-youtube', 'wp-embed-aspect-16-9', 'wp-has-aspect-ratio'];

  // id="https://youtu.be/RvVMIwYUh-E"
  $url = $el->getAttribute('id');
  if ($url) {
    $html  = '<figure class="' . join(' ', $fig_classes) . '"><div class="wp-block-embed__wrapper">';
    $html .= $url;
    $html .= '</div></figure>';
  
    $output = serialize_block(array(
      'blockName' => 'core/embed',
      'attrs' => array (
        'url' => 'https://youtu.be/RvVMIwYUh-E',
        'type' => 'video',
        'providerNameSlug' => 'youtube',
        'responsive' => true,
        'className' => 'wp-embed-aspect-16-9 wp-has-aspect-ratio',
      ),
      'innerBlocks' => [],
      'innerHTML' => $html,
      'innerContent' => [$html],
    ));

    cg_node_replace_with_html($el, $output);
  } else {
    cg_delete_node($el);
  }
}

