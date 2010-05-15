jQuery.noConflict(); jQuery(document).ready(function($) {
  $(".slickr-flickr-gallery").each( function (index) {
     $id = $(this).attr("id");
     $delay = $(this).data("delay");
     if (($delay) && ($delay > 0))
        $("#"+$id+" a[rel='sflightbox']").lightBox( { slideDelay : $delay * 1000 });
     else
        $("#"+$id+" a[rel='sflightbox']").lightBox();
  });
});
