<?php
/**
 * CNO Navwalker
 *
 * Edits the output of wp_nav_menu()
 * <div class='menu-container'>
 *   <ul> // start_lvl()
 *     <li>
 *       <a href="">
 *           <span> // start_el() // </span>
 *       </a>
 *     </li> // end_el()
 *   </ul>
 * </div> // end_lvl()
 *
 * @package ChoctawNation
 * @since 1.8.0
 * @version 2.0
 */

namespace ChoctawNation;

/**
 * Extends the WP Nav Walker to create megamenu option
 * Based on the Bootscore 5 Nav Walker
 *
 * @link https://github.com/bootscore/bootscore/blob/main/inc/class-bootstrap-5-navwalker.php
 *
 * @since 0.1
 * @author Blake Perkins & KJ Roelke
 */
class Navwalker extends \Walker_Nav_Menu {
	/** The current nav item
	 *
	 * @var WP_Post $current_item
	 */
	protected $current_item;

	/**
	 * Bootstrap alignment classes
	 *
	 * @var string[] $dropdown_menu_alignment_values
	 */
	protected $dropdown_menu_alignment_values = array(
		'dropdown-menu-start',
		'dropdown-menu-end',
		'dropdown-menu-sm-start',
		'dropdown-menu-sm-end',
		'dropdown-menu-md-start',
		'dropdown-menu-md-end',
		'dropdown-menu-lg-start',
		'dropdown-menu-lg-end',
		'dropdown-menu-xl-start',
		'dropdown-menu-xl-end',
		'dropdown-menu-xxl-start',
		'dropdown-menu-xxl-end',
	);

	/**
	 * Depth of menu item. Used for padding.
	 *
	 * @var int $depth
	 */
	protected int $depth;

	/**
	 * The array of wp_nav_menu() arguments as an object.
	 *
	 * @var ?\stdClass $args
	 */
	protected ?\stdClass $args;

	/**
	 * Optional. ID of the current menu item. Default 0.
	 *
	 * @var int $id
	 */
	protected int $id;

	/** Whether Current Item in Navwalker is Top Level or not
	 *
	 * @var bool $is_top_level
	 */
	protected bool $is_top_level;

	/**
	 * Whether the current item has an href pointing to '#' or not
	 *
	 * @var bool $href_is_empty
	 */
	protected bool $href_is_empty;

	/**
	 * The Opening Level
	 *
	 * @param string    $output the html
	 * @param int       $depth whether we are at the top-level or a sub-level
	 * @param ?stdClass $args An object of wp_nav_menu() arguments.
	 */
	public function start_lvl( &$output, $depth = 0, $args = \null ) {
		$dropdown_menu_class = array();
		// handle user-inputted classes
		foreach ( $this->current_item->classes as $class ) {
			if ( in_array( $class, $this->dropdown_menu_alignment_values, true ) ) {
				$dropdown_menu_class[] = $class;
			}
		}
		$indent             = str_repeat( "\t", $depth );
		$this->is_top_level = 0 === $depth;
		$submenu            = ( $this->is_top_level ) ? '' : ' sub-menu';

		$output .= "\n$indent<ul class=\"dropdown-menu mt-0 border-0{$submenu} " . esc_attr( implode( ' ', $dropdown_menu_class ) ) . " depth_$depth\">\n";
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
		$this->current_item  = $data_object;
		$this->depth         = $depth;
		$this->args          = $args;
		$this->id            = $id;
		$this->is_top_level  = $this->has_children && 0 === $this->depth;
		$this->href_is_empty = '#' === $this->current_item->url;

		$output .= $this->get_the_li_element();
		if ( $this->is_top_level & ! $this->href_is_empty ) {
			$output .= "<div class='btn-group'>";
		}

		$output .= $this->get_the_anchor_element();
		// if is top-level, add split-toggle dropdown button that only displays on mobile
		if ( $this->has_children && $this->is_top_level && ! $this->href_is_empty ) {
			$output .= '<button type="button" class="btn dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false"><span class="visually-hidden">Toggle Dropdown</span></button>';
		}
	}

