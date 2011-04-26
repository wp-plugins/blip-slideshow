=== Blip Slideshow ===
Contributors: jasonhendriks
Donate link: http://www.jasonhendriks.com/programmer/blip-slideshow/
Tags: slideshow, media, rss, mrss, feed, feeds, photograph, picture, photo, gallery, image, smugmug, flickr, javascript, mootools, slideshow2, lightbox, slimbox, colorbox
Requires at least: 2.7
Tested up to: 3.1.1
Stable tag: 1.0.0

A WordPress slideshow plugin fed from a SmugMug or Flickr RSS feed and displayed using pure Javascript.

== Description ==

**Whoops: v1.0.1 is broken out of the box. If you downloaded it, please delete and re-download v1.0.0.**

A WordPress slideshow plugin fed from a **SmugMug** or **Flickr** RSS feed and displayed using pure Javascript.
Blip does not hardcode what it finds into your blog. Instead the most recent images are loaded in real-time by the user's web browser.

See it in live use at my <a href="http://www.ambientphotography.ca/" alt="Toronto Wedding Photographer">wedding photography</a> website.
Are you using Blip? <a href="http://www.jasonhendriks.com/contact/">Let me know</a> so I can grab a screenshot :-)

== Installation ==

1. Download Blip
1. Unzip and upload the resulting folder to the '/wp-content/plugins/' directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. *(Optional)* Enable caching in the 'Settings' menu in WordPress under 'Blip Slideshow'
1. Place the [slideshow] shortcode in your posts and/or pages

