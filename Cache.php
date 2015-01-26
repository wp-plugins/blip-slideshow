<?php
if (! class_exists ( "Blip_Slideshow_Cache" )) {
	
	/**
	 * Blip_Slideshow_Cache provides two utilitie functions for reading and writing to the cached RSS file
	 */
	class Blip_Slideshow_Cache {
		/**
		 * Caching is enabled.
		 * Retrieve the preferred cache directory name. Create the cache directory and cache file.
		 * Input is the $options retrieved from the database and the raw_url_encoded RSS URL
		 */
		static function is_cache_enabled($options) {
			return $options ["cache_enabled"] && $options ["cache_time"] != 0;
		}
		static function prep_cache($options, $rss) {
			// determine the read/write paths
			$result = Blip_Slideshow_Cache::build_cache_paths ( $options, $rss );
			$cache_dir = $result ["cache_dir"];
			$localfile = $result ["cache_file"];
			
			// if the cache dir doesn't exist
			if (! file_exists ( $cache_dir )) {
				// create the cache dir
				mkdir ( $cache_dir );
				chmod ( $cache_dir, 0777 );
			}
			
			// if the cache file doesn't exist
			if (! file_exists ( $localfile )) {
				// create the cache file
				touch ( $localfile );
				chmod ( $localfile, 0666 );
			}
		}
		static function build_cache_paths($options, $rss) {
			// get the cache path from options
			$stored_cache_dir = $options ["cache_dir"];
			
			// check to see if the cache dir option is an absolute path or not - will probably fail on Windows
			if ($stored_cache_dir [0] == "/") {
				// cache dir option IS the cache dir
				$cache_dir = $stored_cache_dir;
			} else {
				// cache dir is the path of this PHP file concatenated with the cache dir option
				$cache_dir = preg_replace ( "/blip.php/", "", __FILE__ ) . $stored_cache_dir;
			}
			
			// the localfile is the cache dir concatenated with the RawUrlEncoded RSS URL
			$localfile = $cache_dir . "/" . hash ( "md5", $rss );
			
			$result = array (
					"cache_dir" => $cache_dir,
					"cache_file" => $localfile 
			);
			return $result;
		}
	}
}

?>