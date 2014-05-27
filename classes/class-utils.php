<?php
class Slickr_Flickr_Utils {
/** slickr-flickr shortcode parameters
 * @param id -> the Flickr ID of user
 * @param group -> set to y if the Flickr ID is the id of a group and not a user - default is n
 * @param use_key -> set to y to force use of API key - default is n
 * @param api_key -> 32 character alphanumeric API key
 * @param search -> photos, groups, friends, favorites, sets - default is photos
 * @param tag -> identifies what photos to select
 * @param tagmode -> set to ANY for fetching photos with different tags - default is ALL
 * @param set -> used in searching sets
 * @param license -> used to filter photos according to the license, 1-7
 * @param date_type -> (date) taken or upload 
 * @param date -> get photos for this date
 * @param before -> get photos on or before this date
 * @param after -> get photos on or after this date
 * @param text -> matching text in title, description or tags
 * @param cache -> cache method - db or fs (database or file system)
 * @param cache_expiry -> cache expiry in seconds default is 43200 - 12 hours
 * @param items -> maximum number photos to display in the gallery or slideshow - default is 20
 * @param type -> gallery, galleria or slideshow - default is gallery
 * @param captions -> whether captions are on or off - default is on
 * @param delay -> delay in seconds between each image in the slideshow - default is 5
 * @param transition -> slideshow transition - default is 0.5
 * @param start -> first slide in the slideshow - default is 1
 * @param autoplay -> on or off - default is on
 * @param pause -> on or off - default is off 
 * @param orientation -> landscape or portrait - default is landscape
 * @param size -> small, medium, m640, small, large, original - default is medium
 * @param width -> width of slideshow
 * @param height -> height of slideshow
 * @param bottom -> margin at the bottom of the slideshow/gallery/galleria
 * @param thumbnail_size -> square, thumbnail, small - default is square
 * @param thumbnail_scale -> scaling factor as a percentage - default is 100
 * @param thumbnail_captions -> on or off - default is off 
 * @param thumbnail_border -> alternative hightlight color for thumbnail
 * @param photos_per_row -> maximum number number of thumbnails in a gallery row
 * @param align -> left, right or center
 * @param border -> whether slideshow border is on or off - default is off
 * @param descriptions -> show descriptions beneath title caption - default is off
 * @param flickr_link -> include a link to the photo on Flickr on the lightbox - default is off
 * @param link -> url to visit on clicking slideshow
 * @param target -> name of window for showing the slideshow link url - default is the same window: _self
 * @param attribution -> credit the photographer
 * @param nav -> galleria navigation - none, above, below (if not supplied navigation is both above and below)
 * @param sort -> sort photos by date, title or description
 * @param direction -> sort ascending or descending 
 * @param per_page -> photos per page 
 * @param page -> page number  
 * @param restrict -> filter results based on orientation  
 * @param scripts_in_footer -> true or false - default is false 
*/

	const FLICKR_CACHE_TABLE = 'flickr_cache'; 
    const FLICKR_CACHE_FOLDER = 'flickr-cache';
	const SLICKR_FLICKR_OPTIONS = 'slickr_flickr_options';

	protected static $options  = array();
	protected static $defaults = array(
	    'id' => '',
	    'group' => 'n',
	    'use_key' => '',
	    'api_key' => '',
	    'use_rss' => '',  
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
	    'text' => '',
	    'cache' => 'on',
	    'cache_expiry' => 43200,
	    'items' => '20',
	    'type' => 'gallery',
	    'captions' => 'on',
	    'lightbox' => 'sf-lightbox',
	    'galleria'=> 'galleria-latest',
	    'galleria_theme'=> 'classic',
	    'galleria_theme_loading'=> 'static',
    	'galleria_themes_folder'=> 'galleria/themes',
    	'galleria_options' => '',
    	'options' => '',
    	'delay' => '5',
    	'transition' => '0.5',
    	'start' => '1',
    	'autoplay' => 'on',
    	'pause' => '',
    	'orientation' => 'landscape',
    	'size' => 'medium',
    	'bottom' => '',
    	'thumbnail_size' => '',
    	'thumbnail_scale' => '',
    	'thumbnail_captions' => '',
    	'thumbnail_border' => '',
    	'photos_per_row' => '',
    	'align' => '',
    	'border' => '',
    	'descriptions' => '',
    	'ptags' => '',
    	'flickr_link' => '',
    	'flickr_link_title' => 'Click to see the photo on Flickr',
    	'flickr_link_target' => '',
    	'link' => '',
    	'target' => '_self',
    	'attribution' => '',
    	'nav' => '',
    	'sort' => '',
    	'direction' => '',
    	'per_page' => 50,
    	'page' => 1,
    	'pagination'=> '',
	    'element_id' => '',
    	'restrict' => '',
    	'scripts_in_footer' => false
    );
	
