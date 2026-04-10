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
	}

	/**
	 * Schedule the cron events.
	 *
	 * This method checks if the events are already scheduled and schedules them if not.
	 */
	public function schedule_events() {
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
	public function wire_actions() {
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
	 * This method checks all published posts and updates their age based on the 'current_age' custom field. It only runs on January 1st to ensure that ages are updated annually.
	 */
	public function update_age() {
		$today = new DateTime( 'now', wp_timezone() );
		if (  $today ->format( 'm-d' ) !== '01-01' ) {
			return;
		}
		$args  = array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'fields'         => 'ids',
		);
		$posts = get_posts( $args );

		foreach ( $posts as $post_id ) {
			$age = get_field( 'current_age', $post_id );

			if ( '' === $age || null === $age || ! is_numeric( $age ) ) {
				continue;
			}

			update_field( 'current_age', absint( ( (int) $age ) + 1 ), $post_id );
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
