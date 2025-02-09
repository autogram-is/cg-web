<?php
use Brick\PhoneNumber\PhoneNumber;
use Brick\PhoneNumber\PhoneNumberParseException;
use Brick\PhoneNumber\PhoneNumberFormat;

function cg_format_phone(?string $input, string $format = 'display') {
    try {
      $number = PhoneNumber::parse($input);
      if ($number->isValidNumber()) {
        if ($format === 'url') {
          return $number->format(PhoneNumberFormat::RFC3966);
        } else if ($format === 'local') {
          return $number->format(PhoneNumberFormat::NATIONAL);
        } else {
          return $number->format(PhoneNumberFormat::INTERNATIONAL);
        }
      }
    }
    catch (PhoneNumberParseException $e) {
      return $input;
    }
  }
