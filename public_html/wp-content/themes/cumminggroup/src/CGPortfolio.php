<?php

/**
 * Class CGPortfolio
 * 
 * Handles 'project', 'sector', 'service', and 'office' content
 */
class CGPortfolio extends CGContent {
	public function __construct() {
		parent::__construct();
	}

  /**
	 * Gets sector pages connected to the current post.
	 *
	 * @return \Timber\PostCollectionInterface
	 */
	public function sectors(?int $limit = -1) { return parent::_cache_relationship('sectors', $limit); }

	/**
	 * Gets services connected to the current post.
	 *
	 * @return \Timber\PostCollectionInterface
	 */
	public function services(?int $limit = -1) { return parent::_cache_relationship('services', $limit); }

	/**
	 * Gets projects connected to the current post.
	 *
	 * @return \Timber\PostCollectionInterface
	 */
	public function projects(?int $limit = -1) { return parent::_cache_relationship('projects', $limit); }

	/**
	 * Gets projects connected to the current post.
	 *
	 * @return \Timber\PostCollectionInterface
	 */
	public function project_count() { 
		$projects = $this->meta('projects');
		if (is_array($projects)) {
			return count($projects);
		}
		return 0;
	}

	/**
	 * Gets office pages connected to the current post.
	 *
	 * @return \Timber\PostCollectionInterface
	 */
	public function offices(?int $limit = -1) { return parent::_cache_relationship('offices', $limit); }

	/**
	 * Gets services connected to the current post.
	 *
	 * @return \Timber\PostCollectionInterface
	 */
	public function people(?int $limit = -1) { return parent::_cache_relationship('people', $limit); }

  /**
	 * Convenience alias for the 'people' property
	 */
	public function leadership(?int $limit = -1) { return $this->people($limit); }

	/**
	 * Gets services connected to the current post.
	 *
	 * @return \Timber\PostCollectionInterface
	 */
	public function related_news(?int $limit = -1, $exclude_past_events = TRUE) {
		$field_prop = '_related_news';
		if (isset($this->$field_prop)) {
			return $this->$field_prop;
		}

		$meta = $this->meta('related_news');
		if (is_array($meta) && $exclude_past_events) {
			$meta = _remove_items($meta, cg_past_event_ids());
		}

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