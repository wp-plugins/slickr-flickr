<?php
/*
Author: Russell Jamieson
Author URI: http://www.russelljamieson.com
Copyright &copy; 2010-2011 &nbsp; Russell Jamieson
*/
add_action('admin_menu', 'add_slickr_flickr_options');
add_action('init', 'slickr_flickr_admin_header');
add_filter( 'plugin_action_links','slickr_flickr_plugin_action_links', 10, 2 );

//Plugin update actions
add_filter("transient_update_plugins", 'slickr_flickr_check_update');
add_filter("site_transient_update_plugins", 'slickr_flickr_check_update');
//add_action('after_plugin_row_'.SLICKR_FLICKR_PATH, 'slickr_flickr_plugin_row_message');
//add_action('install_plugins_pre_plugin-information', 'slickr_flickr_plugin_version_popup');

function slickr_flickr_admin_header() {
    wp_enqueue_style('slickr-flickr-admin', SLICKR_FLICKR_PLUGIN_URL."/slickr-flickr-admin.css");
    wp_enqueue_script('slickr-flickr-admin', SLICKR_FLICKR_PLUGIN_URL."/slickr-flickr-admin.js",array('jquery','jquery-ui-sortable','postbox'));    
}

function add_slickr_flickr_options() {
    add_options_page('Slickr Flickr', 'Slickr Flickr', 9, basename(__FILE__), 'slickr_flickr_options_panel');
}

function slickr_flickr_check_update($plugin_updates, $cache=true) {

    if (slickr_flickr_check_validity()) {
    	$latest_version_info = slickr_flickr_get_version_info($cache);
       	if (version_compare(SLICKR_FLICKR_VERSION, $latest_version_info["version"], '<')) {
        	$current_version_info = is_array($plugin_updates->response) ? $plugin_updates->response[SLICKR_FLICKR_PATH] : "";
        	if(empty($current_version_info)) $current_version_info = new stdClass();
	        $current_version_info->id = "0";
	        $current_version_info->new_version = $latest_version_info["version"];	
	        $current_version_info->slug = SLICKR_FLICKR_FOLDER;
	        $current_version_info->url = SLICKR_FLICKR_HOME;
	        $current_version_info->package = $latest_version_info["package"];
	        $current_version_info->upgrade_notice = $latest_version_info["notice"];
        	$plugin_updates->response[SLICKR_FLICKR_PATH] =  $current_version_info ;
    	}     
    }
    return $plugin_updates;
}

function slickr_flickr_plugin_action_links( $links, $file ) {
	if ( SLICKR_FLICKR_PATH == $file ) {
		$settings_link = '<a href="' . admin_url( 'options-general.php?page=slickr-flickr-admin.php' ) . '">Settings</a>';
		array_unshift( $links, $settings_link );
	}
	return $links;
}


function slickr_flickr_get_notice(){
    $version_info = slickr_flickr_get_version_info();
    return $version_info['notice'];
}

function slickr_flickr_plugin_row_message($plugin){
    if (slickr_flickr_get_licence() && (!slickr_flickr_check_validity())) {
        echo '</tr><tr class="plugin-update-tr"><td colspan="5" class="plugin-update"><div class="update-message">Did you know you can upgrade to Slickr Flickr Pro for priority support and bonus features? <a href="http://www.slickrflickr.com/upgrade/">Find out more</a>.</div></td>';
    }
}

function slickr_flickr_plugin_version_popup(){
        if($_REQUEST["plugin"] != "slickr-flickr") return;
        echo slickr_flickr_fetch_version_features();
        exit;
}

function slickr_flickr_fetch_version_features(){
        $response = slickr_flickr_remote_call('features');
        if ($response) {
            $response  = substr($response["body"], 0, 20) == "<!--SLICKR FLICKR-->" ? stripslashes($response['body']) : "";
        } else {
            $response = "Unexpected error.<br/>Please try again or <a href='http://www.slickrflickr.com/contact'>contact us</a>.";
        }
        return $response;
}

