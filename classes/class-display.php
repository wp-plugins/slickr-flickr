<?php

class Slickr_Flickr_Display {

	private $pages = 1;
	private $id;

	function __construct() {}

	function show($attr) {
		Slickr_Flickr_Public::note_active();	
  		$params = shortcode_atts( Slickr_Flickr_Utils::get_options(), $attr ); //apply plugin defaults    
  		foreach ( $params as $k => $v ) if (($k != 'id') && ($k != 'options') && ($k != 'galleria_options') && ($k != 'attribution') && ($k != 'flickr_link_title')) $params[$k] = strtolower($v); //set all params as lower case
  		$params['tag'] = str_replace(' ','',$params['tag']);
		if (strpos($params['tag'],',-') !==FALSE) $params['tagmode'] = 'bool';
  		if (empty($params['id'])) return "<p>Please specify a Flickr ID for this ".$params['type']."</p>";
  		if ( (!empty($params['tagmode'])) && empty($params['tag']) && ($params['search']=="photos")) return "<p>Please set up a Flickr tag for this ".$params['type']."</p>";
  		if (empty($params['api_key']) && ($params['use_key'] == "y")) return "<p>Please add your Flickr API Key in Slickr Flickr Admin settings to fetch more than 20 photos.</p>";
		$this->set_api_required($params);
  		if (empty($params['use_key'])) $this->force_api_key($params); //set api_key if required by other parameters
  		$rand_id = rand(1,10000);
  		$this->id = empty($params['element_id']) ? $this->get_unique_id($params,$rand_id) : $params['element_id'];

      	$photos = $this->fetch_photos($params);
      	if (! is_array($photos)) return $photos; //return error message if an array of photos is not returned

  		$divclear = '<div style="clear:both"></div>';
  		$attribution = empty($params['attribution'])?"":('<p class="slickr-flickr-attribution align'.$params['align'].'">'.$params['attribution'].'</p>');
  		$bottom = empty($params['bottom'])?"":(' style="margin-bottom:'.$params['bottom'].'px;"');
  		$lightboxrel = $thumb_scale = $pagination = $s = '';
  		switch ($params['type']) {
    		case "slightbox": {
	    		if (empty($params['ptags'])) $params['ptags'] = "on"; //paragraph tags arounds titles	
        		if (empty($params['thumbnail_size'])) $params['thumbnail_size'] = 'medium'; //set default slideshow size as Medium
        		$this->set_lightboxrel($params,$rand_id);
        		$divstart = sprintf('%1$s<div class="slickr-flickr-slideshow%2$s %3$s %4$s %5$s%6$%7$s" %8$s>',
        			$attribution, 
					$params['lightbox'] == 'sf-lightbox' ? ' sf-lightbox' : '',
        			$params['orientation'], $params['thumbnail_size'],
        			$params['descriptions']=='on' ? 'descriptions ' : '',
        			$params['captions']=='off' ? 'nocaptions ' : '',
        			$params['align'], $bottom);

        		$divend = '</div>'.$this->set_options( array_merge (
        			$this->slideshow_options($params), 
        			$this->lightbox_options($params,$this->prepare_lightbox_data($photos, $params))));
        		break;
       	 	}
   		case "slideshow": {
	    	if (empty($params['ptags'])) $params['ptags'] = "on"; //paragraph tags arounds titles	
    		$divstart = $attribution.'<div class="slickr-flickr-slideshow '.$params['orientation'].' '.$params['size'].($params['descriptions']=="on" ? " descriptions" : "").($params['captions']=="off" ? " nocaptions " : " ").$params['align'].'"'.$bottom.'>';
 			$divend = '</div>'.$this->set_options($this->slideshow_options($params));
        	break;
        }
   		case "galleria": {
    		if (empty($params['thumbnail_size'])) $params['thumbnail_size'] = 'square'; //set default thumbnail size as Square
    		if ($params['galleria'] == 'galleria-original') {
				$params['galleria_theme'] = 'original'; //set a default value
				if (empty($bottom))
					$style = ' style="visibility:hidden;"';
        	    else
        	    	$style = substr($bottom,0,strlen($bottom-2)).'visibility:hidden;"';
        	    $startstop = $params['pause']== 'off' ? '' : ('| <a href="#" class="startSlide">start</a> | <a href="#" class="stopSlide">stop</a>');
 			    $nav = <<<NAV
<p class="nav {$params['size']}"><a href="#" class="prevSlide">&laquo; previous</a> {$startstop} | <a href="#" class="nextSlide">next &raquo;</a></p>
NAV;
				$data = false;
			} else {		
				$style = $bottom;
				$nav= '';
				$data = $this->prepare_galleria_data($photos, $params);
			}
			switch ($params['nav']) {
				case "above": { $nav_below = ''; $nav_above = $nav; break; }
				case "below": { $nav_below = $nav; $nav_above = ''; break; }
				case "none": { $nav_below = ''; $nav_above = ''; break; } 	
				default: { $nav_below = $nav; $nav_above = $nav; break; }
			}
    	    $divstart = '<div class="slickr-flickr-galleria '.$params['orientation'].' '.$params['size'].' '.$params['align'].' '.$params['galleria_theme'].'"'.$style.'>'.$attribution.$nav_above;
    	    $divend = $divclear.$attribution.$nav_below.'</div>'.$this->set_options($this->galleria_options($params,$data));
			Slickr_Flickr_Public::add_galleria_theme($params['galleria_theme']); //add count of gallerias on page		
    	    break;
    	    }
   		default: {
    	    $this->set_thumbnail_params($params);
    	    $this->set_lightboxrel($params,$rand_id);
    	    $divstart = sprintf('<div class="slickr-flickr-gallery%1$s"%2$s>%3$s', 
    	    	$params['lightbox']=='sf-lightbox' ? ' sf-lightbox' : '', $bottom, $attribution);
    	    $divend = '</div>'.$this->set_options($this->lightbox_options($params,$this->prepare_lightbox_data($photos, $params)));
    	    }
  		}

   		if (($params['type']=='galleria') && ($params['galleria'] == 'galleria-latest')) 
   			$content = '';
   		else 
   			$content = $this->wrap_photos ($photos, $params);
		return '<div id="'.$this->id.'">'.$divstart.$content.$divend.$pagination.$divclear.'</div>';
	}
	
