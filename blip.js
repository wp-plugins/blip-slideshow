/*
		** Requires MooTools 1.3 **

    Copyright (C) 2011  Jason Hendriks

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

 */
 
var Blip = new Class({
  Implements: [Options],
  initialize: function(element, rss, link, options){
		this.element = element;
		this.link = new Link(link);
		this.setOptions(options);
		new Request({
			url: rss,
			onSuccess: this.processRequest.bind(this)
		}).send();
	},
	processRequest: function(newResponseText, newResponseXml){
		var lightboxHelper = LightboxHelper.createLightboxHelper(this.link);
		var parser = MediaRssParser.createParser(this.link, newResponseText, newResponseXml);
		if(parser) {
			var slideshowData = SlideshowHelper.createSlideshowData(parser.slideshowImages);
			var myShow = new Slideshow(this.element, slideshowData, this.options);
			if(this.link.lightboxHelper) {
				this.link.lightboxHelper.addEvents(this.element, parser.slideshowImages, myShow);
			}
			console.log(slideshowData);
		}
	}
});

var Link = new Class({
	initialize: function(link) {
		this.link = link;
		this.lightboxHelper = LightboxHelper.createLightboxHelper(link);
		if(link == "full" || link == "true") {
			this.linkNum = 1;
		} else if(link == "none" || link == "false") {
			this.linkNum = 0;
		} else if(link == "href") {
			this.linkNum = 2;
		} else if(this.lightboxHelper) {
			this.linkNum = 4;
		} else {
			this.linkNum = 3;
		}
		var viewport = new Viewport();
		this.viewportWidth = viewport.width;
		this.viewportHeight = viewport.height;
		if(this.lightboxHelper && this.lightboxHelper.isSlimbox()) {
		  // adjust for Slimbox
  		this.viewportWidth = this.viewportWidth - 20; // 10 left 10 right
  		this.viewportHeight = this.viewportHeight - 62; // 10 top 52 bottom
		}
	},
	isImageTooBig: function(imageWidth, imageHeight) {
		if(this.linkNum == 1) {
			return false;
		} else if(this.linkNum == 4) {
			var result = imageWidth > this.viewportWidth || imageHeight > this.viewportHeight;
			return result;
		} else {
			return true;
		}
	},
	setImageLink: function(image) {
		if(this.linkNum == 1) {
			image.linkUrl = image.largeUrl;
		} else if(this.linkNum == 0 || this.linkNum == 4) {
			image.linkUrl = '';
		} else if(this.linkNum == 2) {
			image.linkUrl = image.hrefUrl;
		} else {
			image.linkUrl = this.link;
		}
	}
})

/* The RSS parsers */

var MediaRssParser = new Class({
});
MediaRssParser.createParser = function(newLink, newResponseText, newResponseXml){
	var generator = Slick.find(newResponseXml, 'generator');
	if(generator) {
		generator = generator.firstChild.nodeValue;
	}
	if(generator == "http://www.smugmug.com/") {
		return new SmugMugRssParser(newLink, newResponseText, newResponseXml);
	} else if(generator == "http://www.flickr.com/") {
		return new FlickrRssParser(newLink, newResponseText, newResponseXml);
	} else if(generator == "MobileMe") {
		return new MobileMeRssParser(newLink, newResponseText, newResponseXml);
	} else if(generator == "DotMac 1.0") {
		return new DotMacRssParser(newLink, newResponseText, newResponseXml);
	} else {
		return new GenericRssParser(newLink, newResponseText, newResponseXml);
	}
}

