<?php

function cg_migrate_project($post, $dry_run = false, $preserve = false) {
  $messages = [];
  
  if ($post->post_type === 'avada_portfolio') {
    $messages[] = 'updated type';
    if (!$dry_run) {
      set_post_type($post->ID, 'project');
      $post = get_post($post->ID);
    }
  }

  $attachment_count = cg_attachments_to_galleries($post, true, $dry_run);
  if ($attachment_count) {
    $messages[] = "$attachment_count gallery images";
  }

  if (!$dry_run) {
    cg_save_migration_body($post->ID, $post->post_body);
  }
  $fusion = cg_clean_project_markup($post);
  $post->post_content = $fusion['body'];

  if (!$dry_run) {
    wp_update_post($post);
    if ($fusion['facts']) {
      cg_save_migration_data($post->ID, $fusion['facts']);
    }
  }

  WP_CLI::log(($dry_run ? "Dry Run: " : "") . "Project #$post->ID ($post->post_title): " . join(', ', $messages));
}

function cg_clean_project_markup($post) {
  $dom = cg_get_cleaned_dom($post->post_content);

  $result = [];
  $result['raw'] = $post->post_content;
  $result['body'] = join(PHP_EOL, fusionTextBlocks($dom));
  $result['facts'] = projectFactTable($dom);

  return $result;
}

function fusionTextBlocks($dom, $node = null, &$chunks = []) {
  if ($node) {
    if ($node->nodeType === XML_ELEMENT_NODE) {
      // Fusion Titles get converted to H2s, Fusion Text gets converted to P tags.
      // This needs to be a bit more rigorous but for now it works.
      if ($node->tagName === 'fusion_text') {
        $text = $dom->saveHTML($node);
        $chunks[] = wp_kses($text, cg_allowed_markup());
      }
    }
  } else {
    $node = $dom;
  }

  // Recursively traverse child nodes
  foreach ($node->childNodes as $child) {
    fusionTextBlocks($dom, $child, $chunks);
  }
  return $chunks;
}

function projectFactTable($dom) {
  $tableData = [];
  $rows = $dom->getElementsByTagName('tr');
  foreach ($rows as $row) {
      $cells = $row->getElementsByTagName('td'); // Get all <td> cells
      if ($cells->length === 0) {
          $cells = $row->getElementsByTagName('th'); // Also include header cells
      }

      $rowData = [];
      foreach ($cells as $cell) {
          $rowData[] = trim($cell->textContent);
      }
      $tableData[] = $rowData;
  }

  $output = [];
  foreach ($tableData as $row) {
    $text = str_replace('(s)', 's', $row[0] ?? '');
    $text = str_replace(':', '', $text);
    $text = str_replace(' ', '_', $text);
    $text = strtolower($text);
    $output[$text] = $row[1] ?? '';
  }
  return $output;
}

function cg_attachments_to_galleries($post, $ignore_featured, $dry_run = false) {
  $args = array(
    'order' => 'ASC',
    'post_type' => 'attachment',
    'post_parent' => $post->ID,
    'post_mime_type' => 'image',
    'post_status' => null,
    'fields' => 'ids',
  );
  $attachments = get_posts($args);
  $featured = get_post_thumbnail_id($post->ID);

  if ($ignore_featured) {
    unset($attachments[array_search($featured, $attachments)]);
  }

  if (!$dry_run) {
    update_field('gallery', $attachments, $post->ID);
  }
  return count($attachments);
}
