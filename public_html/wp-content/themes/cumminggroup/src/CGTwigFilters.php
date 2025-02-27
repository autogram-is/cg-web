<?php

use Timber\Site;
use Timber\Timber;

/**
 * Class CGTwigFilters
 */
class CGTwigFilters {

  public function add_to_twig($twig) {
    $twig->addFilter(
      new \Twig\TwigFilter( 'pluralize', [$this, 'pluralize'] )
    );
    $twig->addFilter(
      new \Twig\TwigFilter( 'date_quarter', [$this, 'date_quarter'] )
    );
    $twig->addFilter(
      new \Twig\TwigFilter( 'stylize', [ $this, 'stylize_title' ] )
    );
    $twig->addFilter(
      new \Twig\TwigFilter( 'statistic', [ $this, 'stylize_statistic' ] )
    );
    $twig->addFilter(
      new \Twig\TwigFilter( 'gravityform', [ $this, 'render_gravity_form' ],  )
    );

    $twig->addFilter(
      new \Twig\TwigFilter( 'youtube_id', 'cg_format_youtube_url')
    );
    $twig->addFilter(
      new \Twig\TwigFilter( 'youtube_url', function($url) { return cg_format_youtube_url($url, 'url'); })
    );
    $twig->addFilter(
      new \Twig\TwigFilter( 'youtube_embed', function($url) { return cg_format_youtube_url($url, 'embed'); })
    );
    $twig->addFilter(
      new \Twig\TwigFilter( 'youtube_poster', function($url) { return cg_format_youtube_url($url, 'poster'); })
    );

    $twig->addFilter(
      new \Twig\TwigFilter( 'phone', function($input) { return cg_format_phone($input); })
    );
    $twig->addFilter(
      new \Twig\TwigFilter( 'phone_url', function($input) { return cg_format_phone($input, 'url'); })
    );

    $twig->addFilter(
      new \Twig\TwigFilter( 'share_link', [ $this, 'share_link' ] )
    );

    $twig->addFilter(
      new \Twig\TwigFilter( 'map_link', [ $this, 'map_link' ] )
    );

    $twig->addFilter(
      new \Twig\TwigFilter( 'sort_locations', [ $this, 'sort_locations' ] )
    );

    $twig->addFunction(
      new \Twig\TwigFunction( 'template_list', 'cg_post_templates' )
    );

    return $twig;
  }

  function pluralize(Countable|array $countable, string $pluralString, ?string $singularString = '', ?string $noneString = '') {
    if ($countable) {
      $count = count($countable);
      if ($count < 1) {
        return $noneString ?? '';
      } elseif ($count === 1) {
        return $singularString ?? '';
      } else {
        return $pluralString;
      }  
    } else {
      return $singularString ?? '';
    }
  }

  function render_gravity_form(?string $form_id = null, bool $display_title = true, bool $display_description = true) {
    if ($form_id && function_exists('gravity_form')) {
      $output = gravity_form(
        $form_id,
        $display_title,
        $display_description
      );
      return $output;
    }
  }

  /**
   * Sort offices
   *
   * - Sort by location, then title within the location, alphabetically
   *
   * @param string $text The headline to stylize
   */

  function sort_locations(?Traversable $locations): array {
    if( is_null($locations)) return [];
    $loc_array = iterator_to_array($locations);
    if (count($loc_array) === 0) return [];
    
    foreach ($loc_array as $key => $row) {
      $title[$key]    = $row -> title;
      $location[$key] = $row -> location;
    }
    
    array_multisort($location, SORT_ASC, $title, SORT_ASC, $loc_array);
    return $loc_array;
  }

  /**
   * Apply special styling to the visible title of a page.
   * 
   * - Wraps ' + ' in a span with class 'amp'
   *
   * @param string $text The headline to stylize
   */
  function stylize_title(?string $text) {
    if (is_null($text)) return '';
      $symbol = ' + ';
      return str_replace($symbol, " <span class=\"amp\">" . trim($symbol) . "</span> ", $text);
  }

  /**
   * Apply special styling to the value field of a statistic.
   *
   * Examples:
   * 
   * 	3,029,144 MTCO₂e => 3,029,144<span>MTCO₂e</span>
   *	412,614 MWh => 412,614<span>MWh</span>
   *	115+ MW => 115<span class="sup">+</span><span>MW</span>
   *	2,000+ => 2,000<span class="sup">+</span>
   *	#14 => <span class="sup">#</span>14
   *
   * @param string $text The statistic to stylize
   */
  function stylize_statistic(?string $text) {
    if (is_null($text) || trim($text) === '') {
      return '';
    }

    $prefix = '\-\+#$€~';
    $num = '\d\.,';
    $regex = "/([" . $prefix . "])?([" . $num . "]+)?([" . $prefix . "])?(.+)?/";
    $output = '';

    preg_match($regex, trim($text), $match);
    $prefix = $match[1] ?? '';
    $statistic = $match[2] ?? '';
    $suffix = $match[3] ?? '';
    $remainder = $match[4] ?? '';
    
    if ($statistic) {
      if ($prefix) $output .= '<span class="sup">' . trim($prefix) . '</span>';
      if ($statistic) $output .= trim($statistic);
      if ($suffix) $output .= '<span class="sup">' . trim($suffix) . '</span>';
      if ($remainder) $output .= '<span>' . trim($remainder) . '</span>';
    } else {
      $output = trim($text);
    }
  
    return $output;
  }

  function share_link(int $post_id, ?string $target = 'email') {
    // See https://developer.x.com/en/docs/x-for-websites/tweet-button/overview
    // See https://learn.microsoft.com/en-us/linkedin/consumer/integrations/self-serve/plugins/share-plugin

    $post = get_post($post_id);
    $link = get_post_permalink($post_id);
    $url = '';

    if ('linkedin' === $target) {
      $url = "https://www.linkedin.com/sharing/share-offsite/";
      $url .= "?url=" . urlencode($link);
    } else if ('twitter' === $target) {
      $url = "http://twitter.com/share/";
      $url .= "?text=" . urlencode($post->title + '\n\n' . $link);
    } else {
      $url = "mailto:";
      $url .= "?subject=" . rawurlencode(htmlspecialchars_decode($post->post_title));
      $url .= "&body=" . urlencode($link);
    }

    if ($url) return $url;
  }

  function map_link($address) {
    // Generates a URL-escaped comma-delimited link to https://www.google.com/maps/place/

    if (is_array($address)) {
      $address = join(",", $address);
    } else {
      $address = str_replace("<br />", "\n", $address);
      $address = str_replace("\n", ",", $address);
    }

    return "https://www.google.com/maps/place/" . str_replace(' ', '+', $address);
  }

  /**
   * Takes a date string and returns a 'Q1, 2025' style date string.
   */
  function date_quarter(string $dateStr) {
    $date = strtotime($dateStr);
    $month = date("n", $date);
    $year = date("Y", $date);
    $quarter = ceil($month / 3);
    return "Q$quarter, $year";
  }
}