	function wrap_photos ($photos, $params) {
		$s = $format = $element = $element_style = $gallery_style = $gallery_class = '';
  		switch ($params['type']) {
			case "slideshow":
			case "slightbox":
				$element = 'div'; break;
			case "gallery":
				$element_style = $params['thumbnail_style'];
				$gallery_style = $params['gallery_style'];
				$gallery_class = $params['gallery_class'];
 			default: 
	 			$format= '<ul%2$s%3$s>%1$s</ul>';
				$element = 'li'; 
  		}
		$start = $this->get_start($params, count($photos));
	  	$i = 0;
		foreach ( $photos as $photo ) {
			$i++;
			$s .= sprintf('<%2$s%3$s%4$s>%1$s</%2$s>', $this->get_image($photo, $params), 
				$element, $element_style, $start==$i?' class="active"': '');
		}
	  	return empty($format) ? $s : sprintf($format,  $s, $gallery_class, $gallery_style);
	}

	function prepare_lightbox_data($photos, $params) {
		if ($params['lightbox'] != 'sf-lightbox') return false;
		$data = array();		
		foreach ( $photos as $photo ) {
			$image = $this->prepare_image($photo, $params);
			$item = array();
		    $item['thumb'] = $image['thumb_url'];
    		$item['src'] = $image['full_url'];
    		$item['caption'] = $params['flickr_link']=='on' ?
	    		sprintf('<a %1$s title="%2$s" href="%3$s">%4$s</a>', 
	    			empty($params["flickr_link_target"]) ? '' : sprintf('target="%1$s"',$params["flickr_link_target"]),
	    			$params["flickr_link_title"], $image['link'], $image['title']) : $image['title'];
    		if (in_array($params["descriptions"], array('on','lightbox'))) $item['desc'] = $image['description'];    			    
			$data[] = $item;
		}	
		return $data;	
	}

