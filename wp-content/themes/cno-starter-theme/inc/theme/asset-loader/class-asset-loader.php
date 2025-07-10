<?php
/**
 * Refactoring Enqueue Scripts into a class
 *
 * @package ChoctawNation
 */

namespace ChoctawNation;

/** On Construction, enqueues the assets
 *
 * @property string       $id the file id;
 * @property Enqueue_Type $type which asset type to enqueue
 * @property ?string      $folder the subdirectory
 * @property ?array       $deps the file dependencies
 * @property ?array       $strategy the JS loading strategy
 */
class Asset_Loader {
	/**
	 * The ID of the asset file.
	 *
	 * @var string $id
	 */
	private string $id;

	/**
	 * Any extra dependencies for the asset.
	 *
	 * @var ?array $deps
	 */
	private ?array $deps;

	/**
	 * The Asset File Path
	 *
	 * @var string $file_path
	 */
	private string $file_path;

	/**
	 * The Asset File uri
	 *
	 * @var string $file_uri
	 */
	private string $file_uri;

	/** The asset file
	 *
	 * @var array|false $asset_file
	 */
	private $asset_file;

	/**
	 * The JS Loading strategy
	 *
	 * @var array $strategy
	 */
	private array $strategy;

	/** Init
	 *
	 * @param string       $id the file id;
	 * @param Enqueue_Type $type which asset type to enqueue
	 * @param ?string      $folder the subdirectory
	 * @param ?array       $deps the file dependencies
	 * @param ?array       $strategy the JS loading strategy
	 */
	public function __construct( string $id, Enqueue_Type $type, ?string $folder = null, ?array $deps = null, ?array $strategy = array( 'strategy' => 'defer' ) ) {
		$this->id         = $id;
		$this->file_path  = empty( $folder ) ? get_stylesheet_directory() . '/dist' : get_stylesheet_directory() . "/dist/{$folder}";
		$this->file_uri   = empty( $folder ) ? get_stylesheet_directory_uri() . '/dist' : get_stylesheet_directory_uri() . "/dist/{$folder}";
		$this->asset_file = $this->get_the_asset_file();
		$this->strategy   = $strategy;

		if ( null === $deps ) {
			$deps = Enqueue_Type::both === $type ? array(
				'scripts' => array( 'global' ),
				'styles'  => array( 'global' ),
			) : array( 'global' );
		}

		switch ( $type ) {
			case Enqueue_Type::style:
				$this->deps['styles'] = array_unique( array_merge( $deps, $this->asset_file['dependencies'] ) );
				$this->enqueue_page_style();
				break;
			case Enqueue_Type::script:
				$this->deps['scripts'] = array_unique( array_merge( $deps, $this->asset_file['dependencies'] ) );
				$this->enqueue_page_script();
				break;
			case Enqueue_Type::both:
				$this->deps = $this->set_the_dependencies( $deps );
				$this->enqueue_page_assets();
				break;
		}
	}


	/** Gets the Asset File */
	private function get_the_asset_file(): array|false {
		$file_exists = file_exists( $this->file_path . "/{$this->id}.asset.php" );
		return ( $file_exists ) ? require $this->file_path . "/{$this->id}.asset.php" : false;
	}

	/** Sets the dependencies to a single array with unique values
	 *
	 * @param ?array $deps the user-passed dependencies
	 */
	private function set_the_dependencies( ?array $deps ): ?array {
		if ( isset( $deps['scripts'] ) ) {
			$scripts = array_merge( $this->asset_file['dependencies'], $deps['scripts'] );
		} else {
			$deps = array(
				'styles'  => array(),
				'scripts' => array(),
			);
		}
		return array(
			'scripts' => array_unique( array_merge( $scripts, $deps['scripts'] ) ),
			'styles'  => $deps['styles'],
		);
	}

	/**
	 * Enqueues the page style.
	 */
	public function enqueue_page_style() {
		if ( $this->asset_file ) {
			wp_enqueue_style(
				$this->id,
				$this->file_uri . "/{$this->id}.css",
				$this->deps['styles'],
				$this->asset_file['version']
			);
		}
	}

	/**
	 * Enqueues the page script.
	 */
	public function enqueue_page_script() {
		if ( $this->asset_file ) {
			wp_enqueue_script(
				$this->id,
				$this->file_uri . "/{$this->id}.js",
				$this->deps['scripts'],
				$this->asset_file['version'],
				$this->strategy,
			);
		}
	}

	/**
	 * Enqueues both the page style and script.
	 */
	public function enqueue_page_assets() {
		$this->enqueue_page_style();
		$this->enqueue_page_script();
	}
}
