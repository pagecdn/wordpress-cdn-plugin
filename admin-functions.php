<?php
	
	function PageCDN_is_backend( )
	{
		static $is_backend = null;
		
		if( $is_backend === null )
		{
			$is_backend	= is_admin( );
			
			if( !$is_backend )
			{
				$is_backend	= ( PHP_SAPI === 'cli' ) || ( defined( 'DOING_CRON' ) && DOING_CRON ) || ( defined( 'WP_CLI' ) && WP_CLI );
				
				if( !$is_backend )
				{
					$script		= isset( $_SERVER['PHP_SELF'] ) ? basename( $_SERVER['PHP_SELF'] ) : '';
					
					$is_backend	= in_array( $script, [ 'xmlrpc.php', 'wp-cron.php' ] );
				}
			}
		}
		
		return $is_backend;
	}
	
	function PageCDN_add_action_link( $data )
	{
		if( !current_user_can( 'manage_options' ) )
		{
			return $data;
		}
		
		$settings_url	= add_query_arg( [ 'page' => 'pagecdn' ] , admin_url('options-general.php') );
		
		$settings_text	= __("Settings");
		
		return array_merge( [ "<a href=\"{$settings_url}\">{$settings_text}</a>" , "<a href=\"https://pagecdn.com/signup\" target=\"_blank\" style=\"color:green;\" class=\"dashicons-before dashicons-dashboard\"> Premium</a>" ] , $data );
	}
	
	function PageCDN_compat_check( )
	{
		global $wp_version;
		
		if( version_compare( $wp_version , PAGECDN_MIN_WP , '<' ) )
		{
			show_message( '<div class="notice notice-error "><p>'. __("<strong>".PAGECDN_FULL_NAME."</strong> plugin works with WordPress ". PAGECDN_MIN_WP .". Please disable the plugin or upgrade your WordPress installation (recommended).", "pagecdn") .'</p></div>' );
		}
		
		if( !PageCDN_private_cdn_enabled( ) )
		{
			show_message( '<div class="notice notice-warning is-dismissible"><p>Your website is not fully optimized. Please activate <a href="'.add_query_arg(['page'=>'pagecdn'],admin_url('options-general.php')).'">Premium CDN</a> to get best performance for your website.</p></div>' );
		}
	}
	
	function PageCDN_admin_init( )
	{
		load_plugin_textdomain( 'pagecdn' , false , 'pagecdn/lang' );
		
		register_setting( 'pagecdn' , 'pagecdn' , 'PageCDN_settings_validate' );
	}
	
	function PageCDN_admin_links( $wp_admin_bar )
	{
		if( !PageCDN_private_cdn_enabled( ) || !is_admin_bar_showing( ) || !apply_filters( 'user_can_clear_cache' , current_user_can( 'manage_options' ) ) )
		{
			return;
		}
		
		$current_url	= ( isset( $_SERVER['HTTPS'] ) ? 'https' : 'http' ) . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		
		$goto_url		= get_admin_url( );
		
		if( stristr( $current_url , $goto_url ) )
		{
			$goto_url	= $current_url;
		}
		
		// add admin purge link
		$wp_admin_bar->add_menu(
			[
				'id'	  => 'purge-pagecdn',
				'href'   => wp_nonce_url( add_query_arg('pagecdn_action', 'purge', $goto_url ) , '_cdn__purge_nonce' ),
				'parent' => 'top-secondary',
				'parent' => false,
				'title'	 => '<span class="ab-item">'.esc_html__('Purge CDN', 'pagecdn').'</span>',
				'meta'   => ['title' => esc_html__('Purge CDN', 'pagecdn')],
			]
		);
	}
	
	
	function PageCDN_purge( $data = null )
	{
		if( !( isset( $_GET['pagecdn_action'] ) && $_GET['pagecdn_action'] === 'purge' ) )
		{
			return;
		}
		
		if( !PageCDN_private_cdn_enabled( ) )
		{
			return;
		}
		
		if( !( isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'] , '_cdn__purge_nonce' ) ) )
		{
			return;
		}
		
		if( !is_admin_bar_showing( ) )
		{
			return;
		}
		
		// load if network
		if( !function_exists( 'is_plugin_active_for_network' ) )
		{
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		
		
		$repo		= rawurlencode( ltrim( parse_url( PageCDN_options( 'url' ) , PHP_URL_PATH ) , '/' ) );
		
		$apikey		= rawurlencode( PageCDN_options( 'pagecdn_api_key' ) );
		
		file_put_contents( PAGECDN_CACHE , json_encode( [] ) );
		
		$response	= wp_remote_get( "https://pagecdn.com/api/v2/private/repo/delete-files?repo={$repo}&apikey={$apikey}" , [ 'timeout' => 30 ] );
		
		if( is_wp_error( $response ) )
		{
			printf( '<div class="notice notice-error is-dismissible"><p>%s</p></div>',
					esc_html__('Error connecting to PageCDN API - '. $response->get_error_message(), 'pagecdn')
					);
			
			return;
		}
		
		#	check HTTP response
		if( is_array( $response ) )
		{
			$rc		= ( int ) wp_remote_retrieve_response_code( $response );
			
			if( $rc == 200 )
			{
				#	Request successfully completed. Check API response.
				
				$json		= json_decode( $response['body'] , true );
				
				$api_code	= ( int ) $json['status'];
				
				$api_error	= $json['message'];
				
				$api_info	= $json['details'];
				
				if( $api_code == 200 )
				{
					printf( '<div class="notice notice-success is-dismissible"><p class="dashicons-before dashicons-trash">%s</p></div>',
							esc_html__('PageCDN cache purged. Changes may take 15 seconds or lesser to take effect.')
							);
				}
				else
				{
					printf( '<div class="notice notice-error is-dismissible"><p>%s</p></div>',
							esc_html__( 'PageCDN Error - ' . $api_error . ': '. $api_info . ' Code: '. $api_code , 'pagecdn')
						);
				}
			}
		}
	}
	
	
	#	Settings
	#-------------------------------------------
	
	function PageCDN_settings_validate( $data )
	{
		$data['relative']			= isset( $data['relative'] )		? $data['relative']			: 1;
		$data['https']				= isset( $data['https'] )			? $data['https']			: 1;
		$data['fonts']				= isset( $data['fonts'] )			? $data['fonts']			: 0;
		$data['replace_cdns']		= isset( $data['replace_cdns'] )	? $data['replace_cdns']		: 0;
		$data['reuse_libs']			= isset( $data['reuse_libs'] )		? $data['reuse_libs']		: 0;
		$data['min_files']			= isset( $data['min_files'] )		? $data['min_files']		: 0;
		$data['pagecdn_api_key']	= isset( $data['pagecdn_api_key'] )	? $data['pagecdn_api_key']	: '';
		$data['url']				= isset( $data['url'] )				? $data['url']				: '';
		
		$data['pagecdn_api_key']	= trim( $data['pagecdn_api_key'] );
		$data['url']				= trim( $data['url'] );
		
		
		if( ( $data['url'] == '' ) && strlen( $data['pagecdn_api_key'] ) )
		{
			$postdata					= [];
			$postdata['apikey']			= $data['pagecdn_api_key'];
			$postdata['repo_name']		= get_bloginfo( 'name' );
			$postdata['origin_url']		= home_url();
			$postdata['privacy']		= 'private';
			
			$response	= wp_remote_post( "https://pagecdn.com/api/v2/private/repo/create" , [ 'timeout' => 20 , 'body' => $postdata ] );
			
			if( is_wp_error( $response ) )
			{
				$message	= esc_html__( 'WordPress Error: Unable to create CDN URL - '. $response->get_error_message( ), 'pagecdn' );
				
				add_settings_error( 'pagecdn_settings' , esc_attr( 'pagecdn-settings-error' ) , $message , 'error' );
				
				return;
			}
			
			#	check HTTP response
			if( is_array( $response ) )
			{
				$rc		= ( int ) wp_remote_retrieve_response_code( $response );
				
				if( $rc == 200 )
				{
					$json	= json_decode( $response['body'] , true );
					
					if( isset( $json['response']['cdn_base'] ) && strlen( $json['response']['cdn_base'] ) )
					{
						$data['url']	= $json['response']['cdn_base'];
					}
					else
					{
						$api_code	= ( int ) $json['status'];
						
						if( $api_code !== 200 )
						{
							$message	= 'PageCDN Error: ' . $json['message'] . ' - '. $json['details'];
							
							add_settings_error( 'pagecdn_settings' , esc_attr( 'pagecdn-settings-error' ) , $message , 'error' );
						}
					}
				}
			}
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
	
	function PageCDN_settings_add_page( )
	{
		add_options_page( 'PageCDN' , 'PageCDN' , 'manage_options' , 'pagecdn' , 'PageCDN_settings_page' );
	}
	
	function PageCDN_settings_page( )
	{
		$options = PageCDN_options();
		
		require PAGECDN_DIR . '/settings.php';
	}
	
	