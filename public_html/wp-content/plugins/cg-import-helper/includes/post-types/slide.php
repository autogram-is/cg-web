<?php

function cgih_preprocess_raw_slide($postdata) {
  // Find the page this slide is attached to and shift is metadata over there.
  // Then, mark this post as DO NOT IMPORT
  return $postdata;
}