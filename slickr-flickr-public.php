<?php
/**
 * Slickr Flickr
 *
 * Display a Flickr slideshow or a gallery in a post or widget
 *
 * @param id -> the Flickr ID of user
 * @param group -> set to y if the Flickr ID is the id of a group and not a user - default is n
 * @param use_key -> set to y to force use of API key - default is n
 * @param api_key -> 32 character alphanumeric API key
 * @param search -> photos, groups, friends, favorites, sets - default is photos
 * @param tag -> identifies what photos to select
 * @param tagmode -> set to ANY for fetching photos with different tags - default is ALL
 * @param set -> used in searching sets
 * @param items -> maximum number photos to display in the gallery or slideshow - default is 20
 * @param type -> gallery, galleria or slideshow - default is gallery
 * @param captions -> whether captions are on or off - default is on
 * @param delay -> delay in seconds between each image in the slideshow - default is 5
 * @param start -> first slide in the slideshow - default is 1
 * @param autoplay -> on or off - default is on
 * @param pause -> on or off - default is off 
 * @param orientation -> landscape or portrait - default is landscape
 * @param size -> small, medium, m640, small, large, original - default is medium
 * @param width -> width of slideshow
 * @param height -> height of slideshow
 * @param thumbnail_size -> square, thumbnail, small - default is square
 * @param thumbnail_scale -> scaling factor - default is 100 
 * @param photos_per_row -> maximum number number of thumbnails in a gallery row
 * @param border -> whether slideshow border is on or off - default is off
 * @param descriptions -> show descriptions beneath title caption - default is off
 * @param flickr_link -> include a link to the photo on Flickr on the lightbox - default is off
 * @param link -> url to visit on clicking slideshow
 * @param attribution -> credit the photographer
 * @param sort -> sort photos by date, title or description
 * @param direction -> sort ascending or descending 
*/
require_once(dirname(__FILE__).'/slickr-flickr-photo.php');

function slickr_flickr_display ($attr) {
  $params = shortcode_atts( slickr_flickr_get_options(), $attr ); //apply plugin defaults
  if (($params['type']=="gallery") && ($attr['captions']!="on")) $params['captions'] = "off";
  if (empty($params['id'])) return "<p>Please specify a Flickr ID for this ".$params['type']."</p>";
  if (empty($params['api_key']) && ($params['use_key'] == "y")) return "<p>Please add your Flickr API Key in Slickr Flickr Admin settings to fetch more than 20 photos.</p>";
  if (($params['items'] > 20 ) && (! empty($params['api_key'])) && (empty($params['use_key']))) $params['use_key'] = "y"; // default use_key if request for over 20 photos and API key is present
  if ( (!empty($params['tagmode'])) && empty($params['tag']) && ($params['search']=="photos")) return "<p>Please set up a Flickr tag for this slideshow</p>";

  $rand_id = rand(1,1000);
  $divid = "flickr_".strtolower(str_replace(array(" ","`","-",","),"",$params['tag'])).'_'.$rand_id; //strip spaces, backticks, dashes and commas
  $divclear = '<div style="clear:both"></div>';
  $attribution = empty($params['attribution'])?"":('<p class="slickr-flickr-attribution align'.$params['align'].'">'.$params['attribution'].'</p>');
  $lightboxrel =""; $thumb_scale ="";
  switch ($params['type']) {
    case "slightbox": {
        if (empty($params['thumbnail_size'])) $params['thumbnail_size'] = 'medium'; //set default slideshow as Medium
        slickr_flickr_set_thumbnail_params($params);
        slickr_flickr_set_lightboxrel($params,$rand_id);
        }
   case "slideshow": {
        $scriptdelay = '<script type="text/javascript">jQuery("#'.$divid.'").data("delay","'.$params['delay'].'");jQuery("#'.$divid.'").data("autoplay","'.$params['autoplay'].'");</script>';
        $divstart = $attribution.'<div id="'.$divid.'"'. slickr_flickr_set_slideshow_style($params) .' class="slickr-flickr-slideshow '.$params['orientation'].' '.$params['size'].($params['descriptions']=="on" ? " descriptions" : "").'" '. slickr_flickr_set_slideshow_onclick($params) . '>';
        $divend = '</div>'.$divclear.$scriptdelay;
        $element='div';
        $element_style='';
        break;
        }
   case "galleria": {
        $scriptdelay = '<script type="text/javascript">jQuery("#'.$divid.'").data("delay","'.$params['delay'].'");jQuery("#'.$divid.'").data("autoplay","'.$params['autoplay'].'");jQuery("#'.$divid.'").data("captions","'.$params['captions'].'");jQuery("#'.$divid.'").data("descriptions","'.$params['descriptions'].'");</script>';
        $nav = <<<NAV
<p class="nav {$params['size']}"><a href="#" class="prevSlide">&laquo; previous</a> | <a href="#" class="startSlide">start</a> | <a href="#" class="stopSlide">stop</a> | <a href="#" class="nextSlide">next &raquo;</a></p>
NAV;
        $divstart = '<div id="'.$divid.'" class="slickr-flickr-galleria '.$params['orientation'].' '.$params['size'].'">'.$attribution.$nav.'<ul>';
        $divend = '</ul>'.$divclear.$attribution.$nav.'</div>'.$scriptdelay;
        $element='li';
        $element_style='';
        break;
        }
   default: {
        slickr_flickr_set_thumbnail_params($params);
        slickr_flickr_set_lightboxrel($params,$rand_id);
        $divstart = '<div id="'.$divid.'" class="slickr-flickr-gallery">'. $attribution . '<ul'.$params['gallery_style'].'>';
        $scriptdelay = '<script type="text/javascript">jQuery("#'.$divid.'").data("delay","'.$params['delay'].'");jQuery("#'.$divid.'").data("autoplay","'.$params['autoplay'].'");</script>';
        $divend = '</ul></div>'.$divclear.($params['lightbox'] == "sf-lbox-auto" ? $scriptdelay : "");
        $element='li';
        $element_style = $params['thumbnail_style'];
        }
  }
  $photos = slickr_flickr_feed($params);
  if (! is_array($photos)) return $photos; //return error message if an array of photos is not returned

  $r = slickr_flickr_get_start($params, count($photos));
  $s = "";
  $i = 0;
  foreach ( $photos as $photo ) {
    $i++;
    $s .= '<'.$element.$element_style.($r==$i?' class="active"':'').'>'.slickr_flickr_image($photo, $params).'</'.$element.'>';
  }
  return $divstart . $s . $divend;
}


