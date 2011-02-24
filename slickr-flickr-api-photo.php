<?php
class slickr_flickr_api_photo {

  var $url;
  var $width;
  var $height;
  var $orientation;
  var $title;
  var $description;
  var $date;
  var $link;
  var $original;

  function __construct($item) {
    $farmid = $item['farm']; 
    $serverid = $item['server'];
    $id = $item['id'];
    $secret = $item['secret'];
    $owner = $item['owner'];
    $this->url = "http://farm{$farmid}.static.flickr.com/{$serverid}/{$id}_{$secret}.jpg";
    $this->link = "http://www.flickr.com/photos/{$owner}/{$id}";
    $this->original= array_key_exists('url_o',$item) ? $item['url_o'] : '' ;
    $this->date = array_key_exists('date_taken',$item) ? $item['date_taken'] : '' ;
    $this->title = $this->cleanup($item['title']);
    $this->description = array_key_exists('description',$item) ? $this->cleanup($item['description']) : '' ;
    $height = array_key_exists('o_height',$item) ? $item['o_height'] : 0 ;
    $width = array_key_exists('o_width',$item) ? $item['o_width'] : 0 ;
    $this->orientation = $height > $width ? "portrait" : "landscape" ;
 
  }

  function get_url() { return $this->url; }
  function get_width() { return $this->width; }
  function get_height() { return $this->height; }
  function get_orientation() { return $this->orientation; }
  function get_title() { return $this->title; }
  function get_description() { return $this->description; }
  function get_date() { return $this->date; }
  function get_link() { return $this->link; }
  function get_original() { return $this->original; }

  /* Function that removes all quotes */
  function cleanup($s = null) {
    return $s?str_replace('"', '', str_replace("'", "`",$s)):false;
  }

  /* Function that returns the correctly sized photo URL. */
  function resize($size) {

    $url_array = explode('/', $this->url);
    $photo = array_pop($url_array); //strip the filename

    switch($size)  {
      case 'square': $suffix = '_s.';  break;
      case 'thumbnail': $suffix = '_t.';  break;
      case 'small': $suffix = '_m.';  break;
      case 'm640': $suffix = '_z.';  break;
      case 'large': $suffix = '_b.';  break;
      default:  $suffix = '.';  break; // Medium
      }

    $url_array[] =  preg_replace('/(_(s|t|m|b|z))?\./i', $suffix, $photo); //
    return implode('/', $url_array);
  }
  

}
?>