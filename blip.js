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
  Implements: [Options, Events],
  initialize: function(newElement, newRssUrl, newLink, newOptions){
		this.element = newElement;
		this.rssUrl = newRssUrl;
		this.link = newLink;
		this.setOptions(newOptions);
		var request = this.createRequest(this);
		request.send();
	},
	createRequest: function(newBlip){
		var request = new Request({
			url: this.rssUrl,
			onSuccess: function(newResponseText, newResponseXml) {
				newBlip.processRequest(newResponseText, newResponseXml);
			},
		});
		return request;
	},
	processRequest: function(newResponseText, newResponseXml){
		var parser = MediaRssParser.createParser(this.link, newResponseText, newResponseXml);
		this.createSlideshow(parser.slideshowImages);
	},
	createSlideshow: function(images){
		var slideshowData = SlideshowHelper.createSlideshowData(images);
		var myShow = new Slideshow(this.element, slideshowData, this.options);
		if(this.link == "slimbox") {
			JQuerySlimboxHelper.addEvents(images, myShow);
		}
	}
});

var MediaRssParser = new Class({
		smartLink: function(image, newLink) {
			if(newLink == "full" || newLink == "true") {
				image.linkUrl = image.largeUrl;
			} else if(newLink == "none" || newLink == "false" || newLink == "slimbox") {
				image.linkUrl = '';
			} else if(newLink == "href") {
				// leave image.linkUrl alone
			} else {
				image.linkUrl = newLink;
			}
		}
});

MediaRssParser.createParser = function(newLink, newResponseText, newResponseXml){
	var generator = Slick.find(newResponseXml, 'generator').textContent;
	if(generator == "http://www.smugmug.com/") {
		return new SmugMugRssParser(newLink, newResponseText, newResponseXml);
	} if(generator == "http://www.flickr.com/") {
		return new FlickrRssParser(newLink, newResponseText, newResponseXml);
	} else {
		return new SmugMugRssParser(newLink, newResponseText, newResponseXml);
	}
}

var SmugMugRssParser = new Class({
	Extends: MediaRssParser,
	initialize: function(newLink, newResponseText, newResponseXml){
		var responseXml = newResponseXml;
		//var responseText = newResponseText.replace(/<(\/)?([A-Z]+):([a-z]+)/gi,'<$1$2_$3');
		//responseXml = new DOMParser().parseFromString(responseText, 'text/xml');
		var items = Slick.search(responseXml, 'item');
		var counter = 0;
		var slideshowImages = new Array(items.length);
		this.slideshowImages = slideshowImages;
		items.each(function(item){
			var image = {};
			image.linkUrl = Slick.find(item, 'link').textContent; // the SmugMug gallery
			image.largeUrl = Slick.find(item, 'guid').textContent; // the Original image
			image.caption = Slick.find(item, 'title').textContent;
			this.setSlideImage(image, Slick.find(item, 'media_group > media_content[isDefault]'));
			this.processMediaGroup(image, Slick.search(item, 'media_group > media_content[url]'));
			this.smartLink(image, newLink);
			slideshowImages[counter++] = image;
		}, this);
	},
	setSlideImage: function(newImage, defaultImage) {
		if(defaultImage.getProperty) {
			newImage.slideUrl = defaultImage.getProperty('url'); // the sized image
		} else {
			// stupid internet explorer crap
			newImage.slideUrl = defaultImage.attributes[0].value;
		}
	},
	processMediaGroup: function(newImage, newMediaGroup) {
		var previousBiggest = 0;
		newMediaGroup.each(function(oneImage){
			var height = 0;
			var url = '';
			for(var i=0; i<oneImage.attributes.length; i++) {
				if (oneImage.attributes[i].name == "height") {
					height = parseInt(oneImage.attributes[i].value);
				}
				if (oneImage.attributes[i].name == "url") {
					url = oneImage.attributes[i].value;
				}
			}
			if(height == 100) {
				newImage.thumbUrl = url;
			}
			if(height > previousBiggest) {
				previousBiggest = height;
				newImage.largeUrl = url;
			}
		}, this);
	}
});

var FlickrRssParser = new Class({
	Extends: MediaRssParser,
	initialize: function(newLink, newResponseText, newResponseXml){
		var items = Slick.search(newResponseXml, 'item');
		var counter = 0;
		var slideshowImages = new Array(items.length);
		this.slideshowImages = slideshowImages;
		items.each(function(item){
			var image = {};
			image.linkUrl = Slick.find(item, 'link').textContent; // the Flickr gallery
			image.slideUrl = Slick.find(item, 'description').textContent.replace(/[\s\S]+img src\=.([\s\S]+). width[\s\S]+/g,'$1'); // the Flickr gallery
			image.caption = Slick.find(item, 'media_title').textContent;
			var largeUrl = Slick.find(item, 'media_content'); // the sized image
			if(largeUrl.getProperty) {
				image.largeUrl = largeUrl.getProperty('url');
			} else {
				// stupid internet explorer crap
				image.largeUrl = largeUrl.attributes[0].value;
			}
			var thumbnail = Slick.find(item, 'media_thumbnail'); // the thumbnail
			if(thumbnail.getProperty) {
				image.thumbUrl = thumbnail.getProperty('url');
			} else {
				// stupid internet explorer crap
				image.thumbUrl = thumbnail.attributes[0].value;
			}
			this.smartLink(image, newLink);
			slideshowImages[counter++] = image;
		}, this);
	}
});

var SlideshowHelper = new Class({
});
SlideshowHelper.createSlideshowData = function(newImages) {
	var data = new Object();
	newImages.each(function(image){
		data[image.slideUrl] = {caption: image.caption, href: image.linkUrl, thumbnail: image.thumbUrl};
	});
	return data;
}

var JQuerySlimboxHelper = new Class({
});
JQuerySlimboxHelper.addEvents = function(newImages, newSlideshow) {
	var data = new Object();
	var counter = 0;
	newImages.each(function(image){
		data[counter++] = [image.largeUrl, image.caption];
	});
	$$('.slideshow-images a').each(function(a) {
		a.style.cursor = 'pointer';
	}).addEvent('click', function() {
			jQuery.slimbox(data, newSlideshow.slide, {resizeDuration: 200, overlayFadeDuration: 200, captionAnimationDuration: 100});
			newSlideshow.pause(1);
	});

	$$('#lbOverlay, #lbCloseLink').addEvent('click', function(){
		// theres no callback for the close() function in slimbox so we'll have to manually add
		// a function to the elements that trigger close in order to resume the show
		newSlideshow.pause(0);
	});
			
	$$('#lbPrevLink').addEvent('click', newSlideshow.prev.bind(newSlideshow));
	$$('#lbNextLink').addEvent('click', newSlideshow.next.bind(newSlideshow));			

}