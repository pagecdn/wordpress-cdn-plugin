<?php

/**
 * pagecdn_Settings
 *
 * @since 0.1
 */

class PageCDN_Settings
{
	/**
	 * register settings
	 *
	 * @since   0.1
	 */
	
	public static function register_settings()
	{
		register_setting(
			'pagecdn',
			'pagecdn',
			[
				__CLASS__,
				'validate_settings',
			]
		);
	}


	/**
	 * validation of settings
	 *
	 * @since   0.1
	 *
	 * @param   array  $data  array with form data
	 * @return  array		 array with validated values
	 */

	public static function validate_settings($data)
	{
		if (!isset($data['relative'])) {
			$data['relative'] = 1;
		}
		if (!isset($data['https'])) {
			$data['https'] = 1;
		}
		if (!isset($data['fonts'])) {
			$data['fonts'] = 0;
		}
		if (!isset($data['replace_cdns'])) {
			$data['replace_cdns'] = 0;
		}
		if (!isset($data['reuse_libs'])) {
			$data['reuse_libs'] = 0;
		}
		if (!isset($data['min_files'])) {
			$data['min_files'] = 0;
		}
		
		if (!isset($data['pagecdn_api_key'])) {
			$data['pagecdn_api_key'] = '';
		}
		
		return [
			'url'				=> esc_url( rtrim( $data['url'] , '/' ) )	,
			'dirs'				=> esc_attr( $data['dirs'] )				,
			'excludes'			=> esc_attr( $data['excludes'] )			,
			'pagecdn_api_key'	=> esc_attr( $data['pagecdn_api_key'] )		,
			
			'fonts'				=> ( int ) $data['fonts']					,
			'replace_cdns'		=> ( int ) $data['replace_cdns']			,
			'reuse_libs'		=> ( int ) $data['reuse_libs']				,
			'min_files'			=> ( int ) $data['min_files']				,
			
			'relative'			=> ( int ) $data['relative']				,
			'https'				=> ( int ) $data['https']					,
		];
	}


	/**
	 * add settings page
	 *
	 * @since   0.1
	 */
	
	public static function add_settings_page()
	{
		$page = add_options_page(
			'PageCDN',
			'PageCDN',
			'manage_options',
			'pagecdn',
			[
				__CLASS__,
				'settings_page',
			]
		);
	}


	/**
	 * settings page
	 *
	 * @since   0.1
	 *
	 * @return  void
	 */

