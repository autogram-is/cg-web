<?php

use Timber\Post;
use Timber\Timber;

/**
 * Class CGProject
 * 
 * Handles 'project' content only
 */
class CGProject extends CGPortfolio {
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Gets projects connected to the current post.
	 *
	 * @return \Timber\PostCollectionInterface
	 */
	public function projects(?int $limit = -1) {
		$field_prop = '_similar_projects';
		if (isset($this->$field_prop)) {
			return $this->$field_prop;
		}

		$services = $this->meta('services');
		$sectors = $this->meta('sectors');
		$offices = $this->meta('offices');

		$items = array_merge($services, $sectors, $offices);
		$ids = cg_get_projects_for_portfolio_items($items, $limit, [$this->ID]);

		if (!is_array($ids) || count($ids) === 0) {
			$this->$field_prop = null;
		} else {
			$this->$field_prop = Timber::get_posts($ids);
			return $this->$field_prop;	
		}
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
}