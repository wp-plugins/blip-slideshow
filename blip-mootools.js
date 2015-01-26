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
  initialize: function(element, data, link, type, options){
		this.element = element;
		this.data = data;
		this.link = new Link(link, options.width, options.height);
		this.type = type;
		this.setOptions(options);
		this.processRequest();
	},
	processRequest: function(){
		var lightboxHelper = LightboxHelper.createLightboxHelper(this.link);
		var parser = MediaRssParser.createParser(this.link, '', this.data);
		if(parser) {
			var slideshowData = SlideshowHelper.createSlideshowData(parser.slideshowImages);
			if(this.type == "flash") {
				var myShow = new Slideshow.Flash(this.element, slideshowData, this.options);
			} else if(this.type == "fold") {
				var myShow = new Slideshow.Fold(this.element, slideshowData, this.options);
			} else if(this.type == "kenburns") {
				var myShow = new Slideshow.KenBurns(this.element, slideshowData, this.options);
			} else if(this.type == "push") {
				var myShow = new Slideshow.Push(this.element, slideshowData, this.options);
			} else {
				var myShow = new Slideshow(this.element, slideshowData, this.options);
			}
			if(this.link.lightboxHelper) {
				this.link.lightboxHelper.addEvents(this.element, parser.slideshowImages, myShow);
			}
		}
	}
});

var Link = new Class({
	initialize: function(link, width, height) {
		this.link = link;
		this.width = width;
		this.height = height;
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
	isImageBigEnough: function(imageWidth, imageHeight) {
		if(!this.width || !this.height) {
			return imageWidth >= this.width && imageHeight >= this.height;
		} else {
			return imageWidth >= this.width || imageHeight >= this.height;
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

/* The parse superclass */
var MediaRssParser = new Class({
});
MediaRssParser.createParser = function(newLink, newResponseText, newResponseXml){
	var generator = Slick.find(newResponseXml, 'generator');
	if(generator) {
		generator = generator.firstChild.nodeValue;
	}
	if(generator == "http://www.smugmug.com/") {
		return new SmugMugRssParser(newLink, newResponseText, newResponseXml);
	} else if(generator == "DotMac 1.0") {
		return new DotMacRssParser(newLink, newResponseText, newResponseXml);
	} else {
		return new GenericRssParser(newLink, newResponseText, newResponseXml);
	}
}

/* For SmugMug feeds */
var SmugMugRssParser = new Class({
	Extends: MediaRssParser,
	initialize: function(link, newResponseText, newResponseXml){
		var responseXml = newResponseXml;
		var items = Slick.search(responseXml, 'item');
		var counter = 0;
		var slideshowImages = new Array(items.length);
		this.slideshowImages = slideshowImages;
		items.each(function(item, i){
			var image = {};
			this.processMediaGroup(image, Slick.search(item, 'media_content'), link);
			image.hrefUrl = Slick.find(item, 'link').firstChild.nodeValue; // the SmugMug gallery
			image.caption = Slick.find(item, 'title').firstChild.nodeValue;
			var slideUrl = Slick.find(item, 'media_content[isDefault]').getAttribute('url');
			// test if SmugMug set a default image
			if(slideUrl) {
				// the slide image becomes the (default) sized image
				image.slideUrl = slideUrl; // the sized image
			}
			link.setImageLink(image);
			slideshowImages[counter++] = image;
		}, this);
	},
	/* parse the <media:group> for the best thumb, slide and large urls */
	processMediaGroup: function(newImage, newMediaGroup, link) {
		// determine the thumb image and the large image
		var previousBiggestHeight = 0;
		var previousBiggestWidth = 0;
		newMediaGroup.each(function(oneImage){
			var width = parseInt(oneImage.getAttribute('width'));
			var height = parseInt(oneImage.getAttribute('height'));
			var url = oneImage.getAttribute('url');
			if(height == 100 || width == 100) {
				newImage.thumbUrl = url;
			}
			if((height > previousBiggestHeight || width > previousBiggestWidth) && !link.isImageTooBig(width, height)) {
				previousBiggestHeight = height;
				previousBiggestWidth = width;
				newImage.slideUrl = newImage.largeUrl = url;
			}
		}, this);
	}
});

/* For MobileMe feeds */
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
				image.slideUrl = enclosure.getAttribute('url'); // the sized image
				image.largeUrl = image.slideUrl.replace(/web.jpg/,'large.jpg'); // the large image
			} else {
				image.slideUrl = Slick.find(item, 'description').firstChild.nodeValue.replace(/[\s\S]+img src\=.([\s\S]+). alt=[\s\S]+/g,'$1'); // the sized image
				image.slideUrl = image.slideUrl.replace(/.jpg[\s\S]+/,'/web.jpg'); // the large image
				image.largeUrl = image.slideUrl.replace(/web.jpg/,'large.jpg'); // the large image
			}
			image.thumbUrl = ' ';
			// there is also a hidden medium.jpg
			link.setImageLink(image);
			slideshowImages[counter++] = image;
		}, this);
	}
});

/* For generic feeds like Flickr, Picasa Web and Photobucket */
var GenericRssParser = new Class({
	Extends: MediaRssParser,
	initialize: function(link, newResponseText, newResponseXml){
		var items = Slick.search(newResponseXml, 'item');
		var slideshowImages = new Array(items.length);
		this.slideshowImages = slideshowImages;
		var counter = 0;
		items.each(function(item){
			var image = {};
			image.caption = Slick.find(item, 'title').firstChild.nodeValue; // the title
			image.hrefUrl = Slick.find(item, 'link').firstChild.nodeValue; // the link
			image.thumbUrl = image.slideUrl = image.largeUrl = Slick.find(item, 'media_content').getAttribute('url'); // the large image
			var thumbnail = Slick.find(item, 'media_thumbnail'); // the thumbnail
			if(thumbnail != undefined) {
				image.thumbUrl = thumbnail.getAttribute('url');
			}
			link.setImageLink(image);
			slideshowImages[counter++] = image;
		}, this);
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
		}).addEvent('click', function() {
			var slide = newImages[newSlideshow.slide];
			jQuery.colorbox({title:slide.caption, href:slide.largeUrl, width:"100%", height:"100%", scalePhotos:true, onClosed: function() {newSlideshow.pause(0);}});
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