function slickr_flickr_feed($params) {
  $photos = array();
  if ($params['cache']=='off') slickr_flickr_check_clear_cache();
  $multi_fetch = slickr_flickr_set_fetch_mode($params);
  $striptag = strtolower(str_replace(" ","",$params['tag']));
  $tags = empty($striptag) ? "" : ("&tags=".$striptag);
  $group = strtolower(substr($params['group'],0,1));
  if ($params['use_key'] == 'y') {
        switch($params['search']) {
           case "favorites": {
                $flickr_feed = "http://api.flickr.com/services/rest/?method=flickr.favorites.getPublicList&lang=en-us&format=feed-rss_200&api_key=".$params['api_key']."&user_id=".$params['id'];
                break;
          }
           case "friends": {
                return '<p>Api key based search is not available for friends photos in this release of Slickr Flickr</p>';
                break;
           }
           case "groups": {
                $flickr_feed = "http://api.flickr.com/services/rest/?method=flickr.groups.pools.getPhotos&lang=en-us&format=feed-rss_200&api_key=".$params['api_key']."&group_id=".$params['id'];
                break;
           }
           case "galleries": {
                $flickr_feed = "http://api.flickr.com/services/rest/?method=flickr.galleries.getPhotos&lang=en-us&format=feed-rss_200&api_key=".$params['api_key']."&gallery_id=".$params['id'];
                break;
           }
           case "sets": {
                return '<p>Api key based search is not available for photosets in this release of Slickr Flickr</p>';
                break;
           }
          default: {
                $tagmode = empty($params['tagmode']) ? "" : ("&tag_mode=".($params['tagmode']=="all"?"all":"any"));
                $id = $group=="y" ? "group_id" : "user_id" ;
                $flickr_feed = "http://api.flickr.com/services/rest/?method=flickr.photos.search&lang=en-us&format=feed-rss_200&api_key=".$params['api_key']."&".$id."=".$params['id'].$tagmode.$tags;
          }
       }
   } else {
        switch($params['search']) {
           case "favorites": {
                $flickr_feed = "http://api.flickr.com/services/feeds/photos_faves.gne?lang=en-us&format=rss_200&nsid=".$params['id']; break;
           }
           case "groups": {
                $flickr_feed = "http://api.flickr.com/services/feeds/groups_pool.gne?lang=en-us&format=feed-rss_200&id=".$params['id'];  break;
                break;
           }
           case "friends": {
                $flickr_feed = "http://api.flickr.com/services/feeds/photos_friends.gne?lang=en-us&format=feed-rss_200&user_id=".$params['id']."&display_all=1";  break;
                break;
           }
           case "sets": {
                $set = empty($params['set']) ? $params['tag'] : $params['set'];
                $flickr_feed = "http://api.flickr.com/services/feeds/photoset.gne?lang=en-us&format=feed-rss_200&nsid=".$params['id']."&set=".$set;  break;
                break;
           }
           default: {
                $tagmode = empty($params['tagmode']) ? "" : ("&tagmode=".($params['tagmode']=="any"?"any":"all"));
                $id = $group=="y" ? "g" : "id" ;
                $flickr_feed = "http://api.flickr.com/services/feeds/photos_public.gne?lang=en-us&format=feed-rss_200&".$id."=".$params['id'].$tagmode.$tags;
           }
        }
   }

  if ($multi_fetch) {
    $more_photos = true;
    $total_photos = 0;
    $page=0;
    $per_page=min(50,$params['items']);
    $flickr_feed .= "&per_page=".$per_page."&page=##PAGE##";
    while ($more_photos) {
        $page++;
        $rss = fetch_feed(str_replace('##PAGE##',$page,$flickr_feed));
        if ( is_wp_error($rss) ) return "<p>Error fetching Flickr photos: ".$rss->get_error_message()."</p>";  //exit if cannot fetch the feed
        $numitems = $rss->get_item_quantity($per_page);
        if ($numitems == 0)  {
            if ($total_photos == 0) return '<p>No photos available right now.</p><p>Please verify your settings, clear your RSS cache on the Slickr Flickr Admin page and check your <a target="_blank" href="'.$flickr_feed.'">Flickr feed</a></p>';
            $more_photos = false;
        }
        if ($numitems < $per_page) $more_photos = false;
        $rss_items = $rss->get_items(0, $numitems);
        foreach ( $rss_items as $item ) {
            $photos[] = new slickr_flickr_photo($item);
            $total_photos++;
            if ($total_photos >= $params['items']) { $more_photos = false;  break; }
        }
    }
  } else {
    $rss = fetch_feed($flickr_feed);
    if ( is_wp_error($rss) ) return "<p>Error fetching Flickr photos: ".$rss->get_error_message()."</p>";  //exit if cannot fetch the feed

    $numitems = $rss->get_item_quantity($params['items']);
    if ($numitems == 0)  return '<p>No photos available right now.</p><p>Please verify your settings, clear your RSS cache on the Slickr Flickr Admin page and check your <a target="_blank" href="'.$flickr_feed.'">Flickr feed</a></p>';
    $rss_items = $rss->get_items(0, $numitems);
    foreach ( $rss_items as $item ) {
        $photos[] = new slickr_flickr_photo($item);
    }
  }
  if (!empty($params['sort'])) $photos = slickr_flickr_sort ($photos, $params['sort'], $params['direction']);
  return $photos; //return array of photos
}

