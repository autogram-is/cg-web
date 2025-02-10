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
	public function sectors(?int $limit = -1) { return $this->relationship('sectors', $limit); }

	/**
	 * Gets services connected to the current post.
	 *
	 * @return \Timber\PostCollectionInterface
	 */
	public function services(?int $limit = -1) { return $this->relationship('services', $limit); }

	/**
	 * Gets projects connected to the current post.
	 *
	 * @return \Timber\PostCollectionInterface
	 */
	public function projects(?int $limit = -1) { return $this->relationship('projects', $limit); }

	/**
	 * Gets office pages connected to the current post.
	 *
	 * @return \Timber\PostCollectionInterface
	 */
	public function offices(?int $limit = -1) { return $this->relationship('offices', $limit); }

	/**
	 * Gets services connected to the current post.
	 *
	 * @return \Timber\PostCollectionInterface
	 */
	public function people(?int $limit = -1) { return $this->relationship('people', $limit); }

	/**
	 * Loads the people related to this item.
	 *
	 * @return \Timber\PostCollectionInterface
	 */
	public function internal_bylines() { return $this->relationship('internal_bylines'); }

	/**
	 * Gets services connected to the current post.
	 *
	 * @return \Timber\PostCollectionInterface
	 */
	public function related_news(?int $limit = -1) { return $this->relationship('related_news', $limit); }

	/**
	 * For news, events, and market insight reports, `service` `sector` `project` and `office`
	 * are collapsed to a single field.
	 *
	 * @return \Timber\PostCollectionInterface
	 */
	public function related_portfolio_items() { return $this->relationship('related_portfolio_items'); }

	private function relationship(string $field_name, ?int $limit = -1) {
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
