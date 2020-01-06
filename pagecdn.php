<?php
/*
	Plugin Name: Easy Speedup by PageCDN
	Text Domain: pagecdn
	Description: Speedup WordPress websites with PageCDN's best-in-class 'content' and 'delivery' optimizations.
	Author: PageCDN
	Author URI: https://pagecdn.com
	License: GPLv2 or later
	Version: 4.0
*/
	
	defined( 'ABSPATH' ) OR exit;
	
	define( 'PAGECDN_FULL_NAME'		, 'PageCDN - Easy Speedup'			);
	define( 'PAGECDN_FILE'			, __FILE__							);
	define( 'PAGECDN_DIR'			, dirname( __FILE__ )				);
	define( 'PAGECDN_BASE'			, plugin_basename( __FILE__ )		);
	define( 'PAGECDN_MIN_WP'		, '4.3'								);
	define( 'PAGECDN_CACHE'			, PAGECDN_DIR . '/cache/cache.json'	);
	
	$PageCDN_settings_error		= false;
	
	require PAGECDN_DIR . '/functions.php';
	require PAGECDN_DIR . '/admin-functions.php';
	
	$PageCDN_fonts		= array();
	
	PageCDN_hooks( );
	
	
	