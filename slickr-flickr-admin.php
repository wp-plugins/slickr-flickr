<?php
/*
Author: Russell Jamieson
Author URI: http://www.russelljamieson.com
Copyright &copy; 2010-2011 &nbsp; Russell Jamieson
*/

define('SLICKR_FLICKR_ADMIN', 'slickr-flickr-admin');
$slickr_flickr_admin = new slickr_flickr_admin();

//class that reperesent the complete plugin
class slickr_flickr_admin {

    private $pagehook;

	function __construct() {
		add_filter('screen_layout_columns', array(&$this, 'screen_layout_columns'), 10, 2);
		add_action('admin_menu', array(&$this, 'admin_menu')); 
		add_filter( 'plugin_action_links',array(&$this, 'plugin_action_links'), 10, 2 );
	}

	function screen_layout_columns($columns, $screen) {
		if (!defined( 'WP_NETWORK_ADMIN' ) && !defined( 'WP_USER_ADMIN' )) {
			if ($screen == $this->pagehook) {
				$columns[$this->pagehook] = 2;
			}
		}
		return $columns;
	}

	function admin_menu() {
		$this->pagehook = add_options_page('Slickr Flickr', 'Slickr Flickr', 9, SLICKR_FLICKR_ADMIN, array(&$this, 'options_panel'));
		add_action('load-'.$this->pagehook, array(&$this, 'load_page'));
		add_action('admin_head-'.$this->pagehook, array(&$this, 'load_style'));
		add_action('admin_footer-'.$this->pagehook, array(&$this, 'load_script'));		
		add_action('admin_footer-'.$this->pagehook, array(&$this, 'toggle_postboxes'));
	}

	function load_style() {
    	echo ('<link rel="stylesheet" id="slickr-flickr-admin" href="'.SLICKR_FLICKR_PLUGIN_URL.'/slickr-flickr-admin.css?ver='.SLICKR_FLICKR_VERSION.'" type="text/css" media="all" />');
 	}

	function load_script() {
    	echo('<script type="text/javascript" src="'.SLICKR_FLICKR_PLUGIN_URL.'/slickr-flickr-admin.js?ver='.SLICKR_FLICKR_VERSION.'"></script>');    
	}	

	function plugin_action_links( $links, $file ) {
		if ( SLICKR_FLICKR_PATH == $file ) {
			$settings_link = '<a href="' . admin_url( 'options-general.php?page='.SLICKR_FLICKR_ADMIN) . '">Settings</a>';
			array_unshift( $links, $settings_link );
		}
		return $links;
	}

	function load_page() {
		wp_enqueue_script('common');
		wp_enqueue_script('wp-lists');
		wp_enqueue_script('postbox');	
		add_meta_box('slickr-flickr-identity', __('Flickr Identity',SLICKR_FLICKR), array(&$this, 'id_panel'), $this->pagehook, 'normal', 'core');
		add_meta_box('slickr-flickr-general', __('Display Options',SLICKR_FLICKR), array(&$this, 'general_panel'), $this->pagehook, 'normal', 'core');
		add_meta_box('slickr-flickr-lightbox', __('LightBox Options',SLICKR_FLICKR), array(&$this, 'lightbox_panel'), $this->pagehook, 'normal', 'core');
		add_meta_box('slickr-flickr-galleria', __('Galleria Options',SLICKR_FLICKR), array(&$this, 'galleria_panel'), $this->pagehook, 'normal', 'core');
		add_meta_box('slickr-flickr-advanced', __('Advanced Options',SLICKR_FLICKR), array(&$this, 'advanced_panel'), $this->pagehook, 'normal', 'core');
		add_meta_box('slickr-flickr-pro', __('Pro Options',SLICKR_FLICKR), array(&$this, 'pro_panel'), $this->pagehook, 'normal', 'core');
		add_meta_box('slickr-flickr-help', __('Help',SLICKR_FLICKR), array(&$this, 'help_panel'), $this->pagehook, 'side', 'core');
		add_meta_box('slickr-flickr-cache', __('Caching',SLICKR_FLICKR), array(&$this, 'cache_panel'), $this->pagehook, 'side', 'core');
		add_meta_box('slickr-flickr-lightboxes', __('Compatible LightBoxes',SLICKR_FLICKR), array(&$this, 'lightboxes_panel'), $this->pagehook, 'side', 'core');
	}

