<?php

// Ignore same-title-as-post, 
function cg_fusion_title(DOMElement &$element, $ignore = []) {
  // convert to h2
}

function cg_fusion_text(DOMElement &$element) {
  // Extract internal HTML
}

function cg_fusion_image(DOMElement &$element) {
  // convert to standalone image

  // image="http://localhost/wp-content/uploads/2018/01/Disney-1.png"
  // image_id="19464|full"
}

function cg_fusion_imageframe(DOMElement &$element) {
  // convert to standalone image

  // image_id="52267|full"
  //
  // http://localhost/wp-content/uploads/2023/02/ASHE-Landing-Page-Hero-1.png
}

function cg_fusion_gallery(DOMElement &$element) {
  // Convert to embedded gallery with image ids

  // layout="masonry"
  // columns="5"

  // fusion_gallery_image
  // image="url"
  // image_id="number"
}

function cg_fusion_checklist(DOMElement &$element) {
  // convert to <ul>
}

function cg_fusion_li_item(DOMElement &$element) {
  // convert to <li>
}

function cg_fusion_highlight(DOMElement &$element) {
  // convert to <mark>
}

function cg_fusion_button(DOMElement &$element) {
  // convert to button or ignore?

  // link="url"
  // title="Enhanced Project Monitoring"
  //
  // internal button content
}

function cg_fusion_person(DOMElement &$element) {
  // convert to person post, remove from dom

  // name="Drew Lantz"
  // title="Research Analyst"
  // picture="http://localhost/wp-content/uploads/2021/04/Drew-Lantz-1.jpg"
  // picture_id="39972|full"
  // pic_link=""
  // linkedin=""
  // email="dlantz@wordpress-243686-4130740.cloudwaysapps.com"
  // phone=""
}

function cg_fusion_youtube(DOMElement &$element) {
  // convert to youtube embed, or ignore

  // id="https://youtu.be/RvVMIwYUh-E"
}

function cg_fusion_toggle(DOMElement &$element) {
  // convert to <details> element

  // title=""
  // 
  // <content>
}



function cg_fusion_blog(DOMElement &$element) {
  // Ignore / Delete
}

function cg_fusion_portfolio(DOMElement &$element) {
  // Ignore / Delete
}

function cg_fusion_code(DOMElement &$element) {
  // Ignore / Delete
}