function slickr_flickr_options_panel() {

if (isset($_POST['cache'])) {
   slickr_flickr_clear_cache();
   $class = "updated fade";
   $message = "WordPress RSS cache has been cleared successfully";
   echo '<div id="message" class="' . $class .' "><p>' . $message. '</p></div>';   
}

$cache = true;
// test if options should be updated
if (isset($_POST['options_update'])) {
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
          $slickr_options[$option] = trim(stripslashes($_POST[$option]));
          switch ($option) { 
           	case 'slickr_licence': { 
          		if ($slickr_options[$option] != slickr_flickr_get_licence()) $slickr_options[$option] = md5($slickr_options[$option]);
            	break;
            	}
            default: {
            	if ($slickr_options[$option] != $pro_options[$option]) 
            		$slickr_options[$option] = strrev(base64_encode($slickr_options[$option]));
            }
		  }
        }
    }

   $class = "updated fade";
   // update database option
   $updates =  update_option("slickr_flickr_options", $flickr_options) ;
   $updatespro = update_option("slickr_flickr_pro_options", $slickr_options);
   if ($updates || $updatespro) {
       $message = "<strong>Settings saved.</strong>";
       $cache = false; //force re-read of options and update
   } else
       $message = "No Slickr Flickr settings were changed since last update.";
  } else {
       $class="error";
       $message= "Slickr Flickr settings not found!";
  }
  echo '<div id="message" class="' . $class .' "><p>' . $message. '</p></div>';
}

// retrieve options data from database
$is_pro = false;
$key_status_indicator ='';
$pro_options = slickr_flickr_pro_get_options($cache);
$options = slickr_flickr_get_options($cache);
$is_user = $options['group']!="y"?"selected":"";
$is_group = $options['group']=="y"?"selected":"";
$is_slideshow = $options['type']=="slideshow"?"selected":"";
$is_galleria = $options['type']=="galleria"?"selected":"";
$is_gallery = $options['type']=="gallery"?"selected":"";
$captions_on = $options['captions']!="off"?"selected":"";
$captions_off = $options['captions']=="off"?"selected":"";
$lightbox_auto = $options['lightbox']=="sf-lbox-auto"?"selected":"";
$lightbox_manual = $options['lightbox']=="sf-lbox-manual"?"selected":"";
$thickbox = $options['lightbox']=="thickbox"?"selected":"";
$colorbox = $options['lightbox']=="colorbox"?"selected":"";
$evolution = $options['lightbox']=="evolution"?"selected":"";
$fancybox = $options['lightbox']=="fancybox"?"selected":"";
$highslide = $options['lightbox']=="highslide"?"selected":"";
$prettyphoto = $options['lightbox']=="prettyphoto"?"selected":"";
$shadowbox = $options['lightbox']=="shadowbox"?"selected":"";
$slimbox = $options['lightbox']=="slimbox"?"selected":"";
$shutter = $options['lightbox']=="shutter"?"selected":"";
$norel = $options['lightbox']=="norel"?"selected":"";
$galleria_10 = $options['galleria']=="galleria-1.0"?"selected":"";
$galleria_12 = $options['galleria']=="galleria-1.2"?"selected":"";
$galleria_123 = $options['galleria']=="galleria-1.2.3"?"selected":"";
$galleria_none = $options['galleria']=="galleria-none"?"selected":"";
$scripts_in_footer = $options['scripts_in_footer']=="1"?"checked":"";
$clear_cache = $pro_options['clear-cache']=="1"?"checked":"";
$licence = $pro_options['licence'];
if (! empty($licence)) {
   $version_info = slickr_flickr_get_version_info($cache);
   $is_pro = $version_info["valid_key"];
   $key_status_indicator = "<img src='" . SLICKR_FLICKR_PLUGIN_URL ."/images/".($is_pro ? "tick" : "cross").".png'/>";
  }
$consumer_secret = $pro_options['consumer_secret'];
$token = $pro_options['token'];
$token_secret = $pro_options['token_secret'];