function slickr_flickr_check_clear_cache() {
  if (slickr_flickr_check_validity()) slickr_flickr_clear_cache();
}

function slickr_flickr_set_fetch_mode($params) {
  return ($params['use_key']=='y') && ($params['items'] > 50) && (slickr_flickr_check_validity()) ;
}

function slickr_flickr_set_slideshow_style($params) {
  if ((($params['width']) || ($params['height'])) && (slickr_flickr_check_validity())) {
    $width = $params['width'] ? (' width:'.$params['width'].'px;'):'';
    $height = $params['height'] ? (' height:'.$params['height'].'px;'):'';
    $overflow = 'overflow-y:hidden;overflow-x:hidden';
    return ' style="'.$width.$height.$overflow.'"';
  } else {
    return '';
  }
}

function slickr_flickr_set_slideshow_onclick($params) {
  if (empty($params['link']))
    if ($params['pause'] == "on")
        $link = "slickr_flickr_toggle_slideshows()" ;
     else
        $link = (($params['type'] == "slightbox") && slickr_flickr_check_validity()) ? "" : "slickr_flickr_next_slide(this)";
  else
    $link = "window.location='".$params['link']."'";
  return empty($link) ? "": ('onClick="'.$link.';"') ;
}


function slickr_flickr_set_lightboxrel(&$params, $rand_id) {
    switch ($params['lightbox']) {
      case "shadowbox": $lightboxrel = 'rel="shadowbox['.$rand_id.']"'; break;
      case "thickbox": $lightboxrel = 'rel="thickbox['.$rand_id.']" class="thickbox" '; break;
      case "colorbox":
      case "slimbox":
      case "shutter":   $lightboxrel = 'rel="lightbox['.$rand_id.']"';  break;
      default: $lightboxrel = 'rel="'.$params['lightbox'].'"';
      }
    $params['lightboxrel'] = $lightboxrel;
}

