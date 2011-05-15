<?php
/*

Plugin Name: Blip Slideshow
Plugin URI: http://www.jasonhendriks.com/programmer/blip-slideshow/
Description: A WordPress slideshow plugin fed from a SmugMug, Flickr or MobileMe RSS feed and displayed using pure Javascript.
Version: 1.2.5
Author: Jason Hendriks
Author URI: http://jasonhendriks.com/
License: GPL version 3 or any later version

** Requires WordPress 2.7 **

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

if (!defined("BLIP_SLIDESHOW_NAME")) {
    define("BLIP_SLIDESHOW_NAME", "Blip Slideshow");
}
if (!defined("BLIP_SLIDESHOW_DOMAIN")) {
    define("BLIP_SLIDESHOW_DOMAIN", "Blip_Slideshow");
}
if (!defined("PROTOTYPE_ERROR_MESSAGE")) {
		define("PROTOTYPE_ERROR_MESSAGE", "A user-installed extension has loaded the Javascript framework <em>Prototype</em>. Blip Slideshow can not run when <em>Prototype</em> is loaded.");
}

if(!class_exists("Blip_Slideshow")) {

	/**
	 * Blip_Slideshow handles the generation of the slideshow HTML script.
	 */
	class Blip_Slideshow {
		var $scripts = array();
			
		function Blip_Slideshow() {
			$this->version = $this->get_version();
			$this->counter = 0;
			$this->options = get_option(BLIP_SLIDESHOW_DOMAIN);
			add_shortcode( "slideshow", array( $this, "slideshow_shortcode") );
			add_shortcode( "blip-slideshow", array( $this, "slideshow_shortcode") ); // in case of collissions
			add_shortcode( "blip_slideshow", array( $this, "slideshow_shortcode") ); // for WordPress older than v3
			add_shortcode( "blip-version", array( $this, "version_shortcode") );
			add_shortcode( "blip_version", array( $this, "version_shortcode") ); // for WordPress older than v3
			add_action( "wp_footer", array( $this, "add_footer_scripts") );
			$this->add_header_scripts();
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
			if ( ! function_exists( "get_plugins" ) ) {
				require_once( ABSPATH . "wp-admin/includes/plugin.php" );
			}
			$plugin_folder = get_plugins( "/" . plugin_basename( dirname( __FILE__ ) ) );
			$plugin_file = basename( ( __FILE__ ) );
			return $plugin_folder[$plugin_file]["Version"];
		}

		/**
		 * External function to build the slideshow.
		 * The DIV HTML must be coded externally.
		 */
		public function slideshow($atts) {
			print $this->create_slideshow($atts);
		}

		/**
		 * Shortcode to build the slideshow.
		 *
		 * @return string The Slideshow script HTML, including the DIV HTML
		 */
		function slideshow_shortcode($atts, $content) {
			$script = $this->create_slideshow($atts, $content);
			extract(shortcode_atts(array(
				"id" => "show-" . $this->counter,
			), $atts));

			// if the script output was successful
			if ( $this->scripts["slideshow"] ) {
				$script .= '<div id="' . $id . '" class="slideshow">';
			
				// the contents of the shortcode
				if(!empty( $content )) {
					$script .= '<span class="slideshow-content">' . $content . "</span>";
				}

				$script .= "</div>";
			}
			return $script;
		}

		/**
		 * The scripts that must be ready to run before page load
		 */
		function add_header_scripts() {
			
			// register scripts
			wp_register_script( "blip-slideshow", plugins_url("/blip.js", __FILE__), false, $this->version);
			wp_register_script( "blip-mootools", plugins_url("/blip-mootools.js", __FILE__), array("slideshow2"), $this->version);
			wp_register_script( "slideshow2", plugins_url("/Slideshow/js/slideshow.js", __FILE__), array("mootools-more"), "1.3.1.110417");
			wp_register_script( "slideshow2-flash", plugins_url("/Slideshow/js/slideshow.flash.js", __FILE__), array("slideshow2"), "1.3.1.110417");
			wp_register_script( "slideshow2-fold", plugins_url("/Slideshow/js/slideshow.fold.js", __FILE__), array("slideshow2"), "1.3.1.110417");
			wp_register_script( "slideshow2-kenburns", plugins_url("/Slideshow/js/slideshow.kenburns.js", __FILE__), array("slideshow2"), "1.3.1.110417");
			wp_register_script( "slideshow2-push", plugins_url("/Slideshow/js/slideshow.push.js", __FILE__), array("slideshow2"), "1.3.1.110417");
			wp_register_script( "mootools", plugins_url("/Slideshow/js/mootools-1.3.1-core.js", __FILE__), false, "1.3.1");
			wp_register_script( "mootools-more", plugins_url("/Slideshow/js/mootools-1.3.1.1-more.js", __FILE__), array("mootools"), "1.3.1.1");

			// register CSS
			wp_register_style( "slideshow2", plugins_url("/Slideshow/css/slideshow.css", __FILE__), false, "1.3.1.110417");
			wp_register_style( "blip-slideshow", get_bloginfo("stylesheet_directory") . "/blip-slideshow.css", array("slideshow2"), null);

			if (!is_admin()) {
				// link Blip script
				wp_enqueue_script( "blip-slideshow" );

				// check that this is not an admin page and that prototype, the mootools nemesis, is not loaded
				if (!wp_script_is('prototype', 'queue')) {
					// link MooTools script
					wp_enqueue_script("mootools");
				}
			
				if(!$this->options["optimize_scripts"]) {
					// link Slideshow stylesheet
					wp_enqueue_style( "slideshow2");
	
					// link optional, user-customized Blip-Slideshow stylesheet
					if(file_exists(get_theme_root() . "/" . get_template() . "/blip-slideshow.css")) {
						wp_enqueue_style( "blip-slideshow" );
					}
	
					// link scripts
					wp_enqueue_script( "mootools-more" );
					wp_enqueue_script( "slideshow2" );
					wp_enqueue_script( "slideshow2-flash" );
					wp_enqueue_script( "slideshow2-fold" );
					wp_enqueue_script( "slideshow2-kenburns" );
					wp_enqueue_script( "slideshow2-push" );
					wp_enqueue_script( "blip-mootools" );
					
				}
			}
		}
	
		/**
		 * If scripts and styles were not linked in the header, link them in the footer
		 * (See option "optimize_scripts")
		 */
		function add_footer_scripts() {
			if ( $this->scripts["slideshow"] ) {
				
				// link Slideshow stylesheet
				wp_print_styles( "slideshow2");

				// link optional, user-customized Blip-Slideshow stylesheet
				if(file_exists(get_theme_root() . "/" . get_template() . "/blip-slideshow.css")) {
					wp_print_styles( "blip-slideshow" );
				}

				// link MooTools More script
				wp_print_scripts( "mootools-more" );

				// link Slideshow script
				wp_print_scripts( "slideshow2" );

				// link Slideshow Flash script
				if($this->scripts["flash"]) {
					wp_print_scripts( "slideshow2-flash" );
				}

				// link Slideshow Fold script
				if($this->scripts["fold"]) {
					wp_print_scripts( "slideshow2-fold" );
				}

				// link Slideshow Ken Burns script
				if($this->scripts["kenburns"]) {
					wp_print_scripts( "slideshow2-kenburns" );
				}

				// link Slideshow Push script
				if($this->scripts["push"]) {
					wp_print_scripts( "slideshow2-push" );
				}

				// link Blip script
				wp_print_scripts( "blip-mootools" );
			}
		}

		/**
		 * Builds the slideshow.
		 *
		 * @return string The Slideshow script HTML
		 */
		function create_slideshow($atts) {

			// check if prototype is loaded
			if(wp_script_is("prototype", "queue")) {
				print "<strong>".__("Error", BLIP_SLIDESHOW_DOMAIN).":</strong> ".__(PROTOTYPE_ERROR_MESSAGE, BLIP_SLIDESHOW_DOMAIN);
				return;
			}
	
			$this->counter++;
			$this->slideshow_ready = true;
			$this->scripts["slideshow"] = true;

			// extract rss from shortcode attributes
			$sample_feed = plugins_url("/sample_feed.php", __FILE__);
			extract(shortcode_atts(array(
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
			), $atts));

			// determine which alternative Slideshow, if any, are to be used
			if($type == "flash" || $type == "fold" || $type =="kenburns" || $type == "push") {
				$this->scripts[$type] = true;
			}
	
			// wordpress has encoded the HTML entities
			$decoded_rss = html_entity_decode($rss);

			// enable caching for this file?
			if(Blip_Slideshow_Cache::is_cache_enabled($this->options)) {
				Blip_Slideshow_Cache::prep_cache($this->options, $decoded_rss);
			}
			
			// massage the rss url - apache mod_rewrite gets grumpy unless the URL is encoded twice
			$callback_url = plugins_url("/blip.php/rss/", __FILE__) . rawurlencode(rawurlencode($decoded_rss));
			
			// massage link option
			if($link == "lightbox" && (function_exists("slimbox") || function_exists("wp_slimbox_activate"))) {
				$link = "slimbox";
			} else if($link == "lightbox" && (class_exists("wp_lightboxplus") || class_exists("GameplorersWPColorBox") || class_exists("jQueryLightboxForNativeGalleries") || function_exists("jQueryColorbox"))) {
				$link = "colorbox";
			} else if($link == "lightbox") {
				// no supported lightbox plugins
				$link = "full";
			}
	
			// Slideshow.Fold is broken in IE8
			if($type == "fold") {
				$output .= "<![if !IE]>";
			}
	
			// build Javascript output
			$output .= '<script type="text/javascript">
			//<![CDATA[
			';
		
			// if Prototype is loaded before the Blip script is written, it will fail
			$output .= "if(document.observe){document.write(pErr());}";

			// if MooTools is just plain missing, it will fail
			$output .= "else if(window.addEvent){window.addEvent('domready',function(){var options = {";
	
			// build resize option (handle string and boolean)
			if($resize == "true") {
				$output .= "resize:true,";
			} else if($resize == "false" || $resize == "none") {
				$output .= "resize:false,";
			} else {
				$output .= "resize:'$resize',";
			}
			
			if($type == "flash") {
				// massage the colour option
				$output .= "color:['" . preg_replace("/,/","','",$color) . "'],";
			}
			
			if($type == "kenburns") {
				// output the pan and zoom options
				$output .= "pan:[$pan],zoom:[$zoom],";
			}
	
			// build remainder of script options
			$output .= "captions:$captions," ;
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
			$output .= "if(Blip){new Blip(" . json_encode($id) . "," . json_encode($callback_url) . "," . json_encode($link) . "," . json_encode($type) . ",options);}else{document.getElementById('".$id."').innerHTML=gErr();};});}";
			$output .= "else {document.write(gErr());}
			//]] >
			</script>";

			// Slideshow.Fold is broken in IE8
			if($type == "fold") {
				$output .= "<![endif]>";
			}
			
			return $output;
		}

	}

}

