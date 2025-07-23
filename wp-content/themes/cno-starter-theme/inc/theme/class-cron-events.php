<?php
/**
 * Cron Events
 *
 * @package ChoctawNation
 */

namespace ChoctawNation;

use DateTime;

/**
 * Class Cron_Events
 *
 * Handles scheduled events for the theme.
 */
class Cron_Events {
	/**
	 * A dictionary of keys for cron events
	 *
	 * @var array $action_keys
	 */
	private array $action_keys;

	/**
	 * Constructor function that initializes the cron events.
	 */
	public function __construct() {
		$this->action_keys = array(
			'update_ages'      => 'cno_update_post_ages_daily',
			'expire_old_lists' => 'cno_expire_old_lists',
		);
		$this->schedule_events();
		$this->wire_actions();
	}

	/**
	 * Schedule the cron events.
	 *
	 * This method checks if the events are already scheduled and schedules them if not.
	 */
	private function schedule_events() {
		$events = array(
			'update_ages'      => 'daily',
			'expire_old_lists' => 'daily',
		);
		foreach ( $events as $action => $schedule ) {
			if ( ! in_array( $action, array_keys( $this->action_keys ), true ) ) {
				continue;
			}
			if ( ! wp_next_scheduled( $this->action_keys[ $action ] ) ) {
				wp_schedule_event( time(), $schedule, $this->action_keys[ $action ] );
			}
		}
	}

	/**
	 * Wire up the actions for the cron events.
	 *
	 * This method adds the necessary actions to handle the scheduled events.
	 */
	private function wire_actions() {
		$actions = array(
			'update_ages'      => 'update_age',
			'expire_old_lists' => 'expire_list', // Assuming this method exists in the class.
		);
		foreach ( $actions as $action => $method ) {
			if ( ! method_exists( $this, $method ) || ! in_array( $action, array_keys( $this->action_keys ), true ) ) {
				continue;
			}
			add_action( $this->action_keys[ $action ], array( $this, $method ) );
		}
	}

	/**
	 * Update the age of posts.
	 *
	 * This method retrieves all published posts and updates their 'current_age' field
	 * with the calculated age using the cno_get_age function.
	 */
	public function update_age() {
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

	/**
	 * Expire old talent lists.
	 *
	 * This method checks all published talent lists and trashes those that have expired
	 * based on the 'post_expiry' custom field.
	 */
	public function expire_list() {
		$today = new DateTime( 'now', wp_timezone() );
		$args  = array(
			'post_type'      => 'talent-list',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'fields'         => 'ids',
		);
		$posts = get_posts( $args );

		foreach ( $posts as $post_id ) {
			$expiry = get_field( 'post_expiry', $post_id );
			if ( ! $expiry ) {
				continue;
			}
			$expiry_date = new DateTime( $expiry, wp_timezone() );
			if ( $expiry_date <= $today ) {
				wp_trash_post( $post_id );
			}
		}
	}
}
