<?php
if (! class_exists ( "Blip_Slideshow_Admin" )) {
	
	/**
	 * Blip_Slideshow_Admin provides the installation, removal and settings functions
	 */
	class Blip_Slideshow_Admin {
		function Blip_Slideshow_Admin() {
			register_activation_hook ( __FILE__, array (
					$this,
					"create_options" 
			) );
			add_action ( "admin_init", array (
					$this,
					"register_options" 
			) );
			add_action ( "admin_menu", array (
					$this,
					"add_admin_menu_item" 
			) );
		}
		
		/**
		 * Register the Settings page
		 */
		function register_options() {
			register_setting ( BLIP_SLIDESHOW_DOMAIN, BLIP_SLIDESHOW_DOMAIN );
		}
		
		/**
		 * When Blip is activated, set up the default values in the database
		 */
		function create_options() {
			$options = array ();
			$options ["cache_enabled"] = false;
			$options ["cache_dir"] = "cache";
			$options ["cache_time"] = 86400;
			$options ["optimize_scripts"] = false;
			$options ["curl_redirects_enabled"] = false;
			add_option ( BLIP_SLIDESHOW_DOMAIN, $options, "", "yes" );
		}
		
		/**
		 * Output the HTML for the Settings page
		 */
		function display_admin_page() {
			?>
<div class="wrap">
	<form method="post" id="next_page_form" action="options.php"><?php settings_fields(BLIP_SLIDESHOW_DOMAIN); $options = get_option(BLIP_SLIDESHOW_DOMAIN); ?>
			<input type="hidden"
			name="<?php echo BLIP_SLIDESHOW_DOMAIN ?>[cache_dir]"
			value="<?php echo $options["cache_dir"]; ?>" style="width: 50px" />
		<h2><?php echo BLIP_SLIDESHOW_NAME ?> Options</h2>
		<table class="form-table">
			<tr valign="top">
				<th scope="row">Cache</th>
				<td>
					<fieldset>
						<legend class="screen-reader-text">
							<span>Cache</span>
						</legend>
						<label for="<?php echo BLIP_SLIDESHOW_DOMAIN ?>[cache_enabled]">
						<?php if($options["cache_enabled"]){ $sa = 'checked="checked"'; } ?><input
							name="<?php echo BLIP_SLIDESHOW_DOMAIN ?>[cache_enabled]"
							type="checkbox"
							id="<?php echo BLIP_SLIDESHOW_DOMAIN ?>[cache_enabled]" value="1"
							<?php echo $sa ?> /> Enable caching of media RSS files
						</label><br /> <input
							name="<?php echo BLIP_SLIDESHOW_DOMAIN ?>[cache_time]"
							type="text" id="cache_time"
							value="<?php echo $options["cache_time"]; ?>" class="small-text" />
						<span class="description"> Length of time (in seconds) to cache
							media RSS files</span></label><br /> <input
							name="<?php echo BLIP_SLIDESHOW_DOMAIN ?>[cache_dir]" type="text"
							id="cache_dir" value="<?php echo $options["cache_dir"]; ?>"
							class="small-text" disabled="true" style="color: #aaa" /> <span
							class="description" style="color: #aaa"> Temporary directory
							where cached files are stored</span></label><br />
					</fieldset>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Compatibility</th>
				<td><span class="description"> Warning, enabling these settings on
						some hosts (eg. antagonist.nl) may cause Blip to fail:</span>
					<fieldset>
						<legend class="screen-reader-text">
							<span>Compatibility</span>
						</legend>
						<label for="<?php echo BLIP_SLIDESHOW_DOMAIN ?>[optimize_scripts]">
						<?php if($options['optimize_scripts']){ $sb = 'checked="checked"'; } ?><input
							name="<?php echo BLIP_SLIDESHOW_DOMAIN ?>[optimize_scripts]"
							type="checkbox"
							id="<?php echo BLIP_SLIDESHOW_DOMAIN ?>[optimize_scripts]"
							value="1" <?php echo $sb ?> /> Only load scripts and styles when
							neccessary
						</label><br /> <label
							for="<?php echo BLIP_SLIDESHOW_DOMAIN ?>[curl_redirects_enabled]">
						<?php if($options["curl_redirects_enabled"]){ $sc = 'checked="checked"'; } ?><input
							name="<?php echo BLIP_SLIDESHOW_DOMAIN ?>[curl_redirects_enabled]"
							type="checkbox"
							id="<?php echo BLIP_SLIDESHOW_DOMAIN ?>[curl_redirects_enabled]"
							value="1" <?php echo $sc ?> /> Enable Media RSS redirects
							(required for MobileMe)
						</label><br />
					</fieldset></td>
			</tr>
		</table>
		<p class="submit">
			<input type="submit" name="submit" class="button-primary"
				value="Update Options" />
		</p>
	</form>
</div>
<?php
		}
		
		/**
		 * Register links to the Settings page in the list of Plugins and in the Settings menu
		 */
		function add_admin_menu_item() {
			if (current_user_can ( "manage_options" )) {
				
				add_filter ( "plugin_action_links_" . plugin_basename ( __FILE__ ), array (
						& $this,
						"plugin_settings_link" 
				) );
				add_options_page ( BLIP_SLIDESHOW_NAME, BLIP_SLIDESHOW_NAME, "manage_options", BLIP_SLIDESHOW_DOMAIN, array (
						$this,
						"display_admin_page" 
				) );
			}
		}
		
		/**
		 * Build the hyperlink for the list of Plugins
		 */
		function plugin_settings_link($links) {
			$settings_link = '<a href="options-general.php?page=' . BLIP_SLIDESHOW_DOMAIN . '">' . __ ( "Settings", BLIP_SLIDESHOW_DOMAIN ) . "</a>";
			$links [] = $settings_link;
			return $links;
		}
	}
}
?>