    function use_cache() {
		return !(array_key_exists('options_update',$_POST) && isset($_POST['options_update'])) ;    
    }

   	function clear_cache() {
   		slickr_flickr_clear_cache();
   		$class = "updated fade";
   		$message = __("WordPress RSS cache has been cleared successfully",SLICKR_FLICKR);
   		return '<div id="message" class="' . $class .' "><p>' . $message. '</p></div>';
   }

	function save() {
		check_admin_referer(SLICKR_FLICKR_ADMIN);
  		$flickr_options = array();
  		$slickr_options = array();
  		$options = explode(',', stripslashes($_POST['page_options']));
  		if ($options) {
			$pro_options = slickr_flickr_pro_get_options(false);  
    		// retrieve option values from POST variables
    		foreach ($options as $option) {
       			$option = trim($option);
       			if (substr($option,0,7) == 'flickr_')
    				$flickr_options[$option] = trim(stripslashes($_POST[$option]));
       			else {
          			$old_value = $pro_options[substr($option,7)];
          			$slickr_options[$option] = trim(stripslashes($_POST[$option]));
 					if ($option == 'slickr_licence') { 
 			  			if ($slickr_options[$option] != $old_value) $slickr_options[$option] = md5($slickr_options[$option]);
            		} else {
              			if ($slickr_options[$option] == md5($old_value)) $slickr_options[$option] = $old_value;
					}
	    		}
    		} //end for

   			$updates =  update_option("slickr_flickr_options", $flickr_options) ;
   			$updatespro = update_option("slickr_flickr_pro_options", $slickr_options);
  		    $class="updated fade";
   			if ($updates || $updatespro) 
       			$message = __("Slickr Flickr Settings saved.",SLICKR_FLICKR_ADMIN);
   			else
       			$message = __("No Slickr Flickr settings were changed since last update.",SLICKR_FLICKR_ADMIN);
  		} else {
  		    $class="error";
       		$message= "Slickr Flickr settings not found!";
  		}
  		return '<div id="message" class="' . $class .' "><p>' . $message. '</p></div>';
	}

