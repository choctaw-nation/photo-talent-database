<?php
/**
 * Gravity Forms Handler
 *
 * @package ChoctawNation
 * @subpackage Gravity Forms
 */

namespace ChoctawNation;

/**
 * Gravity Forms Handler
 */
class Gravity_Forms_Handler {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'gform_submit_button', array( $this, 'add_bootstrap_classes' ) );
	}

	/**
	 * Add Bootstrap classes to Gravity Forms buttons.
	 *
	 * @param string $button The button HTML.
	 * @return string The modified button HTML.
	 */
	public function add_bootstrap_classes( string $button ): string {
		$dom = new \DOMDocument();
		$dom->loadHTML( $button );
		$input   = $dom->getElementsByTagName( 'input' )->item( 0 );
		$classes = $input->getAttribute( 'class' );
		$classes = 'btn btn-primary';
		$input->setAttribute( 'class', $classes );
		return $dom->saveHtml( $input );
	}
}
