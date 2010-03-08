=== Slickr Flickr ===
Contributors: powerblogservice
Donate link: http://www.wordpresswise.com/slickr-flickr/donate/
Tags: flickr, flickr slideshow, flickr gallery, lightbox, wordpresswise
Requires at least: 2.8
Tested up to: 2.9.2
Stable tag: 1.3

Displays a set of tagged photos from your Flickr account either as gallery or a unbranded slideshow in posts, pages, or sidebar widgets

== Description ==

* Displays tagged Flickr photos as a gallery or a unbranded slideshow (i.e not Flickr's own slideshow widget)
* Uses "slickr-flickr" shortcode to make adding a Flickr show as easy as possible
* Can display more than one gallery or slideshow on the same page/post
* Can display slideshows in sidebar widgets
* Can display slideshows at medium and small Flickr Photo sizes (500 by 375 and 240 by 180 px)
* Can display slideshows as portrait or landscape
* Best fits the slideshow if a mix of landscape and portrait photos are used
* Can display captions for each photo in the slideshow using the photo title from Flickr
* Can alter slideshow settings for each slideshow by including attributes after the shortcode (e.g orientation="portrait")
* Uses Lightbox jquery plugin to display each photo in the gallery using an overlaid window
* See http://www.wordpresswise.com/slickr-flickr for more about using the plugin

== Installation ==

1. Uncompress the downloaded zip archive in [WordPress install root]/wp-content/plugins
1. Activate the plugin in your WordPress plugins control panel
1. Go to the "Settings" section, and choose "Slickr Flickr"
1. Type In your Flickr Id (it should look something like 12345678@N00) and then click the Save button
1. To use the plugin in a post, page or text widget use the shortcode [slickr-flickr tag="my tag phrase"]
1. If you have no photos in Flickr with this tag then no pictures are displayed
1. See http://www.wordpresswise.com/slickr-flickr#install for more about the plugin installation

== Frequently Asked Questions ==

1. The message "Please set up a Flickr User id for this slideshow" appears in place of a slideshow.

You need either to go to the Settings for slickr-flickr and enter your Flickr Id or specify id="my flickr id" beside the shortcode. For example [slickr-flickr id="23437487@N00" tag="bahamas"]

2. The message "Please set up a Flickr tag for this slideshow" appears in place of a slideshow.

You need to specify a tag beside the shortcode. For example [slickr-flickr tag="bahamas"]

3. The message "No photos available for my tag" appears in place of a slideshow.

This means that Flickr cannot find any photos for the tag you supplied. Sometimes there can be a delay of up tp 30 minutes between tagging a photo on Flickr before it becomes available for a slideshow. It is therefore best to tag your photos in Flickr first before adding them to your Wordpress site.<br/>

4. If I upload a new photo to Flickr and then tag using a phrase for which I have already got a slideshow or gallery, then will the new photos automatically appear on my blog.

Yes. The Slickr Flickr will automatically show the most recent photos.

5. What happens if I try to display both landscape and portrait photos on the same slideshow?

The plugin will try and resize the photos to fit as best as possible.  Ideally you should not mix portrait and landscape photos in the same slideshow.

6. I want to customize the gallery lightbox as my the background color of my site is dark.

You can do this by editing the supplied plugin CSS files at your own risk. For example, to change the highlight color when the cursor is moved over an image in the gallery then edit the background-color in the line: ".slickr-flickr-gallery ul a:hover img { background-color: brown; } " in the file jquery.gallery.css and replace "brown" by a lighter color.

7. I have set the size to large and Flickr tells me there are no photos.

Flickr does not have large photos (1024 x 768) so you need to remove size=”large” parameter or alternatively you need to upload some bigger photos to Flickr and then tag them appropriately

8. How do I display photos tagged with "ice cream" from one of my friends's Flickr galleries

You need to specify your friend's Flickr Id beside the shortcode. For example [slickr-flickr id="87654321@N00" tag="ice cream"]


== Screenshots ==

1. How to Use Slickr Flickr to add a gallery or slideshow to a post

1. Example of a slideshow in a post

1. Example of a gallery in a post

1. Example of a small slideshow in a sidebar widget

1. Using the admin settings to change the plugin defaults


== Changelog ==

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

= 1.3 = 
* Required if your Flickr Id belongs to a group rather than an user


== How to Use The Plugin ==

The Flickr show is inserted into a post or a widget using the slickr-flickr short code

For example to show my pictures from Flickr that have been tagged with "bahamas" I would use : [slickr-flickr tag="bahamas"]

The Slickr Flickr Attributes (Parameters) are

* tag - identifies what photos to display
* id - the Flickr ID of the user
* group - set to 'y' if the Flickr id belongs to a group and not a user (default is n)
* items - maximum number photos to display in the gallery or slideshow (default is 20)
* type - gallery or slideshow (default is gallery)
* orientation - landscape or portrait (default is landscape)
* size - small, medium, large or original (default is medium) - for best results only use large or originals with galleries
* captions - whether captions are on or off (default is on)
* delay - delay in seconds between each image in the slide show (default is 5)
* start - number of the first slide or 'random' for a random start (default is 1)

Only the "tag" parameter is mandatory

You can set the parameters on each individual slideshow or set default values using the Admin Settings.

== How to Set Up The Plugin Defaults ==

If you don't want to specify all the settings for every slideshow you can set up some defaults. The default value is used when you have not specified avalue on the individual slideshow or gallery.

* Go to the "Settings" section, and choose "Slickr Flickr"
* Enter your Flickr Id (the id of the form 12345678@N00) and choose whether it is a user or group id
* Enter the defaults number of photos to show
* Select the type of display - gallery or slideshow
* Choose whether photos captions are displayed
* Enter what the delay in seconds is before the slideshow moves on to the next slide


== Links ==

Here are some of the crucial Slickr Flickr Plugin links

* Plugin Home Page http://www.wordpresswise.com/slickr-flickr
* Tips and Best Practices http://www.wordpresswise.com/slickr-flickr/tips
* Questions and Answers http://www.wordpresswise.com/slickr-flickr/questions
* Known Issues http://www.wordpresswise.com/slickr-flickr/issues
* Download http://downloads.wordpress.org/plugin/slickr-flickr.zip
