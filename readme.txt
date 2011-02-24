=== Slickr Flickr ===
Contributors: powerblogservice
Donate link: http://www.slickrflickr.com/donate/
Tags: wordpress flickr plugin, flickr wordpress plugin, flickr slideshow, flickr gallery, flickr galleria, flickr photo gallery, lightbox, fancybox, shadowbox, slimbox, prettyPhoto, thickbox, wp-prettyphoto, shutterbox, slideshow lightbox
Requires at least: 2.8
Tested up to: 3.0.5
Stable tag: 1.23
A Flickr WordPress plugin that displays your photos either as a gallery, a galleria or a unbranded slideshow in posts, pages, or sidebar widgets

== Description ==
* Displays Flickr photos as a gallery, a galleria, or an unbranded slideshow (i.e. not Flickr's own slideshow widget)
* Uses "slickr-flickr" shortcode to make it as easy as possible to show your Flickr photos on WordPress
* Works well in posts, pages and text widgets in the sidebar, at different sizes, with portrait and landscape photos
* Sorts photos by date, title or description
* With or without captions, photo descriptions and links back to Flickr 
* With manual or autoplay slideshows
* Works with various LightBoxes
* See http://www.slickrflickr.com/ for tutorials on using the plugin
* See http://www.slickrflickr.com/pro/ for Pro Edition Priority Support and Bonus Features


== Installation ==
1. Uncompress the downloaded zip archive in [WordPress install root]/wp-content/plugins or use the WordPress plugin installer
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
1. Example of more than one Flickr Gallery and more than onb Flickr Slideshow in action on a single page
1. Example of a Flickr galleria (a display including a large photo with thumbnails below)
1. Example of an overlaid lightbox which appears on clicking a thumbnail


== Changelog ==

= 1.23 =
* Fix "items" bug where the requested number of photos to be returned was being ignored 

= 1.22 =
* Added Flickr commons license selection  
* Added search by date taken and date uploaded
* Added search by tag for group photos
* Improved slideshow sizing
* Added galleria 1.2 option
* Increased photoset search from 20 to 50 photos
* Added unlimited search for photosets  (Pro Edition only)
* Added search by post publish date  (Pro Edition only)

= 1.21 =
* Added automatic failover from Flickr API to Flickr RSS if no photos are returned (Pro Edition only)
* Added option to disable captions in the lightbox
* Added support for machine tags (e.g geo tags)
* Unbundled ShadowBox - now if you need ShadowBox please install ShadowBox JS plugin
* Improved support for larger slideshows (Medium 640 and Large sizes)
* Improved CSS to allow IE6 and IE7 to display gallery of large thumbnails
* Fix bug with slideshow transitions

= 1.20 =
* Added support for links back to Flickr in the photo captions in the galleria display
* Added support for Evolution LightBox and PrettyPhoto LightBox
* Added better instructions for using third party lightboxes
* Added slideshow "transition" parameter to override the default transition of 0.5 seconds (Pro version)
* Added "align" parameter to make it easier to center a gallery or slideshow
* Improved handling of large thumbnail
* Improved error reporting when attempting to fetch more than 20 photos from a photoset 
* Fixed display issues where there is only a single photo in the slideshow

= 1.19 =
* Reinstated automatic use of API key if more than 20 photos are requested
* Use https protocol for js and css files if admin site is being run securely
* Removed warning message when checking for updates
* Added cursor pointer/hand when hovering over the slideshow to show it is clickable
* Added Slideshow LightBox display in Pro edition

= 1.18 =
* Added images folder

= 1.17 =
* Fix problem with getting photo descriptions
* Fix problem with using multiple tags
* Added a "descriptions" class so that there is less white space beneath the slideshow
* Created Slickr Flickr Pro edition with Priority Support Forum and Bonus Features

= 1.16 =
* Added medium 640 photos - size="m640"
* Moved site to http://www.slickrflickr.com

= 1.15 =
* Renamed Flickr class to avoid conflict with other plugins
* Fixed bug with thumbnail size
* Added option to force use of api key (this fixes the issue with missing descriptions when not using the api key)
* Allow tag not to be specified so most recent photos are returned

= 1.14 =
* Added search=photos|favorites|friends|groups|sets to allow more than just tagged photos to be selected
* Added api_key option to allow selection of up to 50 photos

= 1.13 =
* Added pause=on option for the slideshow so you can pause or resume the slideshow by clicking the photo
* Added autoplay=off option for the galleria so you can stop the galleria slideshow playing automatically
* Fixed bug with captions always displaying in the galleria
* Inhibited display of html on the tooltip popup on placing the mouse over a thumbnail by using the Flickr photo title as the image title attribute and the Flickr photo description as the inage alt attribute
* Used a default value of "wp_" as the table prefix if none is defined.
 
= 1.12 =
* Fixed bug with captions not displaying in the galleria and also allow more than one galleria slideshow per page

= 1.11 =
* Fixed bug in slickr-flickr.js when using other lightboxes

= 1.10 =
* Consolidated script and stylesheet files
* Added more options for the LightBox: Slimbox
* Fixed bug with the speed of play of the galleria slideshow
* Added autoplay off option for the galleria so it does not start automatically 
* Partial support for multiple galleria on a page (a bug remains - only the last galleria slideshow plays)

= 1.9 =
* Added missing stylesheet for the admin panel
* Readme updates

= 1.8 =
* Added more options for the LightBox: ThickBox, ShadowBox, FancyBox, LightBox Plus and Shutter Reloaded 
* Option for large thumbnails in the gallery
* Optional border for the slideshow
* Automatic slideshow option for the galleria
* Slickr Flickr Resources Menu on the admin panel

= 1.7 =
* Add feature in the Slickr Flickr Admin Panel to clear the RSS cache so updates to Flickr appear more quickly on Wordpress
* Change Galleria size to improve presentation of photos of portarit orientation

= 1.6 =
* Fix typo in the CSS that controls the height of a small slideshow

= 1.5 =
* Fix alignment issue with gallery

= 1.4 =
* Add Galleria display which is a slideshow/gallery combo display
* Added autoplay option for gallery lightbox
* Added link to allow a click-through from the slideshow to another page
* Added tagmode to allow selection of photos across different tags
* Added option to show attribution to allow credit to be attributed to the photographer
* Added option to show captions for the thumbnail gallery
* Added option to set the number of photos per row in the gallery
* Added option to sort the photos by date, title or description
* Added option to show descriptions beneath the title in the lightbox display
* Added option to make the title in the lightbox display a link to the photo on Flickr

= 1.3 =
* Added support for Flickr Ids that belongs to groups rather than users
* Changed slideshow background to be transparent rather than white
* Added feature to allow a random or chosen starting slide for the slideshow - this is useful if the same slideshow appears several pages

= 1.2 =
* Corrections to Readme File and FAQ Numbering

= 1.1 =
* Enable shortcode processing in text widgets

= 1.0 =
* Original version


== Upgrade Notice ==

= 1.23 = 
* Mandatory - fixes bug in 1.22 which meant to many photos were being returned


== How to Use The Plugin ==

The Flickr show is inserted into a post or a widget using the slickr-flickr short code.

For example, to show my pictures from Flickr that have been tagged with "bahamas" I use : [slickr-flickr tag="bahamas"]

For the full list of Slickr Flickr parameters go to http://www.slickrflickr.com/56/how-to-use-slickr-flickr-to-create-a-slideshow-or-gallery/


== How to Set Up The Plugin Defaults ==

If you don't want to specify all the settings for every slideshow you can set up some defaults. The default value is used when you have not specified a value on the individual slideshow or gallery.

* Go to the "Settings" section, and choose "Slickr Flickr"
* Enter your Flickr Id (the ID is of the form 12345678@N00) and choose whether it is a user or group id
* Enter your Flickr API Key (optional) 
* Enter your Slickr Flickr Pro license Id (optional)
* Enter the default number of photos to show
* Select the default type of display - gallery, slideshow or galleria (a slideshow/gallery combo)
* Choose whether photos captions are displayed
* Enter the default delay in seconds before the slideshow moves on to the next slide
* Choose the lightbox (manual play, auto play, ThickBox, ShadowBox, FancyBox, Shutterbox, SlimBox LightBox Plus, WP PrettyPhoto or Evolution LightBox)


== Links ==

Here are some of the useful Slickr Flickr WordPress Plugin links

* Plugin Home Page http://www.slickrflickr.com/
* Plugin Help and Support http://www.slickrflickr.com/slickr-flickr-help/
* Plugin Tutorials http://www.slickrflickr.com/slickr-flickr-videos/
* Slickr Flickr Pro http://www.slickrflickr.com/pro/
