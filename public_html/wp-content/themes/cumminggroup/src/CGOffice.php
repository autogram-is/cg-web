<?php

use Timber\Timber;

/**
 * Class CGOffice
 */
class CGOffice extends CGPost {
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Gets projects connected to the current post.
	 *
	 * @return \Timber\PostCollectionInterface
	 */
	public function nearby_projects(?int $limit = -1) {
		$field_prop = '_nearby_projects';
		if (isset($this->$field_prop)) {
		 	return $this->$field_prop;
		}

		$items = cg_projects_with_fallback($this->ID, $limit);

		if (!$items) {
		 	$this->$field_prop = null;
		} else {
	   	$this->$field_prop = Timber::get_posts($items);
			return $this->$field_prop;	
		}
	}

	/**
	 * Gets projects connected to the current post.
	 *
	 * @return \Timber\PostCollectionInterface
	 */
	public function nearby_news(?int $limit = -1) {
		$field_prop = '_nearby_news';
		if (isset($this->$field_prop)) {
		 	return $this->$field_prop;
		}

		$items = cg_news_with_fallback($this->ID, $limit);

		if (!$items) {
		 	$this->$field_prop = null;
		} else {
	   	$this->$field_prop = Timber::get_posts($items);
			return $this->$field_prop;	
		}
	}
}
