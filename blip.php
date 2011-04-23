<?php
/*

    Plugin Name: Blip Slideshow
    Plugin URI: http://www.jasonhendriks.com/programmer/blip-slideshow/
    Description: A WordPress slideshow plugin fed from a SmugMug or Flickr RSS feed and displayed using pure Javascript.
    Version: 0.4.1
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
	class Blip_Slideshow_Rss_Reader {
		
		function Blip_Slideshow_Rss_Reader() {
			$url = rawurldecode($_REQUEST['url']);
			if(function_exists("get_option")) {
				// if we can talk to wordpress
				$content = $this->get_rss_content_from_cache($url);
			} else {
				// can't talk to wordpress; get content directly
				$content = $this->get_rss_content($url);
			}
			$this->print_content($content);
		}
		
		function get_rss_content($url) {
			// sometimes the protocol is given as feed://, but this media type is not recognize by curl or php
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
			} else {
				$content = file_get_contents($url);
			}
			if($content != FALSE) {
				$content = preg_replace("/<(\/)?([A-Za-z][A-Za-z0-9]+):([A-Za-z][A-Za-z0-9]+)/", "<$1$2_$3", $content);
			}
			return $content;
		}
		
		function get_rss_content_from_cache($url) {
			// retrieve saved options
			$options = get_option(BLIP_SLIDESHOW_DOMAIN);
			$result = Blip_Slideshow::build_cache_paths($options, $url);
			$cache_dir = $result["cache_dir"];
			$localfile = $result["cache_file"];
	
			// if cache is enabled and this file is cacheable
			if ($options['cache_enabled'] && file_exists($localfile)){
				// caching is enabled for this file
				if(filesize($localfile) != 0 && (time()-filemtime($localfile)) < $options['cache_time']) {
					// cache is populated and not expired. read from the cache.
					$content = file_get_contents($localfile);
				} else {
					// cache is expired or not populated. populate the cache
					$content = $this->get_rss_content($url);
					if($content != FALSE) {
						$fp=fopen($localfile, "w");
						fwrite($fp, $content); //write contents of feed to cache file
						fclose($fp);
					}
				}
			} else {
				// caching is not enabled for this file
				$content = $this->get_rss_content($url);
			}
			return $content;
		}
		
		function print_content($content) {
			if($content != FALSE) {
				if (!isset($_REQUEST['debug'])) {
					header('HTTP/1.1 200 OK');
					header("Content-Type: text/xml");
					print $content;
				} else {
					print "<html><head></head><body><pre>" . preg_replace("/</", "&lt;", $content) . "</pre></body></html>";
				}
			}
		}

	}
}

if(!class_exists(BLIP_SLIDESHOW_DOMAIN)) {
	class Blip_Slideshow {
		var $counter = 0;
		var $add_script = false;
			
		function Blip_Slideshow() {
			register_activation_hook( __FILE__, array( $this, 'create_options') );
			register_uninstall_hook( __FILE__, array( $this, 'destroy_options') );
			add_shortcode( 'slideshow', array( $this, 'blip_create_slideshow') );
			add_action( 'wp_footer', array( $this, 'add_footer_scripts') );
			add_action("admin_init", array($this, "register_options"));
			add_action( 'admin_menu', array( $this, 'add_admin_menu_item') );
			$this->add_header_scripts();
		}
	
		// register the setting
		function register_options() {
			register_setting(BLIP_SLIDESHOW_DOMAIN, BLIP_SLIDESHOW_DOMAIN);
		}
	
		// default options
		function create_options() {
			$options = array();
			$options['cache_enabled'] = false;
			$options['cache_dir'] = "cache";
			$options['cache_time'] = 3600;
			add_option(BLIP_SLIDESHOW_DOMAIN, $options, '', 'yes');
		}
	
		function destroy_options() {
			delete_option(BLIP_SLIDESHOW_DOMAIN);
			// beta versions of Blip used a different option name (up to v0.4.2).
			// Delete the beta options with this line
			// .. assuming the name "blip" does not collide with another extension you may have installed!!
			delete_option("blip");
		}
	
		function blip_create_slideshow($atts, $content = null) {
			$this->counter++;
			$this->add_script = true;
			
			// retrieve saved options
			$options = get_option(BLIP_SLIDESHOW_DOMAIN);

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
				'id' => 'show-' . $this->counter,
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
				'debug' => 'false',
				'cache' => ''
			), $atts));
	
			// santize the rss url
			$callback_url = plugins_url('/blip.php?url=', __FILE__) . rawurlencode($rss);
			
			// enable caching for this file?
			if($cache != "false" && $options['cache_enabled']) {
				$this->prep_cache($options, $rss);
			}
			
			// handle lightbox link options
			if($link == 'lightbox' && (function_exists('slimbox') || function_exists('wp_slimbox_activate'))) {
				$link = "slimbox";
			} else if($link == 'lightbox' && (class_exists('wp_lightboxplus') || class_exists('GameplorersWPColorBox') || class_exists('jQueryLightboxForNativeGalleries'))) {
				$link = "colorbox";
			} else if($link == "lightbox") {
				// no supported lightbox plugins
				$link = "full";
			}
	
			// build Javascript output
			$output .= '<!-- ' . BLIP_SLIDESHOW_NAME . ' -->';
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
	
			// build remainder of script options
			$output .= "captions: " . $captions . ", center: " . $center .", controller: " . $controller . ", fast: " . $fast . ", height: " . $height . ", loader: " . $loader . ", overlap: " . $overlap . ", thumbnails: " . $thumbnails . ', width: ' . $width . "};";
			$output .= 'new Blip(' . json_encode($id) . ', ' . json_encode($callback_url) . ', ' . json_encode($link) . ', options); });
			//]] >
			</script><div id="' . $id . '" class="slideshow">';
			
			// the contents of the shortcode
			if(!empty( $content )) {
				$output .= '<span class="slideshow-content">' . $content . '</span>';
			}
			$output .= '</div>';
			$output .= '<!-- ' . BLIP_SLIDESHOW_NAME . ' -->';
		
			return $output;
		}
	
		/**
		 * Caching is enabled. Retrieve the preferred cache directory name. Create the cache directory and cache file.
		 * Input is the $options retrieved from the database and the raw_url_encoded RSS URL
		 */ 
		function prep_cache($options, $rss) {
			// build the cache dir
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
				echo '<!-- ' . BLIP_SLIDESHOW_NAME . ' -->';
				wp_register_style( 'slideshow2', plugins_url('/Slideshow/css/slideshow.css', __FILE__));
				wp_print_styles( 'slideshow2');
	
				wp_register_script( 'mootools-more', plugins_url('/Slideshow/js/mootools-1.3.1.1-more.js', __FILE__));
				wp_register_script( 'slideshow2', plugins_url('/Slideshow/js/slideshow.js', __FILE__));
				wp_register_script( BLIP_SLIDESHOW_DOMAIN, plugins_url('/blip.js', __FILE__), null, false, false);
	
				wp_print_scripts( 'mootools-more' );
				wp_print_scripts( 'slideshow2' );
				wp_print_scripts( BLIP_SLIDESHOW_DOMAIN );
				echo '<!-- ' . BLIP_SLIDESHOW_NAME . ' -->';
			}
		}
	
		// Add a new submenu under Options:
		function add_admin_menu_item() {
      if (current_user_can('manage_options')) {
        add_filter('plugin_action_links_' . plugin_basename(__FILE__), array(& $this, 'plugin_settings_link'));
				add_options_page(BLIP_SLIDESHOW_NAME, BLIP_SLIDESHOW_NAME, 'manage_options', BLIP_SLIDESHOW_DOMAIN, array( $this, 'display_admin_page') );
			}
		}
		
		// display the settings link on the plugin page
		// http://striderweb.com/nerdaphernalia/2008/06/wp-use-action-links/
		function plugin_settings_link($links) {
      $settings_link = '<a href="options-general.php?page=' . BLIP_SLIDESHOW_DOMAIN . '">' . __('Settings', BLIP_SLIDESHOW_DOMAIN) . '</a>';
      $links[] = $settings_link;
      return $links;
    }
	
		// displays the options page content
		function display_admin_page() {
			?>
			<div class="wrap">
			<form method="post" id="next_page_form" action="options.php"><?php settings_fields(BLIP_SLIDESHOW_DOMAIN); $options = get_option(BLIP_SLIDESHOW_DOMAIN); ?>
			<input type="hidden" name="<?php echo BLIP_SLIDESHOW_DOMAIN ?>[cache_dir]" value="<?php echo $options['cache_dir']; ?>" style="width:50px"/>
			<h2> Options</h2>
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

if(isset($_REQUEST['url']) && class_exists("Blip_Slideshow_Rss_Reader")) {
	$blog_header_path = preg_replace("/wp-content\/.*/", "wp-blog-hesader.php", getcwd());
	if (file_exists($blog_header_path)) {
		require_once($blog_header_path);
	}
  $blip_rss_reader = new Blip_Slideshow_Rss_Reader();
  die;
}

if (class_exists("Blip_Slideshow")) {
    $blip = new Blip_Slideshow();
}

?>
