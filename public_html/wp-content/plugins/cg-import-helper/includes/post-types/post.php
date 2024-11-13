<?php

// We need special handling for several categories of posts:
// 1. 'Events Hosted by Others' - Turn them into `cg_event` posts.
// 2. 'Podcast episodes' - Right now we have a cg_podcast type, but may just treat it as a
//    special case of news/thought leadership.
// 3. 'Case Study' - in-depth writeups of several projects. Skip them and manually move the
//    data to the appropriate Project post.

function cgih_preprocess_raw_post($postdata) {
  // This covers news and other posts.

  // Catch the incoming topics, tags, and categories; map them to other relationships.

  return $postdata; 
}