	
	<?php defined( 'ABSPATH' ) OR exit; ?>
	
	<div class="wrap">
		
		<h2>
			<?php _e("Easy Speedup Settings", "pagecdn"); ?>
		</h2>
		
		<form method="post" action="options.php">
			
			<div id="poststuff" class="metabox-holder has-right-sidebar">
				
				<div class="inner-sidebar">
					
					<div class="postbox">
						
						<h3 class="hndle">Quick Start</h3>
						<div class="inside">
							<ul>
								<li> →&nbsp; <a target="_blank" href="https://pagecdn.com/pricing?src=<?=$PageCDN_theme_code?>">Pricing</a></li>
								<li> →&nbsp; <a target="_blank" href="https://pagecdn.com/docs/quick-start?src=<?=$PageCDN_theme_code?>#wordpress-integration">Setup Guide</a></li>
								<li> →&nbsp; <a target="_blank" href="https://pagecdn.com/optimizations?src=<?=$PageCDN_theme_code?>">Optimizations</a></li>
							</ul>
						</div>
						
					</div>
					
					<div class="postbox">
						
						<h3 class="hndle">PageCDN Features</h3>
						<div class="inside">
							<!--<p>PageCDN is a Private CDN that also serves Public Content for free. This mixed approach, combined with the below features, optimizes the content delivery to the extremes that was never possible before.</p>-->
							<p>PageCDN uses many advanced techniques to optimize, deliver and caching the content to make your website extremely fast.</p>
							<ul>
								<li> →&nbsp; Immutable Caching</li>
								<li> →&nbsp; Brotli:11 Compression</li>
								<li> →&nbsp; On-the-fly image optimization</li>
								<li> →&nbsp; WebP Conversion</li>
								<li> →&nbsp; On-the-fly CSS/JS minification</li>
								
								<li> →&nbsp; Cache Re-use Across Sites</li>
								<li> →&nbsp; HTTP/2 Server Push</li>
								<li> →&nbsp; Full HTTPS (HSTS)</li>
								<li> →&nbsp; Global Edge Network</li>
								<li> →&nbsp; Public + Private CDN</li>
							</ul>
						</div>
						
					</div>
					
					
					<div class="postbox">
						
						<h3 class="hndle">Get Involved</h3>
						<div class="inside">
							<p>If you are a web developer or opensource contributor, there is more you can do with PageCDN.</p>
							<ul>
								<li> →&nbsp; <a target="_blank" href="https://pagecdn.com/about/non-profits?src=<?=$PageCDN_theme_code?>">Free for open-source sites</a></li>
								<li> →&nbsp; <a target="_blank" href="https://pagecdn.com/about/free-cdn?src=<?=$PageCDN_theme_code?>">Freelancers / Bloggers</a></li>
								<!-- <li> →&nbsp; <a target="_blank" href="https://pagecdn.com/about/reselling?src=<?=$PageCDN_theme_code?>">Resell PageCDN</a></li> -->
								<li> →&nbsp; <a target="_blank" href="https://pagecdn.com/about/suggestions?src=<?=$PageCDN_theme_code?>">Suggest open-source library</a></li>
								<!-- <li> →&nbsp; <a target="_blank" href="https://pagecdn.com/about/contacting-us?src=<?=$PageCDN_theme_code?>">Submit an integration</a></li> -->
								<!-- <li> →&nbsp; <a target="_blank" href="https://pagecdn.com/about/contacting-us?src=<?=$PageCDN_theme_code?>">Commercial content</a></li> -->
							</ul>
							<p>Please feel free to <a target="_blank" href="https://pagecdn.com/about/contacting-us?src=<?=$PageCDN_theme_code?>">contact us</a> with any questions, suggestions or bugs.</p>
						</div>
						
					</div>
					
				</div>
				
				<div class="has-sidebar">
					
					<div id="post-body-content" class="has-sidebar-content">
						
						<div class="postbox">
							
							<h3 class="hndle">Premium CDN <?php if( !PageCDN_private_cdn_enabled( ) ) { ?>[ <a style="font-size:13px;font-weight:600;color:#944ABB;" href="https://pagecdn.com/pricing?src=<?=$PageCDN_theme_code?>" target="_blank">Click here to Enable</a> ]<?php } else { ?><span style="color:#3aa82a;" class="dashicons-before dashicons-yes">Enabled</span><?php } ?></h3>
							<div class="inside">
								
								<?php if( !PageCDN_private_cdn_enabled( ) ) { ?>
								<!-- <p>While Public CDN is free for open-source content on your website, Premium CDN optimizes and supercharges delivery of all your website resources. Setting up Premium CDN is really easy. <a href="https://pagecdn.com/signup" target="_blank">Create an account</a> and copy the API Key from <a href="https://pagecdn.com/account/settings" target="_blank">CDN Settings</a> to the below box and hit 'Save Changes'. Plugin will do the initial setup for you.</p> -->
								<p>Enable Premium CDN to <b>go beyond free features</b> and perform very aggressive cloud based optimizations on your site. Setup is easy. Just <a href="https://pagecdn.com/pricing?src=<?=$PageCDN_theme_code?>" target="_blank">activate your subscription</a> and copy the API Key from <a href="https://pagecdn.com/account/settings?src=<?=$PageCDN_theme_code?>" target="_blank">CDN Settings</a> to the below box.</p>
								<?php } ?>
								
								<table class="form-table" style="clear:none;">
									
									<?php if( PageCDN_private_cdn_enabled( ) ) { ?>
									
									<tr valign="top">
										<td style="">
											<?php _e("PageCDN API Key", "pagecdn"); ?>
										</td>
										<td style="">
											<label for="pagecdn_api_key">
												<input type="password" name="pagecdn[pagecdn_api_key]" id="pagecdn_api_key" value="<?php echo PageCDN_options('pagecdn_api_key'); ?>" size="64" class="regular-text code" />
											</label>
										</td>
									</tr>
									
									<tr valign="top">
										<td style="">
											<?php _e("CDN URL", "pagecdn"); ?>
										</td>
										<td style="">
											<strong><?php echo PageCDN_options('url'); ?></strong> - <small style="opacity:0.7;">( <a href="<?php echo str_replace('.io','.com',PageCDN_options('url')); ?>" target="_blank">View Files</a> on PageCDN )</small>
											<input type="hidden" name="pagecdn[url]" value="<?php echo PageCDN_options('url'); ?>" />
										</td>
									</tr>
									<!--
									<tr>
										<td style="padding-top:0px;"></td>
										<td style="padding-top:0px;">
											<p><small>
												<?php _e("If left empty, plugin will automatically fill this field.", "pagecdn"); ?>
											</small></p>
										</td>
									</tr>
									-->
									
									<tr valign="top">
										<td style="padding-bottom:0px;">
											<?php _e("Included Directories", "pagecdn"); ?>
										</td>
										<td style="padding-bottom:0px;">
											<label for="pagecdn_dirs">
												<input type="text" name="pagecdn[dirs]" id="pagecdn_dirs" value="<?php echo PageCDN_options('dirs'); ?>" size="64" class="regular-text code" />
											</label>
										</td>
									</tr>
									<tr>
										<td style="padding-top:0px;"></td>
										<td style="padding-top:0px;">
											<p><small>
												<?php _e("Assets in this list of <code>,</code> separated directories will be pointed to the CDN URL.", "pagecdn"); ?>
											</small></p>
										</td>
									</tr>
									
									
									<tr valign="top">
										<td style="padding-bottom:0px;">
											<?php _e("Exclusions", "pagecdn"); ?>
										</td>
										<td style="padding-bottom:0px;">
											<label for="pagecdn_excludes">
												<input type="text" name="pagecdn[excludes]" id="pagecdn_excludes" value="<?php echo PageCDN_options('excludes'); ?>" size="64" class="regular-text code" />
											</label>
										</td>
									</tr>
									<tr>
										<td style="padding-top:0px;"></td>
										<td style="padding-top:0px;">
											<p><small>
												<?php _e("Enter the exclusions (directories or extensions) separated by", "pagecdn"); ?> <code>,</code>.
											</small></p>
										</td>
									</tr>
									
									<?php } else { ?>
									
									<tr valign="top">
										<td style="padding-bottom:0px;">
											<?php _e("PageCDN API Key", "pagecdn"); ?>
										</td>
										<td style="padding-bottom:0px;">
											<label for="pagecdn_api_key">
												<input type="password" name="pagecdn[pagecdn_api_key]" id="pagecdn_api_key" value="<?php echo PageCDN_options('pagecdn_api_key'); ?>" size="64" class="regular-text code" placeholder="Paste API Key here" />
											</label>
										</td>
									</tr>
									<tr>
										<td style="padding-top:0px;"></td>
										<td style="padding-top:0px;">
											<p><small>
												Get API Key to enable advanced optimizations. <a href="https://pagecdn.com/pricing?src=<?=$PageCDN_theme_code?>" target="_blank">See plans</a>.
											</small></p>
										</td>
									</tr>
									
									<input type="hidden" name="pagecdn[dirs]" value="<?php echo PageCDN_options('dirs'); ?>" />
									<input type="hidden" name="pagecdn[excludes]" value="<?php echo PageCDN_options('excludes'); ?>" />
									
									<?php } ?>
									
								</table>
								
							</div>
							
						</div>
						
						<?php if( !PageCDN_private_cdn_enabled( ) ) { ?>
						<?php submit_button() ?>
						<?php } ?>
						
						<div class="postbox">
							
							<h3 class="hndle">Content Optimizations &nbsp; <?php if( !PageCDN_private_cdn_enabled( ) ) { ?><span style="color:#3aa82a;" class="dashicons-before dashicons-warning">Free CDN Enabled</span><?php } else { ?><span style="color:#3aa82a;" class="dashicons-before dashicons-yes">Enabled (Configure Below)</span><?php } ?></h3>
							<div class="inside">
								<p>These configuration options help reduce the content size by applying different techniques so that even if the user is on a slow connection, your website still loads faster. All these optimizations are performed at PageCDN servers before the content is delivered, so your server does not need to support these features.</p>
								
								<table class="form-table" style="clear:none;margin-bottom:20px;margin-top:20px;">
									
									<tr valign="top">
										<td style="padding-bottom:0px;width:30px;"></td>
										<td style="padding-bottom:0px;">
											<label for="pagecdn_optimize_images">
												<input name="pagecdn[optimize_images]" type="checkbox" id="pagecdn_optimize_images" value="1" <?=$disabled_if_not_premium?> <?php checked(1, PageCDN_options('optimize_images')) ?> >
												Optimize images on-the-fly
											</label>
										</td>
									</tr>
									
									<tr valign="top">
										<td style="padding-bottom:0px;"></td>
										<td style="padding-bottom:0px;">
											<label for="pagecdn_min_files">
												<input name="pagecdn[min_files]" type="checkbox" id="pagecdn_min_files" value="1" <?=$disabled_if_not_premium?> <?php checked(1, PageCDN_options('min_files')) ?> >
												Minify Javascript &amp; CSS
											</label>
										</td>
									</tr>
									
									<tr valign="top">
										<td style="padding-bottom:0px;"></td>
										<td style="padding-bottom:0px;">
											<label for="pagecdn_enable_precompression">
												<input name="pagecdn[enable_precompression]" type="checkbox" id="pagecdn_enable_precompression" <?=$checked_if_premium?> disabled >
												Enable pre-compression.
											</label>
											<label for="pagecdn_compression_level">
												 Compression level: 
												<select name="pagecdn[compression_level]" id="pagecdn_compression_level">
													<?php if( !PageCDN_private_cdn_enabled( ) ) { ?>
													<option value="" selected>None Selected</option>
													<?php } ?>
													
													<option value="moderate" <?=$disabled_if_not_premium?> <?php selected('moderate', PageCDN_options('compression_level')) ?> >Moderate</option>
													<option value="high" <?=$disabled_if_not_premium?> <?php selected('high', PageCDN_options('compression_level')) ?> >High</option>
													<option value="extreme" <?=$disabled_if_not_premium?> <?php selected('extreme', PageCDN_options('compression_level')) ?> >Extreme</option>
												</select>
											</label>
										</td>
									</tr>
									<tr>
										<td style="padding-top:0px;"></td>
										<td style="padding-top:0px;">
											<p><small style="opacity:0.7;">
												'Moderate' uses brotli-q5 that spends less time on compression, and generates relatively large compressed files. 'Extreme' 
												uses brotli-q11 that generates small files but takes longer for the first run. Pre-compression is performed at PageCDN 
												servers.
											</small></p>
										</td>
									</tr>
									
								</table>
								
							</div>
							
						</div>
						
						
						<div class="postbox">
							
							<h3 class="hndle">Delivery Optimizations &nbsp; <?php if( !PageCDN_private_cdn_enabled( ) ) { ?><span style="color:#3aa82a;" class="dashicons-before dashicons-warning">Free CDN Enabled</span><?php } else { ?><span style="color:#3aa82a;" class="dashicons-before dashicons-yes">Enabled (Configure Below)</span><?php } ?></h3>
							<div class="inside">
								
								<p>Optimizing just the content is not enough in most cases. You need to optimize the content delivery process too. Fortunately, PageCDN has a lot to offer and using an edge network of servers is just one of them.</p>
								<table class="form-table" style="clear:none;margin-bottom:20px;">
									
									<tr valign="top">
										<td style="padding-bottom:0px;"></td>
										<td style="padding-bottom:0px;">
											<label for="pagecdn_replace_cdns">
												<input name="pagecdn[replace_cdns]" type="checkbox" id="pagecdn_replace_cdns" value="1" <?php checked(1, PageCDN_options('replace_cdns')) ?> >
												Reduce DNS lookups - <small style="opacity:0.7;">Uses one public CDN instead of many CDNs that themes or plugins may use</small>
											</label>
										</td>
									</tr>
									
									<tr valign="top">
										<td style="padding-bottom:0px;width:30px;"></td>
										<td style="padding-bottom:0px;">
											<label for="pagecdn_use_cdn">
												<input name="pagecdn[use_cdn]" type="checkbox" id="pagecdn_use_cdn" <?=$checked_if_premium?> disabled >
												Use a Content Delivery Network
											</label>
										</td>
									</tr>
									
									<tr valign="top">
										<td style="padding-bottom:0px;"></td>
										<td style="padding-bottom:0px;">
											<label for="pagecdn_enable_http2">
												<input name="pagecdn[enable_http2]" type="checkbox" id="pagecdn_enable_http2" <?=$checked_if_premium?> disabled >
												Enable HTTP/2 - <small style="opacity:0.7;">Reduces overhead, loads resources in parallel in browser, and more</small>
											</label>
										</td>
									</tr>
									
									<tr valign="top">
										<td style="padding-bottom:0px;"></td>
										<td style="padding-bottom:0px;">
											<label for="pagecdn_avoid_redirects">
												<input name="pagecdn[avoid_redirects]" type="checkbox" id="pagecdn_avoid_redirects" <?=$checked_if_premium?> disabled >
												Avoid redirects - <small style="opacity:0.7;">Resolves redirects at CDN level</small>
											</label>
										</td>
									</tr>
									
									<tr valign="top">
										<td style="padding-bottom:0px;"></td>
										<td style="padding-bottom:0px;">
											<label for="pagecdn_http2_server_push">
												<input name="pagecdn[http2_server_push]" type="checkbox" id="pagecdn_http2_server_push" <?php checked(1, PageCDN_options('http2_server_push')) ?> disabled >
												HTTP/2 Server Push - <small style="opacity:0.7;"><?php if( PageCDN_private_cdn_enabled( ) ) { ?>You can <a href="<?php echo str_replace('.io','.com',PageCDN_options('url')); ?>/configuration" target="_blank">setup this feature here</a>. <?php } ?>This feature sends files to the browser before it even asks for them.</small>
											</label>
										</td>
									</tr>
									
									<tr valign="top">
										<td style="padding-bottom:0px;"></td>
										<td style="padding-bottom:0px;">
											<label for="pagecdn_preconnect_hosts">
												<input name="pagecdn[preconnect_hosts]" type="checkbox" id="pagecdn_preconnect_hosts" value="1" <?php checked(1, PageCDN_options('preconnect_hosts')) ?> >
												Preconnect hosts
											</label>
											<br />
											<textarea style="margin-top:10px;margin-left:25px;" name="pagecdn[preconnect_hosts_list]" rows="5" cols="60" id="pagecdn_preconnect_hosts_list" class="code"><?=PageCDN_options('preconnect_hosts_list')?></textarea>
										</td>
									</tr>
									
									<?php if( PageCDN_private_cdn_enabled( ) ) { ?>
									<tr valign="top">
										<td style="padding-bottom:0px;"></td>
										<td style="padding-bottom:0px;">
											<label for="pagecdn_update_css_paths">
												<input name="pagecdn[update_css_paths]" type="checkbox" id="pagecdn_update_css_paths" value="1" <?php checked(1, PageCDN_options('update_css_paths')) ?> >
												Rewrite URLs in CSS files - <small style="opacity:0.7;">Enable this to fix missing CSS background images, or broken CSS imports.</small>
											</label>
										</td>
									</tr>
									<?php } ?>
									
								</table>
								
							</div>
							
						</div>
						
						
						
						<div class="postbox">
							
							<h3 class="hndle">Cache Optimizations &nbsp; <?php if( !PageCDN_private_cdn_enabled( ) ) { ?><span style="color:#3aa82a;" class="dashicons-before dashicons-warning">Free CDN Enabled</span><?php } else { ?><span style="color:#3aa82a;" class="dashicons-before dashicons-yes">Enabled (Configure Below)</span><?php } ?></h3>
							<div class="inside">
								
								<p>Once the resources are delivered to the browser, you have to make sure the resources stay there as long as possible. PageCDN even lets you use resources cached in browser for 3rd party websites. </p>
								<table class="form-table" style="clear:none;margin-bottom:20px;">
									
									<tr valign="top">
										<td style="padding-bottom:0px;width:30px;"></td>
										<td style="padding-bottom:0px;">
											<label for="pagecdn_fonts">
												<input name="pagecdn[fonts]" type="checkbox" id="pagecdn_fonts" value="1" <?php checked(1, PageCDN_options('fonts')) ?>>
												Use cache friendly fonts - <small style="opacity:0.7;">Fixes "Leverage Browser Caching" issue for fonts.</small>
											</label>
										</td>
									</tr>
									
									<tr valign="top">
										<td style="padding-bottom:0px;"></td>
										<td style="padding-bottom:0px;">
											<label for="pagecdn_reuse_libs">
												<input name="pagecdn[reuse_libs]" type="checkbox" id="pagecdn_reuse_libs" value="1" <?php checked(1, PageCDN_options('reuse_libs')) ?> >
												Use Public CDN - <small style="opacity:0.7;">Reduces your bandwidth bill and reuses files cached in browser by other websites to speedup your site for first time visitors. PageCDN's <a href="https://pagecdn.com/public-cdn?src=<?=$PageCDN_theme_code?>" target="_blank">Public CDN</a> speeds up 100s of open-source libraries, 2,000+ WordPress themes and 10,000+ Plugins.</small>
											</label>
										</td>
									</tr>
									
									<tr valign="top">
										<td style="padding-bottom:0px;"></td>
										<td style="padding-bottom:0px;">
											<label for="pagecdn_cache_control">
												<input name="pagecdn[cache_control]" type="checkbox" id="pagecdn_cache_control" value="1" <?=$disabled_if_not_premium?> <?php checked(1, PageCDN_options('cache_control')) ?> >
												Leverage browser caching with 
												<!-- Set <code>Expires</code> and <code>Cache-Control</code> headers with -->
											</label>
											<label for="pagecdn_http_cache_ttl">
												 <select name="pagecdn[http_cache_ttl]" id="pagecdn_http_cache_ttl">
													<?php if( !PageCDN_private_cdn_enabled( ) ) { ?>
													<option value="" selected>None Selected</option>
													<?php } ?>
													
													<option value="2592000" <?=$disabled_if_not_premium?> <?php selected('2592000', PageCDN_options('http_cache_ttl')) ?> >30 days expiry</option>
													<option value="15552000" <?=$disabled_if_not_premium?> <?php selected('15552000', PageCDN_options('http_cache_ttl')) ?> >6 months expiry</option>
													<option value="31536000" <?=$disabled_if_not_premium?> <?php selected('31536000', PageCDN_options('http_cache_ttl')) ?> >1 Year expiry</option>
													<option value="-1" <?=$disabled_if_not_premium?> <?php selected('-1', PageCDN_options('http_cache_ttl')) ?> >10+ year expiry (Immutable)</option>
													
													<?php if( !in_array( PageCDN_options('http_cache_ttl') , [ 0 , -1 , 2592000 , 15552000 , 31536000 ] ) ) { ?>
													<option value="<?=PageCDN_options('http_cache_ttl')?>" <?=$disabled_if_not_premium?> selected="selected" >Custom expiry</option>
													<?php } ?>
												</select>
												
												<br />
												
												<small style="opacity:0.7;">This sets <code>Expires</code> and <code>Cache-Control</code> HTTP headers for static files to tell the
													browser to keep a copy of static files for specified period.</small>
												
												
												
											</label>
										</td>
									</tr>
									
									<tr valign="top">
										<td style="padding-bottom:0px;"></td>
										<td style="padding-bottom:0px;">
											<label for="pagecdn_remove_querystring">
												<input name="pagecdn[remove_querystring]" type="checkbox" id="pagecdn_remove_querystring" <?=$checked_if_premium?> disabled>
												Remove query string from static resources - <small style="opacity:0.7;">Caching works better with consistent cruftless URLs.</small>
											</label>
										</td>
									</tr>
									
									<tr valign="top">
										<td style="padding-bottom:0px;"></td>
										<td style="padding-bottom:0px;">
											<label for="pagecdn_cookie_free_domain">
												<input name="pagecdn[cookie_free_domain]" type="checkbox" id="pagecdn_cookie_free_domain" <?=$checked_if_premium?> disabled>
												Use cookie-free domain for static resources - <small style="opacity:0.7;">Reduces payload and improves caching.</small>
											</label>
										</td>
									</tr>
									
									<tr valign="top">
										<td style="padding-bottom:0px;"></td>
										<td style="padding-bottom:0px;">
											<label for="pagecdn_use_https_only">
												<input name="pagecdn[use_https_only]" type="checkbox" id="pagecdn_use_https_only" <?=$checked_if_premium?> disabled>
												Use HTTPS-only URLs for static resources - <small style="opacity:0.7;">Using HTTP and HTTPS interchangeably for the same resource increases cache misses. PageCDN is on <a href="https://hstspreload.org/" target="_blank">HSTS preload list</a> of all top browsers, which means your files always load over HTTPS whether or not you explicitly ask the browser to do so.</small>
											</label>
										</td>
									</tr>
									
								</table>
								
							</div>
							
						</div>
						
						<div class="postbox">
							
							<h3 class="hndle">Further Optimizations</h3>
							
							<div class="inside">
								
								<p>PageCDN Plugin is compatible with <u><strong>Autoptimize</strong></u> and <u><strong>WP Super Cache</strong></u>. If you face difficulty in setting up this plugin, you can <a href="https://pagecdn.com/about/contacting-us?src=<?=$PageCDN_theme_code?>" target="_blank">get someone from our side</a> to do it for you.</p>
								
							</div>
							
						</div>
						
					</div>
					
				</div>
				
			</div>
			
			<?php settings_fields('pagecdn') ?>
			<?php submit_button() ?>
			
		</form>
		
	</div>