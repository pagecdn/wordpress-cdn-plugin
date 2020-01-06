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
	
	
	
	
	
	#	Rewriter
	#-------------------------------------------
	
	function PageCDN_rewriter_excludes( )
	{
		Static $excludes = null;
		
		if( $excludes === null )
		{
			$excludes	= array_filter( array_map( 'trim' , explode( ',' , PageCDN_options( 'excludes' ) ) ) );
		}
		
		return $excludes;
	}
	
	function PageCDN_rewriter_exclude_asset( $asset )
	{
		static $excludes = null;
		
		if( $excludes === null )
		{
			$excludes	= array_filter( array_map( 'trim' , explode( ',' , PageCDN_options( 'excludes' ) ) ) );
		}
		
		foreach( $excludes as $exclude )
		{
			if( stripos( $asset , $exclude ) !== false )
			{
				return true;
			}
		}
		
		return false;
	}
	
	function PageCDN_rewriter_exclude_public_lookup( $asset )
	{
		$excludes	= array( '/wp-content/cache/' , '/min/' );
		
		foreach( $excludes as $exclude )
		{
			if( stripos( $asset , $exclude ) !== false )
			{
				return true;
			}
		}
		
		return false;
	}
	
	function PageCDN_rewriter_relative_url( $url )
	{
		return substr( $url , strpos( $url , '//' ) );
	}
	
	function PageCDN_rewriter_get_dir_scope( )
	{
		$input	= array_filter( array_map( 'trim' , explode( ',' , PageCDN_options( 'dirs' ) ) ) );
		
		if( $input === array() )
		{
			return 'wp\-content|wp\-includes|min';
		}
		
		return implode( '|' , array_map( 'quotemeta' , $input ) );
	}
	
	function PageCDN_rewriter_rewrite( $html )
	{
		$regex	= array();
		
		$dirs	= PageCDN_rewriter_get_dir_scope( );
		
		$blog_url	= ( PageCDN_options( 'https' ) ? '(https?:|)' : '(http:|)' ) . quotemeta( PageCDN_rewriter_relative_url( home_url( ) ) );
		
		if( strpos( $blog_url , '//www\\.' ) !== false )
		{
			$blog_url	= str_replace( '//www\\.' , '//(www\\.)?' , $blog_url );
		}
		else
		{
			$blog_url	= str_replace( '//' , '//(www\\.)?' , $blog_url );
		}
		
		$regex_rule	= '#(?<=[(\"\'])';
		
		if( PageCDN_options( 'relative' ) )
		{
			$regex_rule .= '(?:'.$blog_url.')?';
		}
		else
		{
			$regex_rule .= $blog_url;
		}
		
		$regex_rule		.= '/(?:((?:'.$dirs.')[^\"\')]+)|([^/\"\']+\.[^/\"\')]+))(?=[\"\')])#';
		
		$regex['site']	= $regex_rule;
		
		$regex['fonts']	= "#<link[^>]*href=([\"'](https?:|)//fonts\.googleapis\.com/css\?family=[^\"']+[\"'])[^>]*>#";
		
		$regex['easy']	= "#<link[^>]*href=([\"'](https?:|)//pagecdn\.io/lib/easyfonts/[^\"']+[\"'])[^>]*>#";
		
		$public_hosts	= array(
							'cdn\.jsdelivr\.net'			,	#	https://www.jsdelivr.com/
							'cdnjs\.cloudflare\.com'		,	#	https://cdnjs.com/
							'ajax\.aspnetcdn\.com'			,	#	https://docs.microsoft.com/en-us/aspnet/ajax/cdn/overview
							'ajax\.googleapis\.com'			,	#	https://developers.google.com/speed/libraries/
							'stackpath\.bootstrapcdn\.com'	,	#	https://www.bootstrapcdn.com/
							'maxcdn\.bootstrapcdn\.com'		,	#	https://www.bootstrapcdn.com/
							'code\.jquery\.com'				,	#	https://jquery.com/download/
							'cdn\.bootcss\.com'				,	#	https://www.bootcdn.cn/ & https://www.bootcss.com/
							'unpkg\.com'					,	#	https://unpkg.com
							'use\.fontawesome\.com'			,	#	https://fontawesome.com
							'cdn\.rawgit\.com'				,	#	https://rawgit.com
							'cdn\.staticfile\.org'			,	#	http://staticfile.org/
							'apps\.bdimg\.com'				,	#	http://apps.static-bdimg.com/
							'yastatic\.net'					,	#	https://tech.yandex.ru/jslibs/
							'code\.ionicframework\.com'		,	#	https://ionicframework.com/
							'cdn\.ckeditor\.com'			,	#	https://ckeditor.com/
							'cdn\.mathjax\.org'				
						);
		
		$regex['public_scripts']	= "#<script[^>]*src=[\"']((?:https?:|)//(?:".implode('|',$public_hosts).")[^\"']+)[\"'][^>]*>#";
		
		$regex['public_styles']		= "#<link[^>]*href=[\"']((?:https?:|)//(?:".implode('|',$public_hosts).")[^\"']+)[\"'][^>]*>#";
		
		
		if( PageCDN_options( 'fonts' ) )
		{
			$html	= preg_replace_callback( $regex['fonts'] , 'PageCDN_rewriter_rewrite_google_fonts' , $html );
			
			$html	= preg_replace_callback( $regex['easy'] , 'PageCDN_rewriter_rewrite_easy_fonts' , $html );
			
			$html	= PageCDN_rewriter_insert_fonts( $html );
		}
		
		if( PageCDN_options( 'replace_cdns' ) )
		{
			$html	= preg_replace_callback( $regex['public_scripts'] , 'PageCDN_rewriter_rewrite_public_cdns' , $html );
			
			$html	= preg_replace_callback( $regex['public_styles'] , 'PageCDN_rewriter_rewrite_public_cdns' , $html );
		}
		
		$html	= preg_replace_callback( $regex['site'] , 'PageCDN_rewriter_rewrite_url' , $html );
		
		return $html;
	}
	
	function PageCDN_rewriter_rewrite_google_fonts( $match )
	{
		Global $PageCDN_fonts;
		
		if( strlen( $match[0] ) && strlen( $match[1] ) )
		{
			$url	= trim( $match[1] , '\'"' );
			
			$url	= parse_url( $url );
			
			if( isset( $url['query'] ) )
			{
				parse_str( $url['query'] , $vars );
				
				foreach( $vars as $key => $val )
				{
					if( $key == 'family' )
					{
						$family	= $val;
					}
				}
			}
			
			foreach( explode( '|' , $family ) as $ifamily )
			{
				if( strpos( $ifamily , ':' ) )
				{
					$ifamily	= substr( $ifamily , 0 , strpos( $ifamily , ':' ) );
				}
				
				$family_name	= str_replace( ' ' , '-' , strtolower( $ifamily ) ) . '.css';
				
				$PageCDN_fonts[$family_name]	= true;
			}
			
			return '';
		}
	}
	
	
	function PageCDN_rewriter_rewrite_easy_fonts( $match )
	{
		Global $PageCDN_fonts;
		
		if( strlen( $match[0] ) && strlen( $match[1] ) )
		{
			$url	= trim( $match[1] , '\'"' );
			
			$url	= str_replace( 'http://pagecdn.io/lib/easyfonts/' , '' , $url );
			
			$family	= str_replace( 'https://pagecdn.io/lib/easyfonts/' , '' , $url );
			
			if( strpos( $family , '?' ) !== false )
			{
				$family	= substr( $family , 0 , strpos( $family , '?' ) );
			}
			
			$PageCDN_fonts[$family]		= true;
			
			return '';
		}
	}
	
	function PageCDN_rewriter_insert_fonts( $html )
	{
		Global $PageCDN_fonts;
		
		$code	= '';
		
		$base	= 'https://pagecdn.io/lib/easyfonts/';
		
		if( count( $PageCDN_fonts ) < 3 )
		{
			foreach( $PageCDN_fonts as $family => $scrap )
			{
				$code	.= "\n<link rel=\"stylesheet\" href=\"{$base}{$family}\" />";
			}
		}
		else
		{
			$code	.= "\n<link rel=\"stylesheet\" href=\"{$base}fonts.css\" />";
		}
		
		$pos	= strpos( $html , '</head>' );
		
		if( $pos === false )
		{
			$pos	= strpos( strtolower( $html ) , '</head>' );
		}
		
		if( $pos === false )
		{
			$html	= $code . $html;
		}
		else
		{
			$html	= substr_replace( $html, $code.'</head>' , $pos, 7 );
		}
		
		return $html;
	}
	
	function PageCDN_rewriter_rewrite_public_cdns( $url )
	{
		return str_replace( $url[1] , PageCDN_rewriter_cached_url( $url[1] ) , $url[0] );
	}
	
	function PageCDN_rewriter_rewrite_url( $asset )
	{
		$url	= $asset[0];
		
		//file_put_contents( PAGECDN_DIR . '/cache/test.txt' , $url . "\n\n" , FILE_APPEND );
		
		if( is_admin_bar_showing( ) && isset( $_GET['preview'] ) && ( $_GET['preview'] === 'true' ) )
		{
			return $url;
		}
		
		if( PageCDN_rewriter_exclude_asset( $url ) )
		{
			return $url;
		}
		
		$blog_url		= PageCDN_rewriter_relative_url( home_url( ) );
		
		$full_blog_url	= home_url( );
		
		#	Normalize Protocol Relative URLs
		if( strpos( $url , '//' ) === 0 )
		{
			$url	= str_replace( $blog_url , $full_blog_url , $url );
		}
		
		#	Normalize Relative URLs
		else if( strpos( $url , '/' ) === 0 )
		{
			$url	= $full_blog_url . $url;
		}
		
		#	Normalize Absolute URLs
		else #	if( !$this->relative || strstr( $url , $blog_url ) )
		{
			$protocol	= substr( $full_blog_url , 0 , strpos( $full_blog_url , ':' ) );
			
			if( $protocol !== substr( $url , 0 , strpos( $url , ':' ) ) )
			{
				$url	= $protocol . substr( $url , strpos( $url , ':' ) );
			}
		}
		
		if( PageCDN_options( 'reuse_libs' ) )
		{
			$url	= PageCDN_rewriter_cached_url( $url );
			
			if( !( strpos( $url , $full_blog_url ) === 0 ) )
			{
				return $url;
			}
		}
		
		if( PageCDN_private_cdn_enabled( ) )
		{
			#	Remove Query String
			
			$url	= PageCDN_remove_query_string( $url );
			
			if( PageCDN_options('optimize_images') )
			{
				if( strpos( $url , ', ' ) )
				{
					//Its probably a srcset. Replace extension with optimization flag. Images are already sized.
					#	https://example.com/image.png 960w, https://example.com/image-300x169.png 300w
					
					$replaceable	= '';
					
					if( substr_count( $url , '.jpg' ) > 1 )
					{
						$replaceable	= '.jpg';
					}
					else if( substr_count( $url , '.png' ) > 1 )
					{
						$replaceable	= '.png';
					}
					else if( substr_count( $url , '.JPG' ) > 1 )
					{
						$replaceable	= '.JPG';
					}
					else if( substr_count( $url , '.PNG' ) > 1 )
					{
						$replaceable	= '.PNG';
					}
					else if( substr_count( $url , '.JPEG' ) > 1 )
					{
						$replaceable	= '.JPEG';
					}
					
					$url	= str_replace( $replaceable , '._o'. $replaceable , $url ); 
				}
				else if( in_array( strtolower( substr( $url , -4 ) ) , array( '.png' , '.jpg' ) ) )
				{
					$url	= substr( $url , 0 , -4 ) . '._o' . substr( $url , -4 );
				}
			}
			else if( PageCDN_options('min_files') )
			{
				if( !( ( strtolower( substr( $url , -7 ) ) === 'min.css' ) || ( strtolower( substr( $url , -6 ) ) === 'min.js' ) ) )
				{
					if( strtolower( substr( $url , -4 ) ) === '.css' )
					{
						$url	= substr_replace( $url , '.min'. substr( $url , -4 ) , strlen( $url ) - 4 , 4 );
					}
					else if( strtolower( substr( $url , -3 ) ) === '.js' )
					{
						$url	= substr_replace( $url , '.min'. substr( $url , -3 ) , strlen( $url ) - 3 , 3 );
					}
				}
			}
			
			return str_replace( $full_blog_url , PageCDN_options('url') , $url );
		}
		
		return $url;
	}
	
	
	function PageCDN_remove_query_string( $url )
	{
		if( strpos( $url , '?' ) !== false )
		{
			#	Skip URLs like domain.com/?wf... 
			
			$path	= parse_url( $url , PHP_URL_PATH );
			
			if( ( $path == '' ) || ( $path == '/' ) )
			{
				return $url;
			}
			
			$url	= substr( $url , 0 , strpos( $url , '?' ) );
		}
		
		return $url;
	}
	
	function PageCDN_rewriter_cached_url( $url )
	{
		static $cache = array() , $includes_json = array();
		
		if( !$cache )
		{
			@$data	= file_get_contents( PAGECDN_CACHE );
			
			if( $data && strlen( $data ) )
			{
				$cache	= json_decode( $data , true );
			}
		}
		
		#	Remove Query String
		
		$url	= PageCDN_remove_query_string( $url );
		
		//if( strpos( $url , '?' ) !== false )
		//{
		//	#	Skip URLs like domain.com/?wf... 
		//	
		//	$path	= parse_url( $url , PHP_URL_PATH );
		//	
		//	if( ( $path == '' ) || ( $path == '/' ) )
		//	{
		//		return $url;
		//	}
		//	
		//	$url	= substr( $url , 0 , strpos( $url , '?' ) );
		//}
		
		if( ( substr( $url , -4 ) !== '.css' ) && ( substr( $url , -3 ) !== '.js' ) )
		{
			return $url;
		}
		
		if( strpos( $url , '//' ) === 0 )
		{
			$url	= substr( home_url( ) , 0 , strpos( home_url( ) , ':' ) ) . ':' . $url;
		}
		
		if( strpos( $url , '//' ) === false )
		{
			$url	= home_url( ) . $url;
		}
		
		
		#	Check if URL hash exists in Cache
		
		$url_hash		= hash( 'sha256' , $url );
		
		if( isset( $cache[$url_hash] ) )
		{
			return $cache[$url_hash];
		}
		
		
		#	Check if content hash exist in /data/data.json
		
		$contents		= '';
		
		$response		= wp_remote_get( $url );
		
		if( is_array( $response ) )
		{
			$contents	= $response['body'];
			
			$contents_hash	= hash( 'sha256' , $contents );
			
			if( isset( $cache[$contents_hash] ) )
			{
				$cache[$url_hash]	= $cache[$contents_hash];
			}
			else
			{
				#	Check if content hash exists in ./data/data.json
				
				if( !count( $includes_json ) )
				{
					$includes_json	= json_decode( file_get_contents( PAGECDN_DIR . '/data/data.json' ) , true );
				}
				
				if( isset( $includes_json[$contents_hash] ) )
				{
					$cache[$contents_hash]	= 'https://pagecdn.io'.$includes_json[$contents_hash];
					
					$cache[$url_hash]		= 'https://pagecdn.io'.$includes_json[$contents_hash];
				}
				else if( !PageCDN_rewriter_exclude_public_lookup( $url ) )
				{
					$optimized	= '';
					
					//if( PageCDN_options( 'min_files' ) )
					//{
					//	$optimized	= '&optimized=true';
					//}
					
					
					
					//$apikey		= PageCDN_default_apikey( );
					//
					//if( PageCDN_options( 'pagecdn_api_key' ) )
					//{
					//	$apikey	= PageCDN_options( 'pagecdn_api_key' );
					//}
					
					$response	= wp_remote_get( "https://pagecdn.io/lookup/{$contents_hash}" );
					
					if( !is_wp_error( $response ) )
					{
						$response	= json_decode( $response['body'] , true );
						
						if( isset( $response['status'] ) && ( $response['status'] == '200' ) )
						{
							$response	= $response['response'];
							
							if( isset( $response['file_url'] ) && strlen( $response['file_url'] ) )
							{
								$cdn_file	= $response['file_url'];
								
								if( PageCDN_options( 'min_files' ) )
								{
									//Do not test .min.css and .min.js as we need to support -min.css -min.js
									
									if( !( ( substr( $cdn_file , -7 ) === 'min.css' ) || ( substr( $cdn_file , -6 ) === 'min.js' ) ) )
									{
										if( substr( $cdn_file , -4 ) === '.css' )
										{
											$cdn_file	= substr_replace( $cdn_file , '.min.css' , strlen( $cdn_file ) - 4 , 4 );
										}
										else if( substr( $cdn_file , -3 ) === '.js' )
										{
											$cdn_file	= substr_replace( $cdn_file , '.min.js' , strlen( $cdn_file ) - 3 , 3 );
										}
									}
								}
								
								$cache[$contents_hash]	= $cdn_file;
								
								$cache[$url_hash]		= $cdn_file;
							}
						}
					}
				}
			}
		}
		
		if( !isset( $cache[$url_hash] ) )
		{
			$cache[$url_hash]		= $url;
		}
		
		if( isset( $contents_hash ) && !isset( $cache[$contents_hash] ) )
		{
			$cache[$contents_hash]	= $url;
		}
		
		$data	= json_encode( $cache );
		
		file_put_contents( PAGECDN_CACHE , $data );
		
		return $cache[$url_hash];
	}
	
	