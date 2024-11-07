<?php

function tag_to_office($slug) {
  $map = array(
    'aia-healthcare' => false,
    'dallas' => false,
    'durham' => false,
    'italy' => 'milan',
    'new-jersey' => false,
    'san-diego-pf' => 'san-diego',
    'washington-dc' => 'washington',
  );

  $type = 'cg_office';
  if (array_key_exists($slug, $map) && $map[$slug]) {
    return get_post_by_name($map[$slug], $type);
  } else {
    return get_post_by_name($slug, $type);
  }
}

function tag_to_service($slug) {
  $map = array(
    'budget-analysis' => false,
    'commissioning' => false,
    'construction-management' => false,
    'cost-estimating' => 'cost-commercial-management',
    'cost-management' => 'cost-commercial-management',
    'cost-modeling' => 'cost-commercial-management',
    'development-management' => 'program-project-management',
    'exhibit-development' => false,
    'gmp-evaluation' => false,
    'master-planning' => false,
    'move-management' => false,
    'owner-representation' => false,
    'preconstruction-services' => false,
    'program-management' => 'program-project-management',
    'project-management' => 'program-project-management',
    'project-monitoring' => 'project-monitoring',
    'scheduling' => 'scheduling',
    'sustainability' => 'energy-sustainability',
    'value-engineering' => false,
  );

  $type = 'cg_service';
  if (array_key_exists($slug, $map) && $map[$slug]) {
    return get_post_by_name($map[$slug], $type);
  } else {
    return get_post_by_name($slug, $type);
  }
}

function tag_to_sector($slug) {
  $map = array(
    'corporate-solutions-eu' => false,
    'division-lehrer-cumming' => false,
    'hospitality-eu' => 'hospitality',
    'industrial-logistics-warehousing-eu' => false,
    'life-sciences-eu' => false,
    'manufacturing' => false,
    'mixed-use-eu' => false,
    'offices-eu' => false,
    'portfolio-aviation-us' => 'aviation-transportation',
    'portfolio-commercial' => false,
    'portfolio-corporate-us' => false,
    'portfolio-culture-arts-us' => false,
    'portfolio-education-eu' => 'education',
    'portfolio-gaming-us' => 'hospitality-gaming',
    'portfolio-government' => false,
    'portfolio-healthcare-us' => 'healthcare',
    'portfolio-higher-education-us' => 'higher-ed',
    'portfolio-hospitality-us' => 'hospitality-gaming',
    'portfolio-industrial-us' => 'industrial-logistics',
    'portfolio-infrastructure-eu' => false,
    'portfolio-k-12-education-us' => false,
    'portfolio-life-science-us' => false,
    'portfolio-luxury-residential-us' => 'luxury-residential',
    'portfolio-mission-critical-us' => false,
    'portfolio-mixed-use-us' => false,
    'portfolio-retail-us' => 'retail',
    'portfolio-themed-entertainment-us' => 'themed-entertainment',
    'residential-eu' => 'residential',
    'retail-eu' => 'retail',
    'specialty-brownfield-eu' => false,
    'specialty-cladding-eu' => false,
    'sustainability' => false,
    'technology-data-centres-eu' => 'data-centers',
  );

  $type = 'cg_sector';
  if (array_key_exists($slug, $map) && $map[$slug]) {
    return get_post_by_name($map[$slug], $type);
  } else {
    return get_post_by_name($slug, $type);
  }
}