	function prepare_galleria_data($photos, $params) {
		$data = array();
		foreach ( $photos as $photo ) {
			$image = $this->prepare_image($photo, $params);
			$item = array();
		    $item['thumb'] = $image['thumb_url'];
    		$item['image'] = $image['full_url'];
    		$item['title'] = $image['captiontitle'];
    		if ($params["descriptions"] =='on') $item['description'] = $image['description'];
     		if ($params["flickr_link"]=="on") $item['link'] = $image['link'];    			    
			$data[] = $item;
		}	
		return $data;	
	}

	function get_unique_id($params,$rand_id) {
	  $unique_id = array_key_exists('tag',$params) ? $params['tag'] : (
               array_key_exists('set',$params) ? $params['set'] : (
               array_key_exists('gallery',$params) ? $params['gallery'] : 'recent'));
	  return 'flickr_'.strtolower(preg_replace("{[^A-Za-z0-9_]}",'',$unique_id)).'_'.$rand_id; //strip spaces, backticks, dashes and commas
	}

	function force_api_key(&$params) {
	  if (empty($params['use_key']) 
	  && ! empty($params['api_key']) 
	  && (($params['items'] > 20 ) || ($params['api_required'] == 'y'))) 
	   	$params['use_key'] = 'y'; // set use_key if API key is available and is either required or request is for over 20 photos
	}

	function set_api_required(&$params) {
		$params['api_required'] = (($params['use_rss'] == 'n')
			|| (! empty($params['license'])) || (! empty($params['text']))
			|| (! empty($params['date'])) || (! empty($params['before'])) || (! empty($params['after']))
			|| (! empty($params['private'])) || ($params['page'] > 1) || ($params['search'] == 'galleries') 
			|| ( !empty($params['tag']) && ($params["search"]=="groups"))) ? 'y' : 'n'; 
	}

	function set_slideshow_onclick($params) {
	  $link='';
	  if (empty($params['link']))
	    if ($params['pause'] == "on")
	        $link = "toggle" ;
	     else
	        $link = $params['type'] == "slightbox" ? "" : "next";
	  else
	    $link = $params['link'];
	  return $link;
	}

