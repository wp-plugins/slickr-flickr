jQuery.noConflict(); jQuery(document).ready(function($) {
  $(".slickr-flickr-gallery").each( function (index) {
     $delay = $(this).data("delay");
     if (($delay) && ($delay > 0)) {
        $(this).find('a[rel="sf-lbox-auto"]').lightBox( { nextSlideDelay : 1000 * $delay });
     } else {
        $(this).find('a[rel="sf-lbox-manual"]').lightBox();
        }
  });
});