<?php
/**
 * Theme Functions
 *
 * Should be pretty quiet in here besides requiring the appropriate files. Like style.css, this should really only be used for quick fixes with notes to refactor later.
 *
 * @package ChoctawNation
 */

use ChoctawNation\Theme_Init;

require ABSPATH . 'vendor/autoload.php';

/** Get the theme init class */
$theme = new Theme_Init( 'nation' );
add_action( 'after_setup_theme', array( $theme, 'setup_theme' ) );