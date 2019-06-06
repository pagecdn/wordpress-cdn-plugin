<?php

/**
 * PageCDN
 *
 * @since 0.1
 */

class PageCDN
{
	/**
	 * pseudo-constructor
	 *
	 * @since   0.1
	 */
	
	public static function instance() {
		new self();
	}
	
	
	/**
	 * constructor
	 *
	 * @since   0.1
	 */
	
	public function __construct() {
		/* CDN rewriter hook */
		add_action(
			'template_redirect',
			[
				__CLASS__,
				'handle_rewrite_hook',
			]
		);
		
		/* Rewrite rendered content in REST API */
		add_filter(
			'the_content',
			[
				__CLASS__,
				'rewrite_the_content',
			],
			100
		);
		
		/* Hooks */
		add_action(
			'admin_init',
			[
				__CLASS__,
				'register_textdomain',
			]
		);
		add_action(
			'admin_init',
			[
				'PageCDN_Settings',
				'register_settings',
			]
		);
		add_action(
			'admin_menu',
			[
				'PageCDN_Settings',
				'add_settings_page',
			]
		);
		add_filter(
			'plugin_action_links_' . PAGECDN_BASE,
			[
				__CLASS__,
				'add_action_link',
			]
		);
		
		/* admin notices */
		add_action(
			'all_admin_notices',
			[
				__CLASS__,
				'requirements_check',
			]
		);
		
		/* add admin purge link */
		add_action(
			'admin_bar_menu',
			[
				__CLASS__,
				'add_admin_links',
			],
			150
		);
		/* process purge request */
		add_action(
			'admin_notices',
			[
				__CLASS__,
				'process_purge_request',
			]
		);
	}
	
	
	/**
	 * add Zone purge link
	 *
	 * @since   0.1
	 *
	 * @hook	mixed
	 *
	 * @param   object  menu properties
	 */
	
