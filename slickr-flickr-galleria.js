jQuery.noConflict(); jQuery(document).ready(function($) { $(".slickr-flickr-galleria").each(function(index){
     $delay = $(this).data("delay");
     $ul = $(this).children("ul");
     if (($delay) && ($delay > 0))
        $ul.galleria( { slideDelay : $delay * 1000, autoPlay : true});
     else
        $ul.galleria();
    });
});