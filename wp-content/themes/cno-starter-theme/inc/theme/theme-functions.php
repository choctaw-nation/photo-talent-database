<?php
/**
 * The global helper functions to use.
 *
 * This file should be used to define functions that are specifically meant to live in the global scope. Remember to prefix your functions with `cno_` to avoid conflicts.
 *
 * @package ChoctawNation
 */

/**
 * Reads an SVG file and returns its content.
 *
 * @param string       $logo_path The path to the logo file (must exist inside the theme directory).
 * @param string|false $alt_text The alt text for the image. False to not set an alt text.
 * @param string       $fallback The fallback text if the file cannot be read.
 *
 * @return string The SVG content.
 */
function cno_read_svg( string $logo_path, string|false $alt_text, string $fallback = 'This file could not be found' ): string {
	// Initialize the WP Filesystem
	global $wp_filesystem;
	if ( empty( $wp_filesystem ) ) {
		require_once ABSPATH . '/wp-admin/includes/file.php';
		WP_Filesystem();
	}

	// Get the path to the logo file
	$theme_directory = get_template_directory();
	$svg_path        = $theme_directory . $logo_path;

	// Check if file exists and read it
	if ( ! $wp_filesystem->exists( $svg_path ) ) {
		return $fallback;
	}

	$svg_content = $wp_filesystem->get_contents( $svg_path );
	if ( $svg_content ) {
		$svg_content = str_replace( '<svg', '<svg alt="' . esc_attr( $alt_text ) . '"', $svg_content );
		return $svg_content;
	}
	return $fallback;
}

/**
 * Echoes SVG Content
 *
 * @param string       $logo_path The path to the logo file (must exist inside the theme directory).
 * @param string|false $alt_text The alt text for the image. False to not set an alt text.
 * @param string       $fallback The fallback text if the file cannot be read.
 *
 * @return void
 */
function cno_echo_svg( string $logo_path, string|false $alt_text, string $fallback = 'This file could not be found' ): void {
	echo cno_read_svg( $logo_path, $alt_text, $fallback );
}

/**
 * Get the federated privacy policy from Choctaw Nation main site.
 *
 * @return string The federated privacy policy.
 */
function cno_get_federated_privacy_policy(): string|WP_Error {
	$policy = get_transient( 'cno_privacy_policy' );
	if ( ! $policy ) {
		$page_request  = wp_remote_get( 'https://www.choctawnation.com/wp-json/wp/v2/pages/3?_fields=title,acf' );
		$page_response = wp_remote_retrieve_body( $page_request );
		if ( is_wp_error( $page_response ) ) {
			return $page_response;
		}
		$page_data   = json_decode( $page_response, true );
		$thirty_days = 60 * 60 * 24 * 30;
		set_transient( 'cno_privacy_policy', acf_esc_html( $page_data['acf']['content'] ), $thirty_days );
		$policy = get_transient( 'cno_privacy_policy' );
	}
	return $policy;
}


function cno_get_age(int $post_id):string {
	$dob = get_field('dob',$post_id);
	$now = new Date('now',wp_timezone());

}