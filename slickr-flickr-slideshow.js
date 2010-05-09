function  slickr_flickr_next_slide(obj) {
    var j = jQuery(obj);
    var $active = j.children('div.active');
    if ( $active.length == 0 ) $active = j.children('div:last');
    var $next =  $active.next().length ? $active.next() : j.children('div:first');

    $active.addClass('last-active');
    $next.css({opacity: 0.0})
        .addClass('active')
        .animate({opacity: 1.0}, 500, function() {
            $active.removeClass('active last-active');
        });
}

function slickr_flickr_next_slides() {
   jQuery('.slickr-flickr-slideshow').each(function(index){
        slickr_flickr_next_slide(jQuery(this));
   });
}

function  slickr_flickr_get_slideshow_delay() {
   var mindelay = 0;
   jQuery('.slickr-flickr-slideshow').each(function(index){

    delay = jQuery(this).data('delay');
    if ((!(delay == undefined)) && ((mindelay == 0) || (delay < mindelay))) mindelay = delay;
    });
   return mindelay;
}

function slickr_flickr_start_slideshows() {
var flickr_slideshow_delay =  slickr_flickr_get_slideshow_delay();
if (flickr_slideshow_delay > 0) {
    setInterval("slickr_flickr_next_slides()",flickr_slideshow_delay*1000);
    }
}

jQuery(document).ready( function () {  slickr_flickr_start_slideshows(); });