	static function save_options($new_options) {
		$new_options = shortcode_atts( self::$defaults, array_merge(self::get_options(false), $new_options));
		$updated = update_option(self::SLICKR_FLICKR_OPTIONS,$new_options);
		if ($updated) self::get_options(false);
		return $updated;
	}

	static function get_defaults() {
		return self::$defaults;
	}

	static function get_options ($cache = true) {
	   if ($cache && (count(self::$options) > 0)) return self::$options;
	
	   $flickr_options = array();
	   $options = get_option(self::SLICKR_FLICKR_OPTIONS);
	   if (empty($options)) {
	      self::$options = self::$defaults;
	   } else {
	     foreach ($options as $key => $option) {
	    	if ('flickr_' == substr($key,0,7)) 
	       		$flickr_options[substr($key,7)] = $option;
	        else
	        	$flickr_options[$key] = $option;
	     }
	     self::$options = shortcode_atts( self::$defaults, $flickr_options);
	   }
	   return self::$options;
	}

	static function get_option($option_name) {
    	$options = self::get_options();
    	if ($option_name && $options && array_key_exists($option_name,$options))
        	return $options[$option_name];
    	else
        	return false;
	}

	static function get_default($option_name) {
    	if ($option_name && array_key_exists($option_name, self::$defaults))
        	return  self::$defaults[$option_name];
    	else
        	return false;
	}

	static function scripts_in_footer() {
    	return self::get_option('scripts_in_footer');
	}

	static function optimise_options() {
    	global $wpdb, $table_prefix;
    	$prefix = $table_prefix ? $table_prefix : "wp_";
    	$sql = "OPTIMIZE TABLE ".$prefix."options ";
    	try {
    		$wpdb->query($sql); //ignore error if user does not have permission to optimise table
		}
		catch (Exception $e) {
		}
	}

	static function clear_flickr_cache() {
    	global $wpdb, $table_prefix;
    	$prefix = $table_prefix ? $table_prefix : 'wp_';
    	$sql = "TRUNCATE TABLE ".$prefix.self::FLICKR_CACHE_TABLE;
    	try {
    		$wpdb->query($sql); //ignore error if user does not have permission to truncate table
		}
		catch (Exception $e) {
		}
	}

	static function clear_transient_flickr_cache() {
    	global $wpdb, $table_prefix;
    	$prefix = $table_prefix ? $table_prefix : "wp_";
    	$sql = "DELETE FROM ".$prefix."options WHERE option_name LIKE '_transient_flickr_%' ";
    	$wpdb->query($sql);
    	$sql = "DELETE FROM ".$prefix."options WHERE option_name LIKE '_transient_timeout_flickr_%' ";
    	$wpdb->query($sql);
	}

	static function clear_rss_cache_transient() {
    	global $wpdb, $table_prefix;
    	$prefix = $table_prefix ? $table_prefix : "wp_";
    	$sql = "DELETE FROM ".$prefix."options WHERE option_name LIKE '_transient_feed_%' or option_name LIKE '_transient_rss_%' or option_name LIKE '_transient_timeout_%'";
    	$wpdb->query($sql);
	}
	
	static function clear_rss_cache() {
    	global $wpdb, $table_prefix;
    	$prefix = $table_prefix ? $table_prefix : "wp_";
    	$sql = "DELETE FROM ".$prefix."options WHERE option_name LIKE 'rss_%' and LENGTH(option_name) IN (36, 39)";
    	$wpdb->query($sql);
	}

	static function clear_cache() {
    	self::clear_rss_cache();
    	self::clear_rss_cache_transient();
    	self::clear_transient_flickr_cache();
    	self::optimise_options();
		self::clear_flickr_cache();    	
	}

	static function json_encode($params) {	
   		//fix numerics and booleans
		$pat = '/(\")([0-9]+)(\")/';	
		$rep = '\\2';
		return str_replace (array('"false"','"true"'), array('false','true'), 
			preg_replace($pat, $rep, json_encode($params)));
   } 	

	static function upgrade_options() {
		if (($lightbox = self::get_option('lightbox')) 
		&& (substr($lightbox,0,2) =='sf')) 
			$lightbox = self::get_default('lightbox');
		self::save_options(array('lightbox' => $lightbox));
	}
	
}
