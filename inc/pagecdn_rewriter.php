<?php

/**
 * PageCDN_Rewriter
 *
 * @since 0.1
 */

class PageCDN_Rewriter
{
	var $blog_url			= null;		# Origin URL
	var $cdn_url			= null;		# CDN Repo URL
	var $dirs				= null;		# Directories to Optimize
	var $excludes			= [];		# Directories and file extensions to exclude
	var $pagecdn_api_key	= null;		# optional API Key. Required to purge cdn cache
	
	var $fonts				= 1;		# Load Easyfonts instead of Google Fonts
	var $replace_cdns		= 1;		# Capture other Public CDN URLs and load resources from PageCDN if possible
	var $reuse_libs			= 1;		# Use Public libraries that are better optimized, instead of loading opensource content through user's repo
	var $min_files			= 1;		# Select minified files from PageCDN Public Repos where possible.
	
	var $relative			= 1;		# use CDN on relative paths
	var $https				= 1;		# use CDN on HTTPS
	
	
	
	/**
	 * constructor
	 *
	 * @since   0.1
	 */
	
	function __construct( $blog_url , $cdn_url , $dirs , $excludes , $pagecdn_api_key , $fonts , $replace_cdns , $reuse_libs , $min_files , $relative , $https )
	{
		$this->blog_url			= $blog_url;
		$this->cdn_url			= $cdn_url;
		$this->dirs				= $dirs;
		$this->excludes			= $excludes;
		$this->pagecdn_api_key	= $pagecdn_api_key;
		
		$this->fonts			= $fonts;
		$this->replace_cdns		= $replace_cdns;
		$this->reuse_libs		= $reuse_libs;
		$this->min_files		= $min_files;
		
		$this->relative			= $relative;
		$this->https			= $https;
	}
	
	
	/**
	 * exclude assets that should not be rewritten
	 *
	 * @since   0.1
	 *
	 * @param   string  $asset  current asset
	 * @return  boolean  true if need to be excluded
	 */
	
	protected function exclude_asset( $asset )
	{
		foreach( $this->excludes as $exclude )
		{
			if( !!$exclude && ( stristr( $asset , $exclude ) != false ) )
			{
				return true;
			}
		}
		
		return false;
	}
	
	
	/**
	 * relative url
	 *
	 * @since   0.1
	 *
	 * @param   string  $url a full url
	 * @return  string  protocol relative url
	 */
	protected function relative_url( $url )
	{
		return substr( $url , strpos( $url , '//' ) );
	}
	
	
	/**
	 * get directory scope
	 *
	 * @since   0.1
	 *
	 * @return  string  directory scope
	 */
	
	protected function get_dir_scope( )
	{
		$input	= array_filter( array_map( 'trim' , explode( ',' , $this->dirs ) ) );
		
		if( $input === [] )
		{
			return 'wp\-content|wp\-includes';
		}
		
		return implode( '|' , array_map( 'quotemeta' , $input ) );
	}
	
	
	/**
	 * rewrite url
	 *
	 * @since   0.1
	 *
	 * @param   string  $html  current raw HTML doc
	 * @return  string  updated HTML doc with CDN links
	 */
	
	public function rewrite( $html )
	{
		static $regex	= [];
		
		
		// check if HTTPS and use CDN over HTTPS enabled
		//if( !$this->https && isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' )
		//{
		//	return $html;
		//}
		
		if( !( strlen( $this->pagecdn_api_key ) && strlen( $this->cdn_url ) ) )
		{
			return $html;
		}
		
		if( !count( $regex ) )
		{
			$dirs		= $this->get_dir_scope( );
			
			$blog_url	= ( $this->https ? '(https?:|)' : '(http:|)' ) . quotemeta( $this->relative_url( $this->blog_url ) );
			
			if( strpos( $blog_url , '//www\\.' ) !== false )
			{
				$blog_url	= str_replace( '//www\\.' , '//(www\\.)?' , $blog_url );
			}
			else
			{
				$blog_url	= str_replace( '//' , '//(www\\.)?' , $blog_url );
			}
			
			$regex_rule	= '#(?<=[(\"\'])';
			
			if( $this->relative )
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
			
			$public_hosts	= [	'cdn\.jsdelivr\.net'			,				#	https://www.jsdelivr.com/
								'cdnjs\.cloudflare\.com'		,				#	https://cdnjs.com/
								'ajax\.aspnetcdn\.com'			,				#	https://docs.microsoft.com/en-us/aspnet/ajax/cdn/overview
								'ajax\.googleapis\.com'			,				#	https://developers.google.com/speed/libraries/
								'stackpath\.bootstrapcdn\.com'	,				#	https://www.bootstrapcdn.com/
								'maxcdn\.bootstrapcdn\.com'		,				#	https://www.bootstrapcdn.com/
								'code\.jquery\.com'				,				#	https://jquery.com/download/
								'cdn\.bootcss\.com'				,				#	
								'unpkg\.com'					,				#	https://unpkg.com
								'use\.fontawesome\.com'			,				#	https://fontawesome.com
								'cdn\.rawgit\.com'				,				#	https://rawgit.com
								'cdn\.staticfile\.org'			,				#	http://staticfile.org/
								'apps\.bdimg\.com'				,				#	http://apps.static-bdimg.com/
								'yastatic\.net'					,				#	https://tech.yandex.ru/jslibs/
								'cdn\.mathjax\.org'				];
			
			$regex['public_scripts']	= "#<script[^>]*src=[\"']((?:https?:|)//(?:".implode('|',$public_hosts).")[^\"']+)[\"'][^>]*>#";
			
			$regex['public_styles']		= "#<link[^>]*href=[\"']((?:https?:|)//(?:".implode('|',$public_hosts).")[^\"']+)[\"'][^>]*>#";
		}
		
		
		if( $this->fonts )
		{
			$html	= preg_replace_callback( $regex['fonts'] , [$this, 'rewrite_fonts'] , $html );
		}
		
		if( $this->replace_cdns )
		{
			$html	= preg_replace_callback( $regex['public_scripts'] , [$this, 'rewrite_public_cdns'] , $html );
			
			$html	= preg_replace_callback( $regex['public_styles'] , [$this, 'rewrite_public_cdns'] , $html );
		}
		
		
		$html	= preg_replace_callback( $regex['site'] , [$this, 'rewrite_url'] , $html );
		
		return $html;
	}
	
	
	
