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
	public function regions() {
		if (isset($this->regions)) {
			return $this->regions;
		}

		$this->regions = Timber::get_posts($this->meta('regions'));

		return $this->regions;
	}

	/**
	 * Gets office pages connected to the current post.
	 *
	 * @return \Timber\PostCollectionInterface
	 */
	public function offices() {
		if (isset($this->offices)) {
			return $this->offices;
		}

		$this->offices = Timber::get_posts($this->meta('offices'));

		return $this->offices;
	}

	/**
	 * Gets sector pages connected to the current post.
	 *
	 * @return \Timber\PostCollectionInterface
	 */
	public function sectors() {
		if (isset($this->sectors)) {
			return $this->sectors;
		}

		$this->sectors = Timber::get_posts($this->meta('sectors'));

		return $this->sectors;
	}

	/**
	 * Gets service pages connected to the current post.
	 *
	 * @return \Timber\PostCollectionInterface
	 */
	public function services() {
		if (isset($this->services)) {
			return $this->services;
		}

		$this->services = Timber::get_posts($this->meta('services'));

		return $this->services;
	}

	/**
	 * Gets projects connected to the current post.
	 *
	 * @return \Timber\PostCollectionInterface
	 */
	public function projects() {
		if (isset($this->projects)) {
			return $this->projects;
		}

		$this->projects = Timber::get_posts($this->meta('projects'));

		return $this->projects;
	}

	/**
	 * On content with related portfolio-items (events, posts, etc), returns related portfolio pages.
	 * On portfolio pages, returns the related news/events/posts.
	 *
	 * @return \Timber\PostCollectionInterface
	 */
	public function related() {
		if (isset($this->related_pages)) {
			return $this->related_pages;
		} else {
			$this->related_pages = Timber::get_posts($this->meta('related_pages'));
			return $this->related_pages;
		}

		if (isset($this->related_news)) {
			return $this->related_news;
		} else {
			$this->related_news = Timber::get_posts($this->meta('related_news'));
			return $this->related_news;
		}

		return array();
	}
}
