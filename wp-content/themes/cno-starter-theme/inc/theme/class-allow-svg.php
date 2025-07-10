<?php
/**
 * Allow SVG
 *
 * @package ChoctawNation
 */

namespace ChoctawNation;

/** Allows WordPress to use SVG */
class Allow_SVG {
	/** On construction, wires up callback functions */
	public function __construct() {
		add_filter( 'upload_mimes', array( $this, 'cc_mime_types' ) );
		add_action( 'admin_head', array( $this, 'fix_svg' ) );
	}

	/**
	 * Adds SVG to allowed Mime Types
	 *
	 * @param array $mimes the mime types
	 */
	public function cc_mime_types( $mimes ) {
		$mimes['svg'] = 'image/svg+xml';
		return $mimes;
	}

	/** Adds Styles */
	public function fix_svg() {
		echo '<style type="text/css">
				.attachment-266x266, .thumbnail img {
					 width: 100% !important;
					 height: auto !important;
				}
				</style>';
	}
}
