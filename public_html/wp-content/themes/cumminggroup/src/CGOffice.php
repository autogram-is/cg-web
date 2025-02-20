<?php

use Timber\Timber;

/**
 * Class CGOffice
 * 
 * Applies to 'office' content, fills in nearby projects, news, and leadership
 */
class CGOffice extends CGPortfolio {
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Gets projects related to the current office; if not enough projects have been
	 * added to the office, projects from offices in the same region are used as backfill.
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
	 * Gets news and events related to the current office; if not enough projects have been
	 * added to the office, projects from offices in the same region are used as backfill.
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
