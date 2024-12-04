<?php

use Timber\Post;

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
	 * Gets the locale of the current post if one is applicable/available.
	 *
	 * @return \Timber\PostCollectionInterface
	 */
	public function locale() {
		return null;
	}

	/**
	 * Gets region pages connected to the current post.
	 *
	 * @return \Timber\PostCollectionInterface
	 */
	public function regions() { return $this->_related('regions'); }

	/**
	 * Gets office pages connected to the current post.
	 *
	 * @return \Timber\PostCollectionInterface
	 */
	public function offices() { return $this->_related('offices'); }

	/**
	 * Gets sector pages connected to the current post.
	 *
	 * @return \Timber\PostCollectionInterface
	 */
	public function sectors() { return $this->_related('sectors'); }

	/**
	 * Gets services connected to the current post.
	 *
	 * @return \Timber\PostCollectionInterface
	 */
	public function services() { return $this->_related('services'); }

	/**
	 * Gets projects connected to the current post.
	 *
	 * @return \Timber\PostCollectionInterface
	 */
	public function projects() { return $this->_related('projects'); }

	/**
	 * On content with related portfolio-items (events, posts, etc), returns related portfolio pages.
	 * On portfolio pages, returns the related news/events/posts.
	 *
	 * @return \Timber\PostCollectionInterface
	 */
	public function related() {
		return array();
	}

	/**
	 * Returns the portfolio pages a news item is related to, formatted such that they can be passed
	 * to `links` output functions.
	 */
	public function portfolio_tags() {
		return array();
	}

	private function _related($field_name) {
		$field_prop = '-'.$field_name;
		if (isset($this->$field_prop)) {
			return $this->$field_prop;
		}

		$this->$field_prop = Timber::get_posts($this->meta($field_name));
		return $this->$field_prop;
	}
}