if(!class_exists("Blip_Slideshow_Rss_Reader")) {
	
	/**
	 * Blip_Slideshow_Rss_Reader is a proxy to get the content of the Media RSS files.
	 * It supports caching, if it has been enabled in the settings.
	 */
	class Blip_Slideshow_Rss_Reader {
		
		/**
		 * Get the content of the Media RSS URL either directly, or if caching is available, from the cache.
		 */
		function Blip_Slideshow_Rss_Reader() {
			$url = html_entity_decode(rawurldecode(rawurldecode(substr($_SERVER["PATH_INFO"], 5))));

			// check if we can talk to wordpress
			if(function_exists("get_option")) {
				// retrieve saved options
				$this->options = get_option(BLIP_SLIDESHOW_DOMAIN);
				// attempt to get the content from the cache
				$result = $this->get_rss_content_from_cache($url);
			} else {
				// can't talk to wordpress; get content directly
				$result = $this->get_rss_content_from_http($url);
			}
			$this->print_document($result, $url);
		}

		/**
		 * Fetch the content of the Media RSS URL via HTTP.
		 * Will first try file_get_contents, and if that is disabled, will try curl
		 */
		function get_rss_content_from_http($url) {
			// sometimes the protocol is given as feed://, but this media type is not recognize by curl or php
			$url = preg_replace("/^feed\:\/\//", "http://", $url);

			// if curl exists, use it to make the http request
			if(function_exists("curl_init")) {
				// make the http request via curl
				$curl_options = array( 
					CURLOPT_RETURNTRANSFER => true,     // return web page 
					CURLOPT_HEADER         => false,    // don't return headers 
					CURLOPT_FOLLOWLOCATION => $this->options["curl_redirects_enabled"],     // follow redirects 
					CURLOPT_ENCODING       => "",       // handle all encodings 
					CURLOPT_USERAGENT      => BLIP_SLIDESHOW_NAME . "/" . Blip_Slideshow::get_version(), // who am i 
					CURLOPT_AUTOREFERER    => true,     // set referer on redirect 
					CURLOPT_CONNECTTIMEOUT => 30,       // timeout on connect 
					CURLOPT_TIMEOUT        => 30,       // timeout on response 
					CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects 
				); 

				$crl = curl_init($url);
				curl_setopt_array ($crl, $curl_options);
				$content = curl_exec($crl);
				$err     = curl_errno($crl); 
				$errmsg  = curl_error($crl); 
				$header  = curl_getinfo($crl); 
				curl_close($crl);
			} else {
				// curl is not available, so make the http request via PHP
				// this function will not work with MobileMe because of HTTP redirects
				$content = file_get_contents($url);
			}

			// check if content was received
			if($content != FALSE) {
				// massage the content for Slick to handle in Javascript
				$content = preg_replace("/<(\/)?([A-Za-z][A-Za-z0-9]+):([A-Za-z][A-Za-z0-9]+)/", "<$1$2_$3", $content);
			}
			
			// prepare the result
			$now = time();
			$result = array("content" => $content, "max-age" => 0, "date" => $now, "expires" => $now);
			return $result;
		}
		
		/**
		 * Attempt to retrieve the contents of the media RSS URL from the cache.
		 * If the Media RSS is expired or empty, will retrieve the fresh contents via HTTP and then overwrite the cache.
		 */
		function get_rss_content_from_cache($url) {

			// test if cache is enabled
			if (Blip_Slideshow_Cache::is_cache_enabled($this->options)){

				// cache is enabled. determine the read/write paths
				$cache_paths = Blip_Slideshow_Cache::build_cache_paths($this->options, $url);
				$localfile = $cache_paths["cache_file"];

				// test if there is a cache file available for I/O
				if(file_exists($localfile)) {

					// cache file is available. read/write the cache
					return $this->read_write_cache($url, $localfile);
				} else {

					// cache file is available. read from http and return the result
					return $this->get_rss_content_from_http($url);
				}

			} else {
				// cache is not enabled. read from http and return the result
				return $this->get_rss_content_from_http($url);
			}
		}

		/**
		 * Read from the cache if it is valid.
		 * Write to cache if it is not.
		 * Return the fresh or cached content.
		 */
		function read_write_cache($url, $localfile) {
			// determine the amount of time (in seconds) this file can still be considered fresh
			$cache_time = $this->options["cache_time"];
			$last_modified = filemtime($localfile);
			$expires = $cache_time + $last_modified;
			$max_age = max(0, $expires - time());
			$etag = preg_replace("/.*[\\/]/", "", $localfile);

			// fill out what we know so far
			$result = array("max-age" => $max_age, "date" => $last_modified, "expires" => $expires, "cache" => $etag);

			// test if the cache is expired
			if($max_age == 0) {

				// populate the cache
				$result = $this->poulate_cache($url, $result, $localfile, $cache_time);

			// test if the client is validating cache
			}	else if(isset($_SERVER["HTTP_IF_MODIFIED_SINCE"]) && $last_modified <= strtotime(preg_replace("/;.*$/", "", $_SERVER["HTTP_IF_MODIFIED_SINCE"]))) {

				// return HTTP 304 - not modified
				$result["304"] = true;

			// test if the client is validating cache
			} else if(isset($_SERVER["HTTP_IF_NONE_MATCH"]) && $etag == str_replace('"', "", stripslashes($_SERVER["HTTP_IF_NONE_MATCH"]))) {

				// return HTTP 304 - not modified
				$result["304"] = true;

			// test if the cache file is not size zero
			} else if(filesize($localfile) != 0) {

				// cache is populate and valid. read from the cache.
				$result["content"] = file_get_contents($localfile);

			// test if the cache file is size zero
			} else {

				// cache is newly created. populate the cache.
				$result = $this->poulate_cache($url, $result, $localfile, $cache_time);
			}
			// return the result
			return $result;
		}

		/**
		 * populate the cache
		 */
		function poulate_cache($url, $result, $localfile, $cache_time) {
			// read from http
			$result = $this->get_rss_content_from_http($url);
			// determine if we got a valid response
			if($result["content"]) {
				// populate the cache
				$fp=fopen($localfile, "w");
				fwrite($fp, $result["content"]); //write contents of feed to cache file
				fclose($fp);
				$now = time();
				$result["max-age"] = $cache_time;
				$result["date"] = $now;
				$result["expires"] = $now + $cache_time;
			}
			return $result;
		}

		/**
		 * Build the document by outputing XML headers and the content.
		 */		
		function print_document($content, $url) {

				// http://www.php.net/manual/en/function.header.php#77028
				
				// push headers
				header("Via: " . Blip_Slideshow::get_version() . " " . BLIP_SLIDESHOW_NAME);
				header("Content-Location: " . $url);
				header("Last-Modified: " . date(DATE_RFC1123, $content["date"]));
				if($content["304"]) {
					header("HTTP/1.1 304 Not Modified");
				} else {
					header("HTTP/1.1 200 OK");
				}
				header("Expires: " .date(DATE_RFC1123, ($content["expires"])));
				header("Pragma: " . $pragma);

				if($content["cache"]) {
					header("ETag: " . preg_replace("/.*[\\/]/","",$content["cache"]));
					header("Pragma: public");
					header("Cache-Control: max-age=" . $content["max-age"]);
				} else {
					header("Pragma: no-cache");
					header("Cache-Control: no-cache, must-revalidate, max-age=0");
				}

				if (isset($_REQUEST["debug"])) {
					print("<html><head></head><body>");
					foreach(headers_list() as $header) {
						print $header . "<br/>";
					}
					print("Content-Type: text/xml<br/>");
					print("Content-Length: " . strlen($content["content"]));
					print "<pre>" . preg_replace("/</", "&lt;", $content["content"]) . "</pre></body></html>";
				} else if(!$content["304"]){
				  header("Content-Type: text/xml");
					header("Content-Length: " . strlen($content["content"]));
					print $content["content"];
				}
			}

	}
}

