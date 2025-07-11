<?php
/**
 * Gutenberg Handler
 * Handles the Controls and Settings for the Block Editor
 *
 * @package ChoctawNation
 */

namespace ChoctawNation;

use WP_Block_Editor_Context;

/**
 * Gutenberg Handler
 */
class Gutenberg_Handler {
	/**
	 * Constructor
	 */
	public function __construct() {
		// add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_assets' ) ); phpcs:ignore Squiz.PHP.CommentedOutCode.Found
		add_action( 'after_setup_theme', array( $this, 'cno_block_theme_support' ), 50 );
		add_filter( 'block_editor_settings_all', array( $this, 'restrict_gutenberg_ui' ), 10, 1 );
		add_filter( 'allowed_block_types_all', array( $this, 'restrict_block_types' ), 10, 2 );
	}

	/**
	 * Check if the current user is an administrator.
	 *
	 * @return bool
	 */
	private function is_admin(): bool {
		return current_user_can( 'activate_plugins' );
	}

	/**
	 * Enqueue the block editor assets that control the layout of the Block Editor.
	 */
	public function enqueue_block_assets() {
		new Asset_Loader( 'registerBlockVariations', Enqueue_Type::script, 'admin' );
		new Asset_Loader( 'editDefaultBlocks', Enqueue_Type::script, 'admin' );
		new Asset_Loader( 'unregisterBlocks', Enqueue_Type::script, 'admin', array( 'wp-edit-post' ) );
	}

	/**
	 * Init theme supports specific to the block editor.
	 */
	public function cno_block_theme_support() {
		$opt_in_features = array(
			'responsive-embeds',
			'editor-styles',
			'custom-spacing',
			'align-wide',
		);
		foreach ( $opt_in_features as $feature ) {
			add_theme_support( $feature );
		}
		$opt_out_features = array(
			'core-block-patterns',
		);
		foreach ( $opt_out_features as $feature ) {
			remove_theme_support( $feature );
		}
	}

	/**
	 * Restrict access to the locking UI to Administrators.
	 *
	 * @param array $settings Default editor settings.
	 */
	public function restrict_gutenberg_ui( $settings, ) {
		$is_administrator = $this->is_admin();

		if ( ! $is_administrator ) {
			$settings['canLockBlocks']      = false;
			$settings['codeEditingEnabled'] = false;
		}

		return $settings;
	}

	/**
	 * Filters the list of allowed block types in the block editor.
	 *
	 * This function restricts the available block types to Heading, List, Image, and Paragraph only.
	 *
	 * @param array|bool $allowed_block_types Array of block type slugs, or boolean to enable/disable all.
	 *
	 * @return array|bool The array of allowed block types or boolean to enable/disable all.
	 */
	public function restrict_block_types( array|bool $allowed_block_types ): array|bool {
		$is_administrator = $this->is_admin();
		// Get all registered blocks if $allowed_block_types is not already set.
		if ( ! is_array( $allowed_block_types ) || empty( $allowed_block_types ) ) {
			$registered_blocks   = \WP_Block_Type_Registry::get_instance()->get_all_registered();
			$allowed_block_types = array_keys( $registered_blocks );
		}
		if ( $is_administrator ) {
			return true;
		}

		if ( ! $is_administrator ) {
			$allowed_block_types = array(
				'core/heading',
				'core/list',
				'core/list-item',
				'core/image',
				'core/paragraph',
				'core/gallery',
				'core/shortcode',
				'core/freeform',
				'core/pattern',
				'core/table',
				'core/quote',
				'core/pullquote',
				'core/code',
				'core/html',
				'core/block',
				'core/buttons',
				'core/button',
				'gravityforms/form',
			);
			return $allowed_block_types;
		}
		return $allowed_block_types;
	}
}