	/** Generate the Opening `li` tag
	 *
	 * @return string the HTML
	 */
	protected function get_the_li_element(): string {
		$indent        = ( $this->depth ) ? str_repeat( "\t", $this->depth ) : '';
		$li_attributes = '';
		$class_names   = $this->set_the_li_classes();
		$html_id       = $this->set_the_li_id();
		return $indent . '<li' . $html_id . $class_names . $li_attributes . '>';
	}

	/**
	 * Handles the setting of the element's classes and returns an HTML string
	 *
	 * @return string the class names
	 */
	protected function set_the_li_classes(): string {
		$classes = empty( $this->current_item->classes ) ? array() : (array) $this->current_item->classes;

		$classes[] = ( $this->has_children ) ? 'dropdown ' : '';
		$classes[] = ( $this->current_item->current || $this->current_item->current_item_ancestor ) ? 'active' : '';
		$classes[] = "nav-item nav-item-{$this->current_item->ID}";

		if ( $this->depth && $this->has_children ) {
			$classes[] = 'dropdown-menu dropdown-menu-lg-end';
		}

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $this->current_item, $this->args ) );
		$class_names = ' class="' . esc_attr( $class_names ) . '"';
		return $class_names;
	}

	/**
	 * Handles the id generation
	 *
	 * @return string the id
	 */
	protected function set_the_li_id(): string {
		$id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $this->current_item->ID, $this->current_item, $this->args );
		$id = strlen( $id ) ? ' id="' . esc_attr( $id ) . '"' : '';
		return $id;
	}

	/**
	 * Generates the initial anchor tag, or button if the link is a #
	 *
	 * @return string the anchor
	 */
	protected function get_the_anchor_element(): string {
		$attributes = $this->get_the_attributes();

		$title = apply_filters( 'the_title', $this->current_item->title, $this->current_item->ID );
		$title = apply_filters( 'nav_menu_item_title', $title, $this->current_item, $this->args, $this->depth );

		$item_output = $this->args->before;
		if ( '#' === $this->current_item->url ) {
			$item_output .= "<button {$attributes}>";
			$item_output .= $this->args->link_before . $title . $this->args->link_after;
			$item_output .= '</button>';
		} else {
			$item_output .= "<a {$attributes}>";
			$item_output .= $this->args->link_before . $title . $this->args->link_after;
			$item_output .= '</a>';
		}
		$item_output .= $this->args->after;
		$item_output  = apply_filters( 'walker_nav_menu_start_el', $item_output, $this->current_item, $this->depth, $this->args );
		return $item_output;
	}

	/** Builds the anchor attributes */
	protected function get_the_attributes(): string {
		$active_class = ( $this->current_item->current || $this->current_item->current_item_ancestor || in_array( 'current_page_parent', $this->current_item->classes, true ) || in_array( 'current-post-ancestor', $this->current_item->classes, true ) ) ? 'active ' : '';
		if ( ! $this->href_is_empty ) {
			$attributes = array(
				'title'  => $this->current_item->attr_title,
				'target' => $this->current_item->target,
				'rel'    => $this->current_item->xfn,
				'href'   => $this->current_item->url,
				'class'  => $active_class,
			);
		} else {
			$attributes = array(
				'type'              => 'button',
				'class'             => 'btn dropdown-toggle',
				'data-bs-toggle'    => 'dropdown',
				'aria-expanded'     => 'false',
				'data-bs-reference' => 'parent',
				'data-bs-display'   => 'static',
			);
		}

		if ( ! empty( $active_class ) ) {
			$attributes['aria-current'] = 'page';
		}
		if ( $this->depth > 0 ) {
			$attributes['class'] .= 'dropdown-item';
		}

		$attributes['class'] .= ' nav-link';
		return $this->build_atts( $attributes );
	}
}
