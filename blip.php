<?php
/*

    Plugin Name: Blip Slideshow
    Plugin URI: http://www.jasonhendriks.com/programmer/blip-slideshow/
    Description: A WordPress slideshow plugin fed from a SmugMug RSS feed and displayed using pure Javascript.
    Version: 0.2
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

if(!function_exists('bloginfo')) { die; }
// store session data
session_start();

function blip_get_album_id() {
	$counter = $_SESSION['blip_counter'];
	blip_set_album_id($counter + 1);
	return $counter;
}

function blip_set_album_id($newCounter) {
	$_SESSION['blip_counter'] = $newCounter;
}

function blip_cache_enabled() {
	$options = get_option('blip');
	$cache_enabled = $options['cache_enabled'];
	echo "cache_enabled = " . $cache_enabled;
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
		'id' => 'show-' . blip_get_album_id(),
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
	} else if($link == "lightbox") {
		// no supported lightbox plugins
		$link = "full";
	}

	// build Javascript output
	$output = '<script type="text/javascript">
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
</script>
<div id="' . $id . '" class="slideshow">';
	if(!empty( $content )) {
		$output .= '<span class="slideshow-content">' . $content . '</span>';
	}
	$output .= '</div>';
	return $output;
}

add_shortcode('slideshow', 'blip_create_slideshow');

/* Create the admin screen */

// Add a new submenu under Options:
function blip_add_pages() {
	add_options_page('Blip', 'Blip', 'manage_options', 'blip', 'blip_options');
}
add_action('admin_menu', 'blip_add_pages');

// displays the options page content
function blip_options() {
	?>
	<div class="wrap">
	<form method="post" id="next_page_form" action="options.php">
	<h2>Blip Options</h2>
	<p class="submit">
	<input type="submit" name="submit" class="button-primary" value="Update Options" />
	</p>
	</form>
	</div>
	<?php 
}

/* Register and de-register the default options */

// register settings
function register_blip_options() {
	register_setting( 'blip', 'blip' );
}
add_action('admin_init', 'register_blip_options' );

// default options
function blip_activation() {
	$options = array();
	$options['cache_enabled'] = true;
	add_option('blip', $options, '', 'yes');
}
register_activation_hook(__FILE__, 'blip_activation');

// when uninstalled, remove options
register_uninstall_hook(__FILE__, 'blip_delete_options' );

function blip_delete_options() {
	delete_option('blip');
}

/* Queue up the mootools script */

function blip_enqueue_script() {
	if ( !is_admin() ) {
		// only load if it is not the admin area
		wp_register_style( 'slideshow2', plugins_url('/Slideshow/css/slideshow.css', __FILE__) );
		wp_enqueue_style( 'slideshow2');
		wp_register_script( 'mootools', plugins_url('/Slideshow/js/mootools-1.3.1-core.js', __FILE__));
		wp_enqueue_script( 'mootools' );
		wp_register_script( 'mootools-more', plugins_url('/Slideshow/js/mootools-1.3.1.1-more.js', __FILE__));
		wp_enqueue_script( 'mootools-more' );
		wp_register_script( 'slideshow2', plugins_url('/Slideshow/js/slideshow.js', __FILE__));
		wp_enqueue_script( 'slideshow2' );
		wp_register_script( 'blip', plugins_url('/blip.js', __FILE__) );
		wp_enqueue_script( 'blip');
	}
}

add_action('init', 'blip_enqueue_script');

?>
