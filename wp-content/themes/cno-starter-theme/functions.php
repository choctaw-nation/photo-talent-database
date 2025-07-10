<?php
/**
 * Theme Functions
 *
 * Should be pretty quiet in here besides requiring the appropriate files. Like style.css, this should really only be used for quick fixes with notes to refactor later.
 *
 * @package ChoctawNation
 */

use ChoctawNation\Theme_Init;

/** Get the theme init class */
require_once get_template_directory() . '/inc/theme/class-theme-init.php';
new Theme_Init( 'nation' );
