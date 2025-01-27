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
	 * Gets projects connected to the current post.
	 *
	 * @return \Timber\PostCollectionInterface
	 */
	public function people() { return $this->_related('people'); }

	/**
	 * On content with related portfolio-items (events, posts, etc), returns related portfolio pages.
	 * On portfolio pages, returns the related news/events/posts.
	 *
	 * @return \Timber\PostCollectionInterface
	 */
	public function related() { return $this->_related('related'); }

	/**
	 * For any region-tagged entity, find other items tagged with the same region(s).
	 */
	public function nearby($type = NULL, $limit = NULL) {
		$term_list = wp_get_post_terms($this->ID, 'region', array( 'fields' => 'all' ));
		
	}

	private function _related($field_name) {
		$field_prop = '_'.$field_name;
		if (isset($this->$field_prop)) {
			return $this->$field_prop;
		}

		$meta = $this->meta($field_name);
		if (!$meta) {
			$this->$field_prop = null;
		} else {
			$this->$field_prop = Timber::get_posts($meta);
			return $this->$field_prop;	
		}
	}
}
