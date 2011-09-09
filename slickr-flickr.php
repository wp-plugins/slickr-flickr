<?php
/*
Plugin Name: Slickr Flickr
Plugin URI: http://www.slickrflickr.com
Description: Displays photos from Flickr in slideshows and galleries
Version: 1.34
Author: Russell Jamieson
Author URI: http://www.russelljamieson.com

Copyright 2011 Russell Jamieson (russell.jamieson@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
if (!defined('SLICKR_FLICKR_VERSION')) define('SLICKR_FLICKR_VERSION','1.34');
if (!defined('SLICKR_FLICKR')) define('SLICKR_FLICKR', 'slickr-flickr');
if (!defined('SLICKR_FLICKR_FOLDER')) define('SLICKR_FLICKR_FOLDER', SLICKR_FLICKR);
if (!defined('SLICKR_FLICKR_HOME')) define('SLICKR_FLICKR_HOME', 'http://wordpress.org/extend/plugins/'.SLICKR_FLICKR_FOLDER.'/');
if (!defined('SLICKR_FLICKR_PATH')) define('SLICKR_FLICKR_PATH', SLICKR_FLICKR_FOLDER.'/slickr-flickr.php');
if (!defined('SLICKR_FLICKR_PLUGIN_URL')) define ('SLICKR_FLICKR_PLUGIN_URL',slickr_flickr_fix_protocol(WP_PLUGIN_URL) . '/' . SLICKR_FLICKR_FOLDER);
if (!defined('SLICKR_FLICKR_UPGRADER')) define('SLICKR_FLICKR_UPGRADER', 'http://www.diywebmastery.com/slickrflickrpro/slickr-flickr-version.php');

$slickr_flickr_options = array();
$slickr_flickr_pro_options = array();
$slickr_flickr_pro_defaults = array('licence' => '', 'consumer_secret' =>'', 'token' => '', 'token_secret' => '');
$slickr_flickr_defaults = array(
    'id' => '',
    'group' => 'n',
    'use_key' => '',
    'api_key' => '',
    'search' => 'photos',
    'tag' => '',
    'tagmode' => '',
    'set' => '',
    'gallery' => '',
    'license' => '',
    'date_type' => '',
    'date' => '',
    'before' => '',
    'after' => '',
    'cache' => 'on',
    'items' => '20',
    'type' => 'gallery',
    'captions' => 'on',
    'lightbox' => 'sf-lbox-manual',
    'galleria'=> 'galleria-1.0',
    'galleria_theme'=> 'classic',
    'options' => '',
    'delay' => '5',
    'transition' => '0.5',
    'start' => '1',
    'autoplay' => 'on',
    'pause' => '',
    'orientation' => 'landscape',
    'size' => 'medium',
    'width' => '',
    'height' => '',
    'bottom' => '',
    'thumbnail_size' => '',
    'thumbnail_scale' => '',
    'thumbnail_captions' => '',
    'thumbnail_border' => '',
    'photos_per_row' => '',
    'align' => '',
    'border' => '',
    'descriptions' => '',
    'flickr_link' => '',
    'link' => '',
    'target' => '_self',
    'attribution' => '',
    'nav' => '',
    'sort' => '',
    'direction' => '',
    'per_page' => 50,
    'page' => 1,
    'restrict' => '',
    'random' => '',
    'cache_expiry' => 43200,
    'private' => '',
    'scripts_in_footer' => false
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
     if (array_key_exists('lightbox',$flickr_options))
     	switch ($flickr_options['lightbox']) {
          	case "lightbox-slideshow": $flickr_options['lightbox'] = 'sf-lbox-auto'; break;
          	case "lightbox": $flickr_options['lightbox'] = 'sf-lbox-manual'; break;
     	}
     if (array_key_exists('galleria',$flickr_options))
     	switch ($flickr_options['galleria']) {
     		case "":
          	case "galleria_10": $flickr_options['galleria'] = 'galleria-1.0'; break;
          	case "galleria_12": $flickr_options['galleria'] = 'galleria-1.2'; break;
     	}
     $slickr_flickr_options = shortcode_atts( $slickr_flickr_defaults, $flickr_options);
   }
   return $slickr_flickr_options;
}

function slickr_flickr_get_option($option_name) {
    $options = slickr_flickr_get_options();
    if ($option_name && $options && array_key_exists($option_name,$options))
        return $options[$option_name];
    else
        return false;
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

function slickr_flickr_scripts_in_footer() {
    $options = slickr_flickr_get_options();
    return $options['scripts_in_footer'];
}

function slickr_flickr_get_licence(){
    $options = slickr_flickr_pro_get_options();
    return $options['licence'];
}

function slickr_flickr_append_secrets(&$params, $keys = array('consumer_secret','token','token_secret')) {
    $pro_options = slickr_flickr_pro_get_options();
    foreach ($keys as $key) 
    	if (array_key_exists($key,$pro_options)) 
    		$params[$key] = $pro_options[$key];
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
    $slickr_flickr_version_info = $cache ? get_transient("slickr_flickr_version_info") : false;
    if (!$slickr_flickr_version_info) {
        $raw_response = slickr_flickr_remote_call('version');
        $slickr_flickr_version = (is_array($raw_response) && array_key_exists('body',$raw_response)) ? explode("||", $raw_response['body']) : array();
        if (count($slickr_flickr_version) >= 3) {
            $valid_key = $slickr_flickr_version[0];
            $version = $slickr_flickr_version[1];            
            $package = $slickr_flickr_version[2];  
            $notice = $slickr_flickr_version[3];              
    		$current= version_compare(SLICKR_FLICKR_VERSION, $version, '<') ? -1 : 1; 
			}
		else {
			$valid_key = false; $version = ""; $package =  "Unknown";  $notice = "Unable to check for new version. Please try again."; $current = 0;
		}
		$slickr_flickr_version_info = compact("valid_key", "version", "package", "notice", "current");
        set_transient("slickr_flickr_version_info", $slickr_flickr_version_info, 86400); //cache for 24 hours
	}
	return $slickr_flickr_version_info;
}


function slickr_flickr_check_validity(){
    if (slickr_flickr_get_licence()) {
    	$version_info = slickr_flickr_get_version_info();
    	if (array_key_exists('valid_key',$version_info))
    		return $version_info['valid_key'];
		else { //previous version cache
			delete_transient("slickr_flickr_version_info"); //clear cache entry
		    $version_info = slickr_flickr_get_version_info(false); //rebuild cache
    		return $version_info['valid_key'];
			}
    	}
    else 
    	return false;
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

function slickr_flickr_fix_protocol($url) {
   return is_ssl() ? str_replace('http://', 'https://', $url) : $url;
}

require_once(dirname(__FILE__).'/slickr-flickr-'.(is_admin()?'admin':'public').'.php');
?>