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
	public function __construct( string $type = 'nation' ) {
		$this->theme_type = $type;
		$this->load_required_files();
		$this->disable_discussion();
		$this->load_favicons( 'nation' );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );
		add_action( 'enqueue_block_assets', array( $this, 'enqueue_global_assets' ) );
		add_action( 'after_setup_theme', array( $this, 'cno_theme_support' ) );
		add_action( 'init', array( $this, 'alter_post_types' ) );
		/**
		 * Filter the priority of the Yoast SEO metabox
		 */
		add_filter(
			'wpseo_metabox_prio',
			function (): string {
				return 'low';
			}
		);
		add_filter(
			'allowed_redirect_hosts',
			function ( array $hosts ): array {
				/**
				 * A list of allowed redirects for `wp_safe_redirect`
				 *
				 * @var array $allowed_hosts
				 */
				$allowed_hosts = array(
					'choctawnation.com',
					'www.choctawnation.com',
				);
				return array_merge( $hosts, $allowed_hosts );
			}
		);
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
				<link rel='icon' type'='image/png' sizes='192x192' href='{$href}/android-chrome-192x192.png'>
				<link rel='icon' type'='image/png' sizes='512x512' href='{$href}/android-chrome-512x512.png'>
				<link rel='icon' type='image/png' sizes='32x32' href='{$href}/favicon-32x32.png'>
				<link rel='icon' type='image/png' sizes='16x16' href='{$href}/favicon-16x16.png'>
				<link rel='mask-icon' href='{$href}/safari-pinned-tab.svg' color='#000000'>";
			}
		);
	}

	/** Load required files. */
	private function load_required_files() {
		$base_path = get_template_directory() . '/inc';

		/** Loads the Theme Functions File (to keep the actual functions.php file clean) */
		require_once $base_path . '/theme/theme-functions.php';

		$asset_loaders = array(
			'enum-enqueue-type',
			'class-asset-loader',
		);
		foreach ( $asset_loaders as $asset_loader ) {
			require_once $base_path . "/theme/asset-loader/{$asset_loader}.php";
		}

		$navwalkers = array(
			'navwalker',
		);
		foreach ( $navwalkers as $navwalker ) {
			require_once $base_path . "/theme/navwalkers/class-{$navwalker}.php";
		}
		$utility_files = array(
			'allow-svg'             => 'Allow_SVG',
			'role-editor'           => 'Role_Editor',
			'gutenberg-handler'     => 'Gutenberg_Handler',
			'gravity-forms-handler' => null,
			'bootstrap-pagination'  => null,
			'post-override'         => 'Post_Override',
			'acf-handler'           => 'ACF_Handler',
			'rest-router'           => 'REST_Router',
			'cron-events'           => 'Cron_Events',
		);
		foreach ( $utility_files as $utility_file => $class_name ) {
			require_once $base_path . "/theme/class-{$utility_file}.php";
			if ( is_null( $class_name ) ) {
				continue;
			}
			$class = __NAMESPACE__ . '\\' . $class_name;
			new $class();
		}
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
		remove_post_type_support( 'post', 'editor' );
		remove_post_type_support( 'post', 'excerpt' );
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
}