	public static function add_admin_links($wp_admin_bar) {
		Global $wp;
		
		$options = self::get_options();
		
		// check user role
		if ( !is_admin_bar_showing() || !apply_filters('user_can_clear_cache' , current_user_can('manage_options') ) ) {
			return;
		}
		
		if( !array_key_exists( 'pagecdn_api_key' , $options ) || strlen( $options['pagecdn_api_key'] ) !== 64 ) {
			return;
		}
		
		// redirect to admin page if necessary so we can display notification
		$current_url	= ( isset( $_SERVER['HTTPS'] ) ? 'https' : 'http' ) . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		
		$goto_url		= get_admin_url();
		
		if( stristr( $current_url , get_admin_url() ) )
		{
			$goto_url = $current_url;
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
		
		//if ( ! is_admin() ) {
		//	// add admin purge link
		//	$wp_admin_bar->add_menu(
		//		[
		//			'id'	  => 'purge-pagecdn',
		//			'href'   => wp_nonce_url( add_query_arg('pagecdn_action', 'purge', $goto_url ) , '_cdn__purge_nonce' ),
		//			'parent' => 'top-secondary',
		//			'parent' => false,
		//			'title'	 => '<span class="ab-item">'.esc_html__('Purge CDN', 'pagecdn').'</span>',
		//			'meta'   => ['title' => esc_html__('Purge CDN', 'pagecdn')],
		//		]
		//	);
		//}
	}
	
	
	/**
	 * process purge request
	 *
	 * @since   0.1
	 *
	 * @param   array  $data  array of metadata
	 */
	
	public static function process_purge_request( $data )
	{
		if( !( isset( $_GET['pagecdn_action'] ) && $_GET['pagecdn_action'] === 'purge' ) )
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
		
		
		$options	= self::get_options( );
		
		// load if network
		if ( ! function_exists('is_plugin_active_for_network') ) {
			require_once( ABSPATH. 'wp-admin/includes/plugin.php' );
		}
		
		
		$repo		= rawurlencode( ltrim( parse_url( $options['url'] , PHP_URL_PATH ) , '/' ) );
		
		$apikey		= rawurlencode( $options['pagecdn_api_key'] );
		
		file_put_contents( PAGECDN_CACHE , serialize( [] ) );
		
		$response	= wp_remote_get( "https://pagecdn.com/api/v1/private/repo/delete-files?repo={$repo}&apikey={$apikey}" , [ 'timeout' => 20 ] );
		
		if( is_wp_error( $response ) )
		{
			printf( '<div class="notice notice-error is-dismissible"><p>%s</p></div>',
					esc_html__('Error connecting to API - '. $response->get_error_message(), 'pagecdn')
					);
			
			return;
		}
		
		#	check HTTP response
		if( is_array( $response ) && is_admin_bar_showing( ) )
		{
			$json	= json_decode( $response['body'] , true );
			
			$rc		= ( int ) wp_remote_retrieve_response_code( $response );
			
			if( $rc == 200 )
			{
				printf( '<div class="notice notice-warning is-dismissible"><p>%s</p></div>',
						esc_html__('Files purged from PageCDN cache.')
						);
				
				return;
			}
			
			$custom_messages = array(
				400	=> 'PageCDN cannot process the request due to an error in the issued request.'						,
				401	=> 'You are not authorized to perform Purge operation.'												,
				403	=> 'You do not have sufficient permission to perform Purge operation.'								,
				500	=> 'Some error occured at PageCDN end. If you continue to see this error, please contact support.'	,
				503	=> 'PageCDN service is not available temporarily.'													,
			);
			
			if( isset( $custom_messages[$rc] ) )
			{
				printf( '<div class="notice notice-error is-dismissible"><p>%s</p></div>',
						esc_html__('HTTP returned '. $rc .': '.$custom_messages[$rc], 'pagecdn')
						);
				
				return;
			}
			
			printf( '<div class="notice notice-error is-dismissible"><p>%s</p></div>',
					esc_html__('HTTP returned '. $rc)
					);
			
			return;
		}
		
		if( !is_admin( ) )
		{
			wp_safe_redirect( remove_query_arg( '_cache' , wp_get_referer( ) ) );
			
			die;
		}
	}
	
	
	/**
	 * add action links
	 *
	 * @since   0.1
	 *
	 * @param   array  $data  alreay existing links
	 * @return  array  $data  extended array with links
	 */
	
	public static function add_action_link($data) {
		// check permission
		if ( ! current_user_can('manage_options') ) {
			return $data;
		}
		
		return array_merge(
			$data,
			[
				sprintf(
					'<a href="%s">%s</a>',
					add_query_arg(
						[
							'page' => 'pagecdn',
						],
						admin_url('options-general.php')
					),
					__("Settings")
				),
			]
		);
	}
	
	
	/**
	 * run uninstall hook
	 *
	 * @since   0.1
	 */
	
	public static function handle_uninstall_hook() {
		delete_option('pagecdn');
	}
	
	
	/**
	 * run activation hook
	 *
	 * @since   0.1
	 */
	
	public static function handle_activation_hook() {
		add_option(
			'pagecdn',
			[
				'url'				=> get_option('home')			,
				'dirs'				=> 'wp-content,wp-includes'		,
				'excludes'			=> '.php,.xml'					,
				'pagecdn_api_key'	=> ''							,
				
				'fonts'				=> '1'							,
				'replace_cdns'		=> '1'							,
				'reuse_libs'		=> '1'							,
				'min_files'			=> '1'							,
				
				'relative'			=> '1'							,
				'https'				=> '1'							,
			]
		);
	}
	
	
	/**
	 * check plugin requirements
	 *
	 * @since   0.1
	 */
	
	public static function requirements_check() {
		// WordPress version check
		if ( version_compare($GLOBALS['wp_version'], PAGECDN_MIN_WP.'alpha', '<') ) {
			show_message(
				sprintf(
					'<div class="error"><p>%s</p></div>',
					sprintf(
						__("PageCDN Wordpress Plugin is optimized for WordPress %s. Please disable the plugin or upgrade your WordPress installation (recommended).", "pagecdn"),
						PAGECDN_MIN_WP
					)
				)
			);
		}
	}
	
	
	/**
	 * register textdomain
	 *
	 * @since   0.1
	 */
	
	public static function register_textdomain() {
		load_plugin_textdomain(
			'pagecdn',
			false,
			'pagecdn/lang'
		);
	}
	
	
	/**
	 * return plugin options
	 *
	 * @since   0.1
	 *
	 * @return  array  $diff  data pairs
	 */
	
	public static function get_options() {
		return wp_parse_args(
			get_option('pagecdn'),
			[
				'url'				=> get_option('home')			,
				'dirs'				=> 'wp-content,wp-includes'		,
				'excludes'			=> '.php,.xml'					,
				'pagecdn_api_key'	=> ''							,
				
				'fonts'				=> '1'							,
				'replace_cdns'		=> '1'							,
				'reuse_libs'		=> '1'							,
				'min_files'			=> '1'							,
				
				'relative'			=> '1'							,
				'https'				=> '1'							,
			
			]
		);
	}
	
	
	/**
	 * return new rewriter
	 *
	 * @since   0.1
	 *
	 */
	
	public static function get_rewriter() {
		$options = self::get_options();

		$excludes = array_map('trim', explode(',', $options['excludes']));

		return new PageCDN_Rewriter(
			get_option('home'),
			$options['url'],
			$options['dirs'],
			$excludes,
			$options['pagecdn_api_key'],
			
			$options['fonts'],
			$options['replace_cdns'],
			$options['reuse_libs'],
			$options['min_files'],
			
			$options['relative'],
			$options['https']
		);
	}
	
	
	/**
	 * run rewrite hook
	 *
	 * @since   0.1
	 */
	
	public static function handle_rewrite_hook() {
		$options = self::get_options();
		
		// check if origin equals cdn url
		if (get_option('home') == $options['url']) {
			return;
		}
		
		$rewriter = self::get_rewriter();
		ob_start(array(&$rewriter, 'rewrite'));
	}
	
	
	/**
	 * rewrite html content
	 *
	 * @since   0.1
	 */
	
	public static function rewrite_the_content($html) {
		$rewriter = self::get_rewriter();
		return $rewriter->rewrite($html);
	}
	
}
