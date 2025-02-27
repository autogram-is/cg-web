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

	public function has_facts() {
		$facts = $this->meta('facts');
		if (is_array($facts)) {
			foreach ($facts as $key => $value) {
				if ($value) return true;
			}	
		}
		return false;
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
	public function related_news(?int $limit = -1) { return parent::_cache_relationship('related_news', $limit); }

}