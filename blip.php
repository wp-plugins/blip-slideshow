<?php
/*

		Plugin Name: Blip Slideshow
		Plugin URI: http://www.jasonhendriks.com/programmer/blip-slideshow/
		Description: A WordPress slideshow plugin fed from a SmugMug, Flickr or MobileMe RSS feed and displayed using pure Javascript.
		Version: 1.2.0
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

if (!defined('BLIP_SLIDESHOW_NAME')) {
    define('BLIP_SLIDESHOW_NAME', 'Blip Slideshow');
}
if (!defined('BLIP_SLIDESHOW_DOMAIN')) {
    define('BLIP_SLIDESHOW_DOMAIN', 'Blip_Slideshow');
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
			$url = html_entity_decode(urldecode($_REQUEST['url']));

			// check if we can talk to wordpress
			if(function_exists("get_option")) {
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
			if(function_exists('curl_init')) {
				// make the http request via curl
				$curl_options = array( 
					CURLOPT_RETURNTRANSFER => true,     // return web page 
					CURLOPT_HEADER         => false,    // don't return headers 
					CURLOPT_FOLLOWLOCATION => true,     // follow redirects 
					CURLOPT_ENCODING       => "",       // handle all encodings 
					CURLOPT_USERAGENT      => "Blip Slideshow/1.0", // who am i 
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
			$result = array("content" => $content, "max-age" => 0, "date" => time());
			return $result;
		}
		
		/**
		 * Will attempt to retrieve the contents of the media RSS URL from the cache.
		 * If the Media RSS is expired or empty, will retrieve the fresh contents via HTTP and then overwrite the cache.
		 */
		function get_rss_content_from_cache($url) {
			// retrieve saved options
			$options = get_option(BLIP_SLIDESHOW_DOMAIN);
			
			// determine the read/write paths
			$result = Blip_Slideshow::build_cache_paths($options, $url);
			$cache_dir = $result["cache_dir"];
			$localfile = $result["cache_file"];
	
			// if cache is enabled and this file is cacheable
			if ($options['cache_enabled'] && file_exists($localfile)){
				// determine the amount of time (in seconds) this file can still be considered "fresh"
				$date = filemtime($localfile);
				$max_age = max(0, $options['cache_time'] - (time()-$date));
				// determine if the file on disk is populated and fresh
				if(filesize($localfile) != 0 && $max_age > 0) {
					// read from the cache.
					$content = file_get_contents($localfile);
					// prepare the result
					$result = array("content" => $content, "max-age" => $max_age, "date" => $date);
				} else {
					// read from http
					$result = $this->get_rss_content_from_http($url);
					// determine if we got a valid response
					if($result['content'] != FALSE) {
						// populate the cache
						$fp=fopen($localfile, "w");
						fwrite($fp, $result['content']); //write contents of feed to cache file
						fclose($fp);
						$result['max-age'] = $options['cache_time'];
						$result['date'] = time();
					}
				}
				// return the result
				$result['cache'] = $localfile;
				return $result;
			} else {
				// read from http and return the result
				return $this->get_rss_content_from_http($url);
			}
		}

		/**
		 * Build the document by outputing XML headers and the content.
		 */		
		function print_document($content, $url) {
			if($content != FALSE) {
				$http_status = $_SERVER["SERVER_PROTOCOL"] . " 200 OK";
				$content_type = "Content-Type: text/xml";
				// http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.29
				$date = "Last-Modified: " . date(DATE_RFC1123, $content['date']);
				// http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.9
				$cache_control = "Cache-Control: max-age=" . $content['max-age'] . ", must-revalidate";
				// http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.45
				$via = "Via: " . "1.0.0 Blip Slideshow";
				if($content['cache'] != FALSE) {
					$via .= " cachefile=" . preg_replace("/.*[\\/]/","",$content['cache']);
				} else {
					$via .= " no-cache";
				}
				// http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.14
				$content_location = "Content-Location: " . $_REQUEST['url'];
				
				// push headers
				header($http_status);
				header($date);
				header($cache_control);
				header($content_location);
				header($via);

				if (!isset($_REQUEST['debug'])) {
					header($content_type);
					print $content['content'];
				} else {
					print("<html><head></head><body>");
					print($http_status . "<br/>");
					print($content_type . "<br/>");
					print($date . "<br/>");
					print($cache_control . "<br/>");
					print($content_location . "<br/>");
					print($via . "<br/>");
					print "<pre>" . preg_replace("/</", "&lt;", $content['content']) . "</pre></body></html>";
				}
			}
		}

	}
}

if(!class_exists(BLIP_SLIDESHOW_DOMAIN)) {
	class Blip_Slideshow {
		var $counter = 0;
		var $add_script = false;
		
		var $flash_slideshow = false;
		var $fold_slideshow = false;
		var $kenburns_slideshow = false;
		var $push_slideshow = false;
			
		function Blip_Slideshow() {
			register_activation_hook( __FILE__, array( $this, 'create_options') );
			register_uninstall_hook( __FILE__, array( $this, 'destroy_options') );
			add_shortcode( 'slideshow', array( $this, 'blip_create_slideshow') );
			add_action( 'wp_footer', array( $this, 'add_footer_scripts') );
			add_action("admin_init", array($this, "register_options"));
			add_action( 'admin_menu', array( $this, 'add_admin_menu_item') );
			$this->add_header_scripts();
			// beta versions of Blip used a different option name (up to v0.4.2).
			// TODO: This line will be removed for v1.0.2
			delete_option("blip"); // remove the old settings
		}
	
		/**
		 * When Blip is activated, set up the default values in the database
		 */
		function create_options() {
			$options = array();
			$options['cache_enabled'] = false;
			$options['cache_dir'] = "cache";
			$options['cache_time'] = 3600;
			add_option(BLIP_SLIDESHOW_DOMAIN, $options, '', 'yes');
		}

		/**
		 * When Blip is deleted, remove the options from the database
		 */
		function destroy_options() {
			delete_option(BLIP_SLIDESHOW_DOMAIN);
		}
	
		function blip_create_slideshow($atts, $content = null) {
			$this->counter++;
			$this->add_script = true;
			
			// retrieve saved options
			$options = get_option(BLIP_SLIDESHOW_DOMAIN);

			// extract rss from shortcode attributes
			$sample_feed = plugins_url('/sample_feed.php', __FILE__);
			extract(shortcode_atts(array(
				'captions' => 'true',
				'center' => 'true',
				'color' => "#FFF",
				'controller' => 'true',
				'delay' => '2000',
				'duration' => '1000',
				'fast' => 'false',
				'height' => 'false',
				'id' => 'show-' . $this->counter,
				'link' => 'full',
				'loader' => 'true',
				'loop' => 'true',
				'overlap' => 'true',
				'pan' => '100, 100',
				'paused' => 'false',
				'random' => 'false',
				'resize' => 'fill',
				'rss' => $sample_feed,
				'slide' => 0,
				'thumbnails' => 'true',
				'titles' => 'false',
				'transition' => 'sine:in:out',
				'type' => '',
				'width' => 'false',
				'zoom' => '50, 50'
			), $atts));

			// determine which alternative Slideshow, if any, are to be used
			if($type == "flash") {
				$this->flash_slideshow = true;
			} else if ($type == "fold") {
				$this->fold_slideshow = true;
			} else if($type =="kenburns") {
				$this->kenburns_slideshow = true;
			} else if($type == "push") {
				$this->push_slideshow = true;
			}
	
			// wordpress has encoded the HTML entities
			$decoded_rss = html_entity_decode($rss);

			// enable caching for this file?
			if($options['cache_enabled']) {
				$this->prep_cache($options, $rss);
			}
			
			// massage the rss url
			$callback_url = plugins_url('/blip.php?url=', __FILE__) . rawurlencode($decoded_rss);
			
			// massage link option
			if($link == 'lightbox' && (function_exists('slimbox') || function_exists('wp_slimbox_activate'))) {
				$link = "slimbox";
			} else if($link == 'lightbox' && (class_exists('wp_lightboxplus') || class_exists('GameplorersWPColorBox') || class_exists('jQueryLightboxForNativeGalleries'))) {
				$link = "colorbox";
			} else if($link == "lightbox") {
				// no supported lightbox plugins
				$link = "full";
			}

			// build Javascript output
			$output .= '<script type="text/javascript">
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
			
			if($this->flash_slideshow) {
				// massage the colour option
				$output .= "color: " . "['" . preg_replace("/,/","','",$color) . "']" . ", ";
			}
			
			if($this->kenburns_slideshow) {
				$output .= "pan: " . "[" . $pan . "]" . ", zoom: " . "[" . $zoom . "], ";
			}
	
			// build remainder of script options
			$output .= "captions: " . $captions . ", center: " . $center . ", controller: " . $controller . ", fast: " . $fast . ", height: " . $height . ", loader: " . $loader . ", overlap: " . $overlap . ", transition: '" . $transition . "', thumbnails: " . $thumbnails . ", width: " . $width . "};";
			$output .= 'new Blip(' . json_encode($id) . ', ' . json_encode($callback_url) . ', ' . json_encode($link) . ', ' . json_encode($type) . ', options); });
			//]] >
			</script><div id="' . $id . '" class="slideshow">';
			
			// the contents of the shortcode
			if(!empty( $content )) {
				$output .= '<span class="slideshow-content">' . $content . '</span>';
			}
			$output .= '</div>';
		
			return $output;
		}
	
		/**
		 * Caching is enabled. Retrieve the preferred cache directory name. Create the cache directory and cache file.
		 * Input is the $options retrieved from the database and the raw_url_encoded RSS URL
		 */ 
		function prep_cache($options, $rss) {
			// determine the read/write paths
			$result = $this->build_cache_paths($options, $rss);
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
			$stored_cache_dir = $options['cache_dir'];
			
			// check to see if the cache dir option is an absolute path or not - will probably fail on Windows
			if( $stored_cache_dir[0] == '/' ) {
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
	
		function add_header_scripts() {
			if (!is_admin()) {
				wp_register_script('mootools', plugins_url('/Slideshow/js/mootools-1.3.1-core.js', __FILE__));
				wp_enqueue_script('mootools');
			}
		}
	
		function add_footer_scripts() {
			if ( $this->add_script ) {
				wp_register_style( 'slideshow2', plugins_url('/Slideshow/css/slideshow.css', __FILE__));
				wp_print_styles( 'slideshow2');

				if(file_exists(get_theme_root() . '/' . get_template() . '/blip-slideshow.css')) {
					?><link rel='stylesheet' id='blip-slideshow-css' href='<?php bloginfo("stylesheet_directory") ?>/blip-slideshow.css?ver=3.1' type='text/css' media='all' />
<?php
				}

				wp_register_script( 'mootools-more', plugins_url('/Slideshow/js/mootools-1.3.1.1-more.js', __FILE__));
				wp_print_scripts( 'mootools-more' );
				wp_register_script( 'slideshow2', plugins_url('/Slideshow/js/slideshow.js', __FILE__));
				wp_print_scripts( 'slideshow2' );
				if($this->flash_slideshow) {
					wp_register_script( 'slideshow2-flash', plugins_url('/Slideshow/js/slideshow.flash.js', __FILE__));
					wp_print_scripts( 'slideshow2-flash' );
				}
				if($this->fold_slideshow) {
					wp_register_script( 'slideshow2-fold', plugins_url('/Slideshow/js/slideshow.fold.js', __FILE__));
					wp_print_scripts( 'slideshow2-fold' );
				}
				if($this->kenburns_slideshow) {
					wp_register_script( 'slideshow2-kenburns', plugins_url('/Slideshow/js/slideshow.kenburns.js', __FILE__));
					wp_print_scripts( 'slideshow2-kenburns' );
				}
				if($this->push_slideshow) {
					wp_register_script( 'slideshow2-push', plugins_url('/Slideshow/js/slideshow.push.js', __FILE__));
					wp_print_scripts( 'slideshow2-push' );
				}
				wp_register_script( BLIP_SLIDESHOW_DOMAIN, plugins_url('/blip.js', __FILE__), null, false, false);
				wp_print_scripts( BLIP_SLIDESHOW_DOMAIN );
			}
		}
	
		/**
		 * Register the Settings page
		 */
		function register_options() {
			register_setting(BLIP_SLIDESHOW_DOMAIN, BLIP_SLIDESHOW_DOMAIN);
		}
	
		/**
		 * Register links to the Settings page in the list of Plugins and in the Settings menu
		 */
		function add_admin_menu_item() {
			if (current_user_can('manage_options')) {

      	add_filter('plugin_action_links_' . plugin_basename(__FILE__), array(& $this, 'plugin_settings_link'));
				add_options_page(BLIP_SLIDESHOW_NAME, BLIP_SLIDESHOW_NAME, 'manage_options', BLIP_SLIDESHOW_DOMAIN, array( $this, 'display_admin_page') );
			}
		}

		/**
		 * Build the hyperlink for the list of Plugins
		 */
		function plugin_settings_link($links) {
			$settings_link = '<a href="options-general.php?page=' . BLIP_SLIDESHOW_DOMAIN . '">' . __('Settings', BLIP_SLIDESHOW_DOMAIN) . '</a>';
			$links[] = $settings_link;
			return $links;
		}
	
		/**
		 * Output the HTML for the Settings page
		 */
		function display_admin_page() {
			?>
			<div class="wrap">
			<form method="post" id="next_page_form" action="options.php"><?php settings_fields(BLIP_SLIDESHOW_DOMAIN); $options = get_option(BLIP_SLIDESHOW_DOMAIN); ?>
			<input type="hidden" name="<?php echo BLIP_SLIDESHOW_DOMAIN ?>[cache_dir]" value="<?php echo $options['cache_dir']; ?>" style="width:50px"/>
			<h2><?php echo BLIP_SLIDESHOW_NAME ?> Options</h2>
			<p>Caching is disabled by default and must be enabled here.</p>
			<table class="form-table">
				<tr valign="top">
				<th scope="row">Cache</th>
				<td>
					<fieldset><legend class="screen-reader-text"><span>Cache</span></legend>
					<label title="Enabled or disable caching"><?php if($options['cache_enabled']){ $sy = 'checked="checked"'; } ?><input name="<?php echo BLIP_SLIDESHOW_DOMAIN ?>[cache_enabled]" type="checkbox" id="use_cache" value="1" <?php echo $sy ?>/> <span>Enable caching of media RSS files</span></label><br />
					<label title="Cache time"><input name="<?php echo BLIP_SLIDESHOW_DOMAIN ?>[cache_time]" type="text" id="cache_time" value="<?php echo $options['cache_time']; ?>" class="small-text" /> <span class="example"> Length of time (in seconds) to cache media RSS files</span></label><br />
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
	}
}

// check if the request is to read a Media RSS URL
if(isset($_REQUEST['url']) && class_exists("Blip_Slideshow_Rss_Reader")) {
	
	// attempt to talk to Wordpress
	$blog_header_path = preg_replace("/wp-content\/.*/", "wp-blog-header.php", getcwd());
	if (file_exists($blog_header_path)) {
		require_once($blog_header_path);
	}
	// initiate the Media RSS reading
  $blip_rss_reader = new Blip_Slideshow_Rss_Reader();
  // commit suicide
  die;
}

// start the meat and potatoes
if (class_exists("Blip_Slideshow")) {
    $blip = new Blip_Slideshow();
}

?>
