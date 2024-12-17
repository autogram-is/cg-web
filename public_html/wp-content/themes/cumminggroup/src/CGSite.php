<?php

use Timber\Site;

/**
 * Class CGSite
 */
class CGSite extends Site {
		public function __construct() {
				add_action( 'after_setup_theme', array( $this, 'theme_supports' ) );

				add_filter( 'timber/context', array( $this, 'add_to_context' ) );
				add_filter( 'timber/twig', array( $this, 'add_to_twig' ) );

				parent::__construct();
		}

		/**
		 * This is where you add some context
		 *
		 * @param string $context context['this'] Being the Twig's {{ this }}.
		 */
		public function add_to_context( $context ) {
			$context['menu'] = Timber::get_menu(908);
			// $context['menu-eu'] = Timber::get_menu('eu');
			// $context['footer'] = Timber::get_menu('footer');
			$context['site'] = $this;

			return $context;
		}

		public function theme_supports() {
				// Add default posts and comments RSS feed links to head.
				add_theme_support( 'automatic-feed-links' );

				/*
				* Let WordPress manage the document title.
				* By adding theme support, we declare that this theme does not use a
				* hard-coded <title> tag in the document head, and expect WordPress to
				* provide it for us.
				*/
				add_theme_support( 'title-tag' );

				/*
				* Enable support for Post Thumbnails on posts and pages.
				*
				* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
				*/
				add_theme_support( 'post-thumbnails' );

				/*
				* Switch default core markup for search form, comment form, and comments
				* to output valid HTML5.
				*/
				add_theme_support(
						'html5',
						array(
								'comment-form',
								'comment-list',
								'gallery',
								'caption',
						)
				);

				add_theme_support( 'menus' );

				add_theme_support('disable-layout-styles');
				add_theme_support('disable-custom-colors');
				add_theme_support('disable-custom-font-sizes');
				add_theme_support('disable-custom-gradients');
		}

		/**
		 * This is where you can add your own functions to twig.
		 *
		 * @param Twig\Environment $twig get extension.
		 */
		public function add_to_twig( $twig ) {
				/**
				 * Required when you want to use Twig’s template_from_string.
				 * @link https://twig.symfony.com/doc/3.x/functions/template_from_string.html
				 */
				// $twig->addExtension( new Twig\Extension\StringLoaderExtension() );
				// $twig->addFilter( new Twig\TwigFilter( 'myfoo', [ $this, 'myfoo' ] ) );

				$twig->addFilter(new \Twig\TwigFilter( 'pluralize', 'pluralize' ));
				$twig->addFilter(new \Twig\TwigFilter( 'stylize', [ $this, 'stylize_title' ] ));

				return $twig;
		}

		/**
		 * Apply special styling to the visible title of a page.
		 * 
		 * - Wraps ' + ' in a span with class 'amp'
		 *
		 * @param string $text The headline to stylize
		 */
		function stylize_title(string $text) {
				$symbol = ' + ';
				return str_replace($symbol, " <span class=\"amp\">" . trim($symbol) . "</span> ", $text);
		}
}
