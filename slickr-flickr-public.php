<?php
/**
 * Slickr Flickr
 *
 * Display a Flickr slideshow or a gallery in a post of widget
 *
 *
 * @param id -> the Flickr ID of user
 * @param group -> set to Y if the Flickr ID is the id of a group and not a user
 * @param tag -> identifies what photos to select
 * @param tagmode -> set to ANY for fetching photos with different tags (optiona)
 * @param items -> maximum number photos to display in the gallery or slideshow
 * @param type -> gallery or slideshow
 * @param captions -> whether captions are on or off
 * @param delay -> delay in seconds between each image in the slide show
 * @param start -> first slide in the slide show
 * @param link -> url to visit on clicking slideshow (optional)
 * @param attribution -> credit the photographer (optional)
 * @param sort -> sort order of photos (optional)
 * @param direction -> sort order of photos (optional)
 * @param descriptions -> show descriptions beneath title on the lightbox - on or off (optional)
 * @param flickr_link -> include a link to the photo on Fklickr on the lightbox - on or off (optional)
 * @param photos_per_row -> include a link to the photo on Fklickr on the lightbox - on or off (optional)
 *
 */
require_once(dirname(__FILE__).'/flickr.php');


function slickr_flickr_display ($attr) {

  $params = shortcode_atts( slickr_flickr_get_options(), $attr ); //apply plugin defaults
  if (($params['type']=="gallery") && ($attr['captions']!="on")) $params['captions'] = "off";

  if (empty($params['id'])) return "<p>Please set up a Flickr User id for this slideshow</p>";
  if (empty($params['tag'])) return "<p>Please set up a Flickr tag for this slideshow</p>";
  $tagmode = strtolower($params['tagmode'])=="any"?"any":"all";
  $id = $params['group']=="y" ? "g" : "id" ;

  $attribution = empty($params['attribution'])?"":('<p class="slickr-flickr-attribution">'.$params['attribution'].'</p>');

  $divid = "flickr_".strtolower(str_replace(array(" ","-",","),"",$params['tag'])).'_'.rand(1,1000); //strip apostrophes, spaces and commas
  switch ($params['type']) {
    case "slideshow": {
        $link = empty($params['link'])?'next_slide(this);':("window.location='".$params['link']."';");
        $divstart = $attribution.'<div id="'.$divid.'" class="slickr-flickr-slideshow '.$params['orientation'].' '.$params['size'].'" onClick="'.$link.'">';
        $divend = "</div><script type='text/javascript'>jQuery('#".$divid."').data('delay','".$params['delay']."');</script>";
        break;
        }
   case "galleria": {
        $nav = <<<NAV
<p class="nav"><a href="#" onclick="jQuery.galleria.prev(); return false;">&laquo; previous</a> |
<a href="#" onclick="jQuery.galleria.startShow(); return false;">start</a> |
<a href="#" onclick="jQuery.galleria.stopShow(); return false;">stop</a> |
<a href="#" onclick="jQuery.galleria.next(); return false;">next &raquo;</a></p>
NAV;
        $divstart = $attribution.'<div id="'.$divid.'" class="slickr-flickr-galleria">'.$nav.'<ul>';
        $divend = '</ul></div><script type="text/javascript">jQuery("#'.$divid.'").data("delay","'.$params['delay'].'");</script><div style="clear:both;padding-top:10px;"></div>';
        break;
        }
   default: {
        $divstart = '<div id="'.$divid.'" class="slickr-flickr-gallery">'. $attribution . '<ul>';
        $divend = '</ul></div>';
        }
  }
  $striptag = strtolower(str_replace(" ","",$params['tag']));
  $flickr_feed= "http://api.flickr.com/services/feeds/photos_public.gne?lang=en-us&format=rss_200&".$id."=".$params['id']."&tagmode=".$tagmode."&tags=".$striptag;
  $rss = fetch_feed($flickr_feed);
  $numitems = $rss->get_item_quantity($params['items']);
  if ($numitems == 0)  return '<p>No photos available for '.$params['tag'].'</p>';
  $rss_items = $rss->get_items(0, $numitems);
  $r = -1;
  if ($numitems > 1) {
     if ($params['start'] == "random")
        $r = rand(1,$numitems);
     else
        $r = is_numeric($params['start']) && ($params['start'] < $numitems) ? $params['start'] : $numitems;
     }
  $s = "";
  $i = 0;
  if (!empty($params['sort'])) $rss_items = slickr_flickr_sort ($rss_items, $params['sort'], $params['direction']);
  foreach ( $rss_items as $item ) {
    $i++;
    $photo = flickr::find_photo($item);
    $title = flickr::cleanup($photo['title']);
    $description = flickr::cleanup($photo['description']);
    $oriented = $photo['orientation'];
    $full_url = $params['size']=="original" ? $photo['original'] : flickr::resize_photo($photo['url'], $params['size']);
    $thumb_url = flickr::resize_photo($photo['url'], "square");

    $imgsize="";
    if ($oriented != $params['orientation']) $imgsize = $oriented=="landscape"?'width="80%"':'height="90%"';
    $caption = $params['captions']=="off"?"":('<p class="slickr-flickr-caption">'.$title.'</p>');
    switch ($params['type']) {
       case "slideshow": {
            $s .=  '<div' . ($r==$i?' class="active"':'') .'><img '.$imgsize.' src="'.$full_url.'" alt="'.$title.'" />'.$caption.'</div>';
            break;
        }
       case "galleria": {
            $s .= '<li' . ($r==$i?' class="active"':'') .'><img '.$imgsize.' src="'.$full_url.'" alt="'.$title.'" title="'.$title.'"/></li>';
            break;
        }
        default: {
            $lightbox_title = $title;
            if ($params["flickr_link"]=="on") $lightbox_title = "<a title='Click to see photo on Flickr' href='". $photo["link"] . "'>".$lightbox_title."</a>";
            if ($params["descriptions"]=="on") $lightbox_title .= $description;

            $s .= '<li><a href="'.$full_url.'" title="'.$lightbox_title.'"><img src="'.$thumb_url.'" alt="'.$title.'" />'.$caption.'</a>&nbsp;</li>';
            if (($params['photos_per_row'] > 0) && ($i % $params['photos_per_row']== 0 )) $s .= "<br/>";
        }
    }
  }
  return $divstart . $s . $divend;
}

