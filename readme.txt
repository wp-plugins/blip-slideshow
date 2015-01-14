=== Blip Slideshow ===
Contributors: jasonhendriks
Donate link: http://www.jasonhendriks.com/programmer/blip-slideshow/
Tags: slideshow, media, rss, mrss, feed, feeds, photograph, picture, photo, gallery, image, smugmug, flickr, mobileme, picasa, photobucket, javascript, mootools, slideshow2, lightbox, slimbox, colorbox
Requires at least: 2.7
Tested up to: 4.1
Stable tag: 1.2.7

A WordPress slideshow plugin fed from a SmugMug, Flickr, MobileMe, Picasa or Photobucket RSS feed and displayed using pure Javascript.

== Description ==

A WordPress slideshow plugin fed from a **SmugMug**, **Flickr**, **MobileMe**, **Picasa** or **Photobucket** RSS feed and displayed using pure Javascript.
Blip does not hardcode what it finds into your blog. Instead the most recent images are loaded in real-time by the user's web browser.

See it in live use at my <a href="http://www.ambientphotography.ca/" alt="Toronto Wedding Photographer">wedding photography</a> website.

> Please note that Blip is [*not* compatible with the Javascript framework Prototype](http://mootorial.com/wiki/mootorial/00a-mootoolsvsothers). Please check for "prototype.js" in your webpage, included by your theme or other plugin, before contacting me for help.

Requires WordPress 2.7 and PHP 5.

== Installation ==

1. Download Blip Slideshow
1. Unzip and upload the resulting folder to the '/wp-content/plugins/' directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. *(Optional)* Enable caching in the 'Settings' menu in WordPress under 'Blip Slideshow'
1. Place the [blip-slideshow] shortcode in your posts and/or pages. A theme template function call is also available.

**Detailed examples for use can be found at [the Blip homepage](http://www.jasonhendriks.com/programmer/blip-slideshow/)**.

*A simple SmugMug slideshow example:*

> [blip-slideshow thumbnails=false rss=feed://www.smugmug.com/hack/feed.mg?Type=popular&Data=all&format=rss200&Size=Medium]

== Frequently Asked Questions ==

= How does it work? =

Blip is a wrapper for [MooTools Slideshow 2!](http://www.electricprism.com/aeron/slideshow/ 'Javascript MooTools Slideshow') by Aeron Glemann with some nifty client-side RSS-reading magic. If you like Blip, please show your support to him.

= What are the features? =

* Verified to work with SmugMug, Flickr, MobileMe, Picasa Web and Photobucket Media RSS Feeds
* Theoretically compatible with any Media RSS Feed
* WordPress templates load immediately; reading of Media RSS Feeds is performed in the background
* Supports server-side caching of Media RSS Feeds via a writable cache directory for extra performance (must be enabled in Settings)
* Supports client-side caching of Media RSS Feeds via HTTP 304 for extra performance (must be enabled in Settings)
* Supports multiple slideshows in a single post/page
* Supports Lightbox plugins such as [Lightbox Plus](http://wordpress.org/extend/plugins/lightbox-plus/), [jQuery Colorbox](http://wordpress.org/extend/plugins/jquery-colorbox/), [jQuery Lightbox For Native Galleries](http://wordpress.org/extend/plugins/jquery-lightbox-for-native-galleries/), [Slimbox](http://wordpress.org/extend/plugins/slimbox/), [WP-Slimbox2](http://wordpress.org/extend/plugins/wp-slimbox2/) and [Gameplorer's WPColorBox](http://wordpress.org/extend/plugins/gameplorers-wpcolorbox/)

= What is Media RSS? =

[Media RSS (MRSS)](http://www.rssboard.org/media-rss) is an RSS extension used for syndicating multimedia files (audio, video, image) in RSS feeds. Like your WordPress
RSS news feed, media RSS lists pictures instead of articles from photo-sharing websites.

= Where can I find my RSS feed URL? =

* SmugMug has a [Help](http://www.smugmug.com/help/rss-atom-feeds "How to subscribe to RSS feeds") page and some [examples](http://wiki.smugmug.net/display/SmugMug/Feeds+Examples).
* Flickr has a [Help](http://www.flickr.com/get_the_most.gne#rss "How to use RSS and Atom Feeds") page. Also worth checking out is DeGrave.com's [Flickr RSS Feed Generator](http://www.degraeve.com/flickr-rss/).
* Picasa Web has a [Using RSS Feeds](http://picasa.google.com/support/bin/answer.py?hl=en&answer=47351) page and a [Creating custom RSS feeds](http://picasa.google.com/support/bin/answer.py?hl=en&answer=99373) page.
* Photobucket has a [Help](http://pic.pbsrc.com/dev_help/RSS/Photobucket_RSS_Feeds.htm "Photobucket RSS Feeds") page.
* For MobileMe, click the "Subscribe" icon found at the top of your MobileMe gallery on the MobileMe website. Note: This icon is on the *public* version of your gallery (gallery.me.com). Not the version you see when you are logged in (www.me.com).

= Known Issues =

* Blip is [*not* compatible with the Javascript framework Prototype](http://mootorial.com/wiki/mootorial/00a-mootoolsvsothers). Please check for "prototype.js" in your webpage, included by your theme or other plugin, before contacting me for help.
* Although multiple slideshows per page are possible, only Colorbox plugins support two or more of those slideshows having a Lightbox.
* [Slideshow type "Fold" does not work in Internet Explorer](http://code.google.com/p/slideshow/issues/detail?id=195).

= How can I contact the author? =

Send me a [question or comment](http://www.jasonhendriks.com/contact/ "Contact Jason Hendriks") at my webpage.

== Screenshots ==

1. Blip running at [Ambient Photography](http://www.ambientphotography.ca/)
1. Blip running at [Ambient Photography - Services](http://www.ambientphotography.ca/services/)
1. Blip running at [Ambient Photography - Wedding Gallery](http://www.ambientphotography.ca/gallery/wedding-gallery/)

== Changelog ==

= 1.2.7 =
* Release date: 2015-01-13
* Tested in Safari 8 on OS X using Wordpress 4.1 on Apache/OS X
* No change other than a cute Wordpress Plugin icon

= 1.2.6 =
* Release date: 2011-05-15
* Tested in Safari 5/OS X, Firefox 3/OS X, Firefox 4/WinXP, IE 8/WinXP
* Bug: some WordPress hosts can not handle curl redirects. Fix: Added an option in the settings menu to control this behavior
* Bug: some WordPress hosts do not support add_footer_scripts(). Fix: Added an option in the settings menu to control this behavior

= 1.2.5 =
* Release date: 2011-05-05
* Tested in Safari 5/OS X, Firefox 3/OS X, Firefox 4/WinXP, IE 8/WinXP
* Next-Gen Gallery also uses the shortcode [slideshow]. Added shortcodes [blip-slideshow] (for collisions) and [blip_slideshow] (for WordPress installations older than v3)
* Blip will attempt to detect if another extension has loaded Prototype or if Prototype has been hardcoded in a theme or post, or if MooTools has otherwise failed to load and display an error message accordingly.
* In debug mode, the unencoded Media RSS URL is displayed instead of the encoded URL

= 1.2.4 =
* Release date: 2011-05-02
* Tested in Safari 5/OS X, Firefox 3/OS X, Firefox 4/WinXP, IE 8/WinXP
* Bug: SmugMug slides appear blank in the case when SmugMug doesn't recommend a media:content to choose. Fix: Choose the largest slide available.
* Compatible with Colorbox plugin [jQuery Colorbox](http://wordpress.org/extend/plugins/jquery-colorbox/)

= 1.2.3 =
* Release date: 2011-05-01
* Tested in Safari 5/OS X, Firefox 3/OS X, Firefox 3/WinXP, IE 8/WinXP
* Proper version numbers on linked stylesheets and Javascript files
* Compatible with Google Picasa Web RSS feeds
* Compatible with Photobucket RSS feeds

= 1.2.2 =
* Release date: 2011-04-30
* Tested in Safari 5/OS X, Firefox 3/OS X, Firefox 3/WinXP, IE 8/WinXP
* Returns HTTP 304 (Not Modified) when client sends appropriate "if-modified-since" or "if-none-match" header - super speedy!!!
* These options now work as documented: delay, duration, loop, paused, random, slide
* Added function Blip_Slideshow::slideshow($atts) for creating a slideshow directly in a theme template
* Added shortcode [blip-version] for internal use
* Using REST-style URL for retrieving the media RSS file
* MobileMe slideshows no longer search for thumbnails that don't exist
* Always pull large-size images from Flickr feeds

= 1.2.1 =
* Release date: 2011-04-28
* Tested in Safari 5/OS X, Firefox 3/OS X, IE 8/WinXP
* Flickr images will load in high-resolution if a big enough width or height option is passed to Blip

= 1.2.0 =
* Release date: 2011-04-28
* Tested in Safari 5/OS X, Firefox 3/OS X, IE 8/WinXP
* Not strictly backwards compatible with previous versions, as the following defaults have changed to match the out-of-the-box behavior of MooTools Slideshow2: captions=true, controller=true, delay=2000, duration=1000, thumbnails=true
* New documentation: see [Internal Examples](http://www.jasonhendriks.com/programmer/blip-slideshow/blip-slideshow-examples/) and [External Examples](http://www.jasonhendriks.com/programmer/blip-slideshow/blip-slideshow-external-examples/) and [Styling](http://www.jasonhendriks.com/programmer/blip-slideshow/styling-blip-slideshow/)
* New Slideshow types: flash, fold, kenburns and push - see the new type option
* New option color - controls the colour of the flash show
* New option pan - controls the pan for the kenburns show
* New option zoom - controls the zoom for the kenburns show
* New option transition - controls the transition for the push show
* The out-of-the box MooTools Slideshow2 slideshow.css file is back

= 1.1.0 =
* Release date: 2011-04-27
* Tested in Safari 5/OS X, Firefox 3/OS X, IE 8/WinXP
* Compatible with MobileMe RSS feeds
* (Hopefully) compatible with any RSS feed
* Fixed bug closing two or more Colorbox Lightboxes on a page with multiple slideshows
* More robust caching of Media RSS files, both client and server side
* Added fast=3 option to eliminate a "blink" at the start of the slideshow (undocumented for now)
* Handle RSS feeds that send HTTP redirects
* HTTPS support

= 1.0.0 =
* Release date: 2011-04-23
* Tested in Safari 5/OS X, Firefox 3/OS X, IE 8/WinXP
* Official stable release
* Supports server-side caching of RSS feeds (must be enabled in the Settings)
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

= 1.2.6 =
Compatibility fixes (especially for host 'antagonist.nl').

= 1.2.5 =
Will display an error if Prototype is detected. Added shortcode [blip-slideshow] in case of collisions with other extensions.

= 1.2.4 =
Fixed a bug that made some SmugMug images not appear. Compatible with jQuery Colorbox.

= 1.2.3 =
Now with support for Picasa Web and Photobucket feeds, and client-side cache.

= 1.2.0 =
Option defaults have changed, please check the changelog. New slideshow types: Flash, Fold, Ken Burns and Push

= 1.1.0 =
Now with Support for MobileMe RSS Feeds and WordPress installs running HTTPS.

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

== Lightbox Plugin Compatibility Guide ==

* [Lightbox Plus](http://wordpress.org/extend/plugins/lightbox-plus/): OK
* [jQuery Colorbox](http://wordpress.org/extend/plugins/jquery-colorbox/): OK
* [JQuery Colorbox Zoom](http://wordpress.org/extend/plugins/jcolorboxzoom/): failed - uses jQuery, but doesn't load it
* [Gameplorer's WPColorBox](http://wordpress.org/extend/plugins/gameplorers-wpcolorbox/): OK
* [Simple Cbox](http://wordpress.org/extend/plugins/simple-cbox/): failed - does not use jQuery in compatibility mode
* [jQuery Lightbox For Native Galleries](http://wordpress.org/extend/plugins/jquery-lightbox-for-native-galleries/): OK
* [Slimbox](http://wordpress.org/extend/plugins/slimbox/): OK
* [WP Slimbox Reloaded](http://wordpress.org/extend/plugins/wp-slimbox-reloaded/): failed - compatible with MooTools 1.2, not 1.3
* [Slimbox Plugin](http://wordpress.org/extend/plugins/slimbox-plugin/): failed - hardcoded the MooTools API, which is a no-no
* [WP-Slimbox2 Plugin](http://wordpress.org/extend/plugins/wp-slimbox2/): OK
* [SlimBox2 for WordPress](http://wordpress.org/extend/plugins/slimbox2-for-wordpress/): failed - uses jQuery, but doesn't load it

== Resources ==

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
* http://www.mac-forums.com/forums/images-graphic-design-digital-photography/31805-photocast.html
* http://forums.devshed.com/php-development-5/curl-get-final-url-after-inital-url-redirects-544144.html
* http://code.garyjones.co.uk/get-wordpress-plugin-version/
* http://wordpress.stackexchange.com/questions/7782/wp-script-versioning-breaks-cross-site-caching
* http://fgiasson.com/blog/index.php/2006/07/19/hack_for_the_encoding_of_url_into_url_pr/
* http://code.google.com/speed/page-speed/docs/caching.html