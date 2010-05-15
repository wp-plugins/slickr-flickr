<?php
/*
Author: Russell Jamieson
Author URI: http://www.wordpresswise.com
Copyright &copy; 2010 &nbsp; Russell Jamieson
*/
add_action('admin_menu', 'add_slickr_flickr_options');

function add_slickr_flickr_options() {
    add_options_page('Slickr Flickr', 'Slickr Flickr', 9, basename(__FILE__), 'slickr_flickr_options_panel');
}


function slickr_flickr_clear_rss_cache() {
    global $wpdb, $table_prefix;
    $sql = sprintf("DELETE FROM %soptions WHERE option_name LIKE 'rss_%' and LENGTH(option_name) IN (36, 39)",  $table_prefix);
    $wpdb->query($sql);
}

function slickr_flickr_clear_rss_cache_transient() {
    global $wpdb, $table_prefix;
    $sql = sprintf("DELETE FROM %soptions WHERE option_name LIKE '%s' or option_name LIKE '%s' or option_name LIKE '%s'",
            $table_prefix, "\_transient\_feed\_%", "\_transient\_rss\_%", "\_transient\_timeout\_%");
    $wpdb->query($sql);
}

function slickr_flickr_options_panel() {

if (isset($_POST['cache'])) {
   slickr_flickr_clear_rss_cache();
   slickr_flickr_clear_rss_cache_transient();
}

// test if options should be updated
if (isset($_POST['options_update'])) {
  $flickr_options = array();
  $options = explode(',', stripslashes($_POST['page_options']));
  if ($options) {
    // retrieve option values from POST variables
    foreach ($options as $option) {
            $option = trim($option);
            $flickr_options[$option] = trim(stripslashes($_POST[$option]));
    }

   $class = "updated fade";
   // update database option
   if (update_option("slickr_flickr_options", $flickr_options))
       $message = "<strong>Settings saved.</strong>";
   else
       $message = "No Slickr Flickr settings were changed since last update.";
  } else {
       $class="error";
       $message= "Slickr Flickr settings not found!";
  }
  echo '<div id="message" class="' . $class .' "><p>' . $message. '</p></div>';
}

// retrieve options data from database
$options = slickr_flickr_get_options();

$is_user = $options['group']!="y"?"selected":"";
$is_group = $options['group']=="y"?"selected":"";
$is_slideshow = $options['type']=="slideshow"?"selected":"";
$is_galleria = $options['type']=="galleria"?"selected":"";
$is_gallery = $options['type']=="gallery"?"selected":"";
$captions_on = $options['captions']!="off"?"selected":"";
$captions_off = $options['captions']=="off"?"selected":"";
$lightbox_slideshow = $options['lightbox']=="lightbox-slideshow"?"selected":"";
$lightbox = $options['lightbox']=="lightbox"?"selected":"";

print <<< ADMIN_PANEL
<div class="wrap">
<h2>Slickr Flickr Options</h2>

<p>For help on gettting the best from Slickr Flickr visit the <a href="http://slickr-flickr.diywebmastery.com/">Slickr Flickr Plugin Home Page</a></p>

<form method="post" id="slickr_flickr_options">

<h3>Flickr Id</h3>
<p>The Flickr Id is required for you to be able to access your photos.</p>
<p>If you supply it here, the plugin will remember it so you do not need to supply it for every gallery and every slideshow.</p>
<p>You are still able to supply a Flickr id for an individual slideshow perhaps where you want to display photos from a friends Flickr account</p>
<p>A Flickr Id looks something like this : 12345678@N00</p>
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

<h3>Number Of Photos To Display (Maximum is 20)</h3>
<p>If you leave this blank then the plugin will display up to a maximum of 20 photos in each gallery or slideshow.</p>
<p>If you supply a number it here, the plugin will remember it so you do not need to supply it for every gallery and every slideshow.</p>
<p>You are still able to supply the number of photos to display for individual slideshow by specifying it in the post</p>
<p>For example [slickr-flickr tag="bahamas" items="10"] displays up to ten photos tagged with bahamas</p>
<label for="flickr_items">Number of Photos:&nbsp</label><input name="flickr_items" type="text" id="flickr_items" value="{$options['items']}" />

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

<h3>Lightbox</h3>
<p>If you leave this blank then the plugin will use the standard lightbox.</p>
<p>If you select lightbox slideshow then when a photo is clicked the overlaid lightbox will automatically play the slideshow.</p>
<p>For example [slickr-flickr type="gallery" tag="bahamas" delay="6"] displays a gallery which when clicked shows a lightbox slideshow that plays automatically with a six second delay between slides</p>
<label for="flickr_lightbox">Lightbox</label><select name="flickr_lightbox" id="flickr_lightbox">
<option value="lightbox" {$lightbox}>lightbox with manual slideshow</option>
<option value="lightbox-slideshow" {$lightbox_slideshow}>lightbox with autoplay slideshow option</option>
</select>

<p class="submit">
<input type="submit" name="options_update" value="Save Changes" />
<input type="hidden" name="page_options" value="flickr_id,flickr_group,flickr_items,flickr_type,flickr_captions,flickr_delay,flickr_lightbox" />
</p>
</form>

<h3>Clear RSS Cache</h3>
<p>If you have a RSS caching issue where your Flickr updates have not yet appeared on Wordpress then click the button below to clear the RSS cache</p>
<form method="post" id="slickr_flickr_options">
<input type="hidden" name="cache" value="clear"/>
<input type="submit" name="clear" value="Clear Cache"/>
</form>


<h3>Donate</h3>
<p>If you find this plugin useful and use it regularly please feel free to support the writer by donating a few bucks below or visit <a href="http://www.wordpresswise.com/slickr-flickr/donate">Slickr Flickr charity donation</a> page</p>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----">
<input type="image" src="https://www.paypal.com/en_US/GB/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online.">
<img alt="" border="0" src="https://www.paypal.com/en_GB/i/scr/pixel.gif" width="1" height="1">
</form>

<h3>Help With Slickr Flickr</h3>
<p>For help on gettting the best from Slickr Flickr visit the <a href="http://slickr-flickr.diywebmastery.com/">Slickr Flickr Plugin Home Page</a></p>
</div>
ADMIN_PANEL;
}
?>
