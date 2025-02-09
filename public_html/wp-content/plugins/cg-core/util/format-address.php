<?php

function cg_format_address(mixed $address, string $mode = 'lines') {
  if (is_array($address)) {
    $address = join('\n', $address);
  }

  if ($mode === 'lines') {
    return "";
  } elseif ($mode === 'google') {
    $address = str_replace('\n', ',', $address);
    return "https://www.google.com/maps/place/" . str_replace(' ', '+', $address);
  } else {
    // format with BRs
    return "";
  }

  return "";
}
