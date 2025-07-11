<?php
/**
 * Override the default post settings
 *
 * @package ChoctawNation
 */

namespace ChoctawNation;

/**
 * Class Post_Override
 * Overrides the default post settings
 *
 * @package ChoctawNation
 */
class Post_Override {
	/**
	 * Constructor Function
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'alter_post_types' ) );
	}

	/**
	 * Alters the post type
	 */
	public function alter_post_types() {
		$this->update_labels();
	}

	/**
	 * Updates the labels for the post type
	 */
	private function update_labels() {
		$labels                     = get_post_type_labels( get_post_type_object( 'post' ) );
		$labels->name               = 'Talent';
		$labels->singular_name      = 'Talent';
		$labels->add_new            = 'Add New Talent';
		$labels->add_new_item       = 'Add New Talent';
		$labels->edit_item          = 'Edit Talent';
		$labels->new_item           = 'New Talent';
		$labels->view_item          = 'View Talent';
		$labels->view_items         = 'View Talent';
		$labels->search_items       = 'Search Talent';
		$labels->not_found          = 'No Talent found';
		$labels->not_found_in_trash = 'No Talent found in trash';
		$labels->all_items          = 'All Talent';
		$labels->menu_name          = 'Talent';
		$labels->name_admin_bar     = 'Talent';
		$args                       = get_post_type_object( 'post' );
		$args->labels               = $labels;
	}
}
