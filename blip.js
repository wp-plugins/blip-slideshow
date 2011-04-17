/*

    Plugin Name: Blip Slideshow
    Description: A WordPress slideshow plugin fed from a SmugMug RSS feed and displayed using pure Javascript.
 
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
    initialize: function(newElement, newRssUrl, newOptions){
		this.element = newElement;
		this.rssUrl = newRssUrl;
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
		var parser = MediaRSSParser.createParser(newResponseText, newResponseXml);
		this.createSlideshow(parser.images);
	},
	createSlideshow: function(images){
		var slideshowCreator = new Slideshow2Creator(images);
		this.slideshow = new Slideshow(this.element, slideshowCreator.data, this.options);
	}
});

var MediaRSSParser = new Class({
});
MediaRSSParser.createParser = function(newResponseText, newResponseXml){
	var generator = Slick.find(newResponseXml, 'generator').textContent;
	if(generator == "http://www.smugmug.com/") {
		return new SmugMugRSSParser(newResponseText, newResponseXml);
	} else {
		return new SmugMugRSSParser(newResponseText, newResponseXml);
	}
}

var SmugMugRSSParser = new Class({
	Implements: MediaRSSParser,
	initialize: function(newResponseText, newResponseXml){
		var responseXml = newResponseXml;
		//var responseText = newResponseText.replace(/<(\/)?([A-Z]+):([a-z]+)/gi,'<$1$2_$3');
		//responseXml = new DOMParser().parseFromString(responseText, 'text/xml');
		var items = Slick.search(responseXml, 'item');
		var counter = 0;
		var images = new Array(items.length);
		this.images = images;
		items.each(function(item){
			var image = {};
			image.linkUrl = Slick.find(item, 'guid').textContent;
			image.caption = Slick.find(item, 'media_title').textContent;
			var allImages = Slick.search(item, 'media_group > media_content[url]');
			var defaultImage = Slick.find(item, 'media_group > media_content[isDefault]');
			allImages.each(function(oneImage){
				if(oneImage.getProperty) {
					if(oneImage.getProperty('url').match('-Th(-[0-9])?.(jpg|gif)$')) {
						image.thumbUrl = oneImage.getProperty('url');
					}
				} else {
					// stupid internet explorer crap
					for(var i=0; i<oneImage.attributes.length; i++) {
						if (oneImage.attributes[i].name == "url") {
							var url = oneImage.attributes[i].value;
							if(url.match('-Th(-[0-9])?.(jpg|gif)$')) {
								image.thumbUrl = url;
							}
						}
					}
				}
			});
			if(defaultImage.getProperty) {
				image.largeUrl = defaultImage.getProperty('url');
			} else {
				// stupid internet explorer crap
				image.largeUrl = defaultImage.attributes[0].value;
			}
			images[counter++] = image;
		});
	}
});

var Slideshow2Creator = new Class({
	Implements: [Options],
	options: {
	},
	initialize: function(newImages, newOptions){
		this.setOptions(newOptions);
		var object = new Object();
		this.data = object;
		newImages.each(function(image){
			var key = image.largeUrl;
			object[key] = {caption: image.caption, href: image.linkUrl, thumbnail: image.thumbUrl};
		});
	}
});

