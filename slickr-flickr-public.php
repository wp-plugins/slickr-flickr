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
 * @param license -> used to filter photos according to the license, 1-7
 * @param date_type -> (date) taken or upload 
 * @param date -> get photos for this date
 * @param after -> get photos on or after this date
 * @param before -> get photos on or before this date
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
 * @param thumbnail_size -> square, thumbnail, small - default is square
 * @param thumbnail_scale -> scaling factor - default is 100
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
*/
require_once(dirname(__FILE__).'/slickr-flickr-feed.php');
require_once(dirname(__FILE__).'/slickr-flickr-photo.php');
require_once(dirname(__FILE__).'/slickr-flickr-api-photo.php');

function slickr_flickr_display ($attr) {
  $params = shortcode_atts( slickr_flickr_get_options(), $attr ); //apply plugin defaults    
  foreach ( $params as $k => $v ) if (($k != 'id') && ($k != 'options') && ($k != 'attribution')) $params[$k] = strtolower($v); //set all params as lower case
  if (empty($params['id'])) return "<p>Please specify a Flickr ID for this ".$params['type']."</p>";
  if ( (!empty($params['tagmode'])) && empty($params['tag']) && ($params['search']=="photos")) return "<p>Please set up a Flickr tag for this ".$params['type']."</p>";
  if (empty($params['api_key']) && ($params['use_key'] == "y")) return "<p>Please add your Flickr API Key in Slickr Flickr Admin settings to fetch more than 20 photos.</p>";
  if (empty($params['use_key'])) slickr_flickr_force_api_key($params); //set api_key if required by other parameters

  $rand_id = rand(1,1000);
  $divid = "flickr_".strtolower(preg_replace("{[^A-Za-z0-9_]}","",$params['tag'])).'_'.$rand_id; //strip spaces, backticks, dashes and commas
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
        $divstart = $attribution.'<div id="'.$divid.'" class="slickr-flickr-slideshow '.$params['orientation'].' '.$params['size'].($params['descriptions']=="on" ? " descriptions" : "").' '.$params['align'].'">';
        $divend = '</div>'.$divclear;
        $element='div';
        $element_style='';
        slickr_flickr_set_options($divid,slickr_flickr_slideshow_options($params));
        break;
        }
   case "galleria": {
        $nav = <<<NAV
<p class="nav {$params['size']}"><a href="#" class="prevSlide">&laquo; previous</a> | <a href="#" class="startSlide">start</a> | <a href="#" class="stopSlide">stop</a> | <a href="#" class="nextSlide">next &raquo;</a></p>
NAV;
		switch ($params['nav']) {
			case "above": { $nav_below = ''; $nav_above = $nav; break; }
			case "below": { $nav_below = $nav; $nav_above = ''; break; }
			case "none": { $nav_below = ''; $nav_above = ''; break; } 	
			default: { $nav_below = $nav; $nav_above = $nav; break; }
		}
        $divstart = '<div id="'.$divid.'" class="slickr-flickr-galleria '.$params['orientation'].' '.$params['size'].'">'.$attribution.$nav_above.'<ul>';
        $divend = '</ul>'.$divclear.$attribution.$nav_below.'</div>';
        $element='li';
        $element_style='';
        slickr_flickr_set_options($divid,slickr_flickr_galleria_options($params));
        break;
        }
   default: {
        slickr_flickr_set_thumbnail_params($params);
        slickr_flickr_set_lightboxrel($params,$rand_id);
        $divstart = '<div id="'.$divid.'" class="slickr-flickr-gallery">'. $attribution . '<ul'.$params['gallery_class'].$params['gallery_style'].'>';
        $divend = '</ul></div>'.$divclear;
        $element='li';
        $element_style = $params['thumbnail_style'];
        slickr_flickr_set_options($divid,slickr_flickr_lightbox_options($params));
        }
  }
  $photos = slickr_flickr_fetch_feed($params);
  if (! is_array($photos)) return $photos; //return error message if an array of photos is not returned

  $r = slickr_flickr_get_start($params, count($photos));
  $s = "";
  $i = 0;
  if (empty($element)) {
    foreach ( $photos as $photo ) $s .= slickr_flickr_image($photo, $params);
  } else {
    foreach ( $photos as $photo ) {
      $i++;
      $s .= '<'.$element.$element_style.($r==$i?' class="active"':'').'>'.slickr_flickr_image($photo, $params).'</'.$element.'>';
    }
  }
  return $divstart . $s . $divend;
}

