<?php

function cg_format_youtube_url(?string $url, ?string $mode = 'id') {
  $id = '';
  if ($url && preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $url, $match)) {
    $id = $match[1];
  }

  if ($id) {
    if ($mode === 'embed') {
      return "https://www.youtube-nocookie.com/embed/$id";
    } elseif ($mode === 'link') {
      return "https://www.youtube-nocookie.com/v/$id";
    } elseif ($mode === 'poster') {
      return "https://img.youtube.com/vi/$id/hqdefault.jpg";
    } else {
      return $id;
    }
  }

  return $url;
}
