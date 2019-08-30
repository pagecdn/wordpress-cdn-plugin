<?php
/*
   Plugin Name: PageCDN - Easy Speedup
   Text Domain: pagecdn
   Description: Super speedup WordPress sites with PageCDN's public and private Content Delivery Network and the best-in-class features.
   Author: PageCDN
   Author URI: https://pagecdn.com
   License: GPLv2 or later
   Version: 1.0
 */
	
	defined( 'ABSPATH' ) OR exit;
	
	define( 'PAGECDN_FILE'			, __FILE__							);
	define( 'PAGECDN_DIR'			, dirname( __FILE__ )				);
	define( 'PAGECDN_BASE'			, plugin_basename( __FILE__ )		);
	define( 'PAGECDN_MIN_WP'		, '3.8'								);
	define( 'PAGECDN_CACHE'			, PAGECDN_DIR . '/cache/cache.json'	);
	
	require PAGECDN_DIR . '/functions.php';
	
	$PageCDN_fonts		= [];
	
	PageCDN_hooks( );
	
	