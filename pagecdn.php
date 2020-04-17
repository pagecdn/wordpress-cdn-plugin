<?php
/*
	Plugin Name: Easy Speedup by PageCDN
	Text Domain: pagecdn
	Description: Speedup WordPress websites with PageCDN's best-in-class Content, Delivery and Cache optimizations.
	Author: PageCDN
	Author URI: https://pagecdn.com
	License: GPLv2 or later
	Version: 5.2
*/
	
	defined( 'ABSPATH' ) OR exit;
	
	define( 'PAGECDN_FULL_NAME'		, 'PageCDN - Easy Speedup'						);
	define( 'PAGECDN_FILE'			, __FILE__										);
	define( 'PAGECDN_DIR'			, dirname( __FILE__ )							);
	define( 'PAGECDN_BASE'			, plugin_basename( __FILE__ )					);
	define( 'PAGECDN_MIN_WP'		, '4.3'											);
	//define( 'PAGECDN_CACHE'			, WP_CONTENT_DIR . '/cache/pagecdn/cache.json'	);
	//define( 'PAGECDN_IMG_CACHE'		, WP_CONTENT_DIR . '/cache/pagecdn/images.json'	);
	//define( 'PAGECDN_WEBP_CACHE'	, WP_CONTENT_DIR . '/cache/pagecdn/webp.json'	);
	define( 'PAGECDN_VER'			, '5.2'											);
	
	$PageCDN_origin_scheme		= strtolower( parse_url( home_url( ) , PHP_URL_SCHEME ) );
	$PageCDN_origin_host		= parse_url( home_url( ) , PHP_URL_HOST );
	
	$PageCDN_normalized_origin_host	= strtolower( $PageCDN_origin_host );
	
	if( strpos( $PageCDN_normalized_origin_host , 'www.' ) === 0 )
	{
		$PageCDN_normalized_origin_host	= substr( $PageCDN_normalized_origin_host , 4 );
	}
	
	$PageCDN_settings_error		= false;
	
	$PageCDN_fonts				= array();
	
	$PageCDN_check_hosts		= array(	"https://www.{$PageCDN_normalized_origin_host}"		,
											"https://{$PageCDN_normalized_origin_host}"			,
											"http://www.{$PageCDN_normalized_origin_host}"		,
											"http://{$PageCDN_normalized_origin_host}"			,
											"//www.{$PageCDN_normalized_origin_host}"			,
											"//{$PageCDN_normalized_origin_host}"				);
	
	require PAGECDN_DIR . '/init-functions.php';
	require PAGECDN_DIR . '/functions.php';
	require PAGECDN_DIR . '/font-functions.php';
	require PAGECDN_DIR . '/admin-functions.php';
	
	$PageCDN_known_URLs				= array();
	
	$PageCDN_known_img_URLs			= array();
	
	$PageCDN_known_webp_URLs		= array();
	
	$PageCDN_discovered_new_URLs	= false;
	
	$PageCDN_max_font_URLs			= 3;
	
	$PageCDN_webp_support			= isset( $_SERVER['HTTP_ACCEPT'] ) && ( strpos( $_SERVER['HTTP_ACCEPT'] , 'image/webp' ) !== false );
	
	
	$PageCDN_known_URLs				= get_option( 'pagecdn-cache' , array( ) );
	
	$PageCDN_known_img_URLs			= get_option( 'pagecdn-image-cache' , array( ) );
	
	$PageCDN_known_webp_URLs		= get_option( 'pagecdn-webp-cache' , array( ) );
	
	/*
	if( !file_exists( PAGECDN_CACHE ) )
	{
		PageCDN_create_fs_hierarchy( dirname( PAGECDN_CACHE ) );
		
		$PageCDN_empty_file	= json_encode( array() );
		
		file_put_contents( PAGECDN_CACHE , $PageCDN_empty_file );
		
		file_put_contents( PAGECDN_IMG_CACHE , $PageCDN_empty_file );
		
		file_put_contents( PAGECDN_WEBP_CACHE , $PageCDN_empty_file );
	}
	
	$PageCDN_temp	= file_get_contents( PAGECDN_CACHE );
	
	if( $PageCDN_temp && strlen( $PageCDN_temp ) )
	{
		$PageCDN_known_URLs	= json_decode( $PageCDN_temp , true );
	}
	
	unset( $PageCDN_temp );
	
	
	$PageCDN_temp	= file_get_contents( PAGECDN_IMG_CACHE );
	
	if( $PageCDN_temp && strlen( $PageCDN_temp ) )
	{
		$PageCDN_known_img_URLs	= json_decode( $PageCDN_temp , true );
	}
	
	unset( $PageCDN_temp );
	
	
	$PageCDN_temp	= file_get_contents( PAGECDN_WEBP_CACHE );
	
	if( $PageCDN_temp && strlen( $PageCDN_temp ) )
	{
		$PageCDN_known_webp_URLs	= json_decode( $PageCDN_temp , true );
	}
	
	unset( $PageCDN_temp );
	
	*/
	
	
	PageCDN_hooks( );
	
	