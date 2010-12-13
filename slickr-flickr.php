<?php
/*
Plugin Name: Slickr Flickr
Plugin URI: http://www.slickr-flickr.com
Description: Displays tagged photos from Flickr in slideshows and galleries
Version: 1.18
Author: Russell Jamieson
Author URI: http://www.wordpresswise.com
*/
if (!defined('SLICKR_FLICKR_VERSION')) define('SLICKR_FLICKR_VERSION','1.18');
if (!defined('SLICKR_FLICKR_FOLDER')) define('SLICKR_FLICKR_FOLDER', 'slickr-flickr');
if (!defined('SLICKR_FLICKR_HOME')) define('SLICKR_FLICKR_HOME', 'http://wordpress.org/extend/plugins/'.SLICKR_FLICKR_FOLDER.'/');
if (!defined('SLICKR_FLICKR_PATH')) define('SLICKR_FLICKR_PATH', SLICKR_FLICKR_FOLDER.'/slickr-flickr.php');
if (!defined('SLICKR_FLICKR_PLUGIN_URL')) define ('SLICKR_FLICKR_PLUGIN_URL',WP_PLUGIN_URL . '/' . SLICKR_FLICKR_FOLDER);
if (!defined('SLICKR_FLICKR_UPGRADER')) define('SLICKR_FLICKR_UPGRADER', 'http://www.diywebmastery.com/slickrflickrpro/slickr-flickr-version.php');

$slickr_flickr_options = array();
$slickr_flickr_pro_options = array();
$slickr_flickr_pro_defaults = array('licence' => '',);
$slickr_flickr_defaults = array(
    'id' => '',
    'group' => 'n',
    'use_key' => '',
    'api_key' => '',
    'search' => 'photos',
    'tag' => '',
    'tagmode' => '',
    'type' => 'gallery',
    'set' => '',
    'cache' => 'on',
    'lightbox' => 'sf-lbox-manual',
    'items' => '20',
    'delay' => '5',
    'start' => '1',
    'autoplay' => 'on',
    'width' => '',
    'height' => '',
    'border' => 'off',
    'captions' => 'on',
    'descriptions' => 'off',
    'flickr_link' => 'off',
    'pause' => 'off',
    'size' => 'medium',
    'orientation' => 'landscape',
    'thumbnail_size' => 'square',
    'thumbnail_scale' => '100',
    'link' => '',
    'attribution' => '',
    'photos_per_row' => '',
    'sort' => '',
    'direction' => ''
    );

function slickr_flickr_get_options ($cache = true) {
   global $slickr_flickr_options;
   global $slickr_flickr_defaults;
   if ($cache && (count($slickr_flickr_options) > 0)) return $slickr_flickr_options;

   $flickr_options = array();
   $options = get_option("slickr_flickr_options");
   if (empty($options)) {
      $slickr_flickr_options = $slickr_flickr_defaults;
   } else {
     foreach ($options as $key => $option) {
       if (isset($options[$key]) && strpos($key,"flickr_")==0)  $flickr_options[substr($key,7)] = $option;
     }
     switch ($flickr_options['lightbox']) {
          case "lightbox-slideshow": $flickr_options['lightbox'] = 'sf-lbox-auto'; break;
          case "lightbox": $flickr_options['lightbox'] = 'sf-lbox-manual'; break;
     }
     $slickr_flickr_options = shortcode_atts( $slickr_flickr_defaults, $flickr_options);
   }
   return $slickr_flickr_options;
}

function slickr_flickr_pro_get_options ($cache = true) {
   global $slickr_flickr_pro_options;
   global $slickr_flickr_pro_defaults;
   if ($cache && (count($slickr_flickr_pro_options) > 0)) return $slickr_flickr_pro_options;

   $options = get_option("slickr_flickr_pro_options");
   if (empty($options)) {
      $slickr_flickr_pro_options = $slickr_flickr_pro_defaults;
   } else  {
     $slickr_options = array();
     foreach ($options as $key => $option) {
       if (isset($options[$key]) && strpos($key,"slickr_")==0)  $slickr_options[substr($key,7)] = $option;
     }
     $slickr_flickr_pro_options = shortcode_atts( $slickr_flickr_pro_defaults, $slickr_options);
   }
   return $slickr_flickr_pro_options;
}

