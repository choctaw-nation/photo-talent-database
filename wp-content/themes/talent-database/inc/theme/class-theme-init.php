<?php
/**
 * Initializes the Theme
 *
 * @package ChoctawNation
 */

namespace ChoctawNation;

/** Builds the Theme */
class Theme_Init {
	/** The type of site
	 *
	 * @var 'nation'|'commerce' $theme_type
	 */
	private string $theme_type;

	/** Constructor Function that also loads the proper favicon package
	 *
	 * @param 'nation'|'commerce' $type the type of site to load favicons for.
	 */
	public function __construct( string $type ) {
		$this->theme_type = $type;
	}

	/**
	 * Sets up the theme (callback for after_setup_theme action hook)
	 */
	public function setup_theme(): void {
		$this->disable_discussion();
		$this->load_favicons();
		$this->cno_theme_support();
		$this->allow_svg();
		$this->handle_plugins();
		$this->handle_gutenberg();
		$this->edit_roles();
		$this->load_cron_events();
		require_once get_template_directory() . '/inc/theme/theme-functions.php';
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );
		add_action( 'enqueue_block_assets', array( $this, 'enqueue_global_assets' ) );
		add_action( 'init', array( $this, 'alter_post_types' ) );
		$rest_router = new Rest_Router();
		add_action( 'rest_api_init', array( $rest_router, 'register_routes' ) );
		add_filter( 'wp_speculation_rules_configuration', array( $this, 'handle_speculative_loading' ) );
		add_filter( 'allowed_redirect_hosts', array( $this, 'add_allowed_redirect_hosts' ) );
		add_filter( 'auto_update_plugin', array( $this, 'handle_auto_update_plugin' ) );
		add_filter( 'wp_resource_hints', array( $this, 'add_resource_hints' ), 10, 2 );
		add_filter( 'style_loader_tag', array( $this, 'preload_stylesheets' ), 10, 3 );
	}

	/**
	 * Load favicons based on the type of site
	 */
	private function load_favicons() {
		add_action(
			'wp_head',
			function () {
				$href = get_stylesheet_directory_uri() . '/img/favicons';
				switch ( $this->theme_type ) {
					case 'commerce':
						$href .= '/commerce';
						break;
					case 'nation':
						$href .= '/nation';
						break;
					default:
				}
				echo "<link rel='apple-touch-icon' sizes='180x180' href='{$href}/apple-touch-icon.png'>
				<link rel='icon' type='image/png' sizes='192x192' href='{$href}/android-chrome-192x192.png'>
				<link rel='icon' type='image/png' sizes='512x512' href='{$href}/android-chrome-512x512.png'>
				<link rel='icon' type='image/png' sizes='32x32' href='{$href}/favicon-32x32.png'>
				<link rel='icon' type='image/png' sizes='16x16' href='{$href}/favicon-16x16.png'>
				<link rel='mask-icon' href='{$href}/safari-pinned-tab.svg' color='#000000'>";
			}
		);
	}

	/** Remove comments, pings and trackbacks support from posts types. */
	private function disable_discussion() {
		// Close comments on the front-end
		add_filter(
			'comments_open',
			fn( $open, $post_id ) => 'talent-list' === get_post_type( $post_id ),
			20,
			2
		);
		add_filter( 'pings_open', '__return_false', 20, 2 );

		// Hide existing comments.
		add_filter(
			'comments_array',
			fn( $comments, $post_id ) => 'talent-list' === get_post_type( $post_id ) ? $comments : array(),
			10,
			2
		);

		// Remove comments page in menu.
		add_action(
			'admin_menu',
			function () {
				remove_menu_page( 'edit-comments.php' );
			}
		);

		// Remove comments links from admin bar.
		add_action(
			'init',
			function () {
				if ( is_admin_bar_showing() ) {
					remove_action( 'admin_bar_menu', 'wp_admin_bar_comments_menu', 60 );
				}
			}
		);
	}

	/**
	 * Allows SVG uploads and fixes display in the admin.
	 */
	private function allow_svg() {
		$svg_handler = new Allow_SVG();
		add_filter( 'upload_mimes', array( $svg_handler, 'cc_mime_types' ) );
		add_action( 'admin_head', array( $svg_handler, 'fix_svg' ) );
	}

	/**
	 * Handles Plugins related theme supports and assets.
	 */
	private function handle_plugins() {
		// ACF
		if ( defined( 'ACF_PRO' ) && defined( 'ACF_VERSION' ) ) {
			$acf_handler = new Plugins\ACF_Handler();
			$acf_handler->init_save_filters();
			add_filter( 'acf/settings/load_json', array( $acf_handler, 'load_json_paths' ) );
		}

		// Yoast
		add_filter( 'wpseo_metabox_prio', fn() => 'low' );
	}

	/**
	 * Handles Gutenberg related theme supports and assets.
	 */
	private function handle_gutenberg() {
		$gutenberg_handler = new Gutenberg_Handler();
		$gutenberg_handler->cno_block_theme_support();
		$hooks = array(
			'filters' => array(
				'block_editor_settings_all'      => 'restrict_gutenberg_ui',
				'allowed_block_types_all'        => array( 'restrict_block_types', array( 10, 2 ) ),
				'use_block_editor_for_post_type' => 'handle_page_templates',
			),
		);
		foreach ( $hooks as $hook_list ) {
			foreach ( $hook_list as $hook => $method ) {
				if ( is_array( $method ) ) {
					add_action( $hook, array( $gutenberg_handler, $method[0] ), ...$method[1] );
				} else {
					add_action( $hook, array( $gutenberg_handler, $method ) );
				}
			}
		}
	}

	/**
	 * Creates custom roles and adds capabilities
	 */
	private function edit_roles() {
		$role_editor = new Role_Editor();
		add_action( 'init', array( $role_editor, 'create_custom_roles' ) );
	}

	/**
	 * Loads and wires up cron events for the theme
	 */
	private function load_cron_events() {
		$cron_events = new Cron_Events();
		$cron_events->schedule_events();
		$cron_events->wire_actions();
	}

	/**
	 * Adds scripts with the appropriate dependencies
	 */
	public function enqueue_frontend_assets() {
		new Asset_Loader(
			'bootstrap',
			Enqueue_Type::both,
			'vendors',
			array(
				'scripts' => array(),
				'styles'  => array(),
			)
		);
		if ( is_user_logged_in() ) {
			new Asset_Loader(
				'global',
				Enqueue_Type::both,
				null,
				array(
					'scripts' => array( 'bootstrap' ),
					'styles'  => array( 'bootstrap' ),
				)
			);
			wp_localize_script(
				'global',
				'cnoApi',
				array(
					'nonce' => wp_create_nonce( 'wp_rest' ),
				)
			);
		} else {
			new Asset_Loader( 'global', Enqueue_Type::style, null, array( 'bootstrap' ) );
		}

		// style.css
		wp_enqueue_style(
			'main',
			get_stylesheet_uri(),
			array( 'global' ),
			wp_get_theme()->get( 'Version' )
		);

		$this->remove_wordpress_styles(
			array(
				'classic-theme-styles',
				'dashicons',
			)
		);
	}

	/** Enqueues Assets needed by both the Block Editor and the Front End */
	public function enqueue_global_assets() {
		wp_enqueue_style(
			'typekit',
			'https://use.typekit.net/jky5sek.css',
			array(),
			null // phpcs:ignore
		);
	}

	/**
	 * Provide an array of handles to dequeue.
	 *
	 * @param array $handles the script/style handles to dequeue.
	 */
	private function remove_wordpress_styles( array $handles ) {
		foreach ( $handles as $handle ) {
			wp_dequeue_style( $handle );
		}
	}

	/** Registers Theme Supports */
	public function cno_theme_support() {
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'title-tag' );
		register_nav_menus(
			array(
				'primary_menu' => __( 'Primary Menu', 'cno' ),
				'footer_menu'  => __( 'Footer Menu', 'cno' ),
			)
		);
	}

	/** Remove post type support from posts types. */
	public function alter_post_types() {
		$post_types = array(
			'post',
			'page',
		);
		foreach ( $post_types as $post_type ) {
			$this->disable_post_type_support( $post_type );
		}
		$post_override = new Post_Override();
		$post_override->alter_post_types();
	}

	/**
	 * Disable post-type-supports from posts
	 *
	 * @param string $post_type the post type to remove supports from.
	 */
	private function disable_post_type_support( string $post_type ) {
		$supports = array(
			'comments',
			'trackbacks',
			'revisions',
			'author',
		);
		foreach ( $supports as $support ) {
			if ( post_type_supports( $post_type, $support ) ) {
				remove_post_type_support( $post_type, $support );
			}
		}
	}

	/**
	 * Handle speculative loading
	 *
	 * @since WP 6.8.0
	 * @link https://make.wordpress.org/core/2025/03/06/speculative-loading-in-6-8/
	 *
	 * @param ?array $config the configuration array. Null if user is logged-in.
	 * @return ?array The new config file, or null
	 */
	public function handle_speculative_loading( ?array $config ): ?array {
		if ( is_array( $config ) ) {
			$config['mode']      = 'auto';
			$config['eagerness'] = 'moderate';
		}
		return $config;
	}

	/**
	 * Adds allowed redirect hosts for `wp_safe_redirect`
	 *
	 * @param array $hosts Current allowed hosts.
	 * @return array
	 */
	public function add_allowed_redirect_hosts( array $hosts ): array {
		$allowed_hosts = array(
			'choctawnation.com',
			'www.choctawnation.com',
		);
		return array_merge( $hosts, $allowed_hosts );
	}

	/**
	 * Disable certain plugins based on the environment type.
	 */
	public function disable_plugins_per_environment() {
		$env = wp_get_environment_type();
		if ( 'production' === $env ) {
			return;
		}

		$plugins_to_disable = array(
			'wordfence/wordfence.php'                 => array( 'local', 'development', 'staging' ),
			'wp-mail-smtp-pro/wp_mail_smtp.php'       => array( 'local', 'development', 'staging' ),
			'google-site-kit/google-site-kit.php'     => array( 'local', 'development', 'staging' ),
			'autoupdater/autoupdater.php'             => array( 'local', 'development', 'staging' ),
			'autoptimize/autoptimize.php'             => array( 'local', 'development' ),
			'wordpress-seo/wp-seo.php'                => array( 'local', 'development' ),
			'yoast-test-helper/yoast-test-helper.php' => array( 'local', 'development' ),
		);

		foreach ( $plugins_to_disable as $plugin => $environments ) {
			if ( in_array( $env, $environments, true ) ) {
				if ( is_plugin_active( $plugin ) ) {
					deactivate_plugins( $plugin );
				}
			}
		}
	}

	/**
	 * Handle automatic plugin updates based on environment.
	 *
	 * @param ?bool $update Whether to update the plugin.
	 * @return bool
	 */
	public function handle_auto_update_plugin( ?bool $update ): ?bool {
		if ( 'production' === wp_get_environment_type() ) {
			return $update;
		}
		return true;
	}

	/**
	 * Add resource hints for Typekit
	 *
	 * @param array                                              $hints         The array of resource hints.
	 * @param 'dns-prefetch'|'preconnect'|'prefetch'|'prerender' $relation_type The relation type the hints are for.
	 * @return array The modified array of resource hints.
	 */
	public function add_resource_hints( array $hints, string $relation_type ) {
		if ( 'preconnect' === $relation_type ) {
			$hints[] = array(
				'href'        => 'https://use.typekit.net',
				'crossorigin' => 'anonymous',
			);
		}
		return $hints;
	}

	/**
	 * Preload specific stylesheets
	 *
	 * @param string $html   The link tag HTML.
	 * @param string $handle The style handle.
	 * @param string $href   The stylesheet URL.
	 * @return string The modified link tag HTML.
	 */
	public function preload_stylesheets( string $html, string $handle, string $href ): string {
		$preload_handles = array(
			'typekit'   => 'external',
			'bootstrap' => null,
		);
		if ( in_array( $handle, array_keys( $preload_handles ), true ) ) {
			$is_crossorigin = 'external' === $preload_handles[ $handle ];
			// Add a preload link before the stylesheet link.
			$preload = sprintf(
				"<link rel='preload' as='style' href='%s' %s />\n",
				$href,
				$is_crossorigin ? 'crossorigin="anonymous"' : ''
			);
			// Add crossorigin attribute if needed.
			if ( $is_crossorigin && ! str_contains( $html, 'crossorigin' ) ) {
				$html = str_replace( "/>\n", ' crossorigin="anonymous" />' . "\n", $html );
			}
			$html = $preload . $html;
		}
		return $html;
	}
}