function slickr_flickr_set_thumbnail_params(&$params) {
    $thumb_rescale= false;
    if (($params['type'] == "slightbox") && (! slickr_flickr_check_validity())) { $params['size'] = $params["thumbnail_size"]; $params['type'] = "slideshow"; }
    switch ($params["thumbnail_size"]) {
      case "thumbnail": $thumb_width = 100; $thumb_height = 75; $thumb_rescale = true; break;
      case "small": $thumb_width = 240; $thumb_height = 180; $thumb_rescale = true; break;
      case "medium": $thumb_width = 500; $thumb_height = 375; $thumb_rescale = true; break;
      case "m640": $thumb_width = 640; $thumb_height = 480; $thumb_rescale = true; break;
      default: $thumb_width = 75; $thumb_height = 75; $params["thumbnail_size"] = 'square';
    }
    if ($params["orientation"]=="portrait" ) { $swp = $thumb_width; $thumb_width = $thumb_height; $thumb_height = $swp; }

    if ($params["thumbnail_scale"] > 0) {
        $thumb_rescale = true;
        $thumb_width = round($thumb_width * $params["thumbnail_scale"] / 100);
        $thumb_height = round($thumb_height * $params["thumbnail_scale"] / 100);
    }
    $params['thumbnail_dimensions'] = $thumb_rescale ? (' width="'.$thumb_width.'" height="'.$thumb_height.'"') : '';

    if (($params['type'] == "gallery") && ($params['photos_per_row'] > 0)) {
        $li_width = ($thumb_width + 15);
        $gallery_width = 1 + ($li_width *  $params['photos_per_row']);
        $params['gallery_style'] = ' style="width:'.$gallery_width.'px"';
        $params['thumbnail_style'] = ' style="width:'.$li_width.'px"';
    } else {
        $params['gallery_style'] = '';
        $params['thumbnail_style'] = '';
    }
}

function slickr_flickr_image($photo, $params) {
    $title = $photo->get_title();
    $description = $photo->get_description();
    $link = $photo->get_link();
    $oriented = $photo->get_orientation();
    $full_url = $params['size']=="original" ? $photo->get_original() : $photo->resize($params['size']);
    $thumb_url = $photo->resize($params['thumbnail_size']);
    $captiontitle = $params["flickr_link"]=="on"?("<a title='Click to see photo on Flickr' href='". $link . "'>".$title."</a>"):$title;
    $alt = $params["descriptions"]=="on"? $description : "";
    $border = $params['border']=='on'?' class="border"':'';
    $imgsize="";
    if ($oriented != $params['orientation']) $imgsize = $oriented=="landscape"?'width="80%"':'height="90%"';
    switch ($params['type']) {
       case "slideshow": {
            $caption = $params['captions']=="off"?"":('<p'.$border.'>'.$captiontitle.'</p>'.$alt);
            return '<img '.$imgsize.$border.' src="'.$full_url.'" alt="'.$alt.'" title="'.$title.'" />'.$caption;
        }
       case "slightbox": {
            $desc = $params["descriptions"]=="on" || $params["descriptions"]=="slideshow" ? $description : "";
            $alt = $params["descriptions"]=="on" || $params["descriptions"]=="lightbox" ? $description : "";
            $caption = $params['captions']=="off"?"":('<p'.$border.'>'.$captiontitle.'</p>'.$desc);
            $lightbox_title = $captiontitle . $alt;
            return '<a '.$params['lightboxrel'].' href="'.$full_url.'" title="'.$lightbox_title.'"><img '.$imgsize.$border.' src="'.$thumb_url.'"'.$params['thumbnail_dimensions'].' alt="'.$alt.'" title="'.$title.'" /></a>'.$caption;
        }
       case "galleria": {
            return '<img '.$imgsize.' src="'.$full_url.'" alt="'.$alt.'" title="'.$title.'" />';
        }
        default: {
            $thumbcaption = $params['captions']=="off"?"":('<br/><span class="slickr-flickr-caption">'.$title.'</span>');
            $lightbox_title = $captiontitle . ($params["descriptions"]=="on" ? $description : "");
            return '<a '.$params['lightboxrel'].' href="'.$full_url.'" title="'.$lightbox_title.'"><img src="'.$thumb_url.'"'.$params['thumbnail_dimensions'].' alt="'.$alt.'" title="'.$title.'" />'.$thumbcaption.'</a>';
        }
    }
}