function slickr_flickr_get_licence(){
    global $slickr_flickr_pro_options;
    $slickr_flickr_pro_options = slickr_flickr_pro_get_options();
    return $slickr_flickr_pro_options['licence'];
}

function slickr_flickr_get_upgrader($cache = true){
        global $wpdb;
        global $slickr_flickr_pro_options;
        $slickr_flickr_pro_options = slickr_flickr_pro_get_options();
        if (empty($slickr_flickr_pro_options['upgrader']) || ($cache == false))
            $slickr_flickr_pro_options['upgrader'] = SLICKR_FLICKR_UPGRADER. sprintf("?of=SlickrFlickr&key=%s&v=%s&wp=%s&php=%s&mysql=%s",
                urlencode(slickr_flickr_get_licence()), urlencode(SLICKR_FLICKR_VERSION), urlencode(get_bloginfo("version")),
                urlencode(phpversion()), urlencode($wpdb->db_version()));

        return  $slickr_flickr_pro_options['upgrader'];
}

function slickr_flickr_remote_call($action){
        $options = array('method' => 'POST', 'timeout' => 3);
        $options['headers'] = array(
            'Content-Type' => 'application/x-www-form-urlencoded; charset=' . get_option('blog_charset'),
            'User-Agent' => 'WordPress/' . get_bloginfo("version"),
            'Referer' => get_bloginfo("url")
        );
        $raw_response = wp_remote_request(slickr_flickr_get_upgrader(). '&act='.$action  , $options);
        if ( is_wp_error( $raw_response ) || 200 != $raw_response['response']['code']){
            return false;
        } else {
            return $raw_response;
        }
}
function slickr_flickr_get_version_info($cache=true){
        $raw_response = $cache ? get_transient("slickr_flickr_version_info") : "";
        if(!$raw_response){
            $raw_response = slickr_flickr_remote_call('version');
            set_transient("slickr_flickr_version_info", $raw_response, 86400); //cache for 24 hours
        }
        if (!$raw_response) return array("valid_key" => "0", "version" => "", "package" => "", "notice" => "");

        $sf_array = explode("||", $raw_response['body']);
        return array("valid_key" => $sf_array[0], "version" => $sf_array[1], "package" => $sf_array[2], "notice" => $sf_array[3]);
}

function slickr_flickr_check_validity(){
    $version_info = slickr_flickr_get_version_info();
    return $version_info['valid_key'];
}

function slickr_flickr_clear_rss_cache() {
    global $wpdb, $table_prefix;
    $prefix = $table_prefix ? $table_prefix : "wp_";
    $sql = "DELETE FROM ".$prefix."options WHERE option_name LIKE 'rss_%' and LENGTH(option_name) IN (36, 39)";
    $wpdb->query($sql);
}

function slickr_flickr_clear_rss_cache_transient() {
    global $wpdb, $table_prefix;
    $prefix = $table_prefix ? $table_prefix : "wp_";
    $sql = "DELETE FROM ".$prefix."options WHERE option_name LIKE '_transient_feed_%' or option_name LIKE '_transient_rss_%' or option_name LIKE '_transient_timeout_%'";
    $wpdb->query($sql);
}

function slickr_flickr_clear_cache() {
    slickr_flickr_clear_rss_cache();
    slickr_flickr_clear_rss_cache_transient();
}

if (is_admin()) {
  require_once(dirname(__FILE__).'/slickr-flickr-admin.php');
} else  {
  require_once(dirname(__FILE__).'/slickr-flickr-public.php');
}
?>