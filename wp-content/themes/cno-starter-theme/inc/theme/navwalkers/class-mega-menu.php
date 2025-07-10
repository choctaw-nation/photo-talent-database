<?php
/**
 * CNO Mega Menu
 * Extends the base Nav Walker to handle Mega Menus.
 *
 * @link https://choctawlanding.com
 *
 * @package ChoctawNation
 */

namespace ChoctawNation;

/**
 * Extends the WP Nav Walker to create megamenu option
 */
class Mega_Menu extends Navwalker {
	/**
	 * An array of the last items' IDs of each top-level menu item
	 *
	 * @var int[] $last_item_ids
	 */
	protected array $last_item_ids;

	/** How many children a menu has
	 *
	 * @var int $children_count
	 */
	protected int $children_count;

	/**
	 * The Opening Level
	 *
	 * @param string    $output the html
	 * @param int       $depth whether we are at the top-level or a sub-level
	 * @param ?stdClass $args An object of wp_nav_menu() arguments.
	 */
	public function start_lvl( &$output, $depth = 0, $args = \null ) {
		$dropdown_menu_class[] = '';
		// handle user-inputted classes
		foreach ( $this->current_item->classes as $class ) {
			if ( in_array( $class, $this->dropdown_menu_alignment_values, true ) ) {
				$dropdown_menu_class[] = $class;
			}
		}
		$indent       = str_repeat( "\t", $depth );
		$is_top_level = 0 === $depth;
		$submenu      = ( $is_top_level ) ? ' mega-menu__container' : ' sub-menu';
		$grid         = $is_top_level ? " style=\"--grid-columns:{$this->children_count}\"" : '';
		$output      .= "\n$indent<ul class=\"dropdown-menu$submenu " . esc_attr( implode( ' ', $dropdown_menu_class ) ) . " depth_$depth\"" . $grid . ">\n";
	}

	/**
	 * Starts the Element Output (inside the `li`)
	 *
	 * @param string   $output       Used to append additional content (passed by reference).
	 * @param WP_Post  $data_object  Menu item data object.
	 * @param int      $depth        Depth of menu item. Used for padding.
	 * @param stdClass $args         An object of wp_nav_menu() arguments.
	 * @param int      $id           Optional. ID of the current menu item. Default 0.
	 */
	public function start_el( &$output, $data_object, $depth = 0, $args = \null, $id = 0 ) {
		$this->current_item = $data_object;
		$this->depth        = $depth;
		$this->args         = $args;
		$this->id           = $id;
		$this->is_top_level = $this->has_children && 0 === $this->depth;

		$output .= $this->get_the_li_element();
		if ( $this->is_top_level ) {
			$output .= "<div class='btn-group'>";
		}
		$output .= $this->get_the_anchor_element();
		// if is title, add split-toggle dropdown button that only displays on mobile
		if ( $this->is_top_level ) {
			$output .= '<button type="button" class="btn dropdown-toggle dropdown-toggle-split d-xl-none" data-bs-toggle="dropdown" aria-expanded="false"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-caret-down-fill" viewBox="0 0 16 16"><path d="M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z"/></svg><span class="visually-hidden">Toggle Dropdown</span></button>';
		}
	}


	/** Generate the Opening `li` tag
	 *
	 * @return string the HTML
	 */
	protected function get_the_li_element(): string {
		if ( $this->has_children ) {
			$this->set_the_children_count();
		}
		$indent        = ( $this->depth ) ? str_repeat( "\t", $this->depth ) : '';
		$li_attributes = '';
		$class_names   = $this->set_the_li_classes();
		$html_id       = $this->set_the_li_id();
		return $indent . '<li' . $html_id . $class_names . $li_attributes . '>';
	}

	/**
	 * Sets the number of children a sub-menu has
	 */
	private function set_the_children_count() {
		// Getting the menu item objects array from the menu.
		$menu_items = wp_get_nav_menu_items( $this->args->menu->term_id );

		// Getting the parent ids by looping through the menu item objects array. This will give an array of parent ids and the number of their children.
		$menu_item_parents = array_map(
			function ( $o ) {
				return $o->menu_item_parent;
			},
			$menu_items
		);
		// Get number of children menu item has.
		$this->children_count = array_count_values( $menu_item_parents )[ $this->current_item->ID ];
	}

