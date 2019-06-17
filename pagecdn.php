<?php
/*
   Plugin Name: PageCDN - Better CDN Plugin
   Text Domain: pagecdn
   Description: Optimize public and private content delivery with PageCDN.
   Author: PageCDN
   Author URI: https://pagecdn.com
   License: GPLv2 or later
   Version: 1.0
 */

/*
   Copyright (C)  2019 PageCDN

   This program is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License along
   with this program; if not, write to the Free Software Foundation, Inc.,
   51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */
	
	defined( 'ABSPATH' ) OR exit;
	
	define( 'PAGECDN_FILE'		, __FILE__						);
	define( 'PAGECDN_DIR'		, dirname( __FILE__ )			);
	define( 'PAGECDN_BASE'		, plugin_basename( __FILE__ )	);
	define( 'PAGECDN_MIN_WP'	, '3.8'							);
	
	define( 'PAGECDN_CACHE'		, PAGECDN_DIR . '/cache/cache.dat'	);
	
	/* loader */
	add_action( 'plugins_loaded' , [ 'PageCDN' , 'instance' ] );
	
	/* uninstall */
	register_uninstall_hook( __FILE__ , [ 'PageCDN' , 'handle_uninstall_hook' ] );
	
	/* activation */
	register_activation_hook( __FILE__ , [ 'PageCDN' , 'handle_activation_hook' ] );
	
	add_action( 'wp_head' , function () { echo "\n\n<link rel='preconnect' href='https://pagecdn.io' crossorigin />\n\n"; } , 0);
	
	require_once PAGECDN_DIR . '/inc/pagecdn.php';
	require_once PAGECDN_DIR . '/inc/pagecdn_rewriter.php';
	require_once PAGECDN_DIR . '/inc/pagecdn_settings.php';
	
	/*
	spl_autoload_register('PageCDN_autoload');
	
	function PageCDN_autoload($class) {
		if ( in_array($class, ['PageCDN', 'PageCDN_Rewriter', 'PageCDN_Settings']) ) {
			require_once(
				sprintf(
					'%s/inc/%s.php',
					PAGECDN_DIR,
					strtolower($class)
				)
			);
		}
	}
	
	*/
	