function slickr_flickr_get_start($params,$numitems) {
  $r = -1;
  if ($numitems > 1) {
     if ($params['start'] == "random")
        $r = rand(1,$numitems);
     else
        $r = is_numeric($params['start']) && ($params['start'] < $numitems) ? $params['start'] : $numitems;
     }
   return $r;
}

function slickr_flickr_sort ($items, $sort, $direction) {
	$do_sort = ($sort=="date") || ($sort=="title") || ($sort=="description");
    $direction = strtolower(substr($direction,0,3))=="des"?"descending":"ascending";
    if ($sort=="date") { foreach ($items as $item) { if (!$item->get_date()) { $do_sort = false; break; } } }
    if ($sort=="description") { foreach ($items as $item) { if (!$item->get_description()) { $do_sort = false; break; } } }
    $ordered_items = $items;
    if ($do_sort) usort($ordered_items, 'sort_by_'.$sort.'_'.$direction);
    return $ordered_items;
}

function sort_by_description_descending($a, $b) { return strcmp($b->get_description(),$a->get_description()); }
function sort_by_description_ascending($a, $b) { return strcmp($a->get_description(),$b->get_description()); }
function sort_by_title_descending($a, $b) { return strcmp($b->get_title(),$a->get_title()); }
function sort_by_title_ascending($a, $b) { return strcmp($a->get_title(),$b->get_title()); }
function sort_by_date_ascending($a, $b) { return ($a->get_date() <= $b->get_date()) ? -1 : 1; }
function sort_by_date_descending($a, $b) { return ($a->get_date() > $b->get_date()) ? -1 : 1; }

function slickr_flickr_header() {

    $path = SLICKR_FLICKR_PLUGIN_URL;

    $options = slickr_flickr_get_options();

    switch ($options['lightbox']) {
     case 'sf-lbox-manual': {
        wp_enqueue_style('lightbox', $path."/lightbox/css/jquery.lightbox-0.5.css");
        wp_enqueue_script('lightbox', $path."/lightbox/js/jquery.lightbox-0.5.js", array('jquery'));
        }
     case 'sf-lbox-auto': {
        wp_enqueue_style('lightbox', $path."/lightbox-slideshow/lightbox.css");
        wp_enqueue_script('lightbox', $path."/lightbox-slideshow/lightbox-slideshow.js", array('jquery'));
        break;
        }
     case 'shadowbox': {
        wp_enqueue_style('shadowbox', $path."/shadowbox/shadowbox.css");
        wp_enqueue_script('shadowbox', $path."/shadowbox/shadowbox.js", array('jquery'));
        }
    case 'thickbox': { //preinstalled by wordpress but needs to be activated
       wp_enqueue_style('thickbox');
       wp_enqueue_script('thickbox');
       break;
    }
    default: { break; } //use another lightbox plugin such as fancybox, shutter, colorbox
    }
    wp_enqueue_style('galleria', $path."/galleria/galleria.css");
    wp_enqueue_style('slickr-flickr', $path."/slickr-flickr.css");
    wp_enqueue_script('galleria', $path."/galleria/galleria.noconflict.js", array('jquery'));
    wp_enqueue_script('slickr-flickr', $path."/slickr-flickr.js", array('jquery','galleria'));
}

add_shortcode('slickr-flickr', 'slickr_flickr_display');
add_action('init', 'slickr_flickr_header');
add_filter('widget_text', 'do_shortcode', 11);
?>