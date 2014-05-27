<?php
class Slickr_Flickr_Admin {
    const CODE = 'slickr_flickr';
    private static $screen_id;
    private static $keys;
	private static $tooltips;    
	private static $tips = array(
			'flickr_id' => array('heading' => 'Flickr ID', 'tip' => 'The Flickr ID is required for you to be able to access your photos.<br/> If you supply it here, the plugin will remember it so you do not need to supply it for every gallery and every slideshow.<br/>You are still able to supply a Flickr ID for an individual slideshow perhaps where you want to display photos from a friends Flickr account.<br/>A Flickr ID looks something like this : 12345678@N00 and you can find your Flickr ID by entering the URL of your Flickr photostream at http://idgettr.com'),
			'flickr_group' => array('heading' => 'Flickr User or Group', 'tip' => 'If you leave this blank then the plugin will assume your default Flickr ID is a user ID.<br/>If you make a selection here, the plugin will remember it so you do not need to supply it for each photo display.<br/>You are still able to override the type of Flickr Id by specifying it in the post.<br/>For example [slickr-flickr tag="bahamas" id="12345678@N01" group="y"] looks up photos assuming that 12345678@N01 is the Flickr ID of a group.'),
			'flickr_api_key' => array('heading' => 'Flickr API Key', 'tip' => 'The Flickr API Key is used if you want to be able to get more than 20 photos at a time.<br/>If you supply it here, the plugin will remember it so you do not need to supply it for every gallery and every slideshow.<br/>A Flickr API key looks something like this : 5aa7aax73kljlkffkf2348904582b9cc and you can find your Flickr API Key by logging in to Flickr and then visiting http://www.flickr.com/services/api/keys/'),
			'flickr_items' => array('heading' => 'Number Of Photos', 'tip' => 'If you supply a number it here, the plugin will remember it so you do not need to supply it for every gallery and every slideshow.<br/>You are still able to supply the number of photos to display for individual slideshow by specifying it in the post. For example, to display up to ten photos tagged with bahamas: <br/>[slickr-flickr tag="bahamas" items="10"].'),
			'flickr_type' => array('heading' => 'Type of Display', 'tip' => 'If you make a selection here, the plugin will remember it so you do not need to supply it for each photo display. You are still able to supply the type of display by specifying it in the post. For example, to display a gallery even if you have set the default display type as slideshow:<br/>[slickr-flickr tag="bahamas" type="gallery"].'),
			'flickr_size' => array('heading' => 'Photo Size', 'tip' => 'If you make a selection here, the plugin will remember it so you do not need to supply it for each photo display. You are still able to supply the photo size by specifying it in the post. For example, to display medium size photos even if you have set the default size as m640:<br/>[slickr-flickr tag="bahamas" size="medium"].'),
			'flickr_captions' => array('heading' => 'Captions', 'tip' => 'If you make a selection here, the plugin will remember it so you do not need to supply it for each display. You are still able to control captions on individual slideshows by specifying it in the post. For example, to switch off captions for that slideshow even if you have set the default captioning here to be on.'),
			'flickr_autoplay' => array('heading' => 'Autoplay', 'tip' => 'If you make a selection here, the plugin will remember it so you do not need to supply it for each display. You are still able to control autoplay on individual displays by specifying it in the post. For example [slickr-flickr tag="bahamas" autoplay="off"] switches off autoplay for that slideshow even if you have set the default autoplay here to be on.'),
			'flickr_delay' => array('heading' => 'Delay Between Images', 'tip' => 'If you supply a number it here, the plugin will remember it so you do not need to supply it for every slideshow/gallery/galleria. You are still able to supply a different delay for individual display of images by specifying it in the post.<br/>For example,  to  display a slideshow with a ten second delay between images:<br/> [slickr-flickr tag="bahamas" type="slideshow" delay="10"].'),
			'flickr_transition' => array('heading' => 'Slideshow Transition', 'tip' => 'If you leave this blank then the plugin will take half a second to fade one slide into the next.<br/>If you supply a number it here, the plugin will remember it so you do not need to supply it for every slideshow.<br/>You are still able to supply a different delay for individual slideshow by specifying it in the post. <br/>For example, to display a slideshow with a 2 second fade transition between slides:<br/>[slickr-flickr tag="bahamas" transition="2"]'),
			'flickr_lightbox' => array('heading' => 'Lightbox', 'tip' => 'By default the plugin will use the standard LightBox.<br/>If you select LightBox slideshow then when a photo is clicked the overlaid lightbox will automatically play the slideshow.<br/>If you select ThickBox then it will use the standard WordPress lightbox plugin which is pre-installed with Wordpress.<br/>If you select one of the other lightboxes then you need to install that lightbox plugin independently from Slickr Flickr.'),
			'flickr_galleria' => array('heading' => 'Galleria Version', 'tip' => 'Choose which version of the galleria you want to use. We recommend you use the latest version of the galleria as this has the most features.'),
			'flickr_galleria_theme' => array('heading' => 'Galleria Theme', 'tip' => 'The default theme is "classic". Only change this value is you have purchased a premium Galleria theme or written one and located it under the themes folder specified below.'),
			'flickr_galleria_theme_loading' => array('heading' => 'Galleria Theme Loading', 'tip' => 'Choose static if you are using the same Galleria theme thoughout the site otherwise choose dynamic if you are using different themes on different pages.'),
			'flickr_galleria_themes_folder' => array('heading' => 'Galleria Themes Folder', 'tip' => 'The recommended location is "galleria/themes". Prior to WordPress 3.3 you could put the themes under wp-content/plugins/slickr-flickr/galleria but this is no longer possible since WordPress now wipes the plugin folder of any extra files that are not part of the plugin.'),
			'flickr_galleria_options' => array('heading' => 'Galleria Options', 'tip' => 'Here you can set default options for the galleria 1.2 and later versions.<br/>The correct format is like CSS with colons to separate the parameter name from the value and semi-colons to separate each pair: param1:value1;param2:value2;<br/>For example, transition:fadeslide;transitionSpeed:1000; sets a one second fade and slide transition.'),
			'flickr_scripts_in_footer' => array('heading' => 'Load JavaScript In Footer', 'tip' => 'This option allows you to load Javascript in the footer instead of the header. This can be useful as it may reduce potential jQuery conflicts with other plugins.<br/>However, it will not work for all WordPress themes, specifically those that do not support loading of scripts in the footer using standard WordPress hooks and filters.'),
			);

