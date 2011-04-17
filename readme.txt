=== Blip Slideshow ===
Contributors: jasonhendriks
Donate link: http://www.jasonhendriks.com/programmer/blip-slideshow/
Tags: slideshow, media, rss, mrss, feed, feeds, photograph, picture, photo, image, smugmug, flickr, javascript, mootools, slideshow2
Requires at least: 2.8
Tested up to: 3.1.1
Stable tag: 0.1.1

A WordPress slideshow plugin fed from a SmugMug RSS feed and displayed using pure Javascript.

== Description ==

A WordPress slideshow plugin fed from a SmugMug RSS feed and displayed using pure Javascript.

Development began Apr 11th, 2011 and this is the first release. **Blip currently supports SmugMug RSS feeds only.**
Other media RSS feed types will be added soon.

== Installation ==

1. Download Blip
1. Unzip and upload the resulting folder to the '/wp-content/plugins/' directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place the [slideshow] shortcode in your posts and/or pages

**Examples for use**

*As a simple slideshow:*

>[slideshow rss=feed://www.smugmug.com/hack/feed.mg?Type=popular&Data=all&format=rss200&Size=Small]

*As a slideshow with thumbs and captions:*

>[slideshow rss=feed://www.smugmug.com/hack/feed.mg?Type=popular&Data=all&format=rss200&Size=Small captions=true thumbnails=true]

More code examples can be found at [The Blip homepage](http://www.jasonhendriks.com/programmer/blip-slideshow/blip-slideshow-examples/).

== Frequently Asked Questions ==

= How does it work? =

Blip is a wrapper for [Slideshow 2!](http://www.electricprism.com/aeron/slideshow/ 'Javascript MooTools Slideshow') by Aeron Glemann with some nifty RSS-reading magic. If you like Blip, please show your support to him.

= What is Media RSS? =

Media RSS (MRSS) is an RSS extension used for syndicating multimedia files (audio, video, image) in RSS feeds. Like your WordPress
RSS news feed, media RSS lists pictures instead of articles from websites like *Flickr* and *SmugMug*.

= Where can I find my SmugMug RSS feed URL? =

SmugMug has a [Help](http://www.smugmug.com/help/rss-atom-feeds "How to subscribe to RSS feeds") page. 

== Screenshots ==

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
the directory of the stable readme.txt, so in this case, `/tags/4.3/screenshot-1.png` (or jpg, jpeg, gif)
2. This is the second screen shot

== Changelog ==

= 0.1.1 =
* Enabled hyperlinking via the href and linked properties
* Switch from SmugMug's tiny images (100x100) to thumb images (150x150) for thumbnails
* Added a loader[true/false] property to the shortcode to control display of the loader icon
* Added CSS to override Slideshow's bottom:50px in the slideshow-thumbnail DIV

= 0.1 =
* Initial release: 2011-04-16

== Upgrade Notice ==

= 0.1.1 =
Thanks for beta testing! Various enhancements are ready.

= 0.1 =
The first version. Yay!