var SmugMugRssParser = new Class({
	Extends: MediaRssParser,
	initialize: function(link, newResponseText, newResponseXml){
		var responseXml = newResponseXml;
		//var responseText = newResponseText.replace(/<(\/)?([A-Z]+):([a-z]+)/gi,'<$1$2_$3');
		//responseXml = new DOMParser().parseFromString(responseText, 'text/xml');
		var items = Slick.search(responseXml, 'item');
		var counter = 0;
		var slideshowImages = new Array(items.length);
		this.slideshowImages = slideshowImages;
		items.each(function(item){
			var image = {};
			image.hrefUrl = Slick.find(item, 'link').firstChild.nodeValue; // the SmugMug gallery
			//image.largeUrl = Slick.find(item, 'guid').firstChild.nodeValue; // the Original image
			image.caption = Slick.find(item, 'title').firstChild.nodeValue;
			// image.slideUrl = Slick.find(item, 'media_group > media_content[isDefault]').getProperty('url'); // the sized image
			image.slideUrl = Slick.find(item, 'media_group > media_content[isDefault]').attributes[0].value;
			this.processMediaGroup(image, Slick.search(item, 'media_group > media_content[url]'), link);
			link.setImageLink(image);
			slideshowImages[counter++] = image;
		}, this);
	},
	processMediaGroup: function(newImage, newMediaGroup, link) {
		// determine the thumb image and the large image
		var previousBiggestHeight = 0;
		var previousBiggestWidth = 0;
		newMediaGroup.each(function(oneImage){
			var width = 0;
			var height = 0;
			var url = '';
			for(var i=0; i<oneImage.attributes.length; i++) {
				if (oneImage.attributes[i].name == "height") {
					height = parseInt(oneImage.attributes[i].value);
				}
				else if (oneImage.attributes[i].name == "width") {
					width = parseInt(oneImage.attributes[i].value);
				}
				else if (oneImage.attributes[i].name == "url") {
					url = oneImage.attributes[i].value;
				}
			}
			if(height == 100 || width == 100) {
				newImage.thumbUrl = url;
			}
			if((height > previousBiggestHeight || width > previousBiggestWidth) && !link.isImageTooBig(width, height)) {
				previousBiggestHeight = height;
				previousBiggestWidth = width;
				newImage.largeUrl = url;
			}
		}, this);
	}
});

var FlickrRssParser = new Class({
	Extends: MediaRssParser,
	initialize: function(link, newResponseText, newResponseXml){
		var items = Slick.search(newResponseXml, 'item');
		var slideshowImages = new Array(items.length);
		this.slideshowImages = slideshowImages;
		var counter = 0;
		items.each(function(item){
			var image = {};
			image.caption = Slick.find(item, 'title').firstChild.nodeValue;
			image.hrefUrl = Slick.find(item, 'link').firstChild.nodeValue; // the Flickr gallery
			image.slideUrl = Slick.find(item, 'description').firstChild.nodeValue.replace(/[\s\S]+img src\=.([\s\S]+). width[\s\S]+/g,'$1'); // the sized image
			// image.largeUrl = Slick.find(item, 'media_content').getProperty('url'); // the large image
			image.largeUrl = Slick.find(item, 'media_content').attributes[0].value; // the large image
			// image.thumbUrl = Slick.find(item, 'media_thumbnail').getProperty('url'); // the thumbnail
			image.thumbUrl = Slick.find(item, 'media_thumbnail').attributes[0].value; // the thumbnail
			link.setImageLink(image);
			slideshowImages[counter++] = image;
		}, this);
	}
});

var MobileMeRssParser = new Class({
	Extends: MediaRssParser,
	initialize: function(link, newResponseText, newResponseXml){
		var items = Slick.search(newResponseXml, 'entry');
		var slideshowImages = new Array(items.length);
		this.slideshowImages = slideshowImages;
		var counter = 0;
		items.each(function(item){
			var image = {};
			image.caption = Slick.find(item, 'dotmac_content').firstChild.nodeValue;
			image.hrefUrl = Slick.find(item, 'link').firstChild.nodeValue; // the Mobile Me gallery
			image.slideUrl = image.hrefUrl + Slick.find(item, 'dotmac_webImagePath').firstChild.nodeValue; // the sized image
			image.largeUrl = image.hrefUrl + Slick.find(item, 'dotmac_largeImagePath').firstChild.nodeValue; // the large image
			link.setImageLink(image);
			slideshowImages[counter++] = image;
		}, this);
	}
});

var DotMacRssParser = new Class({
	Extends: MediaRssParser,
	initialize: function(link, newResponseText, newResponseXml){
		var items = Slick.search(newResponseXml, 'item');
		var slideshowImages = new Array(items.length);
		this.slideshowImages = slideshowImages;
		var counter = 0;
		items.each(function(item){
			var image = {};
			image.caption = Slick.find(item, 'title').firstChild.nodeValue;
			image.hrefUrl = Slick.find(item, 'link').firstChild.nodeValue; // the Mobile Me gallery
			// check for an enclosure
			var enclosure = Slick.find(item, 'enclosure');
			if(enclosure != null) {
				image.slideUrl = enclosure.attributes[0].value;
				image.largeUrl = image.slideUrl.replace(/web.jpg/,'large.jpg'); // the large image
			} else {
				var description = image.slideUrl = Slick.find(item, 'description').firstChild.nodeValue;
				console.log(description);
			}
			// there is also a hidden medium.jpg
			link.setImageLink(image);
			slideshowImages[counter++] = image;
		}, this);
	}
});

