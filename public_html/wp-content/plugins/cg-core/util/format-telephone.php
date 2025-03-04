<?php
use Brick\PhoneNumber\PhoneNumber;
use Brick\PhoneNumber\PhoneNumberParseException;
use Brick\PhoneNumber\PhoneNumberFormat;

function cg_format_phone(?string $input, string $format = 'display') {
    if (str_starts_with($input, '+')) {
      $country = NULL;
    } else {
      $country = 'US';
    }
    try {
      $number = PhoneNumber::parse($input, $country);
      if ($number->isValidNumber()) {
        if ($format === 'url') {
          return $number->format(PhoneNumberFormat::RFC3966);
        } else if ($format === 'local') {
          return $number->format(PhoneNumberFormat::NATIONAL) . " <!-- " . $number->getRegionCode() . " -->";
        } else {
          return $number->format(PhoneNumberFormat::INTERNATIONAL) . " <!-- " . $number->getRegionCode() . " -->";
        }
      } else {
        return $input;
      }
    }
    catch (PhoneNumberParseException $e) {
      return $input . "<!-- " . $e->getMessage() . "-->";
    }
  }
