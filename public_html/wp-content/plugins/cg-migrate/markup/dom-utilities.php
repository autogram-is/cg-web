<?php

function cg_get_dom(string $html, bool $fusion = true) {
  if ($fusion) {
    // Processes any tags matching `[fusion_*]` and `[/fusion_*]`, converting
    // them to pseudo-xml tags that can be processed using DOM manipulation tools.
    $html = preg_replace('/\[(\/?fusion.*?)\]/', '<\1>', $html);
  }

  $dom = new DOMDocument;
  libxml_use_internal_errors(true); // Suppress warnings for malformed HTML

  // Ensure UTF8 characters are handled correctly
  $contentType = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
  $dom->loadHTML($contentType . $html);
  // $tempDoc->loadHTML($contentType . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

  // This is to *remove* the uneeded meta tag once the parsing is done.
  $nodes = iterator_to_array($dom->getElementsByTagName('meta'));
  foreach ($nodes as $node) {
    cg_delete_node($node);
  }

  libxml_clear_errors();
  return $dom;
}

function cg_dom_remove_tags(DOMDocument $dom, array $tags = [], bool $preserve_children = false) {
  foreach ($tags as $tag) {
    $nodes = iterator_to_array($dom->getElementsByTagName($tag));
    foreach ($nodes as $node) {
      cg_delete_node($node, $preserve_children);
    }  
  }
}

function cg_dom_process_fusion_tags(DOMDocument $dom, array $tags = []) {
  foreach ($tags as $tag) {
    $func = 'cg_' . $tag;
    if (function_exists($func)) {
      $nodes = iterator_to_array($dom->getElementsByTagName($tag));
      foreach ($nodes as $node) {
        if (!is_null($node)) $func($node);
      }
    }
  }
}

function cg_dom_process_fusion_titles(DOMDocument $dom, array $ignore = [], ?int $level = NULL) {
  $nodes = iterator_to_array($dom->getElementsByTagName('fusion_title'));
  foreach ($nodes as $node) {
    cg_fusion_title($node, $ignore, $level);
  }
}

function cg_node_replace_with_html(DOMElement $el, string $html) {
  $doc = $el->ownerDocument;
  $tempDoc = new DOMDocument;
  libxml_use_internal_errors(true); // Suppress warnings for malformed HTML

  // Ensure UTF8 characters are handled correctly
  // $contentType = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
  // $tempDoc->loadHTML($contentType . $html);
  $tempDoc->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
  libxml_clear_errors();

  // Import the parsed nodes into the original document
  $fragment = $doc->createDocumentFragment();
  foreach ($tempDoc->childNodes as $node) {
    $importedNode = $doc->importNode($node, true);
    $fragment->appendChild($importedNode);
  }
  
  // Replace the element with the new content
  $el->parentNode->replaceChild($fragment, $el);
}
 
function cg_delete_node(DOMNode &$node, bool $save_children = true) {
  if ($save_children) {
    while ($node->firstChild) {
      $node->parentNode->insertBefore($node->firstChild, $node);
    }
  }
  $node->parentNode->removeChild($node);
}

function cg_rename_node(DOMNode &$oldNode, string $newName, bool | array $preserveAttributes = false) {
  $newNode = $oldNode->ownerDocument->createElement($newName);

  foreach($oldNode->attributes as $attr) {
    if ((is_bool($preserveAttributes) && $preserveAttributes) || (is_array($preserveAttributes) && in_array($attr, $preserveAttributes))) {
      $newNode->appendChild($attr->cloneNode());
    }
  }

  foreach($oldNode->childNodes as $child)
     $newNode->appendChild($child->cloneNode(true));

  $oldNode->parentNode->replaceChild($newNode, $oldNode);
  return $newNode;
}

function cg_log_remaining_fusion_tags(DOMDocument $dom) {
  $remaining_fusion_tags = cg_count_fusion_tags($dom);
  foreach ($remaining_fusion_tags as $tag => $count) {
    WP_CLI::log("  Encountered tag '$tag': $count");
  }
}

function cg_count_fusion_tags(DOMDocument $dom, $node = NULL, &$tags = []) {
  if ($node) {
    if ($node->nodeType === XML_ELEMENT_NODE) {
      if (str_starts_with($node->tagName, 'fusion_')) {
        if (!array_key_exists($node->tagName, $tags)) {
          $tags[$node->tagName] = 0;
        }
        $tags[$node->tagName]++;
      }
    }
  } else {
    $node = $dom;
  }

  // Recursively traverse child nodes
  foreach ($node->childNodes as $child) {
    cg_count_fusion_tags($dom, $child, $tags);
  }
  return $tags;
}