var GenericRssParser = new Class({
	Extends: MediaRssParser,
	initialize: function(link, newResponseText, newResponseXml){
		var items = Slick.search(newResponseXml, 'item');
		var slideshowImages = new Array(items.length);
		this.slideshowImages = slideshowImages;
		var counter = 0;
		items.each(function(item){
			var image = {};
			image.caption = Slick.find(item, 'title').firstChild.nodeValue;
			image.hrefUrl = Slick.find(item, 'link').firstChild.nodeValue; // the Flickr gallery
			image.slideUrl = image.largeUrl = Slick.find(item, 'media_content').attributes[0].value; // the large image
			image.thumbUrl = Slick.find(item, 'media_thumbnail').attributes[0].value; // the thumbnail
			link.setImageLink(image);
			slideshowImages[counter++] = image;
		}, this);
		console.log(slideshowImages);
	}
});

/* The MooTools Slideshow 2! helper */

var SlideshowHelper = new Class({
});
SlideshowHelper.createSlideshowData = function(newImages) {
	var data = new Object();
	newImages.each(function(image){
		data[image.slideUrl] = {caption: image.caption, href: image.linkUrl, thumbnail: image.thumbUrl};
	});
	return data;
}

/* The lightbox helpers */

var LightboxHelper = new Class({
	isSlimbox: function() {
		return false;
	},
	isColorbox: function() {
		return false;
	}
});
LightboxHelper.createLightboxHelper = function(link) {
	if(link == 'slimbox') {
		return new SlimboxHelper();
	} else if(link == 'colorbox') {
		return new ColorboxHelper();
	}
}

var SlimboxHelper = new Class({
	Extends: LightboxHelper,
	isSlimbox: function() {
		return true;
	},
	addEvents: function(newElement, newImages, newSlideshow) {
		$$('div#'+newElement+' div.slideshow-images a').each(function(a) {
			a.style.cursor = 'pointer';
		}).addEvent('click', function() {
			var slide = newImages[newSlideshow.slide];
			jQuery.slimbox(slide.largeUrl, slide.caption, {resizeDuration: 200, overlayFadeDuration: 200, captionAnimationDuration: 100});
			newSlideshow.pause(1);
		});

		$$('#lbOverlay, #lbCloseLink').addEvent('click', function(){
			// theres no callback for the close() function in slimbox so we'll have to manually add
			// a function to the elements that trigger close in order to resume the show
			// @TODO: this breaks with multiple slideshows in a page
			newSlideshow.pause(0);
		});
	}
});

var ColorboxHelper = new Class({
	Extends: LightboxHelper,
	isColorbox: function() {
		return true;
	},
	addEvents: function(newElement, newImages, newSlideshow) {
		$$('div#'+newElement+' div.slideshow-images a').each(function(a) {
			a.style.cursor = 'pointer';
			jQuery('document').colorbox({
				onClosed: function() {
					newSlideshow.pause(0);
				}
			});
		}).addEvent('click', function() {
			var slide = newImages[newSlideshow.slide];
			jQuery.colorbox({title:slide.caption, href:slide.largeUrl, width:"100%", height:"100%", scalePhotos:true});
			newSlideshow.pause(1);
		});
	}
});

var Viewport = new Class({
	initialize: function(newLink){
    if (typeof window.innerWidth != 'undefined'){
			// the more standards compliant browsers (mozilla/netscape/opera/IE7) use window.innerWidth and window.innerHeight
      this.width = window.innerWidth,
      this.height = window.innerHeight
    }
    
    else if (typeof document.documentElement != 'undefined' && typeof document.documentElement.clientWidth != 'undefined' && document.documentElement.clientWidth != 0){
			// IE6 in standards compliant mode (i.e. with a valid doctype as the first line in the document)
      this.width = document.documentElement.clientWidth,
      this.height = document.documentElement.clientHeight
    }
    
    else
    {
	    // older versions of IE
      this.width = document.getElementsByTagName('body')[0].clientWidth,
      this.height = document.getElementsByTagName('body')[0].clientHeight
    }
  }
});