function slickr_flickr_fetch_feed($params) {
   $photos = slickr_flickr_fetch($params);
   if ((! is_array($photos)) && ($params['use_key']=='y') && slickr_flickr_check_validity() && !slickr_flickr_api_required($params)) {
   		$params['use_key']='n';
   		return slickr_flickr_fetch($params);
   	} else {
   		return $photos;
   	}
}

function slickr_flickr_fetch($params) {
  $photos = array();
  if ($params['cache']=='off') slickr_flickr_check_clear_cache();
  $multi_fetch = slickr_flickr_set_fetch_mode($params);
  if (!array_key_exists('per_page',$params)) $params['per_page'] = min(50,$params['items']);
  $feed = new slickr_flickr_feed($params);
  $page=0;
  if ($multi_fetch) {
    $more_photos = true;
    while ($more_photos) {
        $page++;
        $feed->fetch_photos($page);  //fetch the photos
        if ($feed->get_count() > 0)  $photos = array_merge($photos,$feed->get_photos()); 
        if ($feed->get_count() < $params['per_page']) $more_photos = false;  
        if (count($photos) >= $params["items"]) $more_photos = false;
     }  
     if (count($photos) >= $params["items"])  $photos = array_slice($photos,0,$params["items"]);
  } else {
        $photos = $feed->fetch_photos($params['page']);
  }
  if ($feed->is_error()) return $feed->get_message();
  if (!empty($params['sort'])) $photos = slickr_flickr_sort ($photos, $params['sort'], $params['direction']);
  return $photos; //return array of photos
}


function slickr_flickr_force_api_key(&$params) {
  if ((empty($params['use_key'])) 
  && (! empty($params['api_key'])) 
  && (($params['items'] > 20 ) || slickr_flickr_api_required($params))) 
   	$params['use_key'] = "y"; // set use_key if API key is available and is either required or request is for over 20 photos
}

function slickr_flickr_api_required($params) {
	return (! empty($params['license'])) || (! empty($params['date'])) || (! empty($params['before'])) || (! empty($params['after']))
		|| ($params['page'] > 1) || ( !empty($params['tag']) && ($params["search"]=="groups")); 
}

function slickr_flickr_check_clear_cache() {
  if (slickr_flickr_check_validity()) slickr_flickr_clear_cache();
}

function slickr_flickr_set_fetch_mode($params) {
  return ($params['use_key']=='y') && ($params['items'] > 50) && (slickr_flickr_check_validity()) ;
}

function slickr_flickr_set_slideshow_onclick($params) {
  $link='';
  if (empty($params['link']))
    if ($params['pause'] == "on")
        $link = "toggle" ;
     else
        $link = (($params['type'] == "slightbox") && slickr_flickr_check_validity()) ? "" : "next";
  else
    $link = $params['link'];
  return $link;
}

