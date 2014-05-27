<?php
require_once(dirname(__FILE__) . '/class-feed.php');
class Slickr_Flickr_Fetcher {
	protected $id;
	protected $pages;
	protected $message;
	protected $feed;
		
	function get_message() { return $this->message;}
	function get_pages() { return $this->pages;}

	function __construct($id) {  
		$this->message = '';
		$this->pages = 0;
		$this->id = $id;
	}

	function fetch_photos($params) {  
		$this->feed = new Slickr_Flickr_Feed($params);
		$page=$params['page'];
		$photos = $this->feed->fetch_photos($page);
		$this->pages = $this->feed->get_pages();
		if ((count($photos) == 0) || $this->feed->is_error()) {
  	  		$this->message = $this->feed->get_message();
			return false;
		} else {
  	  		return $photos; //return array of photos
		}
	}
}