    private static $skins = array('borderlessdark' => 'Borderless Dark', 'borderlessdark3d' => 'Borderless Dark 3D', 
    	'borderlesslight' => 'Borderless Light', 'borderlesslight3d' => 'Borderless Light 3D', 
    	'carousel'=> 'Carousel', 'darkskin' => 'Dark', 'defaultskin' => 'Default',
    	'fullwidth' => 'Full Width', 'fullwidthdark' => 'Full Width Dark', 'glass' => 'Glass', 
    	'lightskin' => 'Light', 'minimal' => 'Minimal', 'noskin' => 'None', 'preview' => 'Preview');

	private static $galleria_versions = array(
		'galleria-latest' => 'Galleria latest version',
		'galleria-original' => 'Galleria original version',
		'galleria-none' => 'Galleria not required so do not load the script'
	);

    private static $lightboxes  = array(
		'sf-lightbox' => 'Default LightBox (pre-installed with this plugin)',
		'thickbox' => 'Thickbox (pre-installed with Wordpress)',
		'evolution' => 'Evolution LightBox for Wordpress (requires separate installation)',
		'fancybox' => 'FancyBox (requires separate installation)',
		'colorbox' => 'LightBox Plus for Wordpress (requires separate installation)',
		'responsive' => 'Responsive LightBox (requires separate installation)',
		'shutter' => 'Shutter Reloaded for Wordpress (requires separate installation)',
		'slimbox'=> 'SlimBox for Wordpress (requires separate installation)',
		'lightbox' => 'Some Other LightBox(requires separate installation)'
    );