if(!class_exists("Blip_Slideshow_Cache")) {

	/**
	 * Blip_Slideshow_Cache provides two utilitie functions for reading and writing to the cached RSS file
	 */
	class Blip_Slideshow_Cache {
		/**
		 * Caching is enabled. Retrieve the preferred cache directory name. Create the cache directory and cache file.
		 * Input is the $options retrieved from the database and the raw_url_encoded RSS URL
		 */ 

		function is_cache_enabled($options) {
			return $options["cache_enabled"] && $options["cache_time"] != 0;
		}

		function prep_cache($options, $rss) {
			// determine the read/write paths
			$result = Blip_Slideshow_Cache::build_cache_paths($options, $rss);
			$cache_dir = $result["cache_dir"];
			$localfile = $result["cache_file"];
			
			// if the cache dir doesn't exist
			if (!file_exists($cache_dir)) {
				// create the cache dir
				mkdir($cache_dir);
				chmod($cache_dir, 0777); 
			}
			
			// if the cache file doesn't exist
			if (!file_exists($localfile)){
				// create the cache file
				touch($localfile);
				chmod($localfile, 0666); 
			}
		}

		function build_cache_paths($options, $rss) {
			// get the cache path from options
			$stored_cache_dir = $options["cache_dir"];
			
			// check to see if the cache dir option is an absolute path or not - will probably fail on Windows
			if( $stored_cache_dir[0] == "/" ) {
				// cache dir option IS the cache dir
				$cache_dir = $stored_cache_dir;
			} else {
				// cache dir is the path of this PHP file concatenated with the cache dir option
				$cache_dir = preg_replace("/blip.php/", "", __FILE__) . $stored_cache_dir;
			}

			// the localfile is the cache dir concatenated with the RawUrlEncoded RSS URL
			$localfile .= $cache_dir . "/" . hash("md5", $rss);
			
			$result = array("cache_dir" => $cache_dir, "cache_file" => $localfile);
			return $result;
		}
	}
}

