<?php

function cg_fusion_person(DOMElement &$el) {
  $person = array(
    'name' => $el->getAttribute('name'),
    'role' => $el->getAttribute('title'),
    'headshot' => $el->getAttribute('picture'),
    'linkedin' => $el->getAttribute('linkedin'),
    'email' => $el->getAttribute('email'),
    'phone' => $el->getAttribute('phone'),
  );
  cg_delete_node($el);
  return $person;
}

function cg_event_person_components(DOMDocument &$dom) {
  $people = [];

  $nodes = iterator_to_array($dom->getElementsByTagName('fusion_person'));
  foreach ($nodes as $node) {
    $person = cg_fusion_person($node);
    if ($person) $people[] = $person;
  }

  return $people;
}

function cg_event_attendee_markup(DOMDocument &$dom) {
  $people = [];

  // Create a new DOMXPath instance
  $xpath = new DOMXPath($dom);

  // Bios of CG staff in attendance. Structure:
  // h4 Attendees
  // div
  //   p img
  //   div
  //     p name
  //     p title
  //     p a email
  @$h4 = $xpath->query('//h4')->item(0);
  if ($h4 && $h4->textContent === 'Attendees') {
    @$bios = $xpath->query('//h4/following-sibling::div/div');
    foreach (iterator_to_array($bios) as $bio) {
      @$img = $xpath->query('img', $bio)->item(0);
      @$props = $xpath->query('div/p', $bio);
      @$email = $xpath->query('div//a', $bio)->item(0);
      $attendee = array(
        'headshot' => @$img->attributes['src']->value,
        'name' => @$props->item(0)->textContent,
        'role' => @$props->item(1)->textContent,
        'email' => @$email->attributes['href']->value,
      );
      if ($attendee['name']) {
        $people[] = $attendee;
      }
      $bio->parentNode->removeChild($bio);
    }
    $h4->parentNode->removeChild($h4);
  }

  return $people;
}