=== Blip Slideshow ===
Contributors: jasonhendriks
Donate link: http://www.jasonhendriks.com/programmer/blip-slideshow/
Tags: slideshow, media, rss, mrss, feed, feeds, photograph, picture, photo, image, smugmug, flickr, javascript, mootools, slideshow2
Requires at least: 2.8
Tested up to: 3.1.1
Stable tag: trunk

A WordPress slideshow plugin fed from a SmugMug RSS feed and displayed using pure Javascript.

== Description ==

A WordPress slideshow plugin fed from a SmugMug RSS feed and displayed using pure Javascript.

Blip incorporates[ Slideshow 2!](http://www.electricprism.com/aeron/slideshow/) by Aeron Glemann with some nifty RSS-reading magic. If you like Blip, please show your support to him.

**Blip currently supports SmugMug RSS feeds only.**

== Installation ==

1. Download Blip
1. Unzip and upload the resulting folder to the '/wp-content/plugins/' directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place the [slideshow] shortcode in your posts and/or pages

**Examples for use**

*As a single slideshow:*

[slideshow rss=feed://www.smugmug.com/hack/feed.mg?Type=popular&Data=all&format=rss200&Size=Small]

*As a single slideshow with thumbs and captions:*

[slideshow rss=feed://www.smugmug.com/hack/feed.mg?Type=popular&Data=all&format=rss200&Size=Small captions="true" thumbnails="true"]

== Frequently Asked Questions ==

= What is Media RSS? =

Media RSS (MRSS) is an RSS extension used for syndicating multimedia files (audio, video, image) in RSS feeds. Like your WordPress
RSS news feed, media RSS lists pictures instead of articles from websites like *Flickr* and *SmugMug*.

= Where can I find my SmugMug RSS feed URL? =

SmugMug Help has a [WordPress](http://www.smugmug.com/help/rss-atom-feeds "How to subscribe to RSS feeds") page. 

== Screenshots ==

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
the directory of the stable readme.txt, so in this case, `/tags/4.3/screenshot-1.png` (or jpg, jpeg, gif)
2. This is the second screen shot

== Changelog ==

= 0.1 =
* Initial release.

== Upgrade Notice ==

= 0.1 =
2011-04-16: The first version. Yay!

== A brief Markdown Example ==

Ordered list:

1. Some feature
1. Another feature
1. Something else about the plugin

Unordered list:

* something
* something else
* third thing

Here's a link to [WordPress](http://wordpress.org/ "Your favorite software") and one to [Markdown's Syntax Documentation][markdown syntax].
Titles are optional, naturally.

[markdown syntax]: http://daringfireball.net/projects/markdown/syntax
            "Markdown is what the parser uses to process much of the readme file"

Markdown uses email style notation for blockquotes and I've been told:
> Asterisks for *emphasis*. Double it up  for **strong**.

`<?php code(); // goes in backticks ?>`