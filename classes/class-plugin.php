<?php
class Slickr_Flickr_Plugin {

    const ACTIVATE_KEY = 'slickr_flickr_activation';

 	private static $path = SLICKR_FLICKR_PATH;
 	private static $slug = SLICKR_FLICKR_SLUG;
 	private static $version = SLICKR_FLICKR_VERSION;

    public static function get_path() {
		return self::$path;
	}

    public static function get_slug() {
		return self::$slug;
	}
	
	public static function get_version() {
		return self::$version;
	}

	public static function plugin_action_links ( $links, $file ) {
		if ( is_array($links) && (self::get_path() == $file )) {
			$settings_link = '<a href="' . admin_url( 'admin.php?page='.self::get_slug()) . '">Settings</a>';
			array_unshift( $links, $settings_link );
		}
		return $links;
	}

	public static function activate() { //called on plugin activation
    	update_option(self::ACTIVATE_KEY, true);
	}	

	public static function public_init() {
		$dir = dirname(__FILE__) . '/';
		require_once($dir . 'class-utils.php');
		require_once($dir . 'class-public.php');	
		Slickr_Flickr_Public::init();		
	}

	public static function admin_init() {
		$dir = dirname(__FILE__) . '/';	
		require_once($dir . 'class-utils.php');
		require_once($dir . 'class-tooltip.php');
		require_once($dir . 'class-admin.php');
		Slickr_Flickr_Admin::init();
		add_filter('plugin_action_links',array(__CLASS__, 'plugin_action_links'), 10, 2 );
 		if (get_option(self::ACTIVATE_KEY)) add_action('admin_init',array(__CLASS__, 'upgrade'));   		
	}
	
	public static function upgrade() { 
		Slickr_Flickr_Utils::upgrade_options(); //save any new options on plugin update
		delete_option(self::ACTIVATE_KEY); //delete key so upgrade runs only on activation		
		Slickr_Flickr_Utils::clear_cache(); //clear out the cache
	}		
	
}
