<?php

use Timber\Post;
use Timber\Timber;

/**
 * Class CGPost
 */
class CGPost extends Post {
	public function __construct() {
		parent::__construct();
	}

	public function excerpt(array $options = []) {
		return parent::excerpt($options);
	}

	/**
	 * Gets sector pages connected to the current post.
	 *
	 * @return \Timber\PostCollectionInterface
	 */
	public function sectors(?int $limit = -1) { return $this->_cache_relationship('sectors', $limit); }

	/**
	 * Gets services connected to the current post.
	 *
	 * @return \Timber\PostCollectionInterface
	 */
	public function services(?int $limit = -1) { return $this->_cache_relationship('services', $limit); }

	/**
	 * Gets projects connected to the current post.
	 *
	 * @return \Timber\PostCollectionInterface
	 */
	public function projects(?int $limit = -1) { return $this->_cache_relationship('projects', $limit); }

	/**
	 * Gets office pages connected to the current post.
	 *
	 * @return \Timber\PostCollectionInterface
	 */
	public function offices(?int $limit = -1) { return $this->_cache_relationship('offices', $limit); }

	/**
	 * Gets services connected to the current post.
	 *
	 * @return \Timber\PostCollectionInterface
	 */
	public function people(?int $limit = -1) { return $this->_cache_relationship('people', $limit); }

	/**
	 * Loads the people related to this item.
	 *
	 * @return \Timber\PostCollectionInterface
	 */
	public function internal_authors() { return $this->_cache_relationship('internal_authors'); }

	/**
	 * Gets services connected to the current post.
	 *
	 * @return \Timber\PostCollectionInterface
	 */
	public function related_news(?int $limit = -1) { return $this->_cache_relationship('related_news', $limit); }

	/**
	 * For news, events, and market insight reports, `service` `sector` `project` and `office`
	 * are collapsed to a single field.
	 *
	 * @return \Timber\PostCollectionInterface
	 */
	public function related_portfolio_items() { return $this->_cache_relationship('related_portfolio_items'); }

	/**
	 * For news, events, and market insight reports, `service` `sector` `project` and `office`
	 * are collapsed to a single field.
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

	private function _cache_relationship(string $field_name, ?int $limit = -1) {
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
