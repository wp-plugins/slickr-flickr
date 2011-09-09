=== Slickr Flickr ===
Contributors: powerblogservice
Donate link: http://www.slickrflickr.com/donate/
Tags: wordpress flickr plugin, flickr wordpress plugin, flickr slideshow, flickr gallery, flickr galleria, flickr photo gallery, highslide, lightbox, fancybox, shadowbox, slimbox, prettyPhoto, thickbox, wp-prettyphoto, shutterbox, slideshow lightbox
Requires at least: 2.8
Tested up to: 3.2.1
Stable tag: 1.34
A Flickr WordPress plugin that displays your photos either as a gallery, a galleria or a unbranded slideshow in posts, pages, and sidebar widgets.  

== Description ==
* Displays Flickr photos as a gallery, a galleria, or an unbranded slideshow (i.e. not Flickr's own slideshow widget)
* Uses "slickr-flickr" shortcode to make it as easy as possible to show your Flickr photos on WordPress
* Works well in posts, pages and text widgets in the sidebar, at different sizes, with portrait and landscape photos
* Sorts photos by date, title or description
* With or without captions, photo descriptions and links back to Flickr 
* With manual or autoplay slideshows
* Works with various LightBoxes such as Evolution LightBox, FancyBox, Highslide, LightBox Plus, Pretty Photo, Slimbox, ShadowBox, Shutterbox and ThickBox.
* See http://www.slickrflickr.com/ for tutorials on using the plugin
* See http://www.slickrflickr.com/pro/ for Pro Edition Priority Support and Bonus Features
* Latest version 1.34 includes Galleria 1.25 and implements uses separate thumbnails for the galleria which improves the load time. 

== Installation ==
1. Use the standard WordPress plugin automatic updates system for installing and updating to the latest version or use the manual steps below. 
1. Uncompress the downloaded zip archive in [WordPress install root]/wp-content/plugins
1. Activate the plugin in your WordPress plugins control panel
1. Go to the "Settings" section, and choose "Slickr Flickr"
1. Type In your Flickr ID (it should look something like 12345678@N00) and then click the Save button
1. To use the plugin in a post, page or text widget use the shortcode [slickr-flickr tag="my tag phrase"]
1. If you have no photos in Flickr with this tag then no pictures are displayed
1. See http://www.slickrflickr.com/how-to/how-to-install-slickr-flickr-plugin for more about the plugin installation

== Frequently Asked Questions ==

See http://www.slickrflickr.com/slickr-flickr-help/ for the full list of questions and answers about Slickr Flickr

* How do I find my Flickr Id? - see http://www.slickrflickr.com/support/where-do-i-find-my-flickr-id/

* Changes to my photos on Flickr do not appear instantly in the Slickr Flickr Slideshow or Gallery - see http://www.slickrflickr.com/support/tagged-photos-do-not-appear-in-the-blog-immediately/

* How many photos can I display? Only up to 20 photos are displayed when using your Flickr ID to access photos but you can access up to 50 photos in you specify your Flickr API Key or have unlimited numbers of photos if you upgrade to Slickr Flickr Pro - see http://www.slickrflickr.com/support/how-to-show-50-photos-in-a-flickr-gallery/


== Screenshots ==
1. Example of more than one Flickr Gallery and more than one Flickr Slideshow in action on a single page
1. Example of a Flickr galleria (a display including a large photo with thumbnails below)
1. Example of an overlaid lightbox which appears on clicking a thumbnail


== Changelog ==

= 1.34 =
* Bundled with Galleria 1.2.5
* Galleria now uses thumbnails from Flickr which improves loading time
* Galleria theme is added as a class on the galleria container which makes it easier to make CSS modifications if you want to. (e.g class="slickr-flickr-galleria landscape small miniml" would appear as the class of the div elements that contains a galleria of small landscape photos that are styled with the miniml galleria theme)


= 1.33 =
* Added support for single and double quotes in photo titles and descriptions.
* Reduced amount of white space below a slideshow image if captions are off.
* Added new parameter, "bottom" to control the spacing at the bottom of a slideshow, galleria or gallery.
* Added feature to galleria 1.0 to hide the start and stop navigation links if you specify pause="off". 
* Added ability to specify a default value for galleria 1.2.3 options on the admin settings panel.
* Updated Slickr Flickr Admin Settings to support standard WordPress metabox features: screen options and panels than can be closed or hidden. 
* Added support for private photos. (Pro Edition)

= 1.32 =
* Fixed bug with display of height and width of galleria
* Added option for WP Pretty Photo LightBox to support a gallery

See full version history at http://www.slickrflickr.com/about/

== Upgrade Notice ==

= 1.34 = 
* Recommended - has latest Galleria 1.2.5

== How to Use The Plugin ==

The Flickr show is inserted into a post or a widget using the slickr-flickr short code.

For example, to show my pictures from Flickr that have been tagged with "bahamas" I use : [slickr-flickr tag="bahamas"]

For the full list of Slickr Flickr parameters go to http://www.slickrflickr.com/56/how-to-use-slickr-flickr-to-create-a-slideshow-or-gallery/


== How to Set Up The Plugin Defaults ==

If you don't want to specify all the settings for every Flickr slideshow or gallery you can set up some defaults. The default value is used when you have not specified a value on the individual slideshow, gallery or galleria. All the following settings are optional; we only strongly recommend you set the Flickr ID to save having to set it on every use of the plugin.

* Go to the "Settings" section, and choose "Slickr Flickr"
* Enter your Flickr Id (the ID is of the form 12345678@N00) and choose whether it is a user or group id
* Enter your Flickr API Key (optional) 
* Enter your Slickr Flickr Pro Licence (optional) 


For more information on the other defaults you can set up, go to http://www.slickrflickr.com/40/how-to-use-slickr-flickr-admin-settings/

== Links ==

Here are some of the useful Slickr Flickr WordPress Plugin links

* Plugin Home Page http://www.slickrflickr.com/
* Plugin Help and Support http://www.slickrflickr.com/slickr-flickr-help/
* Plugin Tutorials http://www.slickrflickr.com/slickr-flickr-videos/
* Slickr Flickr Pro http://www.slickrflickr.com/pro/
