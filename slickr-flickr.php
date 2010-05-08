<?php
/*
Plugin Name: Slickr Flickr
Plugin URI: http://slickr-flickr.diywebmastery.com
Description: Displays tagged photos from Flickr in galleries and slideshows
Version: 1.5
Author: Russell Jamieson
Author URI: http://www.wordpresswise.com
*/

$slickr_flickr_options = array();

$slickr_flickr_defaults = array(
    'type' => 'gallery',
    'items' => '20',
    'delay' => '5',
    'captions' => 'on',
    'size' => 'medium',
    'orientation' => 'landscape',
    'start' => '1',
    'group' => 'n',
    'id' => '',
    'tag' => '',
    'tagmode' => 'all',
    'lightbox' => 'lightbox',
    'descriptions' => 'off',
    'flickr_link' => 'off',
    'link' => '',
    'attribution' => '',
    'photos_per_row' => '',
    'sort' => '',
    'direction' => ''
    );

function slickr_flickr_get_options () {
   global $slickr_flickr_options;
   global $slickr_flickr_defaults;
   if ($slickr_flickr_options.length >= 1) return $slickr_flickr_options;

   $flickr_options = array();
   $options = get_option("slickr_flickr_options");
   if (empty($options)) {
      $slickr_flickr_options = $slickr_flickr_defaults;
   } else {
     foreach ($options as $key => $option) {
       if (isset($options[$key]) && strpos($key,"flickr_")==0)  $flickr_options[substr($key,7)] = $option;
     }
     $slickr_flickr_options =  shortcode_atts( $slickr_flickr_defaults, $flickr_options);
   }
   return $slickr_flickr_options;
}

if (is_admin()) {
  require_once(dirname(__FILE__).'/slickr-flickr-admin.php');
} else  {
  require_once(dirname(__FILE__).'/slickr-flickr-public.php');
}
?>