	function set_thumbnail_params(&$params) {
	    $thumb_rescale= false;
	    switch ($params["thumbnail_size"]) {
	      case "thumbnail": $thumb_width = 100; $thumb_height = 75; $thumb_rescale = true; break;
	      case "s150": $thumb_width = 150; $thumb_height = 150; $thumb_rescale = true; break;
	      case "small": $thumb_width = 240; $thumb_height = 180; $thumb_rescale = true; break;
	      case "s320": $thumb_width = 320; $thumb_height = 240; $thumb_rescale = true; break;
	      case "medium": $thumb_width = 500; $thumb_height = 375; $thumb_rescale = true; break;
	      case "m640": $thumb_width = 640; $thumb_height = 480; $thumb_rescale = true; break;
	      case "m800": $thumb_width = 800; $thumb_height = 640; $thumb_rescale = true; break;
	      case "large": $thumb_width = 1024; $thumb_height = 768; $thumb_rescale = true; break;	      
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

	function prepare_image($photo, $params) {
	    $image = array();
	    $image['link'] = $photo->get_link();
	    $oriented = $photo->get_orientation();
	    $title = $photo->get_title();
	    $description = $photo->get_description(); 
	    if ($description == '<p></p>') $description = '';
	    $image['border'] = $params['border']=='on'?' class="border"':'';
		$ptags = ('on'==$params['ptags']); //paragraph tags around title?
		//separator is required if title and description end up together on the same line
	    $sep = (($params["descriptions"] =='on') && ($params["type"] !='galleria') && ! $ptags) ? '.&nbsp;' : ''; 
	    $ptitle = empty($title) ? '' : sprintf(($ptags ? '<p%2$s>%1$s</p>' : '<span%2$s>%1$s</span>').$sep ,$title, $image['border']);
		$link_target = empty($params["flickr_link_target"]) ? '' : sprintf('target="%1$s"',$params["flickr_link_target"]);
	    $plink = sprintf($ptags ? '<p>%1$s</p>' : '%1$s' , 
	    	sprintf('<a title="%1$s" %2$s href="%3$s">%4$s</a>%5$s', $params["flickr_link_title"], $link_target, $image['link'], $title, $sep));
	    $image['captiontitle'] = $params["flickr_link"]=="on" ? ($params["lightbox"]=="none" ? $title : $plink) :$ptitle;
	    $image['alt'] = $params["descriptions"]=="on"? ($ptags ? $description : strip_tags($description,'<a>')) : "";
		$image['full_url'] = $params['size']=="original" ? $photo->get_original() : $photo->resize($params['size']);
	    $image['thumb_url'] = $photo->resize($params['thumbnail_size']);
	    $image['title'] = $title;
	    $image['description'] = $description;
		return $image;
	}

	function get_image($photo, $params) {
		$image = $this->prepare_image($photo, $params);

	    switch ($params['type']) {
	       case "slideshow": {
	            $caption = $params['captions']=="off"?"":($image['captiontitle'].$image['alt']);
	            return  sprintf('<img src="%1$s" title="%2$s" alt="%3$s" %4$s />%5$s',
	            $image['full_url'], htmlspecialchars($image['title']), htmlspecialchars($image['alt']), $image['border'], $caption);
	        }
	       case "slightbox": {
	            $desc = $params["descriptions"]=="on" || $params["descriptions"]=="slideshow" ? $image['description'] : "";
	            $alt = $params["descriptions"]=="on" || $params["descriptions"]=="lightbox" ? $image['description'] : "";
	            $caption = $params['captions']=="off"?"":($image['captiontitle'].$desc);
	            $lightbox_title = $image['captiontitle'] . $alt;
	            return sprintf('<a %1$s href="%2$s" title="%3$s"><img src="%4$s" title="%5$s" alt="%6$s" %7$s /></a>%8$s',
	            	$params['lightboxrel'], $image['full_url'], htmlspecialchars($lightbox_title), 
	            	$image['thumb_url'], htmlspecialchars($image['title']), htmlspecialchars($alt), 
	            	$image['border'], $caption);
    	    }
    	   case "galleria": {
    	   		$caption = $params['captions']=="off"?"":$image['captiontitle'];
    	   		return sprintf('<a href="%1$s"><img src="%2$s" title="%3$s" alt="%4$s" /></a>',
    	   				$image['full_url'], $image['thumb_url'], htmlspecialchars($caption), htmlspecialchars($image['alt']));
    	    }
    	    default: {
				return $this->get_lightbox_html ($image,$params );
    	    }
    	}
	}

	function get_lightbox_html ($image, $params) {
    	if ($params['lightbox']=="none") { //if no lightbox then maybe link directly to Flickr
    		$image['full_url'] = !empty($params['link']) ?  $params['link'] : ('on'==$params['flickr_link'] ? $image['link'] : '') ; 
		}
    	$thumbcaption = $params['thumbnail_captions']=="on"?('<br/><span class="slickr-flickr-caption">'.$image['title'].'</span>'):"";
    	$full_caption= ($params["captions"]=="off" ? '' : $image['captiontitle']) . ($params["descriptions"]=="on" ? $image['alt'] : "");
		$img_title = empty($image['title']) ? '' : sprintf('title="%1$s"',htmlspecialchars($image['title']));
		$img_alt = empty($image['alt']) ? '' : sprintf('alt="%1$s"',htmlspecialchars($image['alt']));
		$title = ''; 
		if (! empty($full_caption)) switch ($params['lightbox']) {
	      case "sf-lightbox":  break; //no title 
	      case "fancybox":  $title = sprintf('title="%1$s"', htmlspecialchars($full_caption)); break; //use title
	      case "thickbox": $title = sprintf('title=\'%1$s\'', str_replace("'","&acute;",$full_caption)); break; //avoid thickbox issue with apostrophes
		  default: $title = sprintf('title="%1$s"', htmlspecialchars($full_caption));	
		}
		if (empty($image['full_url']))
    		return sprintf('<img src="%1$s" %2$s %3$s %4$s />%5$s',
				$image['thumb_url'], $params['image_style'], $img_alt, $img_title, $thumbcaption);
    	else	
    		return sprintf('<a href="%1$s" %2$s %3$s><img src="%4$s" %5$s %6$s %7$s />%8$s</a>',
				$image['full_url'], $params['lightboxrel'], $title, 
				$image['thumb_url'], $params['image_style'], $img_alt, $img_title, $thumbcaption);
	}

	function set_lightboxrel(&$params, $rand_id) {
		$ptags = "off";
	    switch ($params['lightbox']) {
	      case "sf-lightbox": 	$lightboxrel = ''; break;
	      case "evolution": 	$lightboxrel = sprintf('rel="group%1%s" class="lightbox"',$rand_id);  break;
	      case "fancybox": 		$lightboxrel = sprintf('rel="fancybox_%1$s" class="fancybox"',$rand_id);  break;
	      case "slimbox":		$lightboxrel = sprintf('rel="lightbox-%1$s"',$rand_id);  break;
	      case "shutter":  		$lightboxrel = sprintf('class="shutterset_%1$s"',$rand_id);  break;
	      case "thickbox": 		$lightboxrel = sprintf('rel="thickbox-%1$s" class="thickbox"',$rand_id); break;
	      case "none":
	      case "norel": $lightboxrel = '' ; break;      
	      default:	$lightboxrel = 'rel="lightbox['.$rand_id.']"';  break;
	    }
		$params['lightboxrel'] = $lightboxrel;
 		$params['lightbox_id'] = $rand_id;
		if (empty($params['ptags'])) $params['ptags'] = $ptags; //paragraph tags arounds titles?
	}

	function get_start($params,$numitems) {
	  $r = 1;
	  if ($numitems > 1) {
	     if ($params['start'] == "random")
	        $r = rand(1,$numitems);
	     else
	        $r = is_numeric($params['start']) && ($params['start'] < $numitems) ? $params['start'] : $numitems;
	     }
	   return $r;
	}

	function restrict_photos ($items, $params) {
	    $filtered_items = array();
	    if ($params['restrict']=='orientation') { 
	    	$orientation = $params['orientation'];    
	    	foreach ($items as $item)  if ($item->get_orientation()==$orientation) $filtered_items[] = $item;
	    	return $filtered_items;
		} else {
		    return $items;
		}
	}

	function sort_photos ($items, $sort, $direction) {
		$do_sort = ($sort=="date") || ($sort=="title") || ($sort=="description");
	    $direction = strtolower(substr($direction,0,3))=="des"?"descending":"ascending";
	    if ($sort=="date") { foreach ($items as $item) { if (!$item->get_date()) { $do_sort = false; break; } } }
	    if ($sort=="description") { foreach ($items as $item) { if (!$item->get_description()) { $do_sort = false; break; } } }
	    $ordered_items = $items;
	    if ($do_sort) usort($ordered_items, array(&$this,'sort_by_'.$sort.'_'.$direction));
	    return $ordered_items;
	}

	function sort_by_description_descending($a, $b) { return strcmp($b->get_description(),$a->get_description()); }
	function sort_by_description_ascending($a, $b) { return strcmp($a->get_description(),$b->get_description()); }
	function sort_by_title_descending($a, $b) { return strcmp($b->get_title(),$a->get_title()); }
	function sort_by_title_ascending($a, $b) { return strcmp($a->get_title(),$b->get_title()); }
	function sort_by_date_ascending($a, $b) { return ($a->get_date() <= $b->get_date()) ? -1 : 1; }
	function sort_by_date_descending($a, $b) { return ($a->get_date() > $b->get_date()) ? -1 : 1; }

	function set_options($options) {
	    if (count($options) > 0) {
	    	$s = sprintf('jQuery("#%1$s").data("options",%2$s);', $this->id, json_encode($options) ); 
	        if (Slickr_Flickr_Utils::scripts_in_footer()) {
	    		Slickr_Flickr_Public::add_jquery($s); //save for later
			} else {
				return sprintf('<script type="text/javascript">%1$s</script>', $s); //output it now
			}
		}
		return '';
	}
	
	function parse_json_options($json, &$options ) {
		$options_list = str_replace(';;',';',trim($json).';');
    	$more_options = array();
		if ((preg_match_all("/([^:\s]+):([^;]+);/i", $options_list, $pairs)) && (count($pairs)>2)) $more_options = array_combine($pairs[1], $pairs[2]);
		foreach ($more_options as $key => $value) {
			if (is_numeric($value)) {
				$options[$key] = $value + 0;
			} else {
			    $val = strtolower(trim($value));
				switch ($val) {
					case "false": { $options[$key] = false; break; }
					case "true": { $options[$key] = true; break; } 
					default:  $options[$key] = $val;
        	    }
			}
		}
	}

	function galleria_options($params, $data=false) {
	    $options = array();
		if ($params['galleria'] == 'galleria-original') {
			$options['delay'] = $params['delay'] * 1000;
			$options['autoPlay'] = $params['autoplay']=='on'?true:false;
			$options['captions'] = $params['captions']=='off'?false:true;
			$options['descriptions'] = $params['descriptions']=='on'?true:false;
	    } else {
			if (!empty($params['galleria_options'])) $this->parse_json_options($params['galleria_options'], $options);
			if (!empty($params['options'])) $this->parse_json_options($params['options'], $options);
    		if (!array_key_exists('autoplay',$options)) $options['autoplay'] = $params['delay']*1000; 
  		  	if (!array_key_exists('transition',$options)) $options['transition'] = 'fade';
  		  	if (!array_key_exists('transitionSpeed',$options)) $options['transitionSpeed'] = $params['transition']*1000;
  		  	if (!array_key_exists('showInfo',$options)) $options['showInfo'] = $params['captions']=='off' ? false: true;
  		  	if (!array_key_exists('imageCrop',$options)) $options['imageCrop'] = true;
  		  	if (!array_key_exists('carousel',$options)) $options['carousel'] = true;    	
  		  	if (!array_key_exists('responsive',$options)) $options['responsive'] = true;  
			if (!array_key_exists('debug',$options)) $options['debug'] = false;  
			if (!array_key_exists('height',$options)) $options['height'] = $params['orientation']=="portrait" ? 1.333 : 0.75;	    
			if ($data && is_array($data) && (count($data)>0)) $options['dataSource']=$data;
    	}
		return $options;
	}

	function slideshow_options($params) {
    	$options['delay'] = $params['delay'] * 1000;
    	$options['autoplay'] = $params['autoplay']=="off"?false:true;
    	$options['transition'] = 500;
    	$options['link'] = $this->set_slideshow_onclick($params);
    	$options['target'] = $params['target'];    
    	if (isset($params['width'])) $options['width'] = $params['width'];
    	if (isset($params['height'])) $options['height'] = $params['height'];
    	if (isset($params['transition'])) $options['transition'] = $params['transition'] * 1000; 
    	return $options;
	}

	function lightbox_options($params, $data = false) {
    	$options = array();
    	if (($params['lightbox'] == "sf-lightbox")) {
			if (!empty($params['options'])) $this->parse_json_options($params['options'], $options);
    		if (!array_key_exists('caption',$options)) $options['caption'] = $params['captions'] == 'off' ? false:true;
     		if (!array_key_exists('desc',$options)) $options['desc'] = (in_array($params['descriptions'], array('on', 'lightbox'))) ? true:false;
    		if (!array_key_exists('pause',$options)) $options['pause'] = $params['delay'] * 1000;
    		if (!array_key_exists('auto',$options)) $options['auto'] = $params['autoplay']=='on'?true:false;
			if ($data && is_array($data) && (count($data)>0)) {
				$options['dynamic'] = true;
				$options['dynamicEl'] = $data;
			}
		}
    	if (array_key_exists('thumbnail_border',$params) && !empty($params['thumbnail_border'])) 
    		$options['border'] = $params['thumbnail_border']; 
		return $options;
	}

	function fetch_photos($params) {
      	$fetcher = new Slickr_Flickr_Fetcher($this->id) ;
      	$photos = $fetcher->fetch_photos($params) ;
      	if (!is_array($photos)) return $fetcher->get_message(); //return error
	  	if (!empty($params['restrict'])) $photos = $this->restrict_photos($photos, $params);
	  	if (!empty($params['sort'])) $photos = $this->sort_photos ($photos, $params['sort'], $params['direction']);
	  	return $photos; //return array of photos
	}

}
