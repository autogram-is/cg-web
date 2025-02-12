<?php

function cg_fusion_title(DOMElement &$el, $ignore = [], ?int $force_level = NULL) {
  $weight = $force_level ?? intval($el->getAttribute('size') ?? 2);
  $text = trim(str_replace('\n', '', $el->textContent));
  // might use wp_strip_all_tags($text) here instead of textContent?

  if (in_array($text, $ignore) || strlen($text) === 0) {
    $el->remove();
  } else {
    $newNode = $el->ownerDocument->createElement('h'.$weight, $text);
    if ($weight < 4) {
      $newNode->setAttribute('class', 'is-style-hed-accent');
    }
    $el->replaceWith($newNode);  
  }
}

function cg_fusion_text(DOMElement &$el, bool $trim = true) {
  return cg_delete_node($el, true);
}

function cg_fusion_checklist(DOMElement &$el) {
  return cg_rename_node($el, 'ul');
}

function cg_fusion_li_item(DOMElement &$el, bool $trim = true) {
  return cg_rename_node($el, 'li');
}

function cg_fusion_highlight(DOMElement &$el) {
  return cg_rename_node($el, 'mark');
}

function cg_fusion_toggle(DOMElement &$el) {
  $title = $el->getAttribute('title');
  $newNode = cg_rename_node($el, 'details');
  $summary = $newNode->ownerDocument->createElement('summary', $title);

  $newNode->insertBefore($summary, $newNode->firstChild);
}

function cg_fusion_button(DOMElement &$el) {
  $link = $el->getAttribute('link');
  $title = $el->getAttribute('title');
  $newNode = cg_rename_node($el, 'a');
  $newNode->setAttribute('href', $link);
  $newNode->setAttribute('title', $title);
  $newNode->setAttribute('class', 'button');
}
