<?php
/*

    Plugin Name: Blip Slideshow
    Plugin URI: http://www.jasonhendriks.com/programmer/blip-slideshow/
    Description: A WordPress slideshow plugin fed from a SmugMug or Flickr RSS feed and displayed using pure Javascript.
    Version: 0.3.1
    Author: Jason Hendriks
    Author URI: http://jasonhendriks.com/
    License: GPL version 3 or any later version

		** Requires WordPress 3.0 **
 
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

// retrieve media rss via http
if (isset($_REQUEST['url'])) {
	$url = html_entity_decode(rawurldecode($_REQUEST['url']));
	$url = preg_replace("/^feed\:\/\//", "http://", $url);
	// make the HTTP request
	if(function_exists('curl_init')) {
		$crl = curl_init();
		$timeout = 5;
		curl_setopt ($crl, CURLOPT_URL, $url);
		curl_setopt ($crl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($crl, CURLOPT_CONNECTTIMEOUT, $timeout);
		$content = curl_exec($crl);
		curl_close($crl);
	}
	if($content == FALSE) {
		$content = file_get_contents($url);
	}
	if($content != FALSE) {
		header('HTTP/1.1 200 OK');
		header("Content-Type: text/xml");
		$content = preg_replace("/<(\/)?([A-Za-z][A-Za-z0-9]+):([A-Za-z][A-Za-z0-9]+)/", "<$1$2_$3", $content);
		print $content;
	}
	die;
}

Blip_Slideshow::init();

class Blip_Slideshow {
	static $counter = 0;
	static $add_script = false;
	static $slimbox = false;
		
	function init() {
		register_activation_hook(__FILE__, 'create_options');
		register_uninstall_hook(__FILE__, 'destroy_options' );
		add_shortcode('slideshow', array(__CLASS__, 'blip_create_slideshow'));
		add_action('wp_footer', array(__CLASS__, 'add_footer_scripts'));
		add_action('admin_menu', array(__CLASS__, 'add_admin_menu_item'));
		self::add_header_scripts();
	}

	// default options
	function create_options() {
		$options = array();
		$options['cache_enabled'] = true;
		add_option('blip', $options, '', 'yes');
	}

	function destroy_options() {
		delete_option('blip');
	}

	function blip_create_slideshow($atts, $content = null) {
		// extract rss from shortcode attributes
		$sample_feed = plugins_url('/sample_feed.php', __FILE__);
		extract(shortcode_atts(array(
			'captions' => 'false',
			'center' => 'true',
			'controller' => 'false',
			'delay' => '2000',
			'duration' => '750',
			'fast' => 'false',
			'height' => 'false',
			'id' => 'show-' . self::$counter,
			'link' => 'full',
			'loader' => 'true',
			'loop' => 'true',
			'overlap' => 'true',
			'paused' => 'false',
			'random' => 'false',
			'resize' => 'fill',
			'rss' => $sample_feed,
			'slide' => 0,
			'thumbnails' => 'false',
			'titles' => 'false',
			'width' => 'false',
		), $atts));

		// encode rss url for passing via HTTP back to blip.php
		$rss = plugins_url('/blip.php?url=', __FILE__) . rawurlencode($rss);

		// handle lightbox link options
		if($link == "lightbox" && (function_exists('slimbox') || function_exists('wp_slimbox_activate'))) {
			$link = "slimbox";
			self::$slimbox = true;
		} else if($link == "lightbox") {
			// no supported lightbox plugins
			$link = "full";
		}

		// build Javascript output
		$output = '<!-- blip --><script type="text/javascript">
		//<![CDATA[
		';
	
		// build remainder of script options
		$output .= "window.addEvent('domready', function(){" . "var options = {";

		// build resize option (handle string and boolean)
		if($resize == "true") {
			$output .= "resize: true, ";
		} else if($resize == "false" || $resize == "none") {
			$output .= "resize: false, ";
		} else {
			$output .= "resize: '$resize', ";
		}

		// build remainder of script options
		$output .= "captions: " . $captions . ", center: " . $center .", controller: " . $controller . ", fast: " . $fast . ", height: " . $height . ", loader: " . $loader . ", overlap: " . $overlap . ", thumbnails: " . $thumbnails . ', width: ' . $width . "};";
		$output .= 'new Blip(' . json_encode($id) . ', ' . json_encode($rss) . ', ' . json_encode($link) . ', options); });
		//]] >
		</script><div id="' . $id . '" class="slideshow">';
		
		if(!empty( $content )) {
			$output .= '<span class="slideshow-content">' . $content . '</span>';
		}
		$output .= '</div><!-- blip -->';
		
		self::$counter++;
		self::$add_script = true;
		return $output;
	}

	function add_header_scripts() {
		if (!is_admin()) {
			wp_register_script('mootools', plugins_url('/Slideshow/js/mootools-1.3.1-core.js', __FILE__));
			wp_enqueue_script('mootools');
		}
	}

	function add_footer_scripts() {
		if ( self::$add_script ) {
			echo '<!-- blip -->';
			wp_register_style( 'slideshow2', plugins_url('/Slideshow/css/slideshow.css', __FILE__));
			wp_print_styles( 'slideshow2');

			wp_register_script( 'mootools-more', plugins_url('/Slideshow/js/mootools-1.3.1.1-more.js', __FILE__));
			wp_register_script( 'slideshow2', plugins_url('/Slideshow/js/slideshow.js', __FILE__));
			wp_register_script( 'blip', plugins_url('/blip.js', __FILE__), null, false, false);

			wp_print_scripts( 'mootools-more' );
			wp_print_scripts( 'slideshow2' );
			wp_print_scripts( 'blip');

			// disable the prev/next buttons on simbox
			if(self::$slimbox) {
				echo '<style>#lbPrevLink, #lbNextLink {width:0}</style><!-- blip -->';
			}

		}
	}

	// Add a new submenu under Options:
	function add_admin_menu_item() {
		add_options_page('Blip Slideshow', 'Blip Slideshow', 'manage_options', 'blip', 'Blip_Slideshow::display_admin_page');
	}

	// displays the options page content
	function display_admin_page() {
		?>
		<div class="wrap">
		<form method="post" id="next_page_form" action="">
		<h2>Blip Slideshow Options</h2>
		<br/>
		<div style="border:1px solid #ddd;width:60%;padding:1em;">
		<label for="use_cache">
			<input name="use_cache" type="checkbox" id="use_cache" value="1" checke="checked" disabled="true" style="width:50px;"/>
			Enable caching of media RSS files
		</label><br />
		<label for="cache_time">
			<input name="cache_time" type="text" id="cache_time" value="3600" disabled="true" style="width:50px"/>
			Length of time (in seconds) to cache media RSS files for
		</label><br />
		<p class="submit">
		<input type="submit" name="submit" class="button-primary" value="Update Options" disabled="true"/>
		</p>
		<em>Option panel coming soon..</em>
		</div>
		</form>
		</div>
		<?php 
	}

}

?>
