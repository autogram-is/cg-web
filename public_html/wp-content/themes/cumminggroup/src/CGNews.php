<?php

/**
 * Class CGNews
 * 
 * Handles 'post', 'report', and 'event' content
 */
class CGNews extends CGContent {
	public function __construct() {
		parent::__construct();
	}

  /**
	 * Loads the people related to this item.
	 *
	 * @return \Timber\PostCollectionInterface
	 */
	public function internal_authors() { return $this->_cache_relationship('internal_authors'); }

	/**
	 * Individuals mentioned in a news article, or attending an event.
	 *
	 * @return \Timber\PostCollectionInterface
	 */
	public function people(?int $limit = -1) { return parent::_cache_relationship('people', $limit); }

  /**
	 * For news, events, and market insight reports, `service` `sector` `project` and `office`
	 * are collapsed to a single field.
	 *
	 * @return \Timber\PostCollectionInterface
	 */
	public function related_portfolio_items() { return $this->_cache_relationship('related_portfolio_items'); }

  public function news_category() {
    $categories = get_the_terms($this->ID, 'news-category');
    if (is_array($categories)) return $categories[0];
    return [];
  }
}