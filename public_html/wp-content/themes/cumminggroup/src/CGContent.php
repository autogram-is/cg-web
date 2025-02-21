<?php

use Timber\Post;
use Timber\Timber;

/**
 * Class CGContent
 * 
 * Base class for all content on the Cumming Group site.
 */
class CGContent extends Post {
	public function __construct() {
		parent::__construct();
	}

	public function excerpt(array $options = []) {
		return parent::excerpt($options);
	}

	public function thumbnail_or_placeholder() {
		$ph = $this->placeholder();
		$thumb = $this->thumbnail();
		
		if ($ph) {
			if ($ph['override'] ?? false) {
				return $ph;
			} elseif (!$thumb && $ph['image']) {
				return $ph['image'];
			}
		}
		return $thumb;
	}

	/**
	 * If no thumbnail image is available for a post, construct a text placeholder.
	 * Uses fallback logic with the `placeholder_text` and `placeholder_bg` fields
	 * to allow post, news category, and post-type level placeholder overrides.
	 */
	public function placeholder() {
		// Global defaults for the post type.
		$placeholder = $this->_placeholder_defaults() ?? [];

		// Override existing settings with post-specific placeholder text.
		$text = $this->meta('placeholder_text');
		$bg = $this->meta('placeholder_bg');
		if ($text) {
			$placeholder['text'] = $text;
		}
		if ($bg) {
			$placeholder['bg'] = $bg;
		}

		// If an image is set, load it.
		if ($placeholder['image'] ?? false) {
			$placeholder['image'] = Timber::get_image($placeholder['image']);
		}

		// If either text or image is set, return a value.
		if (($placeholder['text'] ?? false) || ($placeholder['image'] ?? false)) {
			return $placeholder;
		}
	}

	/**
	 * Builds a convenience bundle of hero treatment information.
	 *
	 * @return \Timber\PostCollectionInterface
	 */
	public function hero() { 
		$field_prop = '_hero';
		if (isset($this->_hero)) {
			return $this->_hero;
		}

		$type = $this->meta('hero_type') ?? 'default';
		$this->_hero = ["type" => $type];
		
		if ('project' === $type) {
			$project_ids = $this->meta('hero_projects');
			if (is_array($project_ids) && count($project_ids) > 0) {
				$this->_hero['project'] = Timber::get_post($project_ids[array_rand($project_ids)]);
			}

		} else if ('statistics' === $type) {
			$this->_hero['statistics'] = $this->meta('hero_statistics') ?? [];

		} else if ('gallery' === $type) {
			$images = $this->meta('hero_gallery');
			if ($images && count($images) > 0) {
				$this->_hero['image'] = Timber::get_image($images[array_rand($images)]);
			}

		} else if ('default' === $type) {
			if ($this->thumbnail()) {
				$this->_hero['image'] = $this->thumbnail();
			}
		}

		return $this->_hero;	
	}

	protected function _placeholder_defaults() {
		$keyed = [];
		$options = get_fields('cg_options');
		if ($options && $options['placeholders']) {
			foreach ($options['placeholders'] as $default) {
				$keyed[$default['type']] = $default;
			}	
		}

		if (key_exists($this->post_type, $keyed)) {
			return $keyed[$this->post_type];
		} elseif (key_exists('default', $keyed)) {
			return $keyed['default'];
		} else {
			return false;
		}
	}

	/**
	 * Helper function for all post type classes; lets us store post IDs but
	 * temporarily cache fully instantiated post lists without worrying about
	 * double-loading them.
	 */
	protected function _cache_relationship(string $field_name, ?int $limit = -1) {
		$field_prop = '_'.$field_name;
		if (isset($this->$field_prop)) {
			return $this->$field_prop;
		}

		$meta = $this->meta($field_name);
		if (is_array($meta) && ($limit > 0)) {
			$meta = array_slice($meta, 0, $limit);
		}
		
		if (!$meta) {
			$this->$field_prop = null;
		} else {
			$this->$field_prop = Timber::get_posts($meta);
			return $this->$field_prop;	
		}
	}
}
