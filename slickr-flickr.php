<?php
/*
 * Plugin Name: Slickr Flickr
 * Plugin URI: http://www.slickrflickr.com
 * Description: Displays photos from Flickr in slideshows and galleries
 * Version: 2.0
 * Author: Russell Jamieson
 * Author URI: http://www.russelljamieson.com
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html  
 */
define('SLICKR_FLICKR_VERSION','2.0');
define('SLICKR_FLICKR_SLUG', 'slickr-flickr');
define('SLICKR_FLICKR_PATH', SLICKR_FLICKR_SLUG.'/slickr-flickr.php');
define('SLICKR_FLICKR_PLUGIN_URL', plugins_url(SLICKR_FLICKR_SLUG));
define('SLICKR_FLICKR_DOMAIN', 'SLICKR_FLICKR_DOMAIN');
define('SLICKR_FLICKR_HOME', 'http://www.slickrflickr.com');
require_once(dirname(__FILE__) . '/classes/class-plugin.php');
register_activation_hook(SLICKR_FLICKR_PATH, array('Slickr_Flickr_Plugin', 'activate'));
add_action ('init',  array('Slickr_Flickr_Plugin', is_admin() ? 'admin_init' : 'public_init'), 0);
?>