	function pro_panel ($post, $metabox) {
		$is_pro = false;
		$key_status_indicator ='';
		$notice ='';
		$cache = $this->use_cache();
		$pro_options = slickr_flickr_pro_get_options($cache);
		$options = slickr_flickr_get_options($cache);
		$licence = $pro_options['licence'];
		if (! empty($licence)) {
   			$version_info = slickr_flickr_get_version_info($cache);
   			$is_pro = $version_info["valid_key"];
   			$notice = $version_info["notice"];
   			$key_status_indicator = "<img src='" . SLICKR_FLICKR_PLUGIN_URL ."/images/".($is_pro ? "tick" : "cross").".png'/>";
  		}
		$consumer_secret = md5($pro_options['consumer_secret']);
		$token = md5($pro_options['token']);
		$token_secret = md5($pro_options['token_secret']);
        $readonly = $is_pro ? '' : 'readonly="readonly" class="readonly"';
		print <<< PRO_PANEL
<h4>Slickr Flickr Pro Licence Key</h4>
<p>The Slickr Flickr Pro Licence Key is required if you want to get support through the <a href="http://www.diywebmastery.com/slickrflickrpro/forum/">Slickr Flickr Pro Forums</a> and also use some of the <a href="http://www.slickrflickr.com/pro/">Slickr Flickr Pro Bonus features</a>.</p>
{$notice}
<label for="slickr_licence">Slickr Flickr Licence Key: </label><input name="slickr_licence" id="slickr_licence" type="password" style="width:320px" value="{$licence}" />&nbsp;{$key_status_indicator}
<h4>Flickr API Secret</h4>
<p>The Flickr API Secret is required if you want to make authenticated requests to Flickr.</p>
<p>This is the secret key that is paired with your API key and can be found by logging in to Flickr and then visiting <a href="http://www.flickr.com/services/api/keys/">Flickr API Keys</a>.</p>
<label for="slickr_consumer_secret"> Flickr API Secret: </label><input name="slickr_consumer_secret" id="slickr_consumer_secret" type="password" style="width:200px" {$readonly} value="{$consumer_secret}" />
<h4> Flickr Authentication Token</h4>
<p>The Flickr Authentication Token is required if you want to be able to fetch private photos.</p>
<p>The token must be set up with READ permission to access your private photos.</p>
<p>Please log in to your <a href="http://www.diywebmastery.com/slickrflickrpro/">Slickr Flickr Pro Dashboard</a> for instructions on requesting a Flickr Authentication Token.</p>
<p>In the next version of Slickr Flickr I will add a "Verify Token" on this page so you can easily check that all the secrets codes have been copied correctly.</p>
<label for="slickr_token">Flickr Token: </label><input name="slickr_token" id="slickr_token" type="password" style="width:480px" {$readonly} value="{$token}" />
<h4>Flickr Authentication Token Secret</h4>
<p>The Flickr Authentication Token Secret is required if you want to be able to fetch private photos. Follow the instructions above to obtain your token secret</p>
<label for="slickr_token_secret">Token Secret: </label><input name="slickr_token_secret" id="slickr_token_secret" type="password" style="width:200px" {$readonly} value="{$token_secret}" />
<h4>Thumbnail Border Color</h4>
<p>If you want to set a default thumbnail border color then supply the color as a hex code preceded by a #. e.g Red is #FF0000</p>
<label for="flickr_thumbnail_border">Thumbnail Border Color: </label><input name="flickr_thumbnail_border" id="flickr_thumbnail_border" type="text" {$readonly} value="{$options['thumbnail_border']}" />
<h4>Slideshow Transition Time</h4>
<p>If you leave this blank then the plugin will take half a second to fade one slide into the next.</p>
<p>If you supply a number it here, the plugin will remember it so you do not need to supply it for every slideshow.</p>
<p>You are still able to supply a different delay for individual slideshow by specifying it in the post</p>
<p>For example [slickr-flickr tag="bahamas" transition="2"] displays a slideshow with a 2 second fade transition between slides</p>
<label for="flickr_transition">Fade Transition Time: </label><input name="flickr_transition" id="flickr_transition" type="text" {$readonly} value="{$options['transition']}" />
PRO_PANEL;
	}

	function id_panel($post, $metabox) {		
		$options = slickr_flickr_get_options($this->use_cache());		
		$is_user = $options['group']!="y"?"selected":"";
		$is_group = $options['group']=="y"?"selected":"";
		print <<< ID_PANEL
<h4>Flickr ID</h4>
<p>The Flickr ID is required for you to be able to access your photos.</p>
<p>If you supply it here, the plugin will remember it so you do not need to supply it for every gallery and every slideshow.</p>
<p>You are still able to supply a Flickr ID for an individual slideshow perhaps where you want to display photos from a friends Flickr account</p>
<p>A Flickr ID looks something like this : 12345678@N00 and you can find your Flickr ID by entering the URL of your Flickr photostream at <a target="_blank" href="http://idgettr.com/">idgettr.com</a></p>
<label for="flickr_id">Flickr ID: </label><input name="flickr_id" type="text" id="flickr_id" value="{$options['id']}" />
<h4>Flickr User or Group</h4>
<p>If you leave this blank then the plugin will assume your default Flickr ID is a user ID</p>
<p>If you make a selection here, the plugin will remember it so you do not need to supply it for each photo display.</p>
<p>You are still able to override the type of Flickr Id by specifying it in the post</p>
<p>For example [slickr-flickr tag="bahamas" id="12345678@N01" group="y"] looks up photos assuming that 12345678@N01 is the Flickr ID of a group</p>
<label for="flickr_group">The Flickr ID above belongs to a : </label><select name="flickr_group" id="flickr_group">
<option value="n" {$is_user}>user</option>
<option value="y" {$is_group}>group</option>
</select>
<h4>Flickr API Key</h4>
<p>The Flickr API Key is used if you want to be able to get more than 20 photos at a time.</p>
<p>If you supply it here, the plugin will remember it so you do not need to supply it for every gallery and every slideshow.</p>
<p>A Flickr API key looks something like this : 5aa7aax73kljlkffkf2348904582b9cc and you can find your Flickr API Key by logging in to Flickr
and then visiting <a target="_blank" href="http://www.flickr.com/services/api/keys/">Flickr API Keys</a></p>
<label for="flickr_api_key">Flickr API Key: </label><input name="flickr_api_key" type="text" id="flickr_api_key" style="width:320px" value="{$options['api_key']}" />		
ID_PANEL;
	}

	function general_panel($post, $metabox) {		
		$options = slickr_flickr_get_options($this->use_cache());		
		$is_slideshow = $options['type']=="slideshow"?"selected":"";
		$is_galleria = $options['type']=="galleria"?"selected":"";
		$is_gallery = $options['type']=="gallery"?"selected":"";
		$captions_on = $options['captions']!="off"?"selected":"";
		$captions_off = $options['captions']=="off"?"selected":"";
		$autoplay_on = $options['autoplay']!="off"?"selected":"";
		$autoplay_off = $options['autoplay']=="off"?"selected":"";
		print <<< GENERAL_PANEL
<h4>Number Of Photos To Display</h4>
<i>Maximum is 20 for fetching photos when using your Flickr ID, 50 for your Flickr API Key and unlimited numbers of photos when using <a href="http://www.slickrflickr.com/upgrade/">Slickr Flickr Pro</a>)</i>
<p>If you leave this blank then the plugin will display up to a maximum of 20 photos in each gallery or slideshow.</p>
<p>If you supply a number it here, the plugin will remember it so you do not need to supply it for every gallery and every slideshow.</p>
<p>You are still able to supply the number of photos to display for individual slideshow by specifying it in the post</p>
<p>For example [slickr-flickr tag="bahamas" items="10"] displays up to ten photos tagged with bahamas</p>
<label for="flickr_items">Number of Photos:&nbsp;</label><input name="flickr_items" type="text" id="flickr_items" value="{$options['items']}" />
<h4>Type of Display</h4>
<p>If you leave this blank then the plugin will display a gallery</p>
<p>If you make a selection here, the plugin will remember it so you do not need to supply it for each photo display.</p>
<p>You are still able to supply the type of display by specifying it in the post</p>
<p>For example [slickr-flickr tag="bahamas" type="gallery"] displays a gallery even if you have set the default display type as slideshow</p>
<label for="flickr_type">Display as: </label><select name="flickr_type" id="flickr_type">
<option value="gallery" {$is_gallery}>a gallery of thumbnail images</option>
<option value="galleria" {$is_galleria}>a galleria slideshow with thumbnail images below</option>
<option value="slideshow" {$is_slideshow}>a slideshow of medium size images</option>
</select>
<h4>Captions</h4>
<p>If you leave this blank then the plugin will display captions either beneath or above photos in a slideshow, lightbox or galleria</p>
<p>If you make a selection here, the plugin will remember it so you do not need to supply it for each display.</p>
<p>You are still able to control captions on individual slideshows by specifying it in the post</p>
<p>For example [slickr-flickr tag="bahamas" captions="off"] switches off captions for that slideshow even if you have set the default captioning here to be on</p>
<label for="flickr_captions">Captions: </label><select name="flickr_captions" id="flickr_captions">
<option value="on" {$captions_on}>on</option>
<option value="off" {$captions_off}>off</option>
</select>
<h4>Autoplay</h4>
<p>If you leave this blank then the plugin will automatically play the images in the slideshow, galleria or lightbox(if you are using the slideshow lightbox that comes with Slickr Flickr)</p>
<p>If you make a selection here, the plugin will remember it so you do not need to supply it for each display.</p>
<p>You are still able to control autoplay on individual displays by specifying it in the post</p>
<p>For example [slickr-flickr tag="bahamas" autoplay="off"] switches off autoplay for that slideshow even if you have set the default autoplay here to be on</p>
<label for="flickr_autoplay">Autoplay: </label><select name="flickr_autoplay" id="flickr_autoplay">
<option value="on" {$autoplay_on}>on</option>
<option value="off" {$autoplay_off}>off</option>
</select>
<h4>Delay Between Images</h4>
<p>If you leave this blank then the plugin will move to the next image every 5 seconds.</p>
<p>If you supply a number it here, the plugin will remember it so you do not need to supply it for every slideshow/gallery/galleria.</p>
<p>You are still able to supply a different delay for individual display of images by specifying it in the post</p>
<p>For example [slickr-flickr tag="bahamas" type="slideshow" delay="10"] displays a slideshow with a ten second delay between images</p>
<label for="flickr_delay">Slide Transition Delay: </label><input name="flickr_delay" type="text" id="flickr_delay" value="{$options['delay']}" />
GENERAL_PANEL;
	}

	function advanced_panel($post, $metabox) {		
		$options = slickr_flickr_get_options($this->use_cache());		
		$scripts_in_footer = $options['scripts_in_footer']=="1"?"checked":"";
		print <<< ADVANCED_PANEL
<h4>Load JavaScript In Footer</h4>
<p>This option allows you to load Javascript in the footer instead of the header.</p>
<p>This option can be useful as it may reduce potential jQuery conflicts with other plugins.</p>
<p>However, it will not work for all WordPress themes, specifically those that do not support loading of scripts in the footer using standard WordPress hooks and filters.</p>
<p>Click for more on <a href="http://www.slickrflickr.com/2328/load-javascript-in-footer-for-earlier-page-display/">loading Slickr Flickr scripts in the footer</a>.</p>
<label for="flickr_scripts_in_footer">Load scripts in Footer: </label><input type="checkbox" name="flickr_scripts_in_footer" id="flickr_scripts_in_footer" value="1" {$scripts_in_footer} />
ADVANCED_PANEL;
	}

	function lightbox_panel($post, $metabox) {		
		$options = slickr_flickr_get_options($this->use_cache());		
		$lightbox_auto = $options['lightbox']=="sf-lbox-auto"?"selected":"";
		$lightbox_manual = $options['lightbox']=="sf-lbox-manual"?"selected":"";
		$thickbox = $options['lightbox']=="thickbox"?"selected":"";
		$colorbox = $options['lightbox']=="colorbox"?"selected":"";
		$evolution = $options['lightbox']=="evolution"?"selected":"";
		$fancybox = $options['lightbox']=="fancybox"?"selected":"";
		$highslide = $options['lightbox']=="highslide"?"selected":"";
		$prettyphoto = $options['lightbox']=="prettyphoto"?"selected":"";
		$prettyphotos = $options['lightbox']=="prettyphotos"?"selected":"";
		$shadowbox = $options['lightbox']=="shadowbox"?"selected":"";
		$slimbox = $options['lightbox']=="slimbox"?"selected":"";
		$shutter = $options['lightbox']=="shutter"?"selected":"";
		$norel = $options['lightbox']=="norel"?"selected":"";
		print <<< LIGHTBOX_PANEL
<p>By default the plugin will use the standard LightBox.</p>
<p>If you select LightBox slideshow then when a photo is clicked the overlaid lightbox will automatically play the slideshow.</p>
<p>If you select ThickBox then it will use the standard WordPress lightbox plugin which is pre-installed with Wordpress.</p>
<p><b>If you select one of the other lightboxes then you need to install that lightbox plugin independently from Slickr Flickr.</b></p>
<p><b>Please read this post about <a href="http://www.slickrflickr.com/1717/using-slickr-flickr-with-other-lightboxes">using Slickr Flickr with other lightboxes</a> before choosing, as not all the third party lightbox plugins support photo descriptions and links to Flickr in the photo title.</b></p> 
<label for="flickr_lightbox">Lightbox</label><select name="flickr_lightbox" id="flickr_lightbox">
<option value="sf-lbox-manual" {$lightbox_manual}>LightBox with manual slideshow (pre-installed)</option>
<option value="sf-lbox-auto" {$lightbox_auto}>LightBox with autoplay slideshow option (pre-installed)</option>
<option value="thickbox" {$thickbox}>Thickbox (pre-installed with Wordpress)</option>
<option value="evolution" {$evolution}>Evolution LightBox for Wordpress (requires separate installation)</option>
<option value="fancybox" {$fancybox}>FancyBox for Wordpress (requires separate installation)</option>
<option value="highslide" {$highslide}>Highslide for Wordpress Reloaded (requires separate installation)</option>
<option value="colorbox" {$colorbox}>LightBox Plus for Wordpress (requires separate installation)</option>
<option value="shadowbox" {$shadowbox}>Shadowbox (requires separate installation)</option>
<option value="shutter" {$shutter}>Shutter Reloaded for Wordpress (requires separate installation)</option>
<option value="slimbox" {$slimbox}>SlimBox for Wordpress (requires separate installation)</option>
<option value="prettyphoto" {$prettyphoto}>WP Pretty Photo - single photos only(requires separate installation)</option>
<option value="prettyphotos" {$prettyphotos}>WP Pretty Photo - with gallery (as above and requires setting to use bundled jQuery)</option>
<option value="norel" {$norel}>Some Other LightBox(requires separate installation)</option>
</select>
LIGHTBOX_PANEL;
	}

	function galleria_panel($post, $metabox) {		
		$options = slickr_flickr_get_options($this->use_cache());
		$galleria_10 = $options['galleria']=="galleria-1.0"?"selected":"";
		$galleria_12 = $options['galleria']=="galleria-1.2"?"selected":"";
		$galleria_123 = $options['galleria']=="galleria-1.2.3"?"selected":"";
		$galleria_125 = $options['galleria']=="galleria-1.2.5"?"selected":"";
		$galleria_none = $options['galleria']=="galleria-none"?"selected":"";
		print <<< GALLERIA_PANEL
<h4>Galleria Version</h4>
<p>Choose which version of the galleria you want to use:</p>
<label for="flickr_galleria">Galleria</label><select name="flickr_galleria" id="flickr_galleria">
<option value="galleria-1.0" {$galleria_10}>Galleria 1.0 - original version</option>
<option value="galleria-1.2" {$galleria_12}>Galleria 1.2 - with carousel and skins</option>
<option value="galleria-1.2.3" {$galleria_123}>Galleria 1.2.3 </option>
<option value="galleria-1.2.5" {$galleria_125}>Galleria 1.2.5 - latest version</option>
<option value="galleria-none" {$galleria_none}>Galleria not required so do not load the script</option>
</select>
<h4>Galleria Theme</h4>
<p>Change this value is you have purchased a <a href="http://galleria.aino.se/themes/">premium Galleria theme</a> or written one and placed the theme folder in the ./wp-content/plugins/slickr-flickr/galleria-x.y.z/themes folder for the version of the galleria you have selected above</p>
<p>The default theme is "classic"</p>
<label for="flickr_galleria_theme">Galleria Theme: </label><input name="flickr_galleria_theme" type="text" id="flickr_galleria_theme" value="{$options['galleria_theme']}" />
<h4>Galleria Options</h4>
<p>Here you can set default options for the galleria 1.2 and later versions.</p>
<p>The correct format is like CSS with colons to separate the parameter name from the value and semi-colons to separate each pair: param1:value1;param2:value2;</p>
<p>For example, transition:fadeslide;transitionSpeed:1000; sets a one second fade and slide transition. See an example of using <a href="http://www.slickrflickr.com/2270/flickr-galleria-slide-transitions-now-supported-by-slickr-flickr/">Galleria Options</a></p>
<label for="flickr_options">Galleria Options: </label><br/><textarea name="flickr_options"  id="flickr_options" cols="80" rows="4" wrap="virtual">{$options['options']}</textarea/>
GALLERIA_PANEL;
	}
	
	function help_panel($post, $metabox) {
		print <<< HELP_PANEL
<ul>
<li><a target="_blank" href="http://www.slickrflickr.com/">Plugin Home Page</a></li>
<li><a target="_blank" href="http://www.slickrflickr.com/40/how-to-use-slickr-flickr-admin-settings/">How To Use Admin Settings</a></li>
<li><a target="_blank" href="http://www.slickrflickr.com/56/how-to-use-slickr-flickr-to-create-a-slideshow-or-gallery/">How To Use The Plugin</a></li>
<li><a target="_blank" href="http://www.slickrflickr.com/slickr-flickr-help/">Get Help</a></li>
<li><a target="_blank" href="http://www.slickrflickr.com/slickr-flickr-videos/">Get FREE Video Tutorials</a></li>
</ul>
<p><img src="http://images.slickrflickr.com/pages/slickr-flickr-tutorials.png" alt="Slickr Flickr Tutorials Signup" /></p>
<form id="slickr_flickr_signup" name="slickr_flickr_signup" method="post" action="http://www.slickrflickr.com/"
onSubmit="return slickr_flickr_validate_form(this)">
<input type="hidden" name="form_storm" value="submit"/>
<input type="hidden" name="destination" value="slickr-flickr"/>
<label for="firstname">First Name
<input id="firstname" name="firstname" type="text" value="" /></label><br/>
<label for="email">Email
<input id="email" name="email" type="text" /></label><br/>
<label id="lsubject" for="subject">Subject
<input id="subject" name="subject" type="text" /></label>
<input type="submit" value="" />
</form>
HELP_PANEL;
	}	
	
	function lightboxes_panel($post, $metabox) {	
		print <<< COMPAT_LIGHTBOX_PANEL
<ul>
<li><a target="_blank" href="http://wordpress.org/extend/plugins/fancybox-for-wordpress/">FancyBox Lightbox for WordPress</a></li>
<li><a target="_blank" href="http://wordpress.org/extend/plugins/highslide-4-wordpress-reloaded/">Highslide for WordPress Reloaded</a></li>
<li><a target="_blank" href="http://wordpress.org/extend/plugins/lightbox-plus/">Lightbox Plus (ColorBox) for WordPress</a></li>
<li><a target="_blank" href="http://wordpress.org/extend/plugins/shadowbox-js/">ShadowBox JS</a></li>
<li><a target="_blank" href="http://wordpress.org/extend/plugins/shutter-reloaded/">Shutter Lightbox for WordPress</a></li>
<li><a target="_blank" href="http://wordpress.org/extend/plugins/slimbox-plugin/">SlimBox for WordPress</a></li>
<li><a target="_blank" href="http://wordpress.org/extend/plugins/wp-prettyphoto/">WP Pretty Photo</a></li>
</ul>
COMPAT_LIGHTBOX_PANEL;
	}

	function cache_panel($post, $metabox) {
		print <<< CACHE_PANEL
<h4>Clear RSS Cache</h4>
<p>If you have a RSS caching issue where your Flickr updates have not yet appeared on Wordpress then click the button below to clear the RSS cache</p>
<form method="post" id="slickr_flickr_cache">
<input type="hidden" name="cache" value="clear"/>
<input type="submit"  class="button-primary" name="clear" value="Clear Cache"/>
</form>
CACHE_PANEL;
	}

    function toggle_postboxes() {
    ?>
	<script type="text/javascript">
		//<![CDATA[
		jQuery(document).ready( function($) {
			// close postboxes that should be closed
			$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
			// postboxes setup
			postboxes.add_postbox_toggles('<?php echo $this->pagehook; ?>');
		});
		//]]>
	</script>
	<?php
    }
    
	function options_panel() {
 		global $screen_layout_columns;
		if (isset($_POST['cache'])) echo $this->clear_cache();  		
 		if (isset($_POST['options_update'])) echo $this->save();
?>
    <div id="poststuff" class="metabox-holder has-right-sidebar">
        <h2>Slickr Flickr Options</h2>
		<p>For help on gettting the best from Slickr Flickr visit the <a href="http://www.slickrflickr.com/">Slickr Flickr Plugin Home Page</a></p>
		<p><b>We recommend you fill in your Flickr ID in the Flickr Identity section. All the other fields are optional.</b></p>
        <div id="side-info-column" class="inner-sidebar">
		<?php do_meta_boxes($this->pagehook, 'side', null); ?>
        </div>
        <div id="post-body" class="has-sidebar">
            <div id="post-body-content" class="has-sidebar-content">
			<form method="post" id="slickr_flickr_options">
			<?php wp_nonce_field(SLICKR_FLICKR_ADMIN); ?>
			<?php wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false ); ?>
			<?php wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false ); ?>
			<?php do_meta_boxes($this->pagehook, 'normal', null); ?>
			<p class="submit">
			<input type="submit"  class="button-primary" name="options_update" value="Save Changes" />
			<input type="hidden" name="page_options" value="flickr_id,flickr_group,flickr_api_key,slickr_licence,slickr_consumer_secret,slickr_token,slickr_token_secret,flickr_items,flickr_type,flickr_captions,flickr_autoplay,flickr_delay,flickr_scripts_in_footer,flickr_transition,flickr_thumbnail_border,flickr_lightbox,flickr_galleria,flickr_galleria_theme,flickr_options" />
			</p>
			</form>
 			</div>
        </div>
        <br class="clear"/>
    </div>
<?php
	}    
    
}
?>