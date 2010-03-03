<?php
/**
 * Slickr Flickr
 *
 * Display a Flickr slideshow or a gallery in a post of widget
 *
 *
 * @param id -> the Flickr ID of user
 * @param tag -> identifies what photos to select
 * @param items -> maximum number photos to display in the gallery or slideshow
 * @param type -> gallery or slideshow
 * @param captions -> whether captions are on or off
 * @param delay -> delay in seconds between each image in the slide show
 *
 */
require_once(dirname(__FILE__).'/flickr.php');

function slickr_flickr_display ($attr) {

  $params = shortcode_atts( slickr_flickr_get_options(), $attr ); //apply plugin defaults

  if (empty($params['id'])) return "<p>Please set up a Flickr User id for this slideshow</p>";
  if (empty($params['tag'])) return "<p>Please set up a Flickr tag for this slideshow</p>";

  if ($params['type']=="slideshow") {
    $is_slideshow = true;
    $divid = "flickr_".str_replace(" ","-",str_replace("'","",$params['tag'])); //strip apostrophes and replace spaces by hyphens
    $divstart = "<div id='".$divid."' class='slickr-flickr-slideshow ".$params['orientation']." ".$params['size']."' onClick='next_slide(this);'>";
    $divend = "</div><script type='text/javascript'>jQuery('#".$divid."').data('delay','".$params['delay']."');</script>";
  } else {
    $is_slideshow = false;
    $divstart = "<div class='slickr-flickr-gallery'><ul>";
    $divend = "</ul></div>";
  }
  $flickr_feed= "http://api.flickr.com/services/feeds/photos_public.gne?lang=en-us&format=rss_200&id=".$params['id']."&tags=".str_replace(" ","",$params['tag']);
  $rss = fetch_feed($flickr_feed);
  $numitems = $rss->get_item_quantity($params['items']);
  if ($numitems == 0)  return '<p>No photos available for '.$params['tag'].'</p>';
  $rss_items = $rss->get_items(0, $numitems);
  $s = "";
  foreach ( $rss_items as $item ) {
    $photo = flickr::find_photo($item);
    $title = flickr::cleanup($photo['title']);
    $oriented = $photo['orientation'];
    $full_url = $params['size']=="original" ? $photo['original'] : flickr::resize_photo($photo['url'], $params['size']);
    $thumb_url = flickr::resize_photo($photo['url'], "square");

    if ($is_slideshow) {
      $imgsize="";
      if ($oriented != $params['orientation']) $imgsize = $oriented=="landscape"?'width="80%"':'height="90%"';
      $s .=  '<div><img '.$imgsize.' src="'.$full_url.'" alt="'.$title.'" /><p>'.($params['captions']=="off"?"":$title).'</p></div>';
    } else {
      $s .= '<li><a class="tt-flickr" href="'.$full_url.'" title="'.$title.'"><img src="'.$thumb_url.'" alt="'.$title.'" /></a></li>';
    }
  }
  return $divstart . $s . $divend;
}

function slickr_flickr_header() {

    if (!defined('SLICKR_FLICKR_PLUGIN_URL')) define ('SLICKR_FLICKR_PLUGIN_URL',WP_PLUGIN_URL . '/slickr-flickr');
    $path = SLICKR_FLICKR_PLUGIN_URL;
    $output = <<<EOB
<link type="text/css" rel="stylesheet" href="{$path}/lightbox/css/jquery.lightbox-0.5.css" media="screen" />
<link type="text/css" rel="stylesheet" href="{$path}/jquery.gallery.css" media="screen" />
<link type="text/css" rel="stylesheet" href="{$path}/jquery.slideshow.css" media="screen" />
<script type="text/javascript" src="{$path}/lightbox/js/jquery.js"></script>
<script type="text/javascript" src="{$path}/lightbox/js/jquery.lightbox-0.5.js"></script>
<script type="text/javascript" src="{$path}/jquery.gallery.js"></script>
<script type="text/javascript" src="{$path}/jquery.slideshow.js"></script>
EOB;
    print $output;
}


add_shortcode('slickr', 'slickr_flickr_display');
add_shortcode('slickr-flickr', 'slickr_flickr_display');
add_action('wp_head', 'slickr_flickr_header');
?>
