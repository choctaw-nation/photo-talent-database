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

// Schedule daily cron event if not already scheduled
if ( ! wp_next_scheduled( 'cno_update_post_ages_daily' ) ) {
	wp_schedule_event( time(), 'daily', 'cno_update_post_ages_daily' );
}

// Hook the function to the cron event
add_action(
	'cno_update_post_ages_daily',
	function () {
		$args  = array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'fields'         => 'ids',
		);
		$posts = get_posts( $args );

		foreach ( $posts as $post_id ) {
			$age = cno_get_age( $post_id );
			update_field( 'current_age', absint( $age ), $post_id );
		}
	}
);
