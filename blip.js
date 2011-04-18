/*

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
  initialize: function(newElement, newRssUrl, newLinked, newOptions){
		this.element = newElement;
		this.rssUrl = newRssUrl;
		this.linked = newLinked;
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
		var parser = MediaRssParser.createParser(this.linked, newResponseText, newResponseXml);
		this.createSlideshow(parser.images);
	},
	createSlideshow: function(images){
		var slideshowCreator = new Slideshow2Creator(images);
		this.slideshow = new Slideshow(this.element, slideshowCreator.data, this.options);
	}
});

var MediaRssParser = new Class({
    whoIs: function(){
        return 'Super';
    },
		smartLink: function(image, newLinked) {
			if(newLinked == "full") {
				image.linkUrl = image.largeUrl;
			} else if(newLinked == "none") {
				image.linkUrl = '';
			} else if(newLinked == "href") {
				// leave image.linkUrl alone
			} else {
				image.linkUrl = newLinked;
			}
		}
});
MediaRssParser.createParser = function(newLinked, newResponseText, newResponseXml){
	var generator = Slick.find(newResponseXml, 'generator').textContent;
	if(generator == "http://www.smugmug.com/") {
		return new SmugMugRssParser(newLinked, newResponseText, newResponseXml);
	} else {
		return new SmugMugRssParser(newLinked, newResponseText, newResponseXml);
	}
}

var SmugMugRssParser = new Class({
	Extends: MediaRssParser,
	initialize: function(newLinked, newResponseText, newResponseXml){
		var responseXml = newResponseXml;
		//var responseText = newResponseText.replace(/<(\/)?([A-Z]+):([a-z]+)/gi,'<$1$2_$3');
		//responseXml = new DOMParser().parseFromString(responseText, 'text/xml');
		var items = Slick.search(responseXml, 'item');
		var counter = 0;
		var images = new Array(items.length);
		this.images = images;
		items.each(function(item){
			var image = {};
			image.linkUrl = Slick.find(item, 'link').textContent; // the SmugMug gallery
			image.largeUrl = Slick.find(item, 'guid').textContent; // the Original image
			image.caption = Slick.find(item, 'title').textContent;
			this.setSlideImage(image, Slick.find(item, 'media_group > media_content[isDefault]'));
			this.processMediaGroup(image, Slick.search(item, 'media_group > media_content[url]'));
			this.smartLink(image, newLinked);
			images[counter++] = image;
		}, this);
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
	},
	setSlideImage: function(newImage, defaultImage) {
		if(defaultImage.getProperty) {
			newImage.slideUrl = defaultImage.getProperty('url'); // the sideshow-sized image
		} else {
			// stupid internet explorer crap
			newImage.slideUrl = defaultImage.attributes[0].value;
		}
	}
});

var Slideshow2Creator = new Class({
	Implements: [Options],
	initialize: function(newImages){
		var object = new Object();
		this.data = object;
		newImages.each(function(image){
			var key = image.slideUrl;
			object[key] = {caption: image.caption, href: image.linkUrl, thumbnail: image.thumbUrl};
		});
	}
});
