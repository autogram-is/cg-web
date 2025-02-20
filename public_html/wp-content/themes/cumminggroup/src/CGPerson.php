<?php

/**
 * Class CGPerson
 * 
 * Applies to 'person' posts
 */
class CGPerson extends CGContent {
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Sectors led by this person.
	 *
	 * @return \Timber\PostCollectionInterface
	 */
	public function sectors(?int $limit = -1) { return parent::_cache_relationship('sectors', $limit); }

	/**
	 * Services led by this person.
	 *
	 * @return \Timber\PostCollectionInterface
	 */
	public function services(?int $limit = -1) { return parent::_cache_relationship('services', $limit); }

	/**
	 * Offices led by this person.
	 *
	 * @return \Timber\PostCollectionInterface
	 */
	public function offices(?int $limit = -1) { return parent::_cache_relationship('offices', $limit); }

	/**
	 * Offices led by this person.
	 *
	 * @return \Timber\PostCollectionInterface
	 */
	public function authored_news(?int $limit = -1) { return parent::_cache_relationship('authored_news', $limit); }

	/**
	 * This flag determines whether we should redirect to a 404 if the user visits a bio page,
	 * and whether we should construct a link to the bio when displaying thumbnails, etc.
	 */
	function generate_bio() {
		$ex = boolval($this->meta('ex_employee'));
		$bio = boolval($this->meta('generate_bio_page'));
		return ($bio && !$ex);
	} 

	/**
	 * This flag determines whether contact information should be displayed when listing a person
	 * or building a bio page. Executive leadership, for example, will often have their bio details
	 * hidden.
	 */
	function show_contact() {
		$hide_contact = boolval($this->meta('hide_contact'));
		$ex = boolval($this->meta('ex_employee'));
		return (!$hide_contact && !$ex);
	}
}