    private static $sizes = array('medium' => 'Medium (500px by 375px)',
			'm640' => 'Medium 640 (640px by 480px)',
			'm800' => 'Medium 800 (800px by 600px)',
			'large' => 'Large (1024px by 768px)',
			'original' => 'Original Size (typically 1920px by 1440px)');

    private static $types = array('gallery' => 'a gallery of thumbnail images',
			'galleria' => 'a galleria slideshow with thumbnail images below',
			'slideshow' => 'a slideshow of medium size images');

    private static function get_screen_id(){
		return self::$screen_id;
	}

	static function init() {
		add_action('admin_menu', array(__CLASS__, 'admin_menu')); 
	}

	static function admin_menu() {
		self::$screen_id = add_options_page('Slickr Flickr', 'Slickr Flickr', 'manage_options', Slickr_Flickr_Plugin::get_slug(), array(__CLASS__, 'options_panel'));
		add_action('load-'.self::$screen_id, array(__CLASS__, 'load_page'));
	}
 
	static function screen_layout_columns($columns, $screen) {
		if (!defined( 'WP_NETWORK_ADMIN' ) && !defined( 'WP_USER_ADMIN' )) {
			if ($screen == self::$screen_id) {
				$columns[self::$screen_id] = 2;
			}
		}
		return $columns;
	}

	static function select_options($selected, $array) {
		$options = '';
		foreach ($array as $val => $name) 
			$options .= sprintf('<option %1$svalue="%2$s">%3$s</option>', 
				selected($val, $selected, false), $val, $name);
		return $options;
	}

	static function radio_options($fld, $selected, $array = array('on','off')) {
		$options = '';
		foreach ($array as $val) 
			$options .= sprintf('<input type="radio" id="%1$s" name="%2$s" %3$s value="%4$s" />&nbsp;%5$s&nbsp;&nbsp;',
				$fld, $fld, checked($val, $selected, false), $val, $val); 
		return sprintf('<fieldset>%1$s</fieldset>',$options); 
	}