**Detailed examples for use can be found at [the Blip homepage](http://www.jasonhendriks.com/programmer/blip-slideshow/)**

*As a simple slideshow:*

>[slideshow rss=feed://www.smugmug.com/hack/feed.mg?Type=popular&Data=all&format=rss200&Size=Small]

*As a slideshow with thumbs and captions:*

>[slideshow captions=true thumbnails=true rss=feed://www.smugmug.com/hack/feed.mg?Type=popular&Data=all&format=rss200&Size=Small]

== Frequently Asked Questions ==

= What are the main features? =

* Reads SmugMug and Flickr Media RSS Feeds
* WordPress templates load immediately; reading of Media RSS Feeds is performed in the background
* Caching of Media RSS Feeds for extra performance (must be enabled in Settings)
* Supports multiple slideshows in a single post/page
* Supports Lightbox plugins such as [Lightbox Plus](http://wordpress.org/extend/plugins/lightbox-plus/), [jQuery Lightbox For Native Galleries](http://wordpress.org/extend/plugins/jquery-lightbox-for-native-galleries/), [Slimbox](http://wordpress.org/extend/plugins/slimbox/), [WP-Slimbox2](http://wordpress.org/extend/plugins/wp-slimbox2/) and [Gameplorer's WPColorBox](http://wordpress.org/extend/plugins/gameplorers-wpcolorbox/)

= How does it work? =

Blip is a wrapper for [MooTools Slideshow 2!](http://www.electricprism.com/aeron/slideshow/ 'Javascript MooTools Slideshow') by Aeron Glemann with some nifty client-side RSS-reading magic. If you like Blip, please show your support to him.

= What is Media RSS? =

Media RSS (MRSS) is an RSS extension used for syndicating multimedia files (audio, video, image) in RSS feeds. Like your WordPress
RSS news feed, media RSS lists pictures instead of articles from websites like *Flickr* and *SmugMug*.

= Where can I find my SmugMug RSS feed URL? =

SmugMug has a [Help](http://www.smugmug.com/help/rss-atom-feeds "How to subscribe to RSS feeds") page and some [examples](http://wiki.smugmug.net/display/SmugMug/Feeds+Examples).

= Where can I find my Flickr RSS feed URL? =

Flickr has a [Help](http://www.flickr.com/get_the_most.gne#rss "How to use RSS and Atom Feeds") page. Also worth checking out is DeGrave.com's [Flickr RSS Feed Generator](http://www.degraeve.com/flickr-rss/).

== Screenshots ==

1. Blip running at [Ambient Photography](http://www.ambientphotography.ca/)
1. Blip running at [Ambient Photography - Services](http://www.ambientphotography.ca/services/)
1. Blip running at [Ambient Photography - Wedding Gallery](http://www.ambientphotography.ca/gallery/wedding-gallery/)

== Changelog ==

= 1.0.0 =
* Release date: 2011-04-23
* Tested in Safari 5/OS X, Firefox 3/OS X, IE 8/WinXP
* Official stable release
* Supports caching of RSS feeds (must be enabled in the Settings)
* Loads "blip-slideshow.css" from the root of your theme directory if it exists

= 0.4.2 =
* Release date: 2011-04-22
* Tested in Safari 5/OS X, Firefox 3/OS X, IE 8/WinXP
* Fixed error on settings page

= 0.4.1 =
* Release date: 2011-04-21
* Tested in Safari 5/OS X, Firefox 3/OS X, IE 8/WinXP
* Version 0.4 was released on WordPress.org prematurely
* Compatible with Colorbox plugin [jQuery Lightbox For Native Galleries](http://wordpress.org/extend/plugins/jquery-lightbox-for-native-galleries/)
* Compatible with Colorbox plugin [Gameplorer's WPColorBox](http://wordpress.org/extend/plugins/gameplorers-wpcolorbox/)

= 0.4 =
* Compatible with Colorbox plugin [Lightbox Plus](http://wordpress.org/extend/plugins/lightbox-plus/)
* Integration with Colorbox! Use link=lightbox and add a compatible plugin
* Removed CSS stlye background-color from slideshow-thumbnails-hidden, slideshow-thumbnails-inactive and slideshow-thumbnails-active. Overriding this inline was not working.
* Fixed bug where only one Slimbox was working in posts with multiple slideshows
* Fixed bug in parsing SmugMug thumbnails that aren't square
* For link=full or link=lightbox, will download SmugMug images that are smaller than the viewport
* Scripts and CSS are no longer loaded on pages that don't use Blip

= 0.3 =
* Release date: 2011-04-19
* Tested in Safari 5/OS X, Firefox 3/OS X, IE 8/WinXP
* Removed blip.css
* Compatible with Flickr RSS feeds 
* Compatible with Lightbox plugin [WP-Slimbox2](http://wordpress.org/extend/plugins/wp-slimbox2/) 
* Compatible with Lightbox plugin [Slimbox](http://wordpress.org/extend/plugins/slimbox/)
* Integration with Lightbox! w00t! Use new link=lightbox option and add a compatible plugin
* Fixed link boolean options
* Fixed resize boolean options

= 0.2 =
* Release date: 2011-04-18
* Tested in Safari 5/OS X, Firefox 3/OS X, IE 8/WinXP
* Bundled with Slideshow-1.3.1.110417
* Fixed the way SmugMug thumbnails are found (by height=100px).
* new shortcode options: center, link and resize.
* added JSON encoding of options for those that might include Javascript characters such as ' and /
* added a CDATA section to the Javascript call for proper parsing and XHTML validation
* Moved hyperlinking code from slideshow.js to blip.php (see http://code.google.com/p/slideshow/issues/detail?id=192)

= 0.1.1 =
* Release date: 2011-04-16
* Tested in Safari 5/OS X, Firefox 3/OS X, IE 8/WinXP
* Enabled hyperlinking via the href and linked properties
* Switch to SmugMug's tiny images (100x100) from thumb images (150x150) for thumbnails
* Added a loader[true/false] property to the shortcode to control display of the loader icon
* Added CSS to override Slideshow's bottom:50px in the slideshow-thumbnail DIV

= 0.1 =
* Release date: 2011-04-16
* Tested in Safari 5/OS X, Firefox 3/OS X, IE 8/WinXP
* Compatible with SmugMug RSS feeds 
* Development began: 2011-04-11
* Bundled with Slideshow-1.3.1

== Upgrade Notice ==

= 1.0.0 =
Thanks for beta testing! The first official stable version offers caching and a CSS style in your theme directory.

= 0.4.1 =
Thanks for beta testing! Now with Colorbox support and a variety of fixes and enhancements.

= 0.3 =
Thanks for beta testing! Now with Slimbox and Flickr support

= 0.2 =
Thanks for beta testing! New options! center, link and resize.

= 0.1.1 =
Thanks for beta testing! Various enhancements are ready.

= 0.1 =
The first version. Yay!

== Known Issues ==

Though MooTools is used in compatibility mode and will function with most Javascript frameworks including jQuery, it will break if script.aculo.us is loaded on your page, for example by [Lightbox 2](http://wordpress.org/extend/plugins/lightbox-2/). Use [Colorbox](http://wordpress.org/extend/plugins/search.php?q=colorbox) or [Slimbox](http://wordpress.org/extend/plugins/search.php?q=slimbox) plugins instead.

Although multiple slideshows per page are possible, only one of those slideshows can have a Lightbox.

== External Plugin Compatibility ==

* [Lightbox Plus](http://wordpress.org/extend/plugins/lightbox-plus/): OK
* [jQuery Colorbox](http://wordpress.org/extend/plugins/jquery-colorbox/): failed - no visible error, just won't show slideshow pics
* [JQuery Colorbox Zoom](http://wordpress.org/extend/plugins/jcolorboxzoom/): failed - uses jQuery, but doesn't load it
* [Gameplorer's WPColorBox](http://wordpress.org/extend/plugins/gameplorers-wpcolorbox/): OK
* [Simple Cbox](http://wordpress.org/extend/plugins/simple-cbox/): failed - does not use jQuery in compatibility mode
* [jQuery Lightbox For Native Galleries](http://wordpress.org/extend/plugins/jquery-lightbox-for-native-galleries/): OK
* [Slimbox](http://wordpress.org/extend/plugins/slimbox/): OK
* [WP Slimbox Reloaded](http://wordpress.org/extend/plugins/wp-slimbox-reloaded/): failed - coded for MooTools 1.2, not MooTools 1.3
* [Slimbox Plugin](http://wordpress.org/extend/plugins/slimbox-plugin/): failed - hardcoded the call to the mootools API, which is a no-no
* [WP-Slimbox2 Plugin](http://wordpress.org/extend/plugins/wp-slimbox2/): OK
* [SlimBox2 for WordPress](http://wordpress.org/extend/plugins/slimbox2-for-wordpress/): failed - uses jQuery, but doesn't load it

== To Do ==

* Enhance: Allow multiple slideshows with Slimboxes per page (find trigger for slideshow resume)
* Enhance: Input validation

== Resources ==

Some information I found invaluable for this project:

* http://groups.google.com/group/mootools-users/browse_thread/thread/4858bdee5b1d0f56/d6ad5aa2fcc99dba?fwc=1
* http://mootools.net/docs/more/Request/Request.Queue
* http://mootools.net/demos/?demo=Slick.Finder
* http://mootools-users.660466.n2.nabble.com/Moo-XML-parsing-1-3-and-today-td5187586.html
* https://gist.github.com/775347
* http://www.regular-expressions.info/javascript.html
* http://php.net/manual/en/function.rawurlencode.php
* https://mootools.lighthouseapp.com/projects/2706/tickets/182-request-html-only-parses-xml
* http://ryanflorence.com/mootools-class/
* http://stackoverflow.com/questions/1178511/accessing-a-mootools-class-method-from-outside-the-class
* http://stackoverflow.com/questions/1091022/how-do-i-write-a-simple-php-transparent-proxy
* http://www.howtogeek.com/howto/programming/php-get-the-contents-of-a-web-page-rss-feed-or-xml-file-into-a-string-variable/
* http://www.tek-tips.com/viewthread.cfm?qid=1268652&page=1
* http://www.permadi.com/tutorial/urlEncoding/
* http://php.net/manual/en/function.parse-url.php
* http://php.net/manual/en/language.operators.comparison.php
* http://www.php.net/manual/en/function.html-entity-decode.php
* http://www.w3schools.com/PHP/php_sessions.asp
* http://keetology.com/blog/2009/10/27/up-the-moo-herd-iv-theres-a-class-for-this
* http://stackoverflow.com/questions/66837/when-is-a-cdata-section-necessary-within-a-script-tag
* http://groups.google.com/group/mootools-slideshow/browse_thread/thread/9b10474b60cf7f1a/564f16f97c82167a?lnk=gst&q=slimbox#564f16f97c82167a
* http://codex.wordpress.org/Managing_Plugins
* http://groups.google.com/group/mootools-slideshow/browse_thread/thread/cdeededf62e6b458/f4df7e2cabb12f59?lnk=gst&q=lightbox#f4df7e2cabb12f59
* http://scribu.net/wordpress/optimal-script-loading.html
* http://www.javascriptkit.com/dhtmltutors/ajaxticker/ajaxticker2.shtml
* http://tech.michaelerb.net/wordpress-tutorials/how-to-determine-absolute-path-with-a-tiny-php-script/
* http://wordpress.org/support/topic/how-to-use-wordpress-functions-outside-of-the-blog?replies=7
* http://codex.wordpress.org/Integrating_WordPress_with_Your_Website
* http://striderweb.com/nerdaphernalia/2008/06/wp-use-action-links/
