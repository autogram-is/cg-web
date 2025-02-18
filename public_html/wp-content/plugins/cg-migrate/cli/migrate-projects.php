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

  $processed_content = cg_clean_project_markup($post);
  $content = wp_kses($processed_content['body'], cg_extended_markup());

  if (!$dry_run) {
    $post->post_content = trim($content);
    wp_update_post($post);
  }

  WP_CLI::log(($dry_run ? "Dry Run: " : "") . "Project #$post->ID ($post->post_title): " . join(', ', $messages));
}

function cg_clean_project_markup($post) {
  $output = [];

  $dom = cg_get_dom($post->post_content);

  $preserve_children = ['fusion_accordion', 'fusion_builder_column', 'fusion_builder_row', 'fusion_builder_container', 'fusion_testimonials'];
  $remove_entirely = ['fusion_portfolio', 'fusion_blog', 'fusion_slider', 'fusion_slide', 'fusion_code', 'fusion_global', 'fusion_separator', 'fusion_table'];
  $simple_tags = ['fusion_checklist', 'fusion_li_item', 'fusion_highlight', 'fusion_button', 'fusion_toggle'];
  $media_tags = ['fusion_imageframe', 'fusion_image', 'fusion_gallery', 'fusion_youtube', 'fusion_testimonial'];

  cg_dom_remove_tags($dom, $preserve_children, true);
  cg_dom_remove_tags($dom, $remove_entirely, false);
  cg_dom_process_fusion_tags($dom, $simple_tags);
  cg_dom_process_fusion_tags($dom, $media_tags);
  cg_dom_process_fusion_tags($dom, ['fusion_text']);

  cg_dom_process_fusion_titles($dom, [$post->post_title], 2);
  $output['facts'] = projectFactTable($dom);

  cg_dom_remove_tags($dom, ['table']);

  cg_log_remaining_fusion_tags($dom);

  $output['raw'] = $post->post_content;
  $output['body'] = $dom->saveHTML();

  return $output;
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