	static function load_page() {
		$message ='';
 		if (isset($_POST['options_update'])) $message = self::save();
		if (isset($_POST['cache'])) $message = self::clear_cache();  		
		add_filter('screen_layout_columns', array(__CLASS__, 'screen_layout_columns'), 10, 2);
		$options = Slickr_Flickr_Utils::get_options();
		$callback_params = array ('options' => $options, 'message' => $message);
		add_meta_box('slickr-flickr-intro', __('Intro',SLICKR_FLICKR_DOMAIN), array(__CLASS__, 'intro_panel'), self::get_screen_id(), 'normal', 'core', $callback_params);
		add_meta_box('slickr-flickr-identity', __('Flickr Identity',SLICKR_FLICKR_DOMAIN), array(__CLASS__, 'id_panel'), self::get_screen_id(), 'normal', 'core', $callback_params);
		add_meta_box('slickr-flickr-general', __('Display Options',SLICKR_FLICKR_DOMAIN), array(__CLASS__, 'general_panel'), self::get_screen_id(), 'normal', 'core', $callback_params);
		add_meta_box('slickr-flickr-lightbox', __('LightBox Options',SLICKR_FLICKR_DOMAIN), array(__CLASS__, 'lightbox_panel'), self::get_screen_id(), 'normal', 'core', $callback_params);
		add_meta_box('slickr-flickr-galleria', __('Galleria Options',SLICKR_FLICKR_DOMAIN), array(__CLASS__, 'galleria_panel'), self::get_screen_id(), 'normal', 'core', $callback_params);
		add_meta_box('slickr-flickr-advanced', __('Advanced Options',SLICKR_FLICKR_DOMAIN), array(__CLASS__, 'advanced_panel'), self::get_screen_id(), 'normal', 'core', $callback_params);
		add_meta_box('slickr-flickr-links', __('Useful Links',SLICKR_FLICKR_DOMAIN), array(__CLASS__, 'links_panel'), self::get_screen_id(), 'side', 'core', $callback_params);
		add_meta_box('slickr-flickr-help', __('Help',SLICKR_FLICKR_DOMAIN), array(__CLASS__, 'help_panel'), self::get_screen_id(), 'side', 'core', $callback_params);
		add_meta_box('slickr-flickr-cache', __('Caching',SLICKR_FLICKR_DOMAIN), array(__CLASS__, 'cache_panel'), self::get_screen_id(), 'side', 'core', $callback_params);
		add_meta_box('slickr-flickr-lightboxes', __('Compatible LightBoxes',SLICKR_FLICKR_DOMAIN), array(__CLASS__, 'lightboxes_panel'), self::get_screen_id(), 'side', 'core', $callback_params);
		add_filter('screen_layout_columns', array(__CLASS__, 'screen_layout_columns'), 10, 2);
		$current_screen = get_current_screen();
		if (method_exists($current_screen,'add_help_tab')) {
			$current_screen->add_help_tab( array( 'id' => 'slickr_flickr_overview', 'title' => 'Overview', 		
				'content' => '<p>This admin screen is used to configure your Flickr settings, set display defaults, and choose which LightBox and version of the Galleria /theme you wish to use with Slickr Flickr.</p>'));	
			$current_screen->add_help_tab( array( 'id' => 'slickr_flickr_troubleshooting', 'title' => 'Troubleshooting', 		
				'content' => '<p>Make sure you only have one version of jQuery installed, and have a single LightBox activated otherwise you may have conflicts. For best operation your page should not have any JavaScript errors. Some Javascript conflicts are removed by loading Slickr Flickr in the footer (see Advanced Options)</p>
				<p>For help go to <a href="http://www.slickrflickr.com/slickr-flickr-help/">Slickr Flickr Help</a> or for priority support upgrade to <a href="http://www.slickrflickr.com/upgrade/">Slickr Flickr Pro</a></p>'));	
		}
		add_action ('admin_enqueue_scripts',array(__CLASS__, 'enqueue_styles'));
		add_action ('admin_enqueue_scripts',array(__CLASS__, 'enqueue_scripts'));
	    self::$keys = array_keys(self::$tips);
		self::$tooltips = new DIY_Tooltip(self::$tips);
	}

	static function enqueue_styles() {
		wp_enqueue_style(self::CODE.'-admin', plugins_url('styles/admin.css',dirname(__FILE__)), array(),SLICKR_FLICKR_VERSION);
		wp_enqueue_style(self::CODE.'-tooltip', plugins_url('styles/tooltip.css',dirname(__FILE__)), array(),SLICKR_FLICKR_VERSION);
 	}

	static function enqueue_scripts() {
		wp_enqueue_style(self::CODE.'-admin', plugins_url('scripts/admin.js',dirname(__FILE__)), array(),SLICKR_FLICKR_VERSION);
		wp_enqueue_script('common');
		wp_enqueue_script('wp-lists');
		wp_enqueue_script('postbox');	
		add_action('admin_footer-'.self::get_screen_id(), array(__CLASS__, 'toggle_postboxes'));
	}

    static function toggle_postboxes() {
    	$hook = self::get_screen_id();
    	print <<< SCRIPT
<script type="text/javascript">
//<![CDATA[
	jQuery(document).ready( function($) {
		$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
		postboxes.add_postbox_toggles('{$hook}');
	});
//]]>
</script>

SCRIPT;
    }

   	static function clear_cache() {
   		Slickr_Flickr_Utils::clear_cache();
   		$class = "updated fade";
   		$message = __("WordPress RSS cache has been cleared successfully",SLICKR_FLICKR_DOMAIN);
   		return '<div id="message" class="' . $class .' "><p>' . $message. '</p></div>';
   }

	static function save() {
		check_admin_referer(__CLASS__);
  		$options = explode(',', stripslashes($_POST['page_options']));
  		if ($options) {
  			$flickr_options = array();
    		foreach ($options as $option) 
       			$flickr_options[substr($option,7)] = array_key_exists($option, $_POST) ? trim(stripslashes($_POST[$option])) : '';	
   			$updates = Slickr_Flickr_Utils::save_options($flickr_options) ;
   			if ($updates)  {
       			$message = __("Slickr Flickr settings saved.",SLICKR_FLICKR_DOMAIN);
   			} else
       			$message = __("No Slickr Flickr settings are unchanged since last update.",SLICKR_FLICKR_DOMAIN);
  		    $class="updated fade";
  		} else {
       		$message= "Slickr Flickr settings not found!";
  		    $class="error";
  		}
  		return sprintf('<div id="message" class="%1$s"><p>%2$s</p></div>', $class, $message) ;
	}

	static function id_panel($post, $metabox) {		
		$options = $metabox['args']['options'];	 	
		$groups = self::select_options($options['group'], array('n' => 'user', 'y' => 'group'));
		$tip1 = self::$tooltips->tip('flickr_id');
		$tip2 = self::$tooltips->tip('flickr_group');	
		$tip3 = self::$tooltips->tip('flickr_api_key');	
		print <<< ID_PANEL
<label>{$tip1}</label><input type="text" id="flickr_id" name="flickr_id" value="{$options['id']}" maxlength="15"  style="width:320px" /><br/>
<label>{$tip2}</label><select name="flickr_group" id="flickr_group">{$groups}</select><br/>
<label>{$tip3}</label><input type="text" id="flickr_api_key" name="flickr_api_key" value="{$options['api_key']}" style="width:320px"/><br/>
ID_PANEL;
	}

	static function general_panel($post, $metabox) {		
		$options = $metabox['args']['options'];	 	
		$types = self::select_options($options['type'], self::$types);
		$sizes = self::select_options($options['size'], self::$sizes);		
		$captions = self::radio_options('flickr_captions', $options['captions']);
		$autoplay = self::radio_options('flickr_autoplay', $options['autoplay']);
		$tip1 = self::$tooltips->tip('flickr_items');
		$tip2 = self::$tooltips->tip('flickr_group');	
		$tip3 = self::$tooltips->tip('flickr_size');	
		$tip4 = self::$tooltips->tip('flickr_captions');	
		$tip5 = self::$tooltips->tip('flickr_autoplay');	
		$tip6 = self::$tooltips->tip('flickr_delay');	
		$tip7 = self::$tooltips->tip('flickr_transition');
		$upgrade = SLICKR_FLICKR_HOME . '/upgrade';
		print <<< GENERAL_PANEL
<label>{$tip1}</label><input type="text" name="flickr_items" id="flickr_items" value="{$options['items']}" size="4" /><br/>
<label>{$tip2}</label><select name="flickr_type" id="flickr_type">{$types}</select><br/>
<label>{$tip3}</label><select name="flickr_size" id="flickr_size">{$sizes}</select><br/>
<label>{$tip4}</label>{$captions}<br/>
<label>{$tip5}</label>{$autoplay}<br/>
<label>{$tip6}</label><input type="text" name="flickr_delay" id="flickr_delay" value="{$options['delay']}" size="3" /> seconds</br/>
<label>{$tip7}</label><input type="text" name="flickr_transition" id="flickr_transition" value="{$options['transition']}"  size="3" /> seconds<br/>
GENERAL_PANEL;
	}

	static function advanced_panel($post, $metabox) {		
		$options = $metabox['args']['options'];	 			
		$scripts_in_footer = $options['scripts_in_footer']=="1"?'checked="checked"':'';
		$tip1 = self::$tooltips->tip('flickr_scripts_in_footer');
		$home = SLICKR_FLICKR_HOME;
		print <<< ADVANCED_PANEL
<label>{$tip1}</label><input type="checkbox" name="flickr_scripts_in_footer" id="flickr_scripts_in_footer" {$scripts_in_footer} value="1" /><br/>
ADVANCED_PANEL;
	}

	static function lightbox_panel($post, $metabox) {		
		$options = $metabox['args']['options'];	 	
		$lightboxes = self::select_options($options['lightbox'], self::$lightboxes);			
		$tip1 = self::$tooltips->tip('flickr_lightbox');
		$home = SLICKR_FLICKR_HOME;
		print <<< LIGHTBOX_PANEL
<label>{$tip1}</label><select name="flickr_lightbox" id="flickr_lightbox">{$lightboxes}</select><br/>
LIGHTBOX_PANEL;
	}

	static function lightboxes_panel($post, $metabox) {	
		$options = $metabox['args']['options'];	 		
		print <<< COMPAT_LIGHTBOX_PANEL
<ul>
<li><a href="http://wordpress.org/extend/plugins/fancybox-for-wordpress/" rel="external">FancyBox Lightbox for WordPress</a></li>
<li><a href="http://wordpress.org/extend/plugins/highslide-4-wordpress-reloaded/" rel="external">Highslide for WordPress Reloaded</a></li>
<li><a href="http://s3.envato.com/files/1099520/index.html" rel="external">Evolution Lightbox</a></li>
<li><a href="http://wordpress.org/extend/plugins/easy-fancybox/" rel="external">Easy FancyBoxBox</a></li>
<li><a href="http://wordpress.org/extend/plugins/lightbox-plus/" rel="external">Lightbox Plus (ColorBox) for WordPress</a></li>
<li><a href="http://wordpress.org/extend/plugins/responsive-lightbox/" rel="external"Responsive LIghtbox</a></li>
<li><a href="http://wordpress.org/extend/plugins/shutter-reloaded/" rel="external">Shutter Lightbox for WordPress</a></li>
<li><a href="http://wordpress.org/extend/plugins/slimbox/" rel="external">SlimBox</a></li>
</ul>
COMPAT_LIGHTBOX_PANEL;
	}

	static function galleria_panel($post, $metabox) {		
		$options = $metabox['args']['options'];	 	
		$versions = self::select_options($options['galleria'], self::$galleria_versions);			
		$loading = self::radio_options('flickr_galleria_theme_loading', $options['galleria_theme_loading'], array('static','dynamic'));
		$tip1 = self::$tooltips->tip('flickr_galleria');
		$tip2 = self::$tooltips->tip('flickr_galleria_theme');
		$tip3 = self::$tooltips->tip('flickr_galleria_theme_loading');
		$tip4 = self::$tooltips->tip('flickr_galleria_themes_folder');
		$tip5 = self::$tooltips->tip('flickr_galleria_options');
		$home = SLICKR_FLICKR_HOME;
		print <<< GALLERIA_PANEL
<label>{$tip1}</label><select name="flickr_galleria" id="flickr_galleria">{$versions}</select><br/>
<label>{$tip2}</label><input type="text" name="flickr_galleria_theme" id="flickr_galleria_theme" value="{$options['galleria_theme']}" size="12" /><br/>
<label>{$tip3}</label>{$loading}<br/>
<label>{$tip4}</label><input type="text" name="flickr_galleria_themes_folder" id="flickr_galleria_themes_folder" value="{$options['galleria_themes_folder']}" size="30" /><br/>
<label>{$tip5}</label><textarea name="flickr_galleria_options"  id="flickr_galleria_options" cols="80" rows="4">{$options['galleria_options']}</textarea><br/>
GALLERIA_PANEL;
	}
	
	static function cache_panel($post, $metabox) {
		$options = $metabox['args']['options'];	 	
	
		$url = $_SERVER['REQUEST_URI'];	
		print <<< CACHE_PANEL
<h4>Clear RSS Cache</h4>
<p>If you have a RSS caching issue where your Flickr updates have not yet appeared on Wordpress then click the button below to clear the RSS cache</p>
<form id="slickr_flickr_cache" method="post" action="{$url}" >
<fieldset>
<input type="hidden" name="cache" value="clear" />
<input type="submit"  class="button-primary" name="clear" value="Clear Cache" />
</fieldset>
</form>
CACHE_PANEL;
	}

 	static function help_panel($post, $metabox) {
		$options = $metabox['args']['options'];	 		
		$home = SLICKR_FLICKR_HOME;
		$images_url = plugins_url('images/',dirname(__FILE__));
		print <<< HELP_PANEL
<ul>
<li><a href="{$home}" rel="external">Plugin Home Page</a></li>
<li><a href="{$home}/40/how-to-use-slickr-flickr-admin-settings/" rel="external">How To Use Admin Settings</a></li>
<li><a href="{$home}/56/how-to-use-slickr-flickr-to-create-a-slideshow-or-gallery/" rel="external">How To Use The Plugin</a></li>
<li><a href="{$home}/slickr-flickr-help/" rel="external">Get Help</a></li>
<li><a href="{$home}/slickr-flickr-videos/" rel="external">Get FREE Video Tutorials</a></li>
</ul>
<p><img src="{$images_url}free-video-tutorials-banner.png" alt="Slickr Flickr Tutorials Signup" /></p>
<form id="slickr_flickr_signup" method="post" action="{$home}"
onsubmit="return slickr_flickr_validate_form(this)">
<fieldset>
<input type="hidden" name="form_storm" value="submit"/>
<input type="hidden" name="destination" value="slickr-flickr"/>
<label for="firstname">First Name
<input id="firstname" name="firstname" type="text" value="" /></label><br/>
<label for="email">Email
<input id="email" name="email" type="text" /></label><br/>
<label id="lsubject" for="subject">Subject
<input id="subject" name="subject" type="text" /></label>
<input type="submit" value="" />
</fieldset>
</form>
HELP_PANEL;
	}	
	
	static function links_panel($post, $metabox) {	
		$options = $metabox['args']['options'];	 
		$home = SLICKR_FLICKR_HOME;				
		print <<< LINKS_PANEL
<ul>
<li><a rel="external" href="http://idgettr.com/">Find your Flickr ID</a></li>
<li><a rel="external" href="http://www.flickr.com/services/api/keys/">Get Your Flickr API Keys</a></li>
<li><a rel="external" href="{$home}/1717/using-slickr-flickr-with-other-lightboxes">Using Slickr Flickr with other lightboxes</a></li>
<li><a rel="external" href="http://galleria.aino.se/themes/">Premium Galleria Themes</a></li>
<li><a rel="external" href="{$home}/2328/load-javascript-in-footer-for-earlier-page-display/">Loading Slickr Flickr scripts in the footer</a></li>
<li><a rel="external" href="{$home}/pro/">Slickr Flickr Pro Bonus Features</a></li>
</ul>
LINKS_PANEL;
	}	
	
 	static function intro_panel($post,$metabox){	
		$message = $metabox['args']['message'];	 	
		$home = SLICKR_FLICKR_HOME;
		print <<< INTRO_PANEL
	<p>For help on gettting the best from Slickr Flickr visit the <a href="{$home}">Slickr Flickr Plugin Home Page</a></p>
	<p><b>We recommend you fill in your Flickr ID in the Flickr Identity section. All the other fields are optional.</b></p>
{$message}
INTRO_PANEL;
	}    
    
	static function options_panel() {
 		global $screen_layout_columns;
 		$url = $_SERVER['REQUEST_URI'];
		$keys = implode(',',self::$keys);
?>
<div class="wrap">
    <h2 class="title">Slickr Flickr Options</h2>
    <div id="poststuff" class="metabox-holder has-right-sidebar">
        <div id="side-info-column" class="inner-sidebar">
		<?php do_meta_boxes(self::get_screen_id(), 'side', null); ?>
        </div>
        <div id="post-body" class="has-sidebar">
            <div id="post-body-content" class="has-sidebar-content">
			<form id="slickr_flickr_options" method="post" action="<?php echo $url; ?>">
			<?php do_meta_boxes(self::get_screen_id(), 'normal', null); ?>
			<p class="submit">
			<input type="submit" class="button-primary" name="options_update" value="Save Changes" />
			<input type="hidden" name="page_options" value="<?php echo $keys; ?>" />
			<?php wp_nonce_field(__CLASS__); ?>
			<?php wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false ); ?>
			<?php wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false ); ?>
			</p>
			</form>
 			</div>
        </div>
        <br class="clear"/>
    </div>
</div>
<?php
	}    
}