$pro_panel = <<< PRO_PANEL
<h3>Flickr API Secret</h3>
<p>The Flickr API Secret is required if you want to make authenticated requests to Flickr. Fill this in when you obtain your API key as described above.</p>
<label for="slickr_consumer_secret"> Flickr API Secret: </label><input name="slickr_consumer_secret" id="slickr_consumer_secret" type="password" style="width:320px" value="{$consumer_secret}" />
<h3> Flickr Authentication Token</h3>
<p>The Flickr Authentication Token is required if you want to be able to fetch private photos. You can get your token by logging into FLickr then visiting
the <a href="http://www.flickr.com/services/api/explore/flickr.auth.oauth.getAccessToken">Flickr Access Token</a> page and clicking the "call
method" button. Then copy the oauth_token value into this fields and the oauth_token_secret into the next field</p>
<label for="slickr_token">Flickr Token: </label><input name="slickr_token" id="slickr_token" type="password" style="width:320px" value="{$token}" />
<h3>Flickr Authentication Token Secret</h3>
<p>The Flickr Authentication Token Secret is required if you want to be able to fetch private photos. Follow the instructions above to obtain your token secret</p>
<label for="slickr_token_secret">Token Secret: </label><input name="slickr_token_secret" id="slickr_token_secret" type="password" style="width:320px" value="{$token_secret}" />
PRO_PANEL;

