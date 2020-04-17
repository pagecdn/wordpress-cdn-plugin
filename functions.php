<?php
	
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
	
	function PageCDN_site_regex( )
	{
		$dirs		= PageCDN_rewriter_get_dir_scope( );
		
		$blog_url	= '(https?:|)' . quotemeta( PageCDN_rewriter_relative_url( home_url( ) ) );
		
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
		
		return $regex_rule;
	}
	
	function PageCDN_cache_known_URLs( )
	{
		Global $PageCDN_known_URLs;
		Global $PageCDN_known_img_URLs;
		Global $PageCDN_known_webp_URLs;
		Global $PageCDN_discovered_new_URLs;
		
		if( $PageCDN_discovered_new_URLs )
		{
			update_option( 'pagecdn-cache' , $PageCDN_known_URLs );
			
			update_option( 'pagecdn-image-cache' , $PageCDN_known_img_URLs );
			
			update_option( 'pagecdn-webp-cache' , $PageCDN_known_webp_URLs );
			
			
			//@file_put_contents( PAGECDN_CACHE , json_encode( $PageCDN_known_URLs ) );
			//
			//@file_put_contents( PAGECDN_IMG_CACHE , json_encode( $PageCDN_known_img_URLs ) );
			//
			//@file_put_contents( PAGECDN_WEBP_CACHE , json_encode( $PageCDN_known_webp_URLs ) );
		}
	}
	
	function PageCDN_create_fs_hierarchy( $dir )
	{
		if( file_exists( $dir ) )
		{
			return true;
		}
		
		return ( bool ) mkdir( $dir , 0755 , $recursive = true );
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
	
	
	#	Not used anywhere
	function PageCDN_is_blog_url( $url )
	{
		Global $PageCDN_check_hosts;
		
		foreach( $PageCDN_check_hosts as $host )
		{
			if( strpos( $url , $host ) === 0 )
			{
				return true;
			}
		}
		
		return false;
	}
	
	
	function PageCDN_rewriter_rewrite( $html )
	{
		Global $PageCDN_known_URLs;
		//Global $PageCDN_origin_scheme;
		//Global $PageCDN_origin_host;
		//Global $PageCDN_normalized_origin_host;
		
		Global $PageCDN_known_img_URLs;
		Global $PageCDN_known_webp_URLs;
		Global $PageCDN_webp_support;
		
		#	Replace known URLs.
		$html	= strtr( $html , $PageCDN_known_URLs );
		
		if( $PageCDN_webp_support )
		{
			$html	= strtr( $html , $PageCDN_known_webp_URLs );
		}
		else
		{
			$html	= strtr( $html , $PageCDN_known_img_URLs );
		}
		
		
		#	Discover new URLs.
		
		if( PageCDN_options( 'fonts' ) )
		{
			$regex_fonts	= "#<link[^>]*href=([\"'](https?:|)//fonts\.googleapis\.com/css\?family=[^\"']+[\"'])[^>]*>#";
			
			$regex_easy		= "#<link[^>]*href=([\"'](https?:|)//pagecdn\.io/lib/easyfonts/[^\"']+[\"'])[^>]*>#";
			
			$html	= preg_replace_callback( $regex_fonts , 'PageCDN_rewriter_rewrite_google_fonts' , $html );
			
			$html	= preg_replace_callback( $regex_easy , 'PageCDN_rewriter_rewrite_easy_fonts' , $html );
			
			$html	= PageCDN_rewriter_insert_fonts( $html );
		}
		
		if( PageCDN_options( 'replace_cdns' ) )
		{
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
								'lib\.arvancloud\.com'			,	#	
								'netdna\.bootstrapcdn\.com'		,	#	
								'cdn\.mathjax\.org'				
							);
			
			$regex_public_scripts	= "#<script[^>]*src=[\"']((?:https?:|)//(?:".implode('|',$public_hosts).")[^\"']+)[\"'][^>]*>#";
			
			$regex_public_styles	= "#<link[^>]*href=[\"']((?:https?:|)//(?:".implode('|',$public_hosts).")[^\"']+)[\"'][^>]*>#";
			
			$html	= preg_replace_callback( $regex_public_scripts , 'PageCDN_rewriter_rewrite_public_cdns' , $html );
			
			$html	= preg_replace_callback( $regex_public_styles , 'PageCDN_rewriter_rewrite_public_cdns' , $html );
		}
		
		$regex_site	= PageCDN_site_regex( );
		
		$html	= preg_replace_callback( $regex_site , 'PageCDN_rewriter_rewrite_url' , $html );
		
		//$html	= $html; . '<!--' . json_encode( $PageCDN_known_URLs ) .'-->';
		
		PageCDN_cache_known_URLs( );
		
		return $html;
	}
	
	function PageCDN_rewriter_rewrite_public_cdns( $url )
	{
		return str_replace( $url[1] , PageCDN_rewriter_cached_url( $url[1] ) , $url[0] );
	}
	
	function PageCDN_rewriter_rewrite_url( $asset )
	{
		Global	$PageCDN_known_URLs				,
				$PageCDN_discovered_new_URLs	,
				$PageCDN_check_hosts			,
				$PageCDN_origin_host			,
				$PageCDN_webp_support			,
				$PageCDN_known_webp_URLs		,
				$PageCDN_known_img_URLs			;
		
		$url			= $asset[0];
		
		$original_url	= $url;
		
		//if( isset( $PageCDN_known_URLs[$original_url] ) )
		//{
		//	return $PageCDN_known_URLs[$original_url];
		//}
		
		#	file_put_contents( PAGECDN_DIR . '/cache/test.txt' , $url . "\n\n" , FILE_APPEND );
		
		if( isset( $_GET['preview'] ) && ( $_GET['preview'] === 'true' ) && is_admin_bar_showing( ) )
		{
			return $original_url;
		}
		
		if( PageCDN_rewriter_exclude_asset( $url ) )
		{
			return $original_url;
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
			$optimizable_image	= false;
			
			$url	= PageCDN_remove_query_string( $url );
			
			foreach( $PageCDN_check_hosts as $url_host )
			{
				if( ( strpos( $full_blog_url , $url_host ) === 0 ) && ( strpos( $url , $url_host ) !== 0 ) )
				{
					#	host in URL needs to be corrected
					#	Scheme is already corrected
					
					$_host	= parse_url( $url , PHP_URL_HOST );
					
					$url	= str_replace( $_host , $PageCDN_origin_host , $url );
				}
			}
			
			if( PageCDN_options('optimize_images') )
			{
				$flag	= '._o';
				
				if( $PageCDN_webp_support )
				{
					$flag	= '._o_webp';
				}
				
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
					else if( substr_count( $url , '.jpeg' ) > 1 )
					{
						$replaceable	= '.jpeg';
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
					
					$url	= str_replace( $replaceable , $flag . $replaceable , $url );
					
					$optimizable_image	= true;
				}
				else if( in_array( strtolower( substr( $url , -4 ) ) , array( '.png' , '.jpg' ) ) )
				{
					$url	= substr( $url , 0 , -4 ) . $flag . substr( $url , -4 );
					
					$optimizable_image	= true;
				}
				else if( in_array( strtolower( substr( $url , -5 ) ) , array( '.jpeg' ) ) )
				{
					$url	= substr( $url , 0 , -5 ) . $flag . substr( $url , -5 );
					
					$optimizable_image	= true;
				}
			}
			
			if( PageCDN_options('min_files') )
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
			
			$url	= str_replace( $full_blog_url , PageCDN_options('url') , $url );
			
			
			#	Cache here in private cdn section to make sure errors occured in public cdn 
			#	section do not get cached as such cache was skipped in public cdn too for errors.
			
			$PageCDN_discovered_new_URLs		= true;
			
			if( $optimizable_image )
			{
				if( $PageCDN_webp_support )
				{
					$PageCDN_known_webp_URLs[$original_url]	= $url;
					
					return $PageCDN_known_webp_URLs[$original_url];
				}
				else
				{
					$PageCDN_known_img_URLs[$original_url]	= $url;
					
					return $PageCDN_known_img_URLs[$original_url];
				}
			}
			
			$PageCDN_known_URLs[$original_url]	= $url;
			
			return $PageCDN_known_URLs[$original_url];
		}
		
		return $url;
	}
	
	function PageCDN_rewriter_cached_url( $url )
	{
		Global $PageCDN_known_URLs , $PageCDN_discovered_new_URLs;
		
		static  $includes_json = array();
		
		if( !$includes_json )
		{
			@$data	= file_get_contents( PAGECDN_DIR . '/data/data.json' );
			
			if( $data && strlen( $data ) )
			{
				$includes_json	= json_decode( $data , true );
			}
		}
		
		$original_url	= $url;
		
		
		//QS removal is required here before testing css and js extension
		
		$test_url	= PageCDN_remove_query_string( $url );
		
		if( ( strtolower( substr( $test_url , -4 ) ) !== '.css' ) && ( strtolower( substr( $test_url , -3 ) ) !== '.js' ) )
		{
			return $test_url;
		}
		
		if( strpos( $url , '//' ) === 0 )
		{
			$url	= substr( home_url( ) , 0 , strpos( home_url( ) , ':' ) ) . ':' . $url;
		}
		
		if( strpos( $url , '//' ) === false )
		{
			$url	= home_url( ) . $url;
		}
		
		#	Check if content hash exist in data file.
		
		$contents		= '';
		
		$response		= wp_remote_get( $url );
		
		if( is_array( $response ) )
		{
			$contents	= $response['body'];
			
			$contents_hash	= hash( 'sha256' , $contents );
			
			if( isset( $includes_json[$contents_hash] ) )
			{
				$PageCDN_discovered_new_URLs		= true;
				
				$PageCDN_known_URLs[$original_url]	= 'https://pagecdn.io'.$includes_json[$contents_hash];
				
				return $PageCDN_known_URLs[$original_url];
			}
			else if( !PageCDN_rewriter_exclude_public_lookup( $url ) )
			{
				$optimized	= '';
				
				$response	= wp_remote_get( "https://pagecdn.io/lookup/{$contents_hash}" );
				//echo $url;die;
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
								//Do not test .min.css and .min.js as we need to support -min.css -min.js too
								
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
							
							$PageCDN_discovered_new_URLs		= true;
							
							$PageCDN_known_URLs[$original_url]	= $cdn_file;
							
							return $PageCDN_known_URLs[$original_url];
						}
					}
				}
				else
				{
					#	DO NOT add to $known_URL[]
					
					#	Commented off, due to several websites repeatedly requesting the same /lookup/ endpoint
					//return PageCDN_remove_query_string( $url );
				}
			}
		}
		
		$PageCDN_discovered_new_URLs		= true;
		
		$PageCDN_known_URLs[$original_url]	= PageCDN_remove_query_string( $url );
		
		return $PageCDN_known_URLs[$original_url];
	}
	
	