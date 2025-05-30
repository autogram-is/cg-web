<?php

use Timber\Timber;

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
	 * Overrides the default hero treatment to ensure news content types
	 * ('post', 'event', and 'report') don't use top-of-page hero images.
	 */
	public function hero() {
		return array("type" => "hide");
	}

	/**
	 * If no thumbnail image is available for a post, construct a text placeholder.
	 * Uses fallback logic with the `placeholder_text` and `placeholder_bg` fields
	 * to allow post, news category, and post-type level placeholder overrides.
	 */
	public function placeholder() {
		// Global defaults for the post type.
		$placeholder = $this->_placeholder_defaults();
		if (!$placeholder) $placeholder = [];

		// Override existing settings with post-specific placeholder text.
		$text = $this->meta('placeholder_text');
		$bg = $this->meta('placeholder_bg');
		if ($text) {
			$placeholder['text'] = $text;
		}
		if ($bg) {
			$placeholder['bg'] = $bg;
		}

		// If there's no post-specific placeholder information, check the news_category
		// term and use its placeholder defaults.
		if (!$text || !$bg) {
			$news_category = $this->news_category();
			if ($news_category) {
				$text = $news_category->meta('placeholder_text');
				if ($text) $placeholder['text'] = $text;
			}
			if ($news_category) {
				$bg = $news_category->meta('placeholder_bg');
				if ($bg) $placeholder['bg'] = $bg;
			}
		}

		// If an image is set, load it.
		if ($placeholder['image'] ?? false) {
			$placeholder['image'] = Timber::get_image($placeholder['image']);
		}

		// If either text or image is set, return a value.
		if (($placeholder['text'] ?? false) || ($placeholder['image'] ?? false)) {
			return $placeholder;
		}
	}

	public function news_category() {
    $categories = get_the_terms($this->ID, 'news-category');
    if (is_array($categories) && $categories[0]) return Timber::get_term($categories[0]);
    return NULL;
  }

  /**
	 * Cumming Group team members who authored this post, if applicable.
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
	 * are collapsed to a single field. We retrieve and instantiate them here, but also have type-
	 * specific convenience functions that simply filter this list before returning.
	 *
	 * @return \Timber\PostCollectionInterface
	 */
	public function related_portfolio_items() { return $this->_cache_relationship('related_portfolio_items'); }

	public function sectors() {
		$posts = $this->related_portfolio_items();
		if ($posts) {
			return array_filter(iterator_to_array($posts), function($item) { return $item->post_type === 'sector'; });
		}
	}

	public function services() {
		$posts = $this->related_portfolio_items();
		if ($posts) {
			return array_filter(iterator_to_array($posts), function($item) { return $item->post_type === 'service'; });
		}
	}

	public function offices() {
		$posts = $this->related_portfolio_items();
		if ($posts) {
			return array_filter(iterator_to_array($posts), function($item) { return $item->post_type === 'office'; });
		}
	}

	public function projects() {
		$posts = $this->related_portfolio_items();
		if ($posts) {
			return array_filter(iterator_to_array($posts), function($item) { return $item->post_type === 'project'; });
		}
	}
}