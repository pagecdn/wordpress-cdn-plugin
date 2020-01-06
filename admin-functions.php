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
					
					$is_backend	= in_array( $script, array( 'xmlrpc.php', 'wp-cron.php' ) );
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
		
		$settings_url	= add_query_arg( array( 'page' => 'pagecdn' ) , admin_url('options-general.php') );
		
		$settings_text	= __("Settings");
		
		if( PageCDN_private_cdn_enabled( ) )
		{
			return array_merge( array( "<a href=\"{$settings_url}\">{$settings_text}</a>" , "<span style=\"color:green;\" class=\"dashicons-before dashicons-yes\">Optimized</span>" ) , $data );
		}
		else
		{
			return array_merge( array( "<a href=\"{$settings_url}\">{$settings_text}</a>" , "<a href=\"https://pagecdn.com/signup\" target=\"_blank\" style=\"color:green;\">Optimize</a>" ) , $data );
		}
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
			show_message( '<div class="notice notice-warning is-dismissible"><p>Your website is not fully optimized. Please activate <a href="'.add_query_arg(array('page'=>'pagecdn'),admin_url('options-general.php')).'">Premium CDN</a> to get best performance for your website.</p></div>' );
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
			array(
				'id'	  => 'purge-pagecdn',
				'href'   => wp_nonce_url( add_query_arg('pagecdn_action', 'purge', $goto_url ) , '_cdn__purge_nonce' ),
				'parent' => 'top-secondary',
				'parent' => false,
				'title'	 => '<span class="ab-item">'.esc_html__('Purge CDN', 'pagecdn').'</span>',
				'meta'   => array('title' => esc_html__('Purge CDN', 'pagecdn')),
			)
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
		//if( !function_exists( 'is_plugin_active_for_network' ) )
		//{
		//	require_once ABSPATH . 'wp-admin/includes/plugin.php';
		//}
		
		
		#	Purge local cache
		#	Purge CDN Cache
		#	Deleting files internally triggers a purge operation
		
		file_put_contents( PAGECDN_CACHE , json_encode( array() ) );
		
		$args			= array();
		
		$args['repo']	= trim( parse_url( PageCDN_options( 'url' ) , PHP_URL_PATH ) , '/' );
		
		$args['apikey']	= PageCDN_options( 'pagecdn_api_key' );
		
		if( $response = PageCDN_get_API_response( '/private/repo/delete-files' , $args ) )
		{
			if( !PageCDN_display_API_error( $response ) )
			{
				if( $response['status'] == 200 )
				{
					printf( '<div class="notice notice-success is-dismissible"><p class="dashicons-before dashicons-trash">%s</p></div>',
							esc_html__('PageCDN cache purged. Changes may take 15 seconds to take effect.')
							);
				}
			}
		}
	}
	
	
	#	Settings
	#-------------------------------------------
	
	function PageCDN_settings_validate( $data )
	{
		Global $PageCDN_settings_error;
		
		$PageCDN_settings_error		= true;
		
		$repo_created				= false;
		
		$data['relative']			= isset( $data['relative'] )		? $data['relative']			: 1;
		$data['https']				= isset( $data['https'] )			? $data['https']			: 1;
		$data['fonts']				= isset( $data['fonts'] )			? $data['fonts']			: 0;
		$data['replace_cdns']		= isset( $data['replace_cdns'] )	? $data['replace_cdns']		: 0;
		$data['reuse_libs']			= isset( $data['reuse_libs'] )		? $data['reuse_libs']		: 0;
		$data['pagecdn_api_key']	= isset( $data['pagecdn_api_key'] )	? $data['pagecdn_api_key']	: '';
		$data['url']				= isset( $data['url'] )				? $data['url']				: '';
		
		$data['pagecdn_api_key']	= trim( $data['pagecdn_api_key'] );
		$data['url']				= trim( $data['url'] );
		
		//Premium features
		
		$data['optimize_images']		= isset( $data['optimize_images'] )		? $data['optimize_images']		: 0;
		$data['min_files']				= isset( $data['min_files'] )			? $data['min_files']			: 0;
		$data['preconnect_hosts']		= isset( $data['preconnect_hosts'] )	? $data['preconnect_hosts']		: 0;
		$data['preconnect_hosts_list']	= trim( $data['preconnect_hosts_list'] );
		
		
		
		if( ( $data['url'] == '' ) && strlen( $data['pagecdn_api_key'] ) )
		{
			$post					= array();
			$post['apikey']			= $data['pagecdn_api_key'];
			$post['repo_name']		= get_bloginfo( 'name' );
			$post['origin_url']		= home_url();
			$post['privacy']		= 'private';
			
			if( $response = PageCDN_post_API_response( '/private/repo/create' , $post ) )
			{
				if( PageCDN_display_API_error( $response ) )
				{
					$data['pagecdn_api_key']	= '';
					$data['url']				= '';
				}
				
				$response	= $response['response'];
				
				if( isset( $response['cdn_base'] ) && strlen( $response['cdn_base'] ) )
				{
					$data['url']	= $response['cdn_base'];
					
					$repo_created	= true;
				}
			}
			else
			{
				$data['pagecdn_api_key']	= '';
				$data['url']				= '';
			}
		}
		
		if( strlen( $data['url'] ) && !strlen( trim( $data['pagecdn_api_key'] ) ) )
		{
			#	API Key removed by the user
			#	Reset some of the premium options
			
			$data['pagecdn_api_key']	= '';
			$data['url']				= '';
			$data['optimize_images']	= 0;
			$data['min_files']			= 0;
		}
		
		
		if( strlen( $data['url'] ) && strlen( $data['pagecdn_api_key'] ) )
		{
			if( !$repo_created )
			{
				#	By default the cache control fields are no set. Sending this request without user's intent will cause
				#	bad user experience.
				#	Not sending this request will ensure that CDN's defaults are used unless user explicitly changes them.
				
				//Update the repo
				
				$post						= array();
				$post['compression_level']	= isset( $data['compression_level'] )	? $data['compression_level']	: 'moderate';
				$post['update_css_paths']	= isset( $data['update_css_paths'] )	? $data['update_css_paths']		: 0;
				$post['http_cache_ttl']		= isset( $data['http_cache_ttl'] )		? $data['http_cache_ttl']		: 0;
				$post['cache_control']		= isset( $data['cache_control'] )		? $data['cache_control']		: 0;
				
				if( $post['cache_control'] == 0 )
				{
					$post['http_cache_ttl']	= '0';		//Honor origin's headers
				}
				
				$post['apikey']				= $data['pagecdn_api_key'];
				$post['repo']				= trim( parse_url( $data['url'] , PHP_URL_PATH ) , '/' );
				
				if( $response = PageCDN_post_API_response( '/private/repo/configure' , $post ) )
				{
					PageCDN_display_API_error( $response );
				}
			}
		}
		
		
		
		$PageCDN_settings_error		= false;
		
		if( strlen( $data['preconnect_hosts_list'] ) )
		{
			$str	= $data['preconnect_hosts_list'];
			
			$str	= str_replace( " " , "\n" , str_replace( "\r" , "\n" , str_replace( "\r\n" , "\n" , $str ) ) );
			
			$str	= implode( "\n" , array_filter( explode( "\n" , $str ) ) ) . "\n";
			
			$data['preconnect_hosts_list']	= $str;
		}
		
		return array(
			'url'				=> esc_url( rtrim( $data['url'] , '/' ) )	,		//clean the URL before storing
			'dirs'				=> $data['dirs']							,
			'excludes'			=> $data['excludes']						,
			'pagecdn_api_key'	=> $data['pagecdn_api_key']					,
			'fonts'				=> ( int ) $data['fonts']					,
			'replace_cdns'		=> ( int ) $data['replace_cdns']			,
			'reuse_libs'		=> ( int ) $data['reuse_libs']				,
			'relative'			=> ( int ) $data['relative']				,
			'https'				=> ( int ) $data['https']					,
			
			//Premium features
			
			'optimize_images'		=> ( int ) $data['optimize_images']		,
			'min_files'				=> ( int ) $data['min_files']			,
			'preconnect_hosts'		=> ( int ) $data['preconnect_hosts']	,
			'preconnect_hosts_list'	=> $data['preconnect_hosts_list']		
		);
	}
	
	function PageCDN_settings_add_page( )
	{
		add_options_page( 'PageCDN' , 'PageCDN' , 'manage_options' , 'pagecdn' , 'PageCDN_settings_page' );
	}
	
	
	function PageCDN_get_API_response( $endpoint , $args )
	{
		$args		= http_build_query( $args );
		
		$response	= wp_remote_get( "https://pagecdn.com/api/v2{$endpoint}?{$args}" , array( 'timeout' => 30 ) );
		
		if( is_wp_error( $response ) )
		{
			PageCDN_display_error( 'Error connecting to PageCDN API - '. $response->get_error_message( ) );
			
			return false;
		}
		
		if( is_array( $response ) )
		{
			$rc		= ( int ) wp_remote_retrieve_response_code( $response );
			
			if( $rc == 200 )
			{
				return json_decode( $response['body'] , true );
			}
			else
			{
				PageCDN_display_error( 'PageCDN Error - Server returned error code ' . $rc . '.' );
			}
		}
		
		return false;
	}
	
	
	
	function PageCDN_post_API_response( $endpoint , $data )
	{
		$response	= wp_remote_post( "https://pagecdn.com/api/v2{$endpoint}" , array( 'timeout' => 20 , 'body' => $data ) );
		
		if( is_wp_error( $response ) )
		{
			PageCDN_display_error( 'Error connecting to PageCDN API - '. $response->get_error_message( ) );
			
			return false;
		}
		
		if( is_array( $response ) )
		{
			$rc		= ( int ) wp_remote_retrieve_response_code( $response );
			
			if( $rc == 200 )
			{
				return json_decode( $response['body'] , true );
			}
			else
			{
				PageCDN_display_error( 'PageCDN Error - Server returned error code ' . $rc . '.' );
			}
		}
	}
	
	function PageCDN_display_error( $message )
	{
		Global $PageCDN_settings_error;
		
		if( $PageCDN_settings_error )
		{
			add_settings_error( 'pagecdn_settings' , esc_attr( 'pagecdn-settings-error' ) , $message , 'error' );
		}
		else
		{
			$error_HTML	= '<div class="notice notice-error is-dismissible"><p>%s</p></div>';
			
			printf( $error_HTML , $message );
		}
	}
	
	function PageCDN_display_API_error( $response )
	{
		if( is_array( $response ) && ( $response['status'] != 200 ) )
		{
			PageCDN_display_error( "PageCDN Error - {$response['message']}: {$response['details']} Code: {$response['status']}" );
			
			return true;
		}
		
		return false;
	}
	
	function PageCDN_settings_page( )
	{
		$checked_if_premium			= '';
		
		$disabled_if_not_premium	= 'disabled';
		
		if( PageCDN_private_cdn_enabled( ) )
		{
			$checked_if_premium			= 'checked';
			
			$disabled_if_not_premium	= '';
		}
		
		$options = PageCDN_options();
		
		require PAGECDN_DIR . '/settings.php';
	}
	
	