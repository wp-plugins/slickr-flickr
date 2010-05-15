<?php
class flickr {
  /* Function that removes all quotes */
  function cleanup($s = null) {
    return $s?str_replace('"', '', str_replace("'", "",$s)):false;
  }

  /* Function that returns the correctly sized photo URL. */
  function resize_photo($url, $size) {
    
    $url = explode('/', $url);
    $photo = array_pop($url);

    switch($size)  {
      case 'square': $suffix = '_s.';  break;
      case 'thumb': $suffix = '_t.';  break;
      case 'small': $suffix = '_m.';  break;
      case 'large': $suffix = '_b.';  break;
      default:  $suffix = '.';  break; // Medium   
      }

    $url[] =  preg_replace('/(_(s|t|m|b))?\./i', $suffix, $photo);;
    return implode('/', $url);
  }

  /* Function that get the photo url, size, height and original URL from feed item */
  function find_photo($item) {
    $photo['title'] = $item->get_title();

    $data = $item->get_description();

    preg_match_all('/<img src="([^"]*)"([^>]*)>/i', $data, $m);
    $photo['url'] = $m[1][0];

    preg_match_all('/<a href="([^"]*)"([^>]*)>/i', $data, $m);
    $photo['link'] = $m[1][1];

    preg_match_all('/width="([^"]*)"([^>]*)>/i', $data, $m);
    $photo['width'] = $m[1][0];

    preg_match_all('/height="([^"]*)"([^>]*)>/i', $data, $m);
    $photo['height'] = $m[1][0];

    $photo['orientation'] = $photo['height'] > $photo['width'] ? "portrait" : "landscape" ;

    $enclosure = $item->get_enclosure(0);
    $photo["original"] = $enclosure==null ? $photo['url'] : $enclosure->get_link();
    $photo["description"] = $enclosure==null ? "" : $enclosure->get_description();
    return $photo;
  }
}
?>
