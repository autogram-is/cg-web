<?php

use Timber\Site;
use Timber\Timber;

/**
 * Class CGSite
 * 
 * Registers critical hooks on init, 
 */
class CGSite extends Site {
	public function __construct() {
		$twig_handler = new CGTwigFilters();
		add_filter('timber/twig', array($twig_handler, 'add_to_twig') );
		add_filter('timber/context', array( $this, 'add_to_context') );

		add_action('after_setup_theme', array($this, 'theme_supports'));
		add_action('after_setup_theme', array($this, 'theme_menu_locations'));

		// Adds theme styles to block editor pages
		add_action('enqueue_block_assets', array($this, 'enqueue_editor_assets'));

		// Adds Dashicons to the front end; we'd like to eliminate this.
		add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
		
		// Populate data for blocks that run dynamic queries
		add_filter('timber/acf-gutenberg-blocks-data', 'cg_populate_custom_block_data');
	
		// Add custom-registered block variations
		add_filter('get_block_type_variations', 'cg_block_type_variations', 10, 2);

		add_action('after_setup_theme', 'cg_register_block_styles');

		// If we're using Gravity Forms, disable its out-of-box CSS.
		add_filter( 'gform_disable_css', '__return_true' );

		parent::__construct();
	}

	/**
	 * This is where you add some context
	 *
	 * @param string $context context['this'] Being the Twig's {{ this }}.
	 */
	public function add_to_context( $context ) {
		$context['settings'] = get_fields('cg_options');

		$context['menu'] = Timber::get_menu('primary');
		$context['menu_eu'] = Timber::get_menu('primary-eu');

		$context['footer_links'] = Timber::get_menu('footer');
		$context['fine_print_links'] = Timber::get_menu('fine-print');
	
		$context['site'] = $this;
		return $context;
	}
	
	public function theme_menu_locations() {
		register_nav_menus([
			'primary' => 'Primary Menu',
			'primary-eu' => 'Primary Menu EU',
			'footer' => 'Footer Links',
			'fine-print' => 'Fine Print Links',
		]);
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


		add_theme_support('menus');
		
		add_theme_support('editor-styles');
		add_theme_support('disable-layout-styles');
		add_theme_support('disable-custom-colors');
		add_theme_support('disable-custom-font-sizes');
		add_theme_support('disable-custom-gradients');

		remove_theme_support('custom-header');
		remove_theme_support('core-block-patterns');
		remove_theme_support('post-formats');
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

	/**
	 * Used to display social footer icons; this is less than ideal but works.
	 * In the future, straight SVGs would be much better.
	 */
	public function enqueue_assets() {
		wp_enqueue_style('dashicons');
	}
}
