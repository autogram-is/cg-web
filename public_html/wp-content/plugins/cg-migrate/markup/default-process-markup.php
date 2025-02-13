<?php

function cg_default_process_markup(object $post) {
  $output = [];
  $output['original'] = $post->post_content;

  $dom = cg_get_dom($post->post_content);

  $preserve_children = ['fusion_accordion', 'fusion_builder_column', 'fusion_builder_row', 'fusion_builder_container', 'fusion_testimonials'];
  $remove_entirely = ['fusion_portfolio', 'fusion_blog', 'fusion_slider', 'fusion_slide', 'fusion_code', 'fusion_global', 'fusion_separator'];
  $simple_tags = ['fusion_checklist', 'fusion_li_item', 'fusion_highlight', 'fusion_button', 'fusion_toggle'];
  $media_tags = ['fusion_imageframe', 'fusion_image', 'fusion_gallery', 'fusion_youtube', 'fusion_testimonial'];

  $headings_to_ignore = [$post->post_title];

  cg_dom_remove_tags($dom, $preserve_children, true);
  cg_dom_remove_tags($dom, $remove_entirely, false);
  cg_dom_process_fusion_tags($dom, $simple_tags);
  cg_dom_process_fusion_tags($dom, $media_tags);
  cg_dom_process_fusion_tags($dom, ['fusion_text']);

  cg_dom_process_fusion_titles($dom, $headings_to_ignore);

  $output['processed'] = $dom->saveHTML();
  
  cg_log_remaining_fusion_tags($dom);

  return $output;  
}
