<?php

function cg_fusion_testimonial(DOMElement &$el) {
  // Convert to a CG Pullquote

  $attribution = $el->getAttribute('name') ?? NULL;
  $image = $el->getAttribute('image') ?? NULL;
  $quote = trim($el->textContent);

  // Pull the leading/trailing quote characters
  if (str_starts_with($quote, '"')) {
    $quote = substr($quote, 1);
  }
  if (str_ends_with($quote, '"')) {
    $quote = substr($quote, 0, strlen($quote)-1);
  }

  $output = serialize_block(array(
    "blockName" => "acf/cg-pullquote",
    "attrs" => array(
      "name"=> "acf/cg-pullquote",
      "data" => array(
        "type" => "default",
        "quote" => $quote,
        "attribution" => $attribution,
        "_type" => "field_67a4c3c3eeb92",
        "_quote" => "field_672bc6098990a",
        "_attribution" => "field_672bc6348990c",
      ),
      "align" => false,
      "mode" => 'preview'
    ),
    "innerBlocks" => [],
    "innerHTML" => '',
    "innerContent" => [],
  ));

  cg_node_replace_with_html($el, $output);
}