	/**
	 * Handles the setting of the element's classes and returns an HTML string
	 *
	 * @return string the class names
	 */
	protected function set_the_li_classes(): string {
		$classes   = empty( $this->current_item->classes ) ? array() : (array) $this->current_item->classes;
		$classes[] = ( $this->has_children ) ? 'dropdown ' : '';
		$classes[] = ( $this->is_top_level ) ? 'mega-menu position-static' : '';
		$classes[] = ( $this->current_item->current || $this->current_item->current_item_ancestor ) ? 'active' : '';
		$classes[] = 'nav-item';
		$classes[] = 'nav-item-' . $this->current_item->ID;

		if ( $this->depth && $this->has_children ) {
			$classes[] = 'dropdown-menu dropdown-menu-end';
		}

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $this->current_item, $this->args ) );
		$class_names = ' class="' . esc_attr( $class_names ) . '"';
		return $class_names;
	}

	/**
	 * Generates the intial `<a></a>` tag
	 *
	 * @return string the anchor
	 */
	protected function get_the_anchor_element(): string {
		$attributes = $this->get_the_attributes();

		$title = apply_filters( 'the_title', $this->current_item->title, $this->current_item->ID );
		$title = apply_filters( 'nav_menu_item_title', $title, $this->current_item, $this->args, $this->depth );

		$item_output  = $this->args->before;
		$item_output .= "<a {$attributes}>";
		$item_output .= $this->args->link_before . $title . $this->args->link_after;
		$item_output .= '</a>';
		$item_output .= $this->args->after;
		$item_output  = apply_filters( 'walker_nav_menu_start_el', $item_output, $this->current_item, $this->depth, $this->args );
		return $item_output;
	}

	/** Builds the anchor attributes */
	protected function get_the_attributes(): string {
		$active_class = ( $this->current_item->current || $this->current_item->current_item_ancestor || in_array( 'current_page_parent', $this->current_item->classes, true ) || in_array( 'current-post-ancestor', $this->current_item->classes, true ) ) ? 'active' : '';

		$attributes = array(
			'title'  => $this->current_item->attr_title,
			'target' => $this->current_item->target,
			'rel'    => $this->current_item->xfn,
			'href'   => $this->current_item->url,
			'class'  => $active_class,
		);

		if ( $this->has_children && ! $this->is_top_level ) {
			$attributes['class'] .= ' mega-menu__title';
		}

		if ( ! $this->has_children ) {
			$attributes['class'] .= ' dropdown-item';
		}

		$attributes['class'] .= ' nav-link';
		return $this->build_atts( $attributes );
	}

	/**
	 * Ends the list of after the elements are added. Specifically, this function handles the mega-menu output.
	 *
	 * @see Walker::end_lvl()
	 *
	 * @param string   $output Used to append additional content (passed by reference).
	 * @param int      $depth  Depth of menu item. Used for padding.
	 * @param stdClass $args   An object of wp_nav_menu() arguments.
	 */
	public function end_lvl( &$output, $depth = 0, $args = null ) {
		$nav_items    = wp_get_nav_menu_items( 'main-menu' );
		$last_item    = null;
		$end_of_array = end( $nav_items );
		foreach ( $nav_items as $nav_item ) {
			$is_top_level_item = '0' === $nav_item->menu_item_parent;

			if ( $is_top_level_item || $nav_item === $end_of_array ) {
				$this->last_item_ids[] = $last_item;
			}
			$last_item = $nav_item->ID;
		}

		$mega_menu_content = $this->get_the_mega_menu_content( $this->current_item->ID );
		$discard_spacing   = isset( $args->item_spacing ) && 'discard' === $args->item_spacing;

		$t      = $discard_spacing ? '' : "\t";
		$n      = $discard_spacing ? '' : "\n";
		$indent = str_repeat( $t, $depth );

		if ( 0 === $depth && $mega_menu_content ) {
			$output .= '<li class="mega-menu__acf-field dropdown-menu-end">';
			$output .= $mega_menu_content;
			$output .= '</li>';
		}
		if ( $this->is_top_level ) {
			$output .= '</div>';
		}

		$output .= "{$indent}</ul>{$n}";
	}

	/**
	 * If current_item->ID in $last_item_ids[], get the content
	 *
	 * @param int $id the current item's ID
	 * @return string the markup
	 */
	protected function get_the_mega_menu_content( int $id ): string {
		$mega_menu_content = '';

		if ( ! in_array( $id, $this->last_item_ids, true ) ) {
			return $mega_menu_content;
		}

		switch ( $id ) {
			case $this->last_item_ids[1]:
				$mega_menu = new Mega_Menu_Content( 'option', get_field( 'stay_content', 'option' ) );
				break;
			case $this->last_item_ids[2]:
				$mega_menu = new Mega_Menu_Content( 'option', get_field( 'eat_and_drink_content', 'option' ) );
				break;
			case $this->last_item_ids[3]:
				$mega_menu = new Mega_Menu_Content( 'option', get_field( 'entertainment_content', 'option' ) );
				break;
			case $this->last_item_ids[4]:
				$mega_menu = new Mega_Menu_Content( 'option', get_field( 'things_to_do_content', 'option' ) );
				break;
			case $this->last_item_ids[5]:
				$mega_menu = new Mega_Menu_Content( 'option', get_field( 'mercantile_content', 'option' ) );
				break;
		}

		$mega_menu_content = $mega_menu->get_the_content();
		return $mega_menu_content;
	}
}
