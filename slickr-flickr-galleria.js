jQuery.noConflict(); jQuery(document).ready(function($) { $(".slickr-flickr-galleria ul").each(function(index){
     $delay = $(this).data("delay");
     if (($delay) && ($delay > 0))
        $(this).galleria( { slideDelay : $delay * 1000, autoPlay : true});
     else
        $(this).galleria();
    });
});