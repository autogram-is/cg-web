<?php

use Timber\Site;
use Timber\Timber;
use \CGRelatedContentHelper;

/**
 * Class CGTwigFilters
 */
class CGTwigFilters {

  public function add_to_twig($twig) {
    $twig->addFilter(
      new \Twig\TwigFilter( 'pluralize', 'pluralize' )
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
      new \Twig\TwigFilter( 'youtube_url', [ $this, 'youtube_url' ],  )
    );
    return $twig;
  }


  function render_gravity_form(?string $form_id = null) {
    if ($form_id && function_exists('gravity_form')) {
      $output = gravity_form(
        $form_id,
        $display_title = true,
        $display_description = true,
        $display_inactive = false,
        $field_values = null,
        $ajax = false,
        $tabindex = 0,
        $echo = true,
        $form_theme = null,
        $style_settings = null
      );

      return $output;
    }
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


  /**
   * Given a YouTube video URL or ID, extract just the ID itself.
   *
   * @param string $text ID or URL to format
   */
  function youtube_url(?string $url, ?string $mode = 'id') {
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
}