function slickr_flickr_sort ($items, $sort, $direction) {
	$do_sort = ($sort=="date") || ($sort=="title") || ($sort=="description");
    $direction = strtolower(substr($direction,0,3))=="des"?"descending":"ascending";
    if ($sort=="date") {
        //CHECK WE HAVE A DATE ON ALL ITEMS
        foreach ($items as $item) {
        if (!$item->get_date('U')) {
        	$do_sort = false;
        	break;
            }
        }
    }
	$ordered_items = $items;
	if ($do_sort) usort($ordered_items, 'sort_by_'.$sort.'_'.$direction);
    return $ordered_items;
}


function sort_by_description_descending($a, $b) {
    return strcmp($b->get_description(),$a->get_description());
}
function sort_by_description_ascending($a, $b) {
    return strcmp($a->get_description(),$b->get_description());
}

function sort_by_title_descending($a, $b) {
    return strcmp($b->get_title(),$a->get_title());
}
function sort_by_title_ascending($a, $b) {
    return strcmp($a->get_title(),$b->get_title());
}

function sort_by_date_ascending($a, $b) {
    return ($a->get_date('U') <= $b->get_date('U')) ? -1 : 1;
}

function sort_by_date_descending($a, $b) {
    return ($a->get_date('U') > $b->get_date('U')) ? -1 : 1;
}

function slickr_flickr_header() {
    if (!defined('SLICKR_FLICKR_PLUGIN_URL')) define ('SLICKR_FLICKR_PLUGIN_URL',WP_PLUGIN_URL . '/slickr-flickr');
    $path = SLICKR_FLICKR_PLUGIN_URL;

    $options = slickr_flickr_get_options();

    switch ($options['lightbox']) {
      case 'lightbox-slideshow': {
        wp_enqueue_script('lightbox-slideshow', $path."/lightbox-slideshow/lightbox-slideshow.js", array('jquery'));
        wp_enqueue_style('lightbox-slideshow-css', $path."/lightbox-slideshow/lightbox.css");
        break;
        }
    default: {
        wp_enqueue_script( 'lightbox', $path."/lightbox/js/jquery.lightbox-0.5.js", array('jquery'));
        wp_enqueue_style('lightbox-css', $path."/lightbox/css/jquery.lightbox-0.5.css");
        }
    }
    wp_enqueue_style('galleria-css', $path."/galleria/galleria.css");
    wp_enqueue_style('slickr-flickr-galleria-css', $path."/slickr-flickr-galleria.css");
    wp_enqueue_style('slickr-flickr-gallery-css', $path."/slickr-flickr-gallery.css");
    wp_enqueue_style('slickr-flickr-slideshow-css', $path."/slickr-flickr-slideshow.css");
    wp_enqueue_script('galleria', $path."/galleria/galleria.noconflict.js", array('jquery'));
    wp_enqueue_script('slickr-flickr-galleria', $path."/slickr-flickr-galleria.js", array('jquery','galleria'));
    wp_enqueue_script('slickr-flickr-gallery', $path."/slickr-flickr-gallery.js", array('jquery'));
    wp_enqueue_script('slickr-flickr-slideshow', $path."/slickr-flickr-slideshow.js", array('jquery'));
}

add_shortcode('slickr-flickr', 'slickr_flickr_display');
add_action('init', 'slickr_flickr_header');
add_filter('widget_text', 'do_shortcode', SHORTCODE_PRIORITY);
?>