function slickr_flickr_set_lightboxrel(&$params, $rand_id) {
    switch ($params['lightbox']) {
      case "shadowbox": $lightboxrel = 'rel="shadowbox['.$rand_id.']"'; break;
      case "thickbox": $lightboxrel = 'rel="thickbox['.$rand_id.']" class="thickbox" '; break;
      case "evolution": $lightboxrel = 'rel="lightbox'.$rand_id.'" class="lightbox" '; break;
      case "fancybox":   $lightboxrel = 'rel="fancybox_'.$rand_id.'" class="fancybox" ';  break;
      case "prettyphoto": $lightboxrel = 'rel="wp-prettyPhoto-'.$rand_id.'"' ; break;      
      case "colorbox":
      case "slimbox":
      case "shutter":   $lightboxrel = 'rel="lightbox['.$rand_id.']"';  break;
      case "sf-lbox-manual":
      case "sf-lbox-auto": $lightboxrel = 'rel="sf-lightbox"' ; break;
      case "none":
      case "norel": $lightboxrel = '' ; break;      
      default: 	$lightboxrel = 'rel="'.$params['lightbox'].'_'.$rand_id.'"';
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
    $params['image_style'] = $thumb_rescale ? (' style="height:'.$thumb_height.'px; max-width:'.$thumb_width.'px;"') : '';

    if (($params['type'] == "gallery") && ($params['photos_per_row'] > 0)) {
        $li_width = ($thumb_width + 10);
        $li_height = ($thumb_height + 10);
        $gallery_width = 1 + (($li_width + 4) *  $params['photos_per_row']);
        $params['gallery_style'] = ' style=" width:'.$gallery_width.'px"';
        $params['thumbnail_style'] = ' style="width:'.$li_width.'px; height:'.$li_height.'px;"';
    } else {
        $params['gallery_style'] = '';
        $params['thumbnail_style'] = '';
    }
    $params['gallery_class'] = $params['align'] ? (' class="'.$params['align'].'"'):'';
}

function slickr_flickr_image($photo, $params) {
    $title = $photo->get_title();
    $description = $photo->get_description();
    if ($description == '<p></p>') $description = '';
    $link = $photo->get_link();
    $oriented = $photo->get_orientation();
    $full_url = $params['size']=="original" ? $photo->get_original() : $photo->resize($params['size']);
    $thumb_url = $photo->resize($params['thumbnail_size']);
    $captiontitle = $params["flickr_link"]=="on"?("<a title='Click to see photo on Flickr' href='". $link . "'>".$title."</a>"):$title;
    $alt = $params["descriptions"]=="on"? $description : "";
    $border = $params['border']=='on'?' class="border"':'';
    switch ($params['type']) {
       case "slideshow": {
            $caption = $params['captions']=="off"?"":('<p'.$border.'>'.$captiontitle.'</p>'.$alt);
            return '<img '.$border.' src="'.$full_url.'" alt="'.$alt.'" title="'.$title.'" />'.$caption;
        }
       case "slightbox": {
            $desc = $params["descriptions"]=="on" || $params["descriptions"]=="slideshow" ? $description : "";
            $alt = $params["descriptions"]=="on" || $params["descriptions"]=="lightbox" ? $description : "";
            $caption = $params['captions']=="off"?"":('<p'.$border.'>'.$captiontitle.'</p>'.$desc);
            $lightbox_title = $captiontitle . $alt;
            return '<a '.$params['lightboxrel'].' href="'.$full_url.'" title="'.$lightbox_title.'"><img '.$imgsize.$border.' src="'.$thumb_url.'"'.$params['thumbnail_dimensions'].' alt="'.$alt.'" title="'.$title.'" /></a>'.$caption;
        }
       case "galleria": {
            return '<img src="'.$full_url.'" alt="'.$alt.'" title="'.$captiontitle.'" /></a>';
            //return '<a href="'.$full_url.'"><img src="'.$thumb_url.'" alt="'.$alt.'" title="'.$captiontitle.'" />';
        }
        default: {
        	if ($params['lightbox']=="none") $full_url = $link; 
            $thumbcaption = $params['thumbnail_captions']=="on"?('<br/><span class="slickr-flickr-caption">'.$title.'</span>'):"";
            $lightbox_title = ($params["captions"]=="on" ? $captiontitle :"") . ($params["descriptions"]=="on" ? $description : "");
            return '<a '.$params['lightboxrel'].' href="'.$full_url.'" title="'.$lightbox_title.'"><img src="'.$thumb_url.'"'.$params['image_style'].' alt="'.$alt.'" title="'.$title.'" />'.$thumbcaption.'</a>';
        }
    }
}

function slickr_flickr_get_start($params,$numitems) {
  $r = 1;
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

function slickr_flickr_slideshow_options($params) {
    $options['delay'] = $params['delay'] * 1000;
    $options['autoplay'] = $params['autoplay']=="off"?false:true;
    $options['transition'] = 500;
    $options['link'] = slickr_flickr_set_slideshow_onclick($params);
    $options['target'] = $params['target'];    
    if (slickr_flickr_check_validity()) {
    	if ($params['width']) $options['width'] = $params['width'];
    	if ($params['height']) $options['height'] = $params['height'];
    	if ($params['transition']) $options['transition'] = $params['transition'] * 1000;    	
    }
	return $options;
}

function slickr_flickr_lightbox_options($params) {
    $options = array();
    if ($params['lightbox'] == "sf-lbox-auto") {
    	$options['nextSlideDelay'] = $params['delay'] * 1000;
    	$options['autoPlay'] = $params['autoplay']=="on"?true:false;
	}
    if (array_key_exists('thumbnail_border',$params) && (!empty($params['thumbnail_border'])) && slickr_flickr_check_validity()) $options['border'] = $params['thumbnail_border']; 
	return $options;
}

function slickr_flickr_galleria_options($params) {
   
	if ($params['galleria'] == 'galleria_12') {
		if (empty($params['options'])) {
    	    $options['autoplay'] = $params['delay']*1000; 
    	    $options['transition'] = 'fade';
    	    $options['transition_speed'] = $params['transition']*1000;
    	    $options['show_info'] = $params['captions']=='on' ? true: false;
    	    $options['image_crop'] = true;
    	    $options['carousel'] = true;
		} else {
	    	$options = str_replace(';;',';',trim($params['options']).';');
			if ((preg_match_all("/([^:\s]+):([^;]+);/i", $options, $pairs)) && (count($pairs)>2)) $options = array_combine($pairs[1], $pairs[2]);
    	}
	} else {
		$options['delay'] = $params['delay'] * 1000;
		$options['autoPlay'] = $params['autoplay']=="on"?true:false;
		$options['captions'] = $params['captions']=="on"?true:false;
		$options['descriptions'] = $params['descriptions']=="on"?true:false;
    }
	return $options;
}

function slickr_flickr_set_options($divid, $options) {
    global $slickr_flickr_jquery_data; //array of slickr flickr config data
    if (count($options) > 0) $slickr_flickr_jquery_data[] = 'jQuery("#'.$divid.'").data("options",'.json_encode($options).');'; 
}

global $slickr_flickr_jquery_data; 
$slickr_flickr_jquery_data = array();

function slickr_flickr_start() {
	global $slickr_flickr_jquery_data; 
    echo ('<script type="text/javascript">'."\r\n");
    echo ('jQuery.noConflict(); jQuery(document).ready(function() {'."\r\n");
    if (count($slickr_flickr_jquery_data)>0) foreach ($slickr_flickr_jquery_data as $data) echo ($data."\r\n");
    echo("slickr_flickr_start();\r\n");
    echo ("});\r\n");
 	echo ("</script>\r\n");
}

function slickr_flickr_init() {
    global $slickr_flickr_jquery_data; 
    $slickr_flickr_jquery_data= array(); //initialize jquery config

    $path = SLICKR_FLICKR_PLUGIN_URL;

    $options = slickr_flickr_get_options();

    $deps = array('jquery');
    switch ($options['lightbox']) {
     case 'sf-lbox-manual': {
        wp_enqueue_style('lightbox', $path."/lightbox/css/jquery.lightbox-0.5.css","0.5");
        wp_enqueue_script('lightbox', $path."/lightbox/js/jquery.lightbox-0.5.js", array('jquery'),SLICKR_FLICKR_VERSION,true);
        $deps[] = 'lightbox';
        }
     case 'sf-lbox-auto': {
        wp_enqueue_style('lightbox', $path."/lightbox-slideshow/lightbox.css");
        wp_enqueue_script('lightbox', $path."/lightbox-slideshow/lightbox-slideshow.js", array('jquery'),SLICKR_FLICKR_VERSION,true);
        $deps[] = 'lightbox';
        break;
        }
    case 'thickbox': { //preinstalled by wordpress but needs to be activated
       wp_enqueue_style('thickbox');
       wp_enqueue_script('thickbox');
       $deps[] = 'thickbox';
 	   break;
    }
    default: { break; } //use another lightbox plugin such as fancybox, shutter, colorbox
    }
     
    if ($options['galleria'] =="galleria_12") {
    	wp_enqueue_style('galleria-classic', $path."/galleria12/galleria.classic.css","1.2");
    	wp_enqueue_script('galleria', $path."/galleria12/galleria.js", array('jquery'),"1.2",true);
    	wp_enqueue_script('galleria-classic', $path."/galleria12/galleria.classic.js", array('galleria'),"1.2",true);    
        $deps[] = 'galleria';
    } else {
    	wp_enqueue_style('galleria', $path."/galleria/galleria.css");
    	wp_enqueue_script('galleria', $path."/galleria/galleria.noconflict.js", array('jquery'),SLICKR_FLICKR_VERSION,true);
        $deps[] = 'galleria';
	}
    wp_enqueue_style('slickr-flickr', $path."/slickr-flickr.css",SLICKR_FLICKR_VERSION);
    wp_enqueue_script('slickr-flickr', $path."/slickr-flickr.js", $deps,SLICKR_FLICKR_VERSION,true);
}

add_action('init', 'slickr_flickr_init');
add_filter('print_footer_scripts', 'slickr_flickr_start');
add_filter('widget_text', 'do_shortcode', 11);
add_shortcode('slickr-flickr', 'slickr_flickr_display');
?>
