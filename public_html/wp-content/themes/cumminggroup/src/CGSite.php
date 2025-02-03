<?php

use Timber\Site;
use Timber\Timber;

/**
 * Class CGSite
 */
class CGSite extends Site {
		public function __construct() {
				add_action( 'after_setup_theme', array( $this, 'theme_supports' ) );
				
				add_action( 'after_setup_theme', function () {
					register_nav_menus([
						'primary' => 'Primary Menu',
						'primary-eu' => 'Primary Menu EU',
						'footer' => 'Footer Links',
						'fine-print' => 'Fine Print Links',
					]);

					cg_register_block_styles();
				});
				add_action('enqueue_block_assets', array($this, 'enqueue_editor_assets'));
				add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
				add_filter( 'timber/context', array( $this, 'add_to_context' ) );
				add_filter( 'timber/twig', array( $this, 'add_to_twig' ) );
				
				add_shortcode('index', 'cg_shortcode_index');
				add_shortcode('events-upcoming', 'cg_shortcode_events_upcoming' );
				add_shortcode('events-past', 'cg_shortcode_events_past' );
				add_shortcode('offices', 'cg_shortcode_region_offices' );

				add_filter('get_block_type_variations', 'cg_block_type_variations', 10, 2);
				add_filter('timber/acf-gutenberg-blocks-data', 'cg_populate_custom_block_data');

				// If we end up using Gravity Forms, we need this set to true to avoid its parade of custom CSS.
				add_filter( 'gform_disable_css', '__return_true' );

				parent::__construct();
		}

		/**
		 * This is where you add some context
		 *
		 * @param string $context context['this'] Being the Twig's {{ this }}.
		 */
		public function add_to_context( $context ) {
			$context['menu'] = Timber::get_menu('primary');
			$context['menu_eu'] = Timber::get_menu('primary-eu');

			$context['options'] = get_fields('option');

			$context['footer'] = get_fields('footer');
			$context['footer']['nav'] = Timber::get_menu('footer');
			$context['footer']['fine_print'] = Timber::get_menu('fine-print');
		
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
				
				add_theme_support('editor-styles');
				add_theme_support('disable-layout-styles');
				add_theme_support('disable-custom-colors');
				add_theme_support('disable-custom-font-sizes');
				add_theme_support('disable-custom-gradients');
		}

		/**
		 * Include baseline styles for the Gutenberg editor
		 */
		public function enqueue_editor_assets() {
			if (is_admin()) {
				wp_enqueue_style(
					'cg-editor-editor-styles',
					get_theme_root_uri() . "/cumminggroup/assets/css/editor-styles.css"
				);	
			}
		}

		public function enqueue_assets() {
			wp_enqueue_style('dashicons');
		}

		/**
		 * This is where you can add your own functions to twig.
		 *
		 * @param Twig\Environment $twig get extension.
		 */
		public function add_to_twig( $twig ) {
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
	}