print <<< ADMIN_PANEL
<div class="wrap">
<div style="float:left; width:70%">
<h2>Slickr Flickr Options</h2>
<p>For help on gettting the best from Slickr Flickr visit the <a href="http://www.slickrflickr.com/">Slickr Flickr Plugin Home Page</a></p>
<p><b>We recommend you fill in your Flickr ID. All the other fields are optional</b></p>
<form method="post" id="slickr_flickr_options">
<h3>Flickr Id</h3>
<p>The Flickr Id is required for you to be able to access your photos.</p>
<p>If you supply it here, the plugin will remember it so you do not need to supply it for every gallery and every slideshow.</p>
<p>You are still able to supply a Flickr id for an individual slideshow perhaps where you want to display photos from a friends Flickr account</p>
<p>A Flickr Id looks something like this : 12345678@N00 and you can find your Flickr ID at <a target="_blank" href="http://idgettr.com/">idgettr.com</a></p>
<label for="flickr_id">Flickr Id: </label><input name="flickr_id" type="text" id="flickr_id" value="{$options['id']}" />
<h3>Flickr User or Group</h3>
<p>If you leave this blank then the plugin will assume your default Flickr ID is a user ID</p>
<p>If you make a selection here, the plugin will remember it so you do not need to supply it for each photo display.</p>
<p>You are still able to override the type of Flickr Id by specifying it in the post</p>
<p>For example [slickr-flickr tag="bahamas" id="12345678@N01" group="y"] looks up photos assuming that 12345678@N01 is the Flickr ID of a group</p>
<label for="flickr_group">The Flickr ID above belongs to a : </label><select name="flickr_group" id="flickr_group">
<option value="n" {$is_user}>user</option>
<option value="y" {$is_group}>group</option>
</select>
<h3>Flickr API Key</h3>
<p>The Flickr API Key is used if you want to be able to get more than 20 photos at a time.</p>
<p>If you supply it here, the plugin will remember it so you do not need to supply it for every gallery and every slideshow.</p>
<p>A Flickr API key looks something like this : 5aa7aax73kljlkffkf2348904582b9cc and you can find your Flickr API Key by logging in to Flickr
and then visiting <a target="_blank" href="http://www.flickr.com/services/api/keys/">Flickr API Keys</a></p>
<label for="flickr_api_key">Flickr API Key: </label><input name="flickr_api_key" type="text" id="flickr_api_key" style="width:320px" value="{$options['api_key']}" />
<h3>Slickr Flickr Pro Licence Key</h3>
<p>The Slickr Flickr Pro Licence Key is required if you want to get support through the Slickr Flickr Forum and also use some of the <a href="http://www.slickrflickr.com/pro/">Slickr Flickr Pro Bonus features</a>.</p>
{$version_info["notice"]}
<label for="slickr_licence">Slickr Flickr Licence Key: </label><input name="slickr_licence" id="slickr_licence" type="password" style="width:320px" value="{$licence}" />&nbsp;{$key_status_indicator}
<h3>Number Of Photos To Display (Maximum is 20 for fetch using Flickr ID, 50 for Flickr API Key and unlimited for Pro version)</h3>
<p>If you leave this blank then the plugin will display up to a maximum of 20 photos in each gallery or slideshow.</p>
<p>If you supply a number it here, the plugin will remember it so you do not need to supply it for every gallery and every slideshow.</p>
<p>You are still able to supply the number of photos to display for individual slideshow by specifying it in the post</p>
<p>For example [slickr-flickr tag="bahamas" items="10"] displays up to ten photos tagged with bahamas</p>
<label for="flickr_items">Number of Photos:&nbsp;</label><input name="flickr_items" type="text" id="flickr_items" value="{$options['items']}" />
<h3>Type of Display</h3>
<p>If you leave this blank then the plugin will display a gallery</p>
<p>If you make a selection here, the plugin will remember it so you do not need to supply it for each photo display.</p>
<p>You are still able to supply the type of display by specifying it in the post</p>
<p>For example [slickr-flickr tag="bahamas" type="gallery"] displays a gallery even if you have set the default display type as slideshow</p>
<label for="flickr_type">Display as: </label><select name="flickr_type" id="flickr_type">
<option value="gallery" {$is_gallery}>a gallery of thumbnail images</option>
<option value="galleria" {$is_galleria}>a slideshow with a gallery of thumbnail images below</option>
<option value="slideshow" {$is_slideshow}>a slideshow of medium size images</option>
</select>
<h3>Captions</h3>
<p>If you leave this blank then the plugin will display captions beneath photos in a slideshow</p>
<p>If you make a selection here, the plugin will remember it so you do not need to supply it for each slideshow.</p>
<p>You are still able to control captions on individual slideshows by specifying it in the post</p>
<p>For example [slickr-flickr tag="bahamas" captions="off"] switches off captions for that slideshow even if you have set the default captioning here to be on</p>
<label for="flickr_captions">Captions: </label><select name="flickr_captions" id="flickr_captions">
<option value="on" {$captions_on}>on</option>
<option value="off" {$captions_off}>off</option>
</select>
<h3>Delay Between Slides</h3>
<p>If you leave this blank then the plugin will move the slideshow on every 5 seconds.</p>
<p>If you supply a number it here, the plugin will remember it so you do not need to supply it for every slideshow.</p>
<p>You are still able to supply a different delay for individual slideshow by specifying it in the post</p>
<p>For example [slickr-flickr tag="bahamas" delay="10"] displays a slideshow with a ten second delay between slides</p>
<label for="flickr_delay">Slide Transition Delay: </label><input name="flickr_delay" type="text" id="flickr_delay" value="{$options['delay']}" />
<h3>Fade Transition Time</h3>
<p>If you leave this blank then the plugin will take half a second to fade one slide into the next.</p>
<p>If you supply a number it here, the plugin will remember it so you do not need to supply it for every slideshow.</p>
<p>You are still able to supply a different delay for individual slideshow by specifying it in the post</p>
<p>For example [slickr-flickr tag="bahamas" transition="2"] displays a slideshow with a 2 second fade transition between slides</p>
<label for="flickr_transition">Fade Transition Time: </label><input name="flickr_transition" type="text" id="flickr_transition" value="{$options['transition']}" />
<h3>Lightbox</h3>
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
<option value="prettyphoto" {$prettyphoto}>WP Pretty Photo (requires separate installation)</option>
<option value="norel" {$norel}>Some Other LightBox(requires separate installation)</option>
</select>
<h3>Galleria</h3>
<p>Choose which version of the galleria you want to use:</p>
<label for="flickr_galleria">Galleria</label><select name="flickr_galleria" id="flickr_galleria">
<option value="galleria-1.0" {$galleria_10}>Galleria 1.0 - original version</option>
<option value="galleria-1.2" {$galleria_12}>Galleria 1.2 - with carousel and skins</option>
<option value="galleria-1.2.3" {$galleria_123}>Galleria 1.2.3 - latest version</option>
<option value="galleria-none" {$galleria_none}>Galleria not required so do not load the script</option>
</select>
<h3>Galleria Theme</h3>
<p>Change this value is you have purchased or written a Galleria theme and placed the theme folder in the ./wp-content/plugins/slickr-flickr/galleria-1.2.3/themes folder for the version of the galleria you have selected above</p>
<p>The default theme is "classic"</p>
<label for="flickr_galleria_theme">Galleria Theme: </label><input name="flickr_galleria_theme" type="text" id="flickr_galleria_theme" value="{$options['galleria_theme']}" />
<h3>Load JavaScript In Footer</h3>
<p>This option allows you to load Javascript in the footer which should made your pages appear sooner</p>
<label for="flickr_scripts_in_footer">Load scripts in Footer</label><input type="checkbox" name="flickr_scripts_in_footer" id="flickr_scripts_in_footer" value="1" {$scripts_in_footer} />
<p class="submit">
<input type="submit"  class="button-primary" name="options_update" value="Save Changes" />
<input type="hidden" name="page_options" value="flickr_id,flickr_group,flickr_api_key,slickr_licence,slickr_consumer_secret,slickr_token,slickr_token_secret,flickr_items,flickr_type,flickr_captions,flickr_delay,flickr_transition,flickr_lightbox,flickr_galleria,flickr_galleria_theme,flickr_scripts_in_footer" />
</p>
</form>
<h3>Clear RSS Cache</h3>
<p>If you have a RSS caching issue where your Flickr updates have not yet appeared on Wordpress then click the button below to clear the RSS cache</p>
<form method="post" id="slickr_flickr_cache">
<input type="hidden" name="cache" value="clear"/>
<input type="submit"  class="button-primary" name="clear" value="Clear Cache"/>
</form>

<h3>Help With Slickr Flickr</h3>
<p>For help on gettting the best from Slickr Flickr visit the <a href="http://www.slickrflickr.com/">Slickr Flickr Plugin Home Page</a></p>
</div>
<div style="float:right; width: 180px">
<h3>Slickr Flickr Links</h3>
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
<h3>Compatible LightBoxes</h3>
<ul>
<li><a target="_blank" href="http://wordpress.org/extend/plugins/fancybox-for-wordpress/">FancyBox Lightbox for WordPress</a></li>
<li><a target="_blank" href="http://wordpress.org/extend/plugins/highslide-4-wordpress-reloaded/">Highslide for WordPress Reloaded</a></li>
<li><a target="_blank" href="http://wordpress.org/extend/plugins/lightbox-plus/">Lightbox Plus (ColorBox) for WordPress</a></li>
<li><a target="_blank" href="http://wordpress.org/extend/plugins/shadowbox-js/">ShadowBox JS</a></li>
<li><a target="_blank" href="http://wordpress.org/extend/plugins/shutter-reloaded/">Shutter Lightbox for WordPress</a></li>
<li><a target="_blank" href="http://wordpress.org/extend/plugins/slimbox-plugin/">SlimBox for WordPress</a></li>
<li><a target="_blank" href="http://wordpress.org/extend/plugins/wp-prettyphoto/">WP Pretty Photo</a></li>
</ul>
<h3>Compatible Flickr Plugins</h3>
<ul>
<li><a target="_blank" href="http://wordpress.org/extend/plugins/wordpress-flickr-manager/">Flickr Manager</a></li>
<li><a target="_blank" href="http://wordpress.org/extend/plugins/flickr-gallery/">Flickr Gallery</a></li>
</ul>
</div>
<div style="clear:both"></div>
</div>
ADMIN_PANEL;
}
?>