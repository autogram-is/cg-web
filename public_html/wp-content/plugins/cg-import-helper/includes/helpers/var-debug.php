<?php

function var_debug($data = null, $exit = false) {
  echo('<pre>');
  var_dump($data);
  echo('</pre>');
  if ($exit) {
    exit();
  }
}