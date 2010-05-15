=== Slickr Flickr ===
Contributors: powerblogservice
Donate link: http://www.wordpresswise.com/slickr-flickr/donate/
Tags: flickr, flickr slideshow, flickr gallery, lightbox, galleria, jquery, wordpresswise
Requires at least: 2.8
Tested up to: 2.9.2
Stable tag: 1.7
Displays a set of tagged photos from your Flickr account either as gallery or a unbranded slideshow in posts, pages, or sidebar widgets

== Description ==

* Displays tagged Flickr photos as a gallery or an unbranded slideshow (i.e. not Flickr's own slideshow widget)
* Uses "slickr-flickr" shortcode to make adding a Flickr show as easy as possible
* Can display more than one gallery or slideshow on the same page/post
* Can display slideshows in sidebar widgets
* Can display slideshows at medium and small Flickr Photo sizes (500 by 375 and 240 by 180 px)
* Can display slideshows as portrait or landscape
* Best fits the slideshow if a mix of landscape and portrait photos are used
* Can display captions for each photo in the slideshow using the photo title from Flickr
* Can alter settings for each slideshow by including attributes after the shortcode (e.g. orientation="portrait")
* Can use Lightbox jQuery plugin to display each full size version of the thumbnail photos in the gallery using an overlaid window
* Can use Lightbox Slideshow jquery plugin to display a full size slideshow of photo in the thumbnails gallery using an overlaid window
* Can use Galleria jQuery plugin to display photos as slideshow/gallery combo
* Can select photos with different tags
* Can click through from slideshow to specified link
* Can sort photos by date, title or description
* See http://slickr-flickr.diywebmastery.com for more about using the plugin

== Installation ==

1. Uncompress the downloaded zip archive in [WordPress install root]/wp-content/plugins
1. Activate the plugin in your WordPress plugins control panel
1. Go to the "Settings" section, and choose "Slickr Flickr"
1. Type In your Flickr Id (it should look something like 12345678@N00) and then click the Save button
1. To use the plugin in a post, page or text widget use the shortcode [slickr-flickr tag="my tag phrase"]
1. If you have no photos in Flickr with this tag then no pictures are displayed
1. See http://slickr-flickr.diywebmastery.com/how-to/how-to-install-slickr-flickr-plugin for more about the plugin installation

== Frequently Asked Questions ==


* How do I find my Flickr Id

See http://slickr-flickr.diywebmastery.com/slickr-flickr-questions/where-do-i-find-my-flickr-id


* Changes to my photos on Flickr do not appear instantly in the Slickr Flickr slideshow or Gallery

This is due to caching of the Flickr RSS feed. No action is required. The cache will clear itself in a few hours. We hope to find a solution in the next release so we can flush the cache at will. 

* Only 20 photos are displayed in the gallery and I have tagged more than 20 photos

Slickr Flickr uses the Flickr RSS feed which has a limitation of returning only the 20 most recent tagged photos 


See http://slickr-flickr.diywebmastery.com/slickr-flickr-help for the full list of questions and answers about Slickr Flickr


== Screenshots ==

1. Using the admin settings to change the plugin defaults

1. How to Use Slickr Flickr to add a gallery or slideshow to a post

1. Example of a slideshow in a post

1. Example of a gallery in a post

1. Example of a small slideshow in a sidebar widget

1. Example of a slideshow/gallery combo in a post

1. Example of an overlaid lightbox which appears on clicking a thumbnail


== Changelog ==


= 1.7 =
* Make it clearer in the documentation that there is a limit of 20 photos
* Include attribution at both top and bottom of the galleria

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

= 1.7 = 
* Recommended but not mandatory


== How to Use The Plugin ==

The Flickr show is inserted into a post or a widget using the slickr-flickr short code

For example to show my pictures from Flickr that have been tagged with "bahamas" I would use : [slickr-flickr tag="bahamas"]

The Slickr Flickr Attributes (Parameters) are as follows: only the "tag" parameter is required

* tag - identifies what photos to display
* tagmode - set to ANY for fetching photos with different tags (default is ALL)   
* items - maximum number photos to display in the gallery or slideshow (default is 20)
* type - gallery or slideshow (default is gallery)
* orientation - landscape or portrait (default is landscape)
* size - small, medium, large or original (default is medium) 
* captions - whether captions are on or off (default is on)
* delay - delay in seconds between each image in the slide show (default is 5)
* start - number of the first slide or 'random' for a random start (default is 1)
* link - url to visit on clicking slideshow (optional) 
* id - the Flickr ID of the user (default is set up in the admin panel)
* group - set to 'y' if the Flickr id belongs to a group and not a user (default is n)
* attribution - credit the photographer (optional)
* sort - sort order of photos (optional)
* direction - sort order of photos (optional)
* descriptions - show descriptions beneath title on the lightbox - on or off (optional)
* flickr_link - include a link to the photo on Flickr on the lightbox - on or off (optional)
* photos_per_row - limit the number of photos per row in the gallery (optional)


You can set the parameters on each individual slideshow or set default values using the Admin Settings.

== How to Set Up The Plugin Defaults ==

If you don't want to specify all the settings for every slideshow you can set up some defaults. The default value is used when you have not specified a value on the individual slideshow or gallery.

* Go to the "Settings" section, and choose "Slickr Flickr"
* Enter your Flickr Id (the id of the form 12345678@N00) and choose whether it is a user or group id
* Enter the defaults number of photos to show
* Select the type of display - gallery, slideshow or slideshow/gallery combo
* Choose whether photos captions are displayed
* Enter what the delay in seconds is before the slideshow moves on to the next slide
* Choose the lightbox with either manual or autoplay slideshows


== Links ==

Here are some of the crucial Slickr Flickr Plugin links

* Plugin Home Page http://slickr-flickr.diywebmastery.com/
* Plugin Help and Support http://slickr-flickr.diywebmastery.com/slickr-flickr-help
