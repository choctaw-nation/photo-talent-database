<?php
/**
 * ACF Handler Class
 *
 * Enables ACF-JSON files to be saved and loaded from the theme directory.
 *
 * @package ChoctawNation
 */

namespace ChoctawNation;

/**
 * ACF_Handler Class
 */
class ACF_Handler {
	/**
	 * The base path for ACF fields.
	 *
	 * @var string $base_path
	 */
	private string $base_path;

	/**
	 * A map of ACF field types to their respective directories.
	 *
	 * @var array $paths_array
	 */
	private array $paths_array;

	/**
	 * Constructor function that initializes the ACF Handler.
	 */
	public function __construct() {
		if ( ! defined( 'ACF_PRO' ) || ! defined( 'ACF_VERSION' ) ) {
			return;
		}

		$this->base_path   = get_stylesheet_directory() . '/inc/acf/acf-fields/';
		$this->paths_array = array(
			'field-group'     => 'fields',
			'post-type'       => 'post-types',
			'taxonomy'        => 'taxonomies',
			'ui-options-page' => 'options',
		);
		$this->init_save_filters();
		add_filter( 'acf/settings/load_json', array( $this, 'load_json_paths' ) );
	}

	/**
	 * Loops through the paths array and adds filters to save ACF JSON files
	 */
	private function init_save_filters() {
		foreach ( $this->paths_array as $slug => $dir ) {
			add_filter(
				'acf/settings/save_json/type=acf-' . $slug,
				fn() => $this->save_json( $dir )
			);
		}
	}

	/**
	 * Saves ACF JSON files to the specified directory. Creates the directory if it does not exist.
	 *
	 * @param string $dir The directory to save the JSON files in.
	 * @return string The full path where the JSON files are saved.
	 */
	public function save_json( string $dir ): string {
		// Ensure the directory exists
		if ( ! is_dir( $this->base_path . $dir ) ) {
			wp_mkdir_p( $this->base_path . $dir );
		}
		return $this->base_path . $dir;
	}

	/**
	 * Filter to modify the loading paths for ACF JSON files.
	 *
	 * @param array $paths The existing paths for ACF JSON files.
	 */
	public function load_json_paths( array $paths ): array {
		unset( $paths[0] ); // Remove the default path
		$paths = array_map( fn( $dir ) => $this->base_path . $dir, array_values( $this->paths_array ) );
		return $paths;
	}
}