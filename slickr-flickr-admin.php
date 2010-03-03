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

function slickr_flickr_options_panel() {

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

$is_slideshow = $options['type']=="slideshow"?"selected":"";
$is_gallery = $options['type']=="gallery"?"selected":"";;
$captions_on = $options['captions']!="off"?"selected":"";
$captions_off = $options['captions']=="off"?"selected":"";


print <<< ADMIN_PANEL
<div class="wrap">
<h2>Slickr Flickr Options</h2>
<form method="post" id="slickr_flickr_options">

<h3>Flickr Id</h3>
<p>The Flickr Id is required for you to be able to access your photos.</p>
<p>If you supply it here, the plugin will remember it so you do not need to supply it for every gallery and every slideshow.</p>
<p>You are still able to supply a Flickr id for individual slideshow perhaps where you want to display photos from a friends Flickr account</p>
<p>A Flickr Id looks something like this : 47103510@N00</p>
<label for="flickr_id">Flickr Id: </label><input name="flickr_id" type="text" id="flickr_id" value="{$options['id']}" />

<h3>Number Of Photos To Display</h3>
<p>If you leave this blank then the plugin will display up to 20 photos in each gallery or slideshow.</p>
<p>If you supply a number it here, the plugin will remember it so you do not need to supply it for every gallery and every slideshow.</p>
<p>You are still able to supply the number of photos to display for individual slideshow by specifying it in the post</p>
<p>For example [slickr-flickr: tag="bahamas" items="10"] displays up to ten photos tagged with bahamas</p>
<label for="flickr_items">Number of Photos:&nbsp</label><input name="flickr_items" type="text" id="flickr_items" value="{$options['items']}" />

<h3>Type of Display</h3>
<p>If you leave this blank then the plugin will display a gallery</p>
<p>If you make a selection here, the plugin will remember it so you do not need to supply it for each photo display.</p>
<p>You are still able to supply the type of display by specifying it in the post</p>
<p>For example [slickr-flickr: tag="bahamas" type="gallery"] displays a gallery even if you have set the default display type as slideshow</p>
<label for="flickr_type">Display as: </label><select name="flickr_type" id="flickr_type">
<option value="gallery" {$is_gallery}>a gallery of thumbnail images</option>
<option value="slideshow" {$is_slideshow}>a slideshow of medium size images</option>
</select>

<h3>Captions</h3>
<p>If you leave this blank then the plugin will display captions beneath photos in a slideshow</p>
<p>If you make a selection here, the plugin will remember it so you do not need to supply it for each slideshow.</p>
<p>You are still able to control captions on individual slideshows by specifying it in the post</p>
<p>For example [slickr-flickr: tag="bahamas" captions="off"] switches off captions for that slideshow even if you have set the default captioning here to be on</p>
<label for="flickr_captions">Captions: </label><select name="flickr_captions" id="flickr_captions">
<option value="on" {$captions_on}>on</option>
<option value="off" {$captions_off}>off</option>
</select>

<h3>Delay Between Slides</h3>
<p>If you leave this blank then the plugin will move the slideshow on every 5 seconds.</p>
<p>If you supply a number it here, the plugin will remember it so you do not need to supply it for every slideshow.</p>
<p>You are still able to supply a different delay for individual slideshow by specifying it in the post</p>
<p>For example [slickr-flickr: tag="bahamas" delay="10"] displays a slideshow with a ten second delay between slides</p>
<label for="flickr_delay">Slide Transition Delay: </label><input name="flickr_delay" type="text" id="flickr_delay" value="{$options['delay']}" />

<p class="submit">
<input type="submit" name="options_update" value="Save Changes" />
<input type="hidden" name="page_options" value="flickr_id,flickr_items,flickr_type,flickr_captions,flickr_delay" />
</p>
</form>

<table style="margin-top: 20px;">
<tr><td>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
">
<input type="image" src="https://www.paypal.com/en_US/GB/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online.">
<img alt="" border="0" src="https://www.paypal.com/en_GB/i/scr/pixel.gif" width="1" height="1">
</form>
</td>
<td>If you find this plugin useful and use it regularly please feel free to support the writer by donating a few bucks.</td>
</tr>
</table>
</div>
ADMIN_PANEL;
}
?>