if(!class_exists("Blip_Slideshow_Admin")) {

	/**
	 * Blip_Slideshow_Admin provides the installation, removal and settings functions
	 */
	class Blip_Slideshow_Admin {

		function Blip_Slideshow_Admin() {
			register_activation_hook( __FILE__, array( $this, "create_options") );
			register_uninstall_hook( __FILE__, array( $this, "destroy_options") );
			add_action("admin_init", array($this, "register_options"));
			add_action( "admin_menu", array( $this, "add_admin_menu_item") );
		}

		/**
		 * When Blip is deleted, remove the options from the database
		 */
		function destroy_options() {
			delete_option(BLIP_SLIDESHOW_DOMAIN);
		}
	
		/**
		 * Register the Settings page
		 */
		function register_options() {
			register_setting(BLIP_SLIDESHOW_DOMAIN, BLIP_SLIDESHOW_DOMAIN);
		}
	
		/**
		 * When Blip is activated, set up the default values in the database
		 */
		function create_options() {
			$options = array();
			$options["cache_enabled"] = false;
			$options["cache_dir"] = "cache";
			$options["cache_time"] = 86400;
			$options["optimize_scripts"] = false;
			$options["curl_redirects_enabled"] = false;
			add_option(BLIP_SLIDESHOW_DOMAIN, $options, "", "yes");
		}

		/**
		 * Output the HTML for the Settings page
		 */
		function display_admin_page() {
			?>
			<div class="wrap">
			<form method="post" id="next_page_form" action="options.php"><?php settings_fields(BLIP_SLIDESHOW_DOMAIN); $options = get_option(BLIP_SLIDESHOW_DOMAIN); ?>
			<input type="hidden" name="<?php echo BLIP_SLIDESHOW_DOMAIN ?>[cache_dir]" value="<?php echo $options["cache_dir"]; ?>" style="width:50px"/>
			<h2><?php echo BLIP_SLIDESHOW_NAME ?> Options</h2>
			<p>Caching is disabled by default and must be enabled here.</p>
			<table class="form-table">
				<tr valign="top">
				<th scope="row">Cache</th>
				<td>
					<fieldset><legend class="screen-reader-text"><span>Cache</span></legend>
					<label for="<?php echo BLIP_SLIDESHOW_DOMAIN ?>[cache_enabled]">
						<?php if($options["cache_enabled"]){ $sa = 'checked="checked"'; } ?><input name="<?php echo BLIP_SLIDESHOW_DOMAIN ?>[cache_enabled]" type="checkbox" id="<?php echo BLIP_SLIDESHOW_DOMAIN ?>[cache_enabled]" value="1" <?php echo $sa ?>/> Enable caching of media RSS files
					</label><br />
					<input name="<?php echo BLIP_SLIDESHOW_DOMAIN ?>[cache_time]" type="text" id="cache_time" value="<?php echo $options["cache_time"]; ?>" class="small-text" /> <span class="description"> Length of time (in seconds) to cache media RSS files</span></label><br />
					<input name="<?php echo BLIP_SLIDESHOW_DOMAIN ?>[cache_dir]" type="text" id="cache_dir" value="<?php echo $options["cache_dir"]; ?>" class="small-text" disabled="true" style="color:#aaa"/> <span class="description" style="color:#aaa"> Temporary directory where cached files are stored</span></label><br />
					</fieldset>
				</td>
				</tr>
				<tr valign="top">
				<th scope="row">Compatibility</th>
				<td>
					<span class="description"> Depending on your host's configuration, enabling the following options may break Blip Slideshow:</span>
					<fieldset><legend class="screen-reader-text"><span>Compatibility</span></legend>
					<label for="<?php echo BLIP_SLIDESHOW_DOMAIN ?>[optimize_scripts]">
						<?php if($options["optimize_scripts"]){ $sb = 'checked="checked"'; } ?><input name="<?php echo BLIP_SLIDESHOW_DOMAIN ?>[optimize_scripts]" type="checkbox" id="<?php echo BLIP_SLIDESHOW_DOMAIN ?>[optimize_scripts]" value="1" <?php echo $sb ?>/> Only load scripts and styles as neccessary
					</label><br />
					<label for="<?php echo BLIP_SLIDESHOW_DOMAIN ?>[curl_redirects_enabled]">
						<?php if($options["curl_redirects_enabled"]){ $sc = 'checked="checked"'; } ?><input name="<?php echo BLIP_SLIDESHOW_DOMAIN ?>[curl_redirects_enabled]" type="checkbox" id="<?php echo BLIP_SLIDESHOW_DOMAIN ?>[curl_redirects_enabled]" value="1" <?php echo $sc ?>/> Enable Media RSS redirects (required for MobileMe)
					</label><br />
					</fieldset>
				</td>
				</tr>
			</table>
			<p class="submit">
				<input type="submit" name="submit" class="button-primary" value="Update Options"/>
			</p>
			</form>
			</div>
			<?php 
		}
	
		/**
		 * Register links to the Settings page in the list of Plugins and in the Settings menu
		 */
		function add_admin_menu_item() {
      if (current_user_can("manage_options")) {

        add_filter("plugin_action_links_" . plugin_basename(__FILE__), array(& $this, "plugin_settings_link"));
				add_options_page(BLIP_SLIDESHOW_NAME, BLIP_SLIDESHOW_NAME, "manage_options", BLIP_SLIDESHOW_DOMAIN, array( $this, "display_admin_page") );
			}
		}
		
		/**
		 * Build the hyperlink for the list of Plugins
		 */
		function plugin_settings_link($links) {
      $settings_link = '<a href="options-general.php?page=' . BLIP_SLIDESHOW_DOMAIN . '">' . __("Settings", BLIP_SLIDESHOW_DOMAIN) . "</a>";
      $links[] = $settings_link;
      return $links;
    }
	
	}
}

/**
 * And this is where code execution starts
 */

// check if the request is to read a Media RSS URL
if(substr($_SERVER["PATH_INFO"], 0, 5) == "/rss/" && class_exists("Blip_Slideshow_Rss_Reader")) {
	
	// attempt to talk to Wordpress
	$blog_header_path = preg_replace("/wp-content\/.*/", "wp-blog-header.php", getcwd());
	if (file_exists($blog_header_path)) {
		require_once($blog_header_path);
	}
	// initiate the Media RSS reading
	$blip_rss_reader = new Blip_Slideshow_Rss_Reader();

} else {

	// start the maintenance
	if (class_exists("Blip_Slideshow_Admin")) {
  	$blip_slideshow_admin = new Blip_Slideshow_Admin();
	}

	// start the meat and potatoes
	if (class_exists("Blip_Slideshow")) {
  	$blip_slideshow = new Blip_Slideshow();
	}
}

?>
