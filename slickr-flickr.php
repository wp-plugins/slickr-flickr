<?php
/*
Plugin Name: Slickr Flickr
Plugin URI: http://www.wordpresswise.com/slickr-flickr
Description: Display tagged photos from Flickr in galleries and slideshows
Version: 1.0
Author: Russell Jamieson
Author URI: http://www.wordpresswise.com
*/

$slickr_flickr_defaults = array(
    'type' => 'gallery',
    'items' => '20',
    'delay' => '5',
    'captions' => 'on',
    'size' => 'medium',
    'orientation' => 'landscape',
    'tag' => '',
    'id' => ''
    );

function slickr_flickr_get_options () {
global $slickr_flickr_defaults;
   $flickr_options = array();
   $options = get_option("slickr_flickr_options");
   if (empty($options)) {
     return $slickr_flickr_defaults;
   } else {
     foreach ($options as $key => $option) {
       if (isset($options[$key]) && strpos($key,"flickr_")==0)  $flickr_options[substr($key,7)] = $option;
     }
     return shortcode_atts( $slickr_flickr_defaults, $flickr_options);
   }
}

if (is_admin()) {
  require_once(dirname(__FILE__).'/slickr-flickr-admin.php');
} else  {
  require_once(dirname(__FILE__).'/slickr-flickr-public.php');
}
?>