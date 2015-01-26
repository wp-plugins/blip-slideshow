<?php
if (! class_exists ( "Blip_Slideshow_Rss_Reader" )) {
	
	/**
	 * Blip_Slideshow_Rss_Reader is a proxy to get the content of the Media RSS files.
	 * It supports caching, if it has been enabled in the settings.
	 */
	class Blip_Slideshow_Rss_Reader {
		
		/**
		 * Get the content of the Media RSS URL either directly, or if caching is available, from the cache.
		 */
		function Blip_Slideshow_Rss_Reader($url) {
			
			// check if we can talk to wordpress
			if (function_exists ( "get_option" )) {
				debug ( 'attempt to get feed from cache' );
				// retrieve saved options
				$this->options = get_option ( BLIP_SLIDESHOW_DOMAIN );
				// attempt to get the content from the cache
				$result = $this->get_rss_content_from_cache ( $url );
			} else {
				debug ( 'pull feed from Internet' );
				// can't talk to wordpress; get content directly
				$result = $this->get_rss_content_from_http ( $url );
			}
			debug ( 'printing document to response' );
			$this->print_document ( $result, $url );
		}
		
		/**
		 * Fetch the content of the Media RSS URL via HTTP.
		 * Will first try file_get_contents, and if that is disabled, will try curl
		 */
		function get_rss_content_from_http($url) {
			// sometimes the protocol is given as feed://, but this media type is not recognize by curl or php
			$url = preg_replace ( "/^feed\:\/\//", "http://", $url );
			debug ( 'scrubbed url=' . $url );
			
			// if curl exists, use it to make the http request
			if (function_exists ( "curl_init" )) {
				debug ( 'curl exists' );
				// make the http request via curl
				$curl_options = array (
						CURLOPT_RETURNTRANSFER => true, // return web page
						CURLOPT_HEADER => false, // don't return headers
						CURLOPT_FOLLOWLOCATION => $this->options ["curl_redirects_enabled"], // follow redirects
						CURLOPT_ENCODING => "", // handle all encodings
						CURLOPT_USERAGENT => BLIP_SLIDESHOW_NAME . "/" . Blip_Slideshow::get_version (), // who am i
						CURLOPT_AUTOREFERER => true, // set referer on redirect
						CURLOPT_CONNECTTIMEOUT => 30, // timeout on connect
						CURLOPT_TIMEOUT => 30, // timeout on response
						CURLOPT_MAXREDIRS => 10 
				); // stop after 10 redirects
				
				$crl = curl_init ( $url );
				curl_setopt_array ( $crl, $curl_options );
				$content = curl_exec ( $crl );
				$err = curl_errno ( $crl );
				$errmsg = curl_error ( $crl );
				$header = curl_getinfo ( $crl );
				curl_close ( $crl );
			} else {
				// curl is not available, so make the http request via PHP
				// this function will not work with MobileMe because of HTTP redirects
				$content = file_get_contents ( $url );
			}
			
			// check if content was received
			if ($content != FALSE) {
				// massage the content for Slick to handle in Javascript
				$content = preg_replace ( "/<(\/)?([A-Za-z][A-Za-z0-9]+):([A-Za-z][A-Za-z0-9]+)/", "<$1$2_$3", $content );
			}
			
			// prepare the result
			$now = time ();
			$result = array (
					"content" => $content,
					"max-age" => 0,
					"date" => $now,
					"expires" => $now 
			);
			return $result;
		}
		
		/**
		 * Attempt to retrieve the contents of the media RSS URL from the cache.
		 * If the Media RSS is expired or empty, will retrieve the fresh contents via HTTP and then overwrite the cache.
		 */
		function get_rss_content_from_cache($url) {
			
			// test if cache is enabled
			if (Blip_Slideshow_Cache::is_cache_enabled ( $this->options )) {
				
				debug ( 'cache is enabled' );
				// cache is enabled. determine the read/write paths
				$cache_paths = Blip_Slideshow_Cache::build_cache_paths ( $this->options, $url );
				$localfile = $cache_paths ["cache_file"];
				
				// test if there is a cache file available for I/O
				if (file_exists ( $localfile )) {
					
					debug ( 'cache file is available' );
					// cache file is available. read/write the cache
					return $this->read_write_cache ( $url, $localfile );
				} else {
					
					debug ( 'cache file is not available - reading from http' );
					// cache file is available. read from http and return the result
					return $this->get_rss_content_from_http ( $url );
				}
			} else {
				debug ( 'cache is not enabled' );
				// cache is not enabled. read from http and return the result
				return $this->get_rss_content_from_http ( $url );
			}
		}
		
		/**
		 * Read from the cache if it is valid.
		 * Write to cache if it is not.
		 * Return the fresh or cached content.
		 */
		function read_write_cache($url, $localfile) {
			// determine the amount of time (in seconds) this file can still be considered fresh
			$cache_time = $this->options ["cache_time"];
			$last_modified = filemtime ( $localfile );
			$expires = $cache_time + $last_modified;
			$max_age = max ( 0, $expires - time () );
			$etag = preg_replace ( "/.*[\\/]/", "", $localfile );
			
			// fill out what we know so far
			$result = array (
					"max-age" => $max_age,
					"date" => $last_modified,
					"expires" => $expires,
					"cache" => $etag 
			);
			
			// test if the cache is expired
			if ($max_age == 0) {
				
				// populate the cache
				$result = $this->poulate_cache ( $url, $result, $localfile, $cache_time );
				
				// test if the client is validating cache
			} else if (isset ( $_SERVER ["HTTP_IF_MODIFIED_SINCE"] ) && $last_modified <= strtotime ( preg_replace ( "/;.*$/", "", $_SERVER ["HTTP_IF_MODIFIED_SINCE"] ) )) {
				
				// return HTTP 304 - not modified
				$result ["304"] = true;
				
				// test if the client is validating cache
			} else if (isset ( $_SERVER ["HTTP_IF_NONE_MATCH"] ) && $etag == str_replace ( '"', "", stripslashes ( $_SERVER ["HTTP_IF_NONE_MATCH"] ) )) {
				
				// return HTTP 304 - not modified
				$result ["304"] = true;
				
				// test if the cache file is not size zero
			} else if (filesize ( $localfile ) != 0) {
				
				// cache is populate and valid. read from the cache.
				$result ["content"] = file_get_contents ( $localfile );
				
				// test if the cache file is size zero
			} else {
				
				// cache is newly created. populate the cache.
				$result = $this->poulate_cache ( $url, $result, $localfile, $cache_time );
			}
			// return the result
			return $result;
		}
		
		/**
		 * populate the cache
		 */
		function poulate_cache($url, $result, $localfile, $cache_time) {
			// read from http
			$result = $this->get_rss_content_from_http ( $url );
			// determine if we got a valid response
			if ($result ["content"]) {
				// populate the cache
				$fp = fopen ( $localfile, "w" );
				fwrite ( $fp, $result ["content"] ); // write contents of feed to cache file
				fclose ( $fp );
				$now = time ();
				$result ["max-age"] = $cache_time;
				$result ["date"] = $now;
				$result ["expires"] = $now + $cache_time;
			}
			return $result;
		}
		
		/**
		 * Build the document by outputing XML headers and the content.
		 */
		function print_document($content, $url) {
			
			// http://www.php.net/manual/en/function.header.php#77028
			
			// push headers
			header ( "Via: " . Blip_Slideshow::VERSION . " " . BLIP_SLIDESHOW_NAME );
			header ( "Content-Location: " . $url );
			header ( "Last-Modified: " . date ( DATE_RFC1123, $content ["date"] ) );
			if (isset ( $content ["304"] )) {
				header ( "HTTP/1.1 304 Not Modified" );
			} else {
				header ( "HTTP/1.1 200 OK" );
			}
			header ( "Expires: " . date ( DATE_RFC1123, ($content ["expires"]) ) );
// 			header ( "Pragma: " . $pragma );
			
			if ($content ["cache"]) {
				header ( "ETag: " . preg_replace ( "/.*[\\/]/", "", $content ["cache"] ) );
				header ( "Pragma: public" );
				header ( "Cache-Control: max-age=" . $content ["max-age"] );
			} else {
				header ( "Pragma: no-cache" );
				header ( "Cache-Control: no-cache, must-revalidate, max-age=0" );
			}
			
			if (isset ( $_REQUEST ["debug"] )) {
				print ("<html><head></head><body>") ;
				foreach ( headers_list () as $header ) {
					print $header . "<br/>";
				}
				print ("Content-Type: text/xml<br/>") ;
				print ("Content-Length: " . strlen ( $content ["content"] )) ;
				print "<pre>" . preg_replace ( "/</", "&lt;", $content ["content"] ) . "</pre></body></html>";
			} else if (! isset ( $content ["304"] )) {
				header ( "Content-Type: text/xml" );
				header ( "Content-Length: " . strlen ( $content ["content"] ) );
				print $content ["content"];
			}
		}
	}
}

?>