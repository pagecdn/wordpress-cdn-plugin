	
	<?php defined( 'ABSPATH' ) OR exit; ?>
	
	
	<div class="wrap">
		
		<h2>
			<?php _e("PageCDN Settings", "pagecdn"); ?>
		</h2>
		
		<form method="post" action="options.php">
			
			<div id="poststuff" class="metabox-holder has-right-sidebar">
				
				<div class="inner-sidebar">
					
					<div class="postbox">
						
						<h3 class="hndle">Setup Guide</h3>
						<div class="inside">
							<ul>
								<li> →&nbsp; <a target="_blank" href="https://docs.pagecdn.com/quick-start#wordpress-integration">How it Works</a></li>
								<li> →&nbsp; <a target="_blank" href="https://docs.pagecdn.com/quick-start#wordpress-integration">Guide for Initial Setup</a></li>
								<li> →&nbsp; <a target="_blank" href="https://docs.pagecdn.com/quick-start#optimizing-caching-and-performance">Optimize caching &amp; performance</a></li>
								<li> →&nbsp; <a target="_blank" href="https://docs.pagecdn.com/quick-start#working-with-easy-fonts">Using Easy Fonts</a></li>
							</ul>
						</div>
						
					</div>
					
					<div class="postbox">
						
						<h3 class="hndle">PageCDN Features</h3>
						<div class="inside">
							<p>PageCDN is a Private CDN that also serves Public Content for free. This mixed approach, combined with the below features, optimizes the content delivery to the extremes that was never possible before.</p>
							<ul>
								<li> →&nbsp; Immutable Caching</li>
								<li> →&nbsp; Brotli-11 Compression</li>
								<li> →&nbsp; Cache Re-use Across Sites</li>
								<li> →&nbsp; HTTP/2 Server Push</li>
								<li> →&nbsp; Full HTTPS (HSTS)</li>
							</ul>
						</div>
						
					</div>
					
					
					<div class="postbox">
						
						<h3 class="hndle">Get Involved</h3>
						<div class="inside">
							<p>If you are a web developer or opensource contributor, there is more you can do with PageCDN.</p>
							<ul>
								<li> →&nbsp; <a target="_blank" href="https://pagecdn.com/about/non-profits">Opensource / Non-Profits</a></li>
								<li> →&nbsp; <a target="_blank" href="https://pagecdn.com/about/reselling">Resell PageCDN</a></li>
								<li> →&nbsp; <a target="_blank" href="https://pagecdn.com/about/free-cdn">Freelancers / Bloggers</a></li>
								<li> →&nbsp; <a target="_blank" href="https://pagecdn.com/about/suggestions">Suggest a library</a></li>
								<li> →&nbsp; <a target="_blank" href="https://pagecdn.com/about/contacting-us">Submit an integration</a></li>
								<li> →&nbsp; <a target="_blank" href="https://pagecdn.com/about/contacting-us">Commercial content</a></li>
							</ul>
							<p>Please feel free to <a target="_blank" href="https://pagecdn.com/about/contacting-us">contact us</a> with any comments, questions, suggestions and bugs.</p>
						</div>
						
					</div>
					
				</div>
				
				<div class="has-sidebar">
					
					<div id="post-body-content" class="has-sidebar-content">
						
						<div class="postbox">
							<h3 class="hndle"><span>Free CDN</span></h3>
							
							<div class="inside">
								<!--<p style="margin-bottom:30px;">Free CDN feature looks up JS and CSS file and Google Fonts in the Public CDN provided by PageCDN and links to corresponding resources. This optimizes page loads to the next level since the resources served by PageCDN are compressed using <strong>brotli-11 compression</strong> and there are several more optimizations applied in the process of delivery.</p>-->
								
								<p style="margin-bottom:30px;">Free CDN speeds up your website by optimizing delivery of <a href="https://pagecdn.com/lib" target="_blank">opensource libraries</a>, <a href="https://pagecdn.com/theme?s=wordpress" target="_blank">2000+ wordpress themes</a>, plugins and <a href="https://pagecdn.com/lib/easyfonts" target="_blank">fonts</a>. Optimizations include brotli-11 compression, better HTTP/2 multiplexing, immutable caching, HTTP cache reuse, global edge network and <a href="https://pagecdn.com/features" target="_blank">more</a>.</p>
								
								<ul>
									<li style="margin-bottom:10px;margin-left:20px;">
										<input type="checkbox" name="pagecdn[fonts]" id="pagecdn_fonts" value="1" <?php checked(1, $options['fonts']) ?> >
										<label style="vertical-align: top;font-size: 14px;" for="pagecdn_fonts"><?php _e("Fix leverage browser caching issue for Google Fonts.", "pagecdn"); ?></label> <a style="text-decoration:none;" href="javascript:void(0);" onclick="jQuery(this).parents('li').find('.description').removeClass('hidden');jQuery(this).hide();"><span style="font-size:8px;">▼</span> More info</a><br>
										<p class="description hidden" style="margin-bottom:20px;margin-left:24px;">
											<?php _e("This will replace Google Fonts with <a href=\"https://pagecdn.com/lib/easyfonts\" target=\"_blank\">Easy Fonts</a> that are more cacheable and developer friendly.", "pagecdn"); ?>
										</p>
									</li>
									
									<li style="margin-bottom:10px;margin-left:20px;">
										<input type="checkbox" name="pagecdn[replace_cdns]" id="pagecdn_replace_cdns" value="1" <?php checked(1, $options['replace_cdns']) ?> >
										<label style="vertical-align: top;font-size: 14px;" for="pagecdn_replace_cdns"><?php _e("Load opensource content from PageCDN only.", "pagecdn"); ?></label> <a style="text-decoration:none;" href="javascript:void(0);" onclick="jQuery(this).parents('li').find('.description').removeClass('hidden');jQuery(this).hide();"><span style="font-size:8px;">▼</span> More info</a><br>
										<p class="description hidden" style="margin-bottom:20px;margin-left:24px;">
											<?php _e("Themes and plugins usually load resources from many different Opensource CDNs. This is sub-optimal as it not only requires several DNS lookups, but also under-utilizes HTTP/2 multiplexing. Enabling this feature can in most cases improve user experience on mobile devices by attempting to rewrite URLs of other Opensource CDNs for resources that are available on PageCDN, so that not only <u>DNS lookups can be saved</u>, but also the content can be compressed using <u>brotli-11 compression</u> and <u>multiplexed using HTTP/2</u>.", "pagecdn"); ?>
										</p>
									</li>
									
									<li style="margin-bottom:10px;margin-left:20px;">
										<input type="checkbox" name="pagecdn[reuse_libs]" id="pagecdn_reuse_libs" value="1" <?php checked(1, $options['reuse_libs']) ?> >
										<label style="vertical-align: top;font-size: 14px;" for="pagecdn_reuse_libs"><?php _e("Optimize Opensource content.", "pagecdn"); ?></label> <a style="text-decoration:none;" href="javascript:void(0);" onclick="jQuery(this).parents('li').find('.description').removeClass('hidden');jQuery(this).hide();"><span style="font-size:8px;">▼</span> More info</a><br>
										<p class="description hidden" style="margin-bottom:20px;margin-left:24px;">
											<?php _e("Same as the above option, but works for resources not yet on any Opensource CDN. Enabling this feature will attempt to load opensource resources from PageCDN. This provides many benefits including <u>better cache reuse</u>, <u>HTTP/2</u>, <u>brotli-11 compression</u>, etc. You will enjoy even faster page loads if you are using one of the <a href=\"https://pagecdn.com/theme?s=wordpress\" target=\"_blank\">2000+ Wordpress themes</a> already available on PageCDN for free.", "pagecdn"); ?>
										</p>
									</li>
									
									<li style="margin-bottom:10px;margin-left:20px;">
										<input type="checkbox" name="pagecdn[min_files]" id="pagecdn_min_files" value="1" <?php checked(1, $options['min_files']) ?> >
										<label style="vertical-align: top;font-size: 14px;" for="pagecdn_min_files"><?php _e("Use minified public files where possible.", "pagecdn"); ?></label> <a style="text-decoration:none;" href="javascript:void(0);" onclick="jQuery(this).parents('li').find('.description').removeClass('hidden');jQuery(this).hide();"><span style="font-size:8px;">▼</span> More info</a><br>
										<p class="description hidden" style="margin-bottom:20px;margin-left:24px;">
										<?php _e("This option works in combination with the 'Optimize Opensource Content' option above. Enabling this option will attempt to replace opensource files with minified files if the original files are not already minified and if a minified variant of that original file is available on PageCDN.", "pagecdn"); ?>
											<span style="color:red;"><?php _e("Please note that integrity hash of minified files do not match with that of original files. Files may fail to load for this reason. If so, you may need to disable this option.", "pagecdn"); ?></span>
										</p>
									</li>
									
								</ul>
							</div>
						</div>
						
						
						<div class="postbox">
							
							<h3 class="hndle">Premium CDN</h3>
							<div class="inside">
								<!-- <p>With <a href="https://pagecdn.com/" target="_blank">PageCDN</a>, you can also supercharge private content delivery using our premium CDN for private files. Setting up premium CDN is really easy. Just copy the API Key from <a href="https://pagecdn.com/account/settings" target="_blank">CDN Settings</a> to the below box and hit 'Save Changes'. Everything else should configure itself automatically.</p> -->
								<p>Premium CDN supercharges delivery of your website images and other website or commercial content. Setting up premium CDN is really easy. Just copy the API Key from <a href="https://pagecdn.com/account/settings" target="_blank">CDN Settings</a> to the below box and hit 'Save Changes'. Plugin will do everything else for you.</p>
								
								<table class="form-table" style="clear:none;">
									
									<tr valign="top">
										<td style="padding-bottom:0px;">
											<?php _e("PageCDN API Key", "pagecdn"); ?>
										</td>
										<td style="padding-bottom:0px;">
											<label for="pagecdn_api_key">
												<input type="password" name="pagecdn[pagecdn_api_key]" id="pagecdn_api_key" value="<?php echo $options['pagecdn_api_key']; ?>" size="64" class="regular-text code" />
											</label>
										</td>
									</tr>
									<tr>
										<td style="padding-top:0px;"></td>
										<td style="padding-top:0px;">
											<p><small>
												<?php _e("PageCDN API key is required for several CDN operations.", "pagecdn"); ?> <a href="https://pagecdn.com/signup" target="_blank">Signup</a> to get the API Key.
											</small></p>
										</td>
									</tr>
									
									
									<tr valign="top">
										<td style="padding-bottom:0px;">
											<?php _e("CDN URL", "pagecdn"); ?>
										</td>
										<td style="padding-bottom:0px;">
											<label for="pagecdn_url">
												<input type="text" name="pagecdn[url]" id="pagecdn_url" value="<?php echo $options['url']; ?>" size="64" class="regular-text code" placeholder="(Optional)" />
											</label>
										</td>
									</tr>
									<tr>
										<td style="padding-top:0px;"></td>
										<td style="padding-top:0px;">
											<p><small>
												<?php _e("If left empty, plugin will automatically fill this field.", "pagecdn"); ?>
											</small></p>
										</td>
									</tr>
									
									
									<tr valign="top">
										<td style="padding-bottom:0px;">
											<?php _e("Included Directories", "pagecdn"); ?>
										</td>
										<td style="padding-bottom:0px;">
											<label for="pagecdn_dirs">
												<input type="text" name="pagecdn[dirs]" id="pagecdn_dirs" value="<?php echo $options['dirs']; ?>" size="64" class="regular-text code" />
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
												<input type="text" name="pagecdn[excludes]" id="pagecdn_excludes" value="<?php echo $options['excludes']; ?>" size="64" class="regular-text code" />
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
									
								</table>
								
							</div>
							
						</div>
						
						
						
						<div class="postbox">
							
							<h3 class="hndle">Further Optimization</h3>
							
							<div class="inside">
								
								<p>PageCDN Plugin is compatible with <u><strong>Autoptimize</strong></u> and <u><strong>WP Super Cache</strong></u>. If you face difficulty in setting up this plugin, you can <a href="https://pagecdn.com/about/contacting-us" target="_blank">get someone from our side</a> to do it for you.</p>
								
							</div>
							
						</div>
						
					</div>
					
				</div>
				
			</div>
			
			<?php settings_fields('pagecdn') ?>
			<?php submit_button() ?>
			
		</form>
		
	</div>