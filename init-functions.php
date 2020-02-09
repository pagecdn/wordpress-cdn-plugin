<?php
	
	function PageCDN_hooks( )
	{
		#	Rewrite content in REST API
		#	add_filter( 'the_content' , array( 'PageCDN' , 'rewrite_the_content' ) , 100 );
		
		add_action( 'wp_head'								, 'PageCDN_preconnect'			, 0		);
		
		if( !PageCDN_is_backend( ) )
		{
			# Compatible with
			#	autoptimize
			#	w3total cache page cache feature
			#	wp super cache page cache feature
			add_action( 'plugins_loaded'					, 'PageCDN_check_available_plugins'			, PHP_INT_MAX	);
		}
		
		add_action( 'admin_init'							, 'PageCDN_admin_init'					);
		add_action( 'admin_menu'							, 'PageCDN_settings_add_page'			);
		add_filter( 'plugin_action_links_' . PAGECDN_BASE	, 'PageCDN_add_action_link'				);
		add_action( 'all_admin_notices'						, 'PageCDN_compat_check'				);
		add_action( 'admin_bar_menu'						, 'PageCDN_admin_links'			, 150	);
		add_action( 'admin_notices'							, 'PageCDN_purge'						);
		
		register_activation_hook( PAGECDN_FILE , 'PageCDN_activation' );
		register_uninstall_hook( PAGECDN_FILE , 'PageCDN_uninstall' );
	}
	
	function PageCDN_defaults( )
	{
		return array(
					'url'					=> ''							,
					'dirs'					=> 'wp-content,wp-includes,min'	,	#/min/ is used by LiteSpeed Cache
					'excludes'				=> '.php,.xml,.txt'				,
					'pagecdn_api_key'		=> ''							,
					'fonts'					=> 1							,
					'replace_cdns'			=> 1							,
					'reuse_libs'			=> 1							,
					'relative'				=> 1							,
					'https'					=> 1							,
					
					//Premium features
					
					'optimize_images'		=> 0							,
					'min_files'				=> 0							,
					'preconnect_hosts'		=> 1							,
					'preconnect_hosts_list'	=> "https://pagecdn.io\n"		,
					
					//Fresh from API. Not stored in Database
					
					'compression_level'		=> 0							,
					'http2_server_push'		=> 0							,
					'update_css_paths'		=> 0							,
					'http_cache_ttl'		=> 0							,
					'cache_control'			=> 0							
				);
	}
	
	function PageCDN_options( $option = null )
	{
		Static $options	= null;
		
		if( $options === null )
		{
			$options	= wp_parse_args( get_option('pagecdn') , PageCDN_defaults( ) );
			
			if( is_admin( ) && strlen( $options['pagecdn_api_key'] ) && strlen( $options['url'] ) )
			{
				$repo	= trim( parse_url( $options['url'] , PHP_URL_PATH ) , '/' );
				
				$apikey	= $options['pagecdn_api_key'];
				
				if( $response = PageCDN_get_API_response( '/private/repo/info' , array( 'apikey' => $apikey , 'repo' => $repo ) ) )
				{
					if( !PageCDN_display_API_error( $response ) )
					{
						$response	= $response['response'];
						
						$options['compression_level']	= $response['compression_level'];
						$options['http2_server_push']	= $response['server_push'] && $response['server_push_trigger'];
						$options['update_css_paths']	= $response['update_css_paths'];
						$options['http_cache_ttl']		= strtotime( "+{$response['browser_cache_number']} {$response['browser_cache_period']}" , 0 );
						$options['cache_control']		= !!$options['http_cache_ttl'];
					}
				}
			}
		}
		
		if( $option )
		{
			if( isset( $options[$option] ) )
			{
				return $options[$option];
			}
			
			return '';
		}
		
		return $options;
	}
	
	function PageCDN_preconnect( )
	{
		$list	= array_filter( explode( "\n" , PageCDN_options('preconnect_hosts_list') ) );
		
		if( count( $list ) )
		{
			foreach( $list as $item )
			{
				echo "\n<link rel=\"preconnect\" href=\"{$item}\" crossorigin />";
			}
			
			echo "\n";
		}
	}
	
	function PageCDN_uninstall( )
	{
		delete_option('pagecdn');
	}
	
	function PageCDN_activation( )
	{
		add_option( 'pagecdn' , PageCDN_defaults( ) );
	}
	
	function PageCDN_check_available_plugins( )
	{
		/*
		$fullpage_cache_enabled	= false;
		
		$minify_merge_enabled	= false;
		
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
		
		if( is_plugin_active( 'autoptimize/autoptimize.php' ) )
		{
			$conf = autoptimizeConfig::instance();
			
			if( $conf->get( 'autoptimize_js' ) || $conf->get( 'autoptimize_css' ) )
			{
				$minify_merge_enabled	= true;
			}
		}
		
		if( is_plugin_active( 'w3-total-cache/w3-total-cache.php' ) )
		{
			$w3tc_minify	= new W3TC\Minify_Plugin( );
			
			//if( $w3tc_minify->can_minify( ) )
			//{
				$modules = W3TC\Dispatcher::component( 'ModuleStatus' );
				
				$minify_merge_enabled	= ( bool ) $modules->is_enabled( 'minify' );
				
				$fullpage_cache_enabled	= ( bool) $modules->is_enabled( 'pgcache' );
			//}
		}
		
		
		//	if( class_exists( 'LiteSpeed_Cache' ) || defined( 'LSCWP_DIR' ) )
			
		if( is_plugin_active( 'litespeed-cache/litespeed-cache.php' ) )
		{
			$minify_merge_enabled	= true;
			
			$fullpage_cache_enabled	= true;
		}
		
		
		if( is_plugin_active( 'wp-super-cache/wp-cache.php' ) )
		{
			if( isset( $GLOBALS['cache_enabled'] ) && $GLOBALS['cache_enabled'] )
			{
				$fullpage_cache_enabled	= true;
			}
		}
		
		if( $fullpage_cache_enabled )
		{
			//add_filter( 'the_content' , 'PageCDN_end_buffering' , 100 );
			
			//add_action( 'wp_enqueue_scripts', function(){print_r( $GLOBALS['wp_scripts']);print_r($GLOBALS['wp_styles']);} , 0 );
		}
		
		*/
		
		ob_start( 'PageCDN_end_buffering' );
		
		
		
	}
	
	/*
	function PageCDN_init_buffering( )
	{
		#	https://stackoverflow.com/questions/772510/wordpress-filter-to-modify-final-html-output
		
		#	Close all buffers. This will run callback for autoptimize and other plugins.
		#	Start new output buffering with callback.
		#	Manually run callback for autoptimize.
		
		$final	= '';
		$levels	= ob_get_level( );
		
		for( $i = 0; $i < $levels; $i++ )
		{
			$final	.= ob_get_clean( );
		}
		
		ob_start( 'PageCDN_end_buffering' );
		
		echo $final;
	}
	*/
	
	function PageCDN_end_buffering( $content )
	{
		/*
		if( function_exists( 'autoptimize' ) )
		{
			$content	= autoptimize( )->end_buffering( $content );
		}
		
		if( defined( 'W3TC' ) )
		{
			$w3tc_minify	= new W3TC\Minify_Plugin( );
			
			if( $w3tc_minify->can_minify( ) )
			{
				$modules = W3TC\Dispatcher::component( 'ModuleStatus' );
				
				$minify_enabled = $modules->is_enabled( 'minify' );
				
				if( $minify_enabled )
				{
					$content	= $w3tc_minify->ob_callback( $content );
				}
			}
		}
		
		if( class_exists( 'LiteSpeed_Cache' ) || defined( 'LSCWP_DIR' ) )
		{
			$content		= LiteSpeed_Cache::get_instance( )->send_headers_force( $content );
		}
		
		$content	= PageCDN_rewriter_rewrite( $content );
		
		if( isset( $GLOBALS['cache_enabled'] ) && $GLOBALS['cache_enabled'] && function_exists( 'wp_cache_ob_callback' ) )
		{
			$content	= wp_cache_ob_callback( $content );
		}
		
		
		//if( defined( 'W3TC' ) )
		//{
		//	$w3tc_can_cache	= true;
		//	
		//	switch ( true )
		//	{
		//		case defined( 'DONOTCACHEPAGE' ):
		//		case defined( 'DOING_AJAX' ):
		//		case defined( 'DOING_CRON' ):
		//		case defined( 'APP_REQUEST' ):
		//		case defined( 'XMLRPC_REQUEST' ):
		//		case defined( 'WP_ADMIN' ):
		//		case ( defined( 'SHORTINIT' ) && SHORTINIT ):
		//			$w3tc_can_cache	= false;
		//	}
		//	
		//	if( $w3tc_can_cache )
		//	{
		//		$modules = W3TC\Dispatcher::component( 'ModuleStatus' );
		//		
		//		$pgcache_enabled = $modules->is_enabled( 'pgcache' );
		//		
		//		if( $pgcache_enabled )
		//		{
		//			$w3tc_pagecache	= new W3TC\PgCache_ContentGrabber( );
		//			
		//			//$w3tc_pagecache->process( );
		//			
		//			$content		= $w3tc_pagecache->ob_callback( $content );
		//			
		//			//echo $content;
		//			//die;
		//		}
		//	}
		//}
		*/
		
		
		
		$content	= PageCDN_rewriter_rewrite( $content );
		
		return $content;
	}
	
	
	function PageCDN_private_cdn_enabled( )
	{
		return ( bool ) ( strlen( PageCDN_options( 'pagecdn_api_key' ) ) && strlen( PageCDN_options( 'url' ) ) );
	}
	
	
	
	