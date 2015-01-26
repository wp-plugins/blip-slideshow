<?php
/*
 *
 * Plugin Name: Blip Slideshow
 * Plugin URI: http://www.jasonhendriks.com/programmer/blip-slideshow/
 * Description: A WordPress slideshow plugin fed from a SmugMug, Flickr or MobileMe RSS feed and displayed using pure Javascript.
 * Version: 1.99
 * Author: Jason Hendriks
 * Author URI: http://jasonhendriks.com/
 * License: GPL version 3 or any later version
 *
 * * Requires WordPress 2.7 **
 *
 * Copyright (C) 2011 Jason Hendriks
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */
if (! defined ( "BLIP_SLIDESHOW_NAME" )) {
	define ( "BLIP_SLIDESHOW_NAME", "Blip Slideshow" );
}
if (! defined ( "BLIP_SLIDESHOW_DOMAIN" )) {
	define ( "BLIP_SLIDESHOW_DOMAIN", "Blip_Slideshow" );
}
if (! defined ( "PROTOTYPE_ERROR_MESSAGE" )) {
	define ( "PROTOTYPE_ERROR_MESSAGE", "A user-installed extension has loaded the Javascript framework <em>Prototype</em>. Blip Slideshow can not run when <em>Prototype</em> is loaded." );
}

require_once 'Cache.php';
require_once 'RssReader.php';
require_once 'AdminController.php';

if (! class_exists ( "Blip_Slideshow" )) {
	
	/**
	 * Blip_Slideshow handles the generation of the slideshow HTML script.
	 */
	class Blip_Slideshow {
		const VERSION = "2.0";
		private $scripts = array ();
		private $counter = 0;
		
		//
		function __construct() {
			debug ( 'constructor' );
			$this->options = get_option ( BLIP_SLIDESHOW_DOMAIN );
			add_shortcode ( "slideshow", array (
					$this,
					"slideshow_shortcode" 
			) );
			add_shortcode ( "blip-slideshow", array (
					$this,
					"slideshow_shortcode" 
			) ); // in case of collissions
			add_shortcode ( "blip_slideshow", array (
					$this,
					"slideshow_shortcode" 
			) ); // for WordPress older than v3
			add_shortcode ( "blip-version", array (
					$this,
					"version_shortcode" 
			) );
			add_shortcode ( "blip_version", array (
					$this,
					"version_shortcode" 
			) ); // for WordPress older than v3
			add_action ( 'wp_enqueue_scripts', array (
					$this,
					'themeslug_enqueue_style' 
			) );
			add_action ( 'wp_enqueue_scripts', array (
					$this,
					'themeslug_enqueue_script' 
			) );
			add_action ( 'wp_ajax_my_action', array (
					$this,
					'my_action_callback' 
			) );
			add_action ( 'wp_ajax_nopriv_my_action', array (
					$this,
					'my_action_callback' 
			) );
		}
		function my_action_callback() {
			debug ( 'ajax call!' );
			debug ( $_POST['feed'] );
			// retrieve the RSS
			$blip_rss_reader = new Blip_Slideshow_Rss_Reader ($_POST['feed']);
			wp_die (); // this is required to terminate immediately and return a proper response
		}
		function themeslug_enqueue_style() {
			debug ( 'in enqueue style' );
			wp_enqueue_style ( 'slideshow2', plugins_url ( 'Slideshow/css/slideshow.css', __FILE__ ), false, '1.3.1.110417' );
			wp_enqueue_style ( 'blip-slideshow', get_bloginfo ( "stylesheet_directory" ) . "/blip-slideshow.css", array (
					"slideshow2" 
			) );
		}
		function themeslug_enqueue_script() {
			debug ( 'in enqueue script' );
			
			if (! is_admin ()) {
				
				//
				wp_register_script ( "blip-slideshow", plugins_url ( "/blip.js", __FILE__ ), false, Blip_Slideshow::VERSION );
				wp_localize_script ( 'blip-slideshow', 'ajax_object', array (
						'ajax_url' => admin_url ( 'admin-ajax.php' ),
						'we_value' => 1234 
				) );
				// wp_localize_script ( 'blip-slideshow', 'jason', $this->javascriptVariable );
				wp_enqueue_script ( 'blip-slideshow' );
				
				//
				wp_enqueue_script ( "blip-mootools", plugins_url ( "/blip-mootools.js", __FILE__ ), array (
						"slideshow2" 
				), Blip_Slideshow::VERSION );
				wp_enqueue_script ( "slideshow2", plugins_url ( "/Slideshow/js/slideshow.js", __FILE__ ), array (
						"mootools-more" 
				), "1.3.1.110417" );
				wp_enqueue_script ( "slideshow2-flash", plugins_url ( "/Slideshow/js/slideshow.flash.js", __FILE__ ), array (
						"slideshow2" 
				), "1.3.1.110417" );
				wp_enqueue_script ( "slideshow2-fold", plugins_url ( "/Slideshow/js/slideshow.fold.js", __FILE__ ), array (
						"slideshow2" 
				), "1.3.1.110417" );
				wp_enqueue_script ( "slideshow2-kenburns", plugins_url ( "/Slideshow/js/slideshow.kenburns.js", __FILE__ ), array (
						"slideshow2" 
				), "1.3.1.110417" );
				wp_enqueue_script ( "slideshow2-push", plugins_url ( "/Slideshow/js/slideshow.push.js", __FILE__ ), array (
						"slideshow2" 
				), "1.3.1.110417" );
				wp_enqueue_script ( "mootools", plugins_url ( "/Slideshow/js/mootools-1.3.1-core.js", __FILE__ ), false, "1.3.1" );
				wp_enqueue_script ( "mootools-more", plugins_url ( "/Slideshow/js/mootools-1.3.1.1-more.js", __FILE__ ), array (
						"mootools" 
				), "1.3.1.1" );
				
				// check that this is not an admin page and that prototype, the mootools nemesis, is not loaded
				if (! wp_script_is ( 'prototype', 'queue' )) {
					// link MooTools script
					wp_enqueue_script ( "mootools" );
				}
			}
		}
		
		/**
		 * Shortcode to return the current plugin version.
		 * From http://code.garyjones.co.uk/get-wordpress-plugin-version/
		 *
		 * @return string Plugin version
		 */
		function version_shortcode() {
			return $this->version;
		}
		
		/**
		 * Shortcode to return the current plugin version.
		 * From http://code.garyjones.co.uk/get-wordpress-plugin-version/
		 *
		 * @return string Plugin version
		 */
		function get_version() {
			return Blip_Slideshow::VERSION;
		}
		
		/**
		 * External function to build the slideshow.
		 * The DIV HTML must be coded externally.
		 */
		function slideshow($atts) {
			print $this->create_slideshow ( $atts );
		}
		
		/**
		 * Shortcode to build the slideshow.
		 *
		 * @return string The Slideshow script HTML, including the DIV HTML
		 */
		function slideshow_shortcode($atts, $content) {
			$script = $this->create_slideshow ( $atts );
			extract ( shortcode_atts ( array (
					"id" => "show-" . $this->counter 
			), $atts ) );
			
			debug ( 'in slideshow shortcode for ' . $id );
			// if the script output was successful
			if ($this->scripts ["slideshow"]) {
				$script .= '<div id="' . $id . '" class="slideshow">';
				
				// the contents of the shortcode
				if (! empty ( $content )) {
					$script .= '<span class="slideshow-content">' . $content . "</span>";
				}
				
				$script .= "</div>";
			}
			return $script;
		}
		
		/**
		 * Builds the slideshow.
		 *
		 * @return string The Slideshow script HTML
		 */
		function create_slideshow($atts) {
			
			// check if prototype is loaded
			if (wp_script_is ( "prototype", "queue" )) {
				print "<strong>" . __ ( "Error", BLIP_SLIDESHOW_DOMAIN ) . ":</strong> " . __ ( PROTOTYPE_ERROR_MESSAGE, BLIP_SLIDESHOW_DOMAIN );
				return;
			}
			
			$this->counter ++;
			$this->slideshow_ready = true;
			$this->scripts ["slideshow"] = true;
			
			// extract rss from shortcode attributes
			$sample_feed = plugins_url ( "/sample_feed.php", __FILE__ );
			extract ( shortcode_atts ( array (
					"captions" => "true",
					"center" => "true",
					"color" => "#FFF",
					"controller" => "true",
					"delay" => "2000",
					"duration" => "1000",
					"fast" => "false",
					"height" => "false",
					"id" => "show-" . $this->counter,
					"link" => "full",
					"loader" => "true",
					"loop" => "true",
					"overlap" => "true",
					"pan" => "100, 100",
					"paused" => "false",
					"random" => "false",
					"resize" => "fill",
					"rss" => $sample_feed,
					"slide" => 0,
					"thumbnails" => "true",
					"titles" => "false",
					"transition" => "sine:in:out",
					"type" => "",
					"width" => "false",
					"zoom" => "50, 50" 
			), $atts ) );
			
			// determine which alternative Slideshow, if any, are to be used
			if ($type == "flash" || $type == "fold" || $type == "kenburns" || $type == "push") {
				$this->scripts [$type] = true;
			}
			
			// wordpress has encoded the HTML entities
			$decoded_rss = html_entity_decode ( $rss );
			
			// enable caching for this file?
			if (Blip_Slideshow_Cache::is_cache_enabled ( $this->options )) {
				Blip_Slideshow_Cache::prep_cache ( $this->options, $decoded_rss );
			}
			
			// massage the rss url - apache mod_rewrite gets grumpy unless the URL is encoded twice
			$callback_url = plugins_url ( "/blip.php/rss/", __FILE__ ) . rawurlencode ( rawurlencode ( $decoded_rss ) );
			
			// massage link option
			if ($link == "lightbox" && (function_exists ( "slimbox" ) || function_exists ( "wp_slimbox_activate" ))) {
				$link = "slimbox";
			} else if ($link == "lightbox" && (class_exists ( "wp_lightboxplus" ) || class_exists ( "GameplorersWPColorBox" ) || class_exists ( "jQueryLightboxForNativeGalleries" ) || function_exists ( "jQueryColorbox" ))) {
				$link = "colorbox";
			} else if ($link == "lightbox") {
				// no supported lightbox plugins
				$link = "full";
			}
			
			$output = '';
			
			// Slideshow.Fold is broken in IE8
			if ($type == "fold") {
				$output .= "<![if !IE]>";
			}
			
			// build Javascript output
			// $output .= '<script type="text/javascript">
			// //<![CDATA[
			// ';
			
			// if Prototype is loaded before the Blip script is written, it will fail
			$output .= "if(document.observe){document.write(pErr());}";
			
			// if MooTools is just plain missing, it will fail
			$output .= "else if(window.addEvent){window.addEvent('domready',function(){var options = {";
			
			// build resize option (handle string and boolean)
			if ($resize == "true") {
				$output .= "resize:true,";
			} else if ($resize == "false" || $resize == "none") {
				$output .= "resize:false,";
			} else {
				$output .= "resize:'$resize',";
			}
			
			if ($type == "flash") {
				// massage the colour option
				$output .= "color:['" . preg_replace ( "/,/", "','", $color ) . "'],";
			}
			
			if ($type == "kenburns") {
				// output the pan and zoom options
				$output .= "pan:[$pan],zoom:[$zoom],";
			}
			
			// build remainder of script options
			$output .= "captions:$captions,";
			$output .= "center:$center,";
			$output .= "controller:$controller,";
			$output .= "delay:$delay,";
			$output .= "duration:$duration,";
			$output .= "fast:$fast,";
			$output .= "height:$height,";
			$output .= "loader:$loader,";
			$output .= "loop:$loop,";
			$output .= "overlap:$overlap,";
			$output .= "paused:$paused,";
			$output .= "random:$random,";
			$output .= "slide:$slide,";
			$output .= "transition:'$transition',";
			$output .= "thumbnails:$thumbnails,";
			$output .= "titles:$titles,";
			$output .= "width:$width};";
			
			// if Prototype is loaded after the Blip script is written but before Blip runs, it will fail
			$output .= "if(Blip){new Blip(" . json_encode ( $id ) . ", response," . json_encode ( $link ) . "," . json_encode ( $type ) . ",options);}else{document.getElementById('" . $id . "').innerHTML=gErr();};});}";
			$output .= 'else {document.write(gErr());}';
			
			// Slideshow.Fold is broken in IE8
			if ($type == "fold") {
				$output .= "<![endif]>";
			}
			
			$container = '<script type="text/javascript">
					//<![CDATA[
						jQuery(document).ready(function($) {';
			$container .= "var data = {'action': 'my_action','feed': '" . $decoded_rss . "'};";
			$container .= 'jQuery.post(ajax_object.ajax_url, data, function(response) {';
			$container .= $output;
			$container .= '});';
			$container .= '});';
			// ]] >
			$container .= '</script>';
			
			return $container;
		}
	}
}

/**
 * And this is where code execution starts
 */

// check if the request is to read a Media RSS URL
$readRss = false;
if ($readRss && class_exists ( "Blip_Slideshow_Rss_Reader" )) {
	
	// attempt to talk to Wordpress
	$blog_header_path = preg_replace ( "/wp-content\/.*/", "wp-blog-header.php", getcwd () );
	if (file_exists ( $blog_header_path )) {
		require_once ($blog_header_path);
	}
	// initiate the Media RSS reading
	$blip_rss_reader = new Blip_Slideshow_Rss_Reader ();
} else {
	
	// start the maintenance
	if (class_exists ( "Blip_Slideshow_Admin" )) {
		$blip_slideshow_admin = new Blip_Slideshow_Admin ();
	}
	
	// start the meat and potatoes
	if (class_exists ( "Blip_Slideshow" )) {
		$blip_slideshow = new Blip_Slideshow ();
	}
}
function debug($text) {
	error_log ( "Blip: " . $text );
}
function addError($message) {
	$_SESSION [AdminController::ERROR_MESSAGE] = $message;
}
function addWarning($message) {
	$_SESSION [AdminController::WARNING_MESSAGE] = $message;
}
function addMessage($message) {
	$_SESSION [AdminController::SUCCESS_MESSAGE] = $message;
}

?>
