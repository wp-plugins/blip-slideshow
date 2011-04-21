try {

	var slimbox = LightboxHelper.createLightboxHelper('slimbox');
	if(slimbox.isSlimbox() == false) { throw "isSlimbox()" }
	if(slimbox.isColorbox() == true) { throw "isColorbox()" }

	var colorbox = LightboxHelper.createLightboxHelper('colorbox');
	if(colorbox.isSlimbox() == true) { throw "isSlimbox()" }
	if(colorbox.isColorbox() == false) { throw "isColorbox()" }

	var Blip = new Blip('slideshow', 'sample_feed.xml', 'colorbox', {});

} catch(e) {

	console.log(e);

}