	/**
	 * rewrite url
	 *
	 * @since   0.1
	 *
	 * @param   string  $asset  current asset
	 * @return  string  updated url if not excluded
	 */
	
	protected function rewrite_url( $asset )
	{
		$url	= $asset[0];
		
		if( $this->exclude_asset( $url ) )
		{
			return $url;
		}
		
		if( is_admin_bar_showing( ) && array_key_exists( 'preview' , $_GET ) && ( $_GET['preview'] === 'true' ) )
		{
			return $url;
		}
		
		$blog_url		= $this->relative_url( $this->blog_url );
		
		$full_blog_url	= $this->blog_url;
		
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
		
		if( $this->reuse_libs )
		{
			$url	= $this->cached_url( $url );
			
			if( !( strpos( $url , $full_blog_url ) === 0 ) )
			{
				return $url;
			}
		}
		
		return str_replace( $full_blog_url , $this->cdn_url , $url );
	}
	
	
	/**
	 * rewrite fonts
	 *
	 * @since   0.1
	 *
	 * @param   string  $match  current match
	 * @return  string  updated fonts url
	 */
	
	protected function rewrite_fonts( $match ) {
		
		static $families	= [];
		
		$pagecdn_base		= 'https://pagecdn.io/lib/easyfonts/';
		
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
				
				$family_name	= strtolower( $ifamily );
				
				if( !isset( $families[$family_name] ) )
				{
					$families[$family_name]	= false;
				}
			}
			
			$return	= '';
			
			foreach( $families as $family_name => $already_included )
			{
				if( !$already_included )
				{
					$return	.= '<link rel="stylesheet" href="'. $pagecdn_base . str_replace( ' ' , '-' , $family_name ). '.css' .'" />';
					
					$families[$family_name]	= true;
				}
			}
			
			return $return;
		}
	}
	
	protected function rewrite_public_cdns( $url )
	{
		return str_replace( $url[1] , $this->cached_url( $url[1] ) , $url[0] );
	}
	
	protected function cached_url( $url )
	{
		static $cache = [];
		
		if( !$cache )
		{
			@$data	= file_get_contents( PAGECDN_CACHE );
			
			if( $data && strlen( $data ) )
			{
				$cache	= unserialize( $data );
			}
		}
		
		#	Remove Query String
		
		if( strpos( $url , '?' ) !== false )
		{
			$url	= substr( $url , 0 , strpos( $url , '?' ) );
		}
		
		if( ( substr( $url , -4 ) !== '.css' ) && ( substr( $url , -3 ) !== '.js' ) )
		{
			return $url;
		}
		
		if( strpos( $url , '//' ) === 0 )
		{
			$url	= 'http:' . $url;
		}
		
		if( strpos( $url , '//' ) === false )
		{
			$url	= $this->blog_url . $url;
		}
		
		
		if( isset( $cache[$url] ) )
		{
			return $cache[$url];
		}
		
		$optimized	= '';
		
		if( $this->min_files )
		{
			$optimized	= '&optimized=true';
		}
		
		$response	= wp_remote_get( "https://pagecdn.com/api/v1/opensource/lookup?match=url&url=".rawurlencode($url)."&apikey=".$this->pagecdn_api_key.$optimized );
		
		if( !is_wp_error( $response ) )
		{
			$response	= json_decode( $response['body'] , true );
			
			if( isset( $response['status'] ) && ( $response['status'] == '200' ) )
			{
				$response	= $response['response'];
				
				if( isset( $response['count'] ) && $response['count'] )
				{
					if( isset( $response['files'][0]['file_url'] ) )
					{
						$cache[$url]	= $response['files'][0]['file_url'];
					}
				}
			}
		}
		
		if( !isset( $cache[$url] ) )
		{
			$cache[$url]	= $url;
		}
		
		$data	= serialize( $cache );
		
		file_put_contents( PAGECDN_CACHE , $data );
		
		return $cache[$url];
	}
	
}
