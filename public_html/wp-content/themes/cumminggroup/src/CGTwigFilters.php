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

    return $twig;
  }

  function pluralize(Countable $countable, string $pluralString, ?string $singularString = '', ?string $noneString = '') {
    $count = count($countable);
    if ($count < 1) {
      return $noneString ?? '';
    } elseif ($count === 1) {
      return $singularString ?? '';
    } else {
      return $pluralString;
    }
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
}