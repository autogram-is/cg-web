<?php

use Timber\Site;

/**
 * Class CGSite
 */
class CGSite extends Site {
		public function __construct() {
				add_action( 'after_setup_theme', array( $this, 'theme_supports' ) );
				add_action( 'after_setup_theme', function () {
					register_nav_menus([
						'primary' => 'Primary Menu',
						'primary_eu' => 'Primary Menu (EU Version)',
						'footer' => 'Footer Menu',
					]);
				});

				add_filter( 'timber/context', array( $this, 'add_to_context' ) );
				add_filter( 'timber/twig', array( $this, 'add_to_twig' ) );
				
				add_shortcode('index', array( $this, 'shortcode_post_index' ));

				parent::__construct();
		}

		/**
		 * This is where you add some context
		 *
		 * @param string $context context['this'] Being the Twig's {{ this }}.
		 */
		public function add_to_context( $context ) {
			$context['menu'] = Timber::get_menu('primary');
			$context['menu_eu'] = Timber::get_menu('primary_eu');
			$context['footer_nav'] = Timber::get_menu('footer');

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
		function stylize_title(string | NULL $text) {
			if (is_null($text)) return '';
				$symbol = ' + ';
				return str_replace($symbol, " <span class=\"amp\">" . trim($symbol) . "</span> ", $text);
		}

		function shortcode_post_index($atts) {
			if (isset($atts['type'])) {
				$type = sanitize_text_field($atts['type']);
			} else {
				$type = 'post';
			}

			if (isset($atts['limit'])) {
				$limit = (int)sanitize_text_field($atts['limit']);
			} else {
				$limit = get_option('posts_per_page');
			}

			if (isset($atts['category'])) {
				$category = sanitize_text_field($atts['category']);
			} else {
				$category = NULL;
			}

			if (isset($atts['style'])) {
				$style = sanitize_text_field($atts['style']);
			} else {
				$style = NULL;
			}

			// Using the WP_Query argument format.
			$posts = Timber::get_posts( [
				'post_type'     => $type,
				'category_name' => $category,
				'posts_per_page' => $limit,
				'paged' => (get_query_var('paged')) ? get_query_var('paged') : 1
			]);

			$data = array(
				'posts' => $posts,
				'category' => $category,
				'style' => $style
			);

			// This time we use Timber::compile since shortcodes should return the code
			return Timber::compile('shortcodes/index.twig', $data);
		}

}