	public static function settings_page()
	{
		$options = PageCDN::get_options();
	  ?>
		<div class="wrap">
			<h2>
				<?php _e("PageCDN Settings", "pagecdn"); ?>
			</h2>
			
			<form method="post" action="options.php">
				<?php settings_fields('pagecdn') ?>
				
				<table class="form-table">
					
					<tr valign="top">
						<th scope="row">
							<?php _e("CDN URL", "pagecdn"); ?>
						</th>
						<td>
							<fieldset>
								<label for="pagecdn_url">
									<input type="text" name="pagecdn[url]" id="pagecdn_url" value="<?php echo $options['url']; ?>" size="64" class="regular-text code" />
								</label>
								
								<p class="description">
									<?php _e("Enter the CDN URL without trailing", "pagecdn"); ?> <code>/</code>. <a href="https://pagecdn.com/signup" target="_blank">Signup</a> to get the URL.
								</p>
							</fieldset>
						</td>
					</tr>
					
					<tr valign="top">
						<th scope="row">
							<?php _e("Included Directories", "pagecdn"); ?>
						</th>
						<td>
							<fieldset>
								<label for="pagecdn_dirs">
									<input type="text" name="pagecdn[dirs]" id="pagecdn_dirs" value="<?php echo $options['dirs']; ?>" size="64" class="regular-text code" />
								</label>
								
								<p class="description">
									<?php _e("Assets in these directories will be pointed to the CDN URL. Enter the directories separated by", "pagecdn"); ?> <code>,</code>
								</p>
							</fieldset>
						</td>
					</tr>
					
					<tr valign="top">
						<th scope="row">
							<?php _e("Exclusions", "pagecdn"); ?>
						</th>
						<td>
							<fieldset>
								<label for="pagecdn_excludes">
									<input type="text" name="pagecdn[excludes]" id="pagecdn_excludes" value="<?php echo $options['excludes']; ?>" size="64" class="regular-text code" />
									<?php //_e("Default: <code>.php</code>", "pagecdn"); ?>
								</label>
								
								<p class="description">
									<?php _e("Enter the exclusions (directories or extensions) separated by", "pagecdn"); ?> <code>,</code>
								</p>
							</fieldset>
						</td>
					</tr>
					
					<tr valign="top">
						<th scope="row">
							<?php _e("PageCDN API Key", "pagecdn"); ?>
						</th>
						<td>
							<fieldset>
								<label for="pagecdn_api_key">
									<input type="text" name="pagecdn[pagecdn_api_key]" id="pagecdn_api_key" value="<?php echo $options['pagecdn_api_key']; ?>" size="64" class="regular-text code" />
									
									<p class="description">
										<?php _e("PageCDN API key is required to purge CDN cache on request.", "pagecdn"); ?>
									</p>
								</label>
							</fieldset>
						</td>
					</tr>
					
					<tr valign="top">
						<th scope="row">
							<?php //_e("Optimize Fonts", "pagecdn"); ?>
						</th>
						<td>
							<fieldset>
								<label for="pagecdn_fonts">
									<input type="checkbox" name="pagecdn[fonts]" id="pagecdn_fonts" value="1" <?php checked(1, $options['fonts']) ?> />
									<?php _e("Use better cacheable fonts from PageCDN.", "pagecdn"); ?>
								</label>
							</fieldset>
						</td>
					</tr>
					
					<tr valign="top">
						<th scope="row">
							<?php //_e("Reduce DNS Lookups", "pagecdn"); ?>
						</th>
						<td>
							<fieldset>
								<label for="pagecdn_replace_cdns">
									<input type="checkbox" name="pagecdn[replace_cdns]" id="pagecdn_replace_cdns" value="1" <?php checked(1, $options['replace_cdns']) ?> />
									<?php _e("Minimize DNS lookups and better utilize HTTP/2 by loading Opensource content from PageCDN only.", "pagecdn"); ?>
									<p class="description">
										<?php _e("Selecting this option will attempt to rewrite URLs of other Public CDNs.", "pagecdn"); ?>
									</p>
								</label>
							</fieldset>
						</td>
					</tr>
					
					<tr valign="top">
						<th scope="row">
							<?php //_e("Use Reusable Links", "pagecdn"); ?>
						</th>
						<td>
							<fieldset>
								<label for="pagecdn_reuse_libs">
									<input type="checkbox" name="pagecdn[reuse_libs]" id="pagecdn_reuse_libs" value="1" <?php checked(1, $options['reuse_libs']) ?> />
									<?php _e("Optimize Opensource content.", "pagecdn"); ?>
									<p class="description">
										<?php _e("This will attempt to use Public libraries for better cache reuse. (If a minified version is available, it will be used instead.)", "pagecdn"); ?>
									</p>
								</label>
							</fieldset>
						</td>
					</tr>
					
					<tr valign="top">
						<th scope="row">
							<?php //_e("Use Reusable Links", "pagecdn"); ?>
						</th>
						<td>
							<fieldset>
								<label for="pagecdn_min_files">
									<input type="checkbox" name="pagecdn[min_files]" id="pagecdn_min_files" value="1" <?php checked(1, $options['min_files']) ?> />
									<?php _e("Use minified public files where possible.", "pagecdn"); ?>
									<p class="description">
										<?php _e("This option will work if 'Optimize Opensource Content' option is enabled.", "pagecdn"); ?>
										<span style="color:red;"><?php _e("Please note that integrity hash of minified files do not match and files may fail to load if they are included with Subresource Integrity checks.", "pagecdn"); ?></span>
									</p>
								</label>
							</fieldset>
						</td>
					</tr>
					
					<?php //print_r( $options ); ?>
					
					<!--
				   <tr valign="top">
					   <th scope="row">
						   <?php _e("Relative Path", "pagecdn"); ?>
					   </th>
					   <td>
						   <fieldset>
							   <label for="pagecdn_relative">
								   <input type="checkbox" name="pagecdn[relative]" id="pagecdn_relative" value="1" <?php checked(1, $options['relative']) ?> />
								   <?php _e("Enable CDN for relative paths (default: enabled).", "pagecdn"); ?>
							   </label>
						   </fieldset>
					   </td>
				   </tr>
					-->
					<!--
				   <tr valign="top">
					   <th scope="row">
						   <?php _e("CDN HTTPS", "pagecdn"); ?>
					   </th>
					   <td>
						   <fieldset>
							   <label for="pagecdn_https">
								   <input type="checkbox" name="pagecdn[https]" id="pagecdn_https" value="1" <?php checked(1, $options['https']) ?> />
								   <?php _e("Enable CDN for HTTPS connections (default: disabled).", "pagecdn"); ?>
							   </label>
						   </fieldset>
					   </td>
				   </tr>
					-->
				   
					<!--
				   <tr valign="top">
					   <th scope="row">
						   <?php _e("KeyCDN Zone ID", "pagecdn"); ?>
					   </th>
					   <td>
						   <fieldset>
							   <label for="pagecdn_zone_id">
								   <input type="text" name="pagecdn[keycdn_zone_id]" id="pagecdn_zone_id" value="<?php echo $options['keycdn_zone_id']; ?>" size="64" class="regular-text code" />
							   <p class="description">
								   <?php _e("KeyCDN Zone ID of the zone to purge on request", "pagecdn"); ?>
							   </p>
							   </label>
						   </fieldset>
					   </td>
				   </tr>
				   -->
				   
				   
			   </table>

			   <?php submit_button() ?>
		   </form>
		</div><?php
	}
}
