<?php
/**
 * Bootstrap Pagination
 *
 * @package ChoctawNation
 */

namespace ChoctawNation;

use stdClass;
use WP_Query;

/**
 * Bootstrap Pagination
 * Generates bootstrap-style pagination for WP_Query objects
 */
class Bootstrap_Pagination {
	/**
	 * WP_Query
	 *
	 * @var WP_Query
	 */
	private $query;

	/**
	 * Current index (1 based, not 0 based)
	 *
	 * @var int $current_index
	 */
	private int $current_index;

	/**
	 * Constructor
	 *
	 * @param WP_Query|null $wp_query The WP_Query object
	 */
	public function __construct( ?WP_Query $wp_query = null ) {
		if ( is_null( $wp_query ) ) {
			global $wp_query;
		}
		$this->query = $wp_query;
	}

	/**
	 * Get the current page
	 *
	 * @return int|null the current page
	 */
	public function get_current_page(): int {
		return isset( $this->query->query['paged'] ) ? intval( $this->query->query['paged'] ) : 1;
	}



	/**
	 * Returns the full pagination `<ul></ul>` markup
	 *
	 * @return string the pagination
	 */
	public function get_the_pagination(): string {
		$markup  = '<ul class="pagination flex-wrap mb-0">';
		$markup .= $this->get_first_page_link();
		$markup .= $this->get_previous_page_link();

		$current_page   = $this->get_current_page();
		$total_pages    = $this->query->max_num_pages;
		$mid_size_links = $this->mid_size_links( $current_page, $total_pages );

		foreach ( $this->get_paginated_links_array() as $link ) {
			if ( in_array( $link->page, $mid_size_links, true ) || $link->is_current ) {
				$markup .= $this->get_paginated_link( $link );
			}
		}

		if ( ! in_array( $total_pages, $mid_size_links, true ) ) {
			if ( end( $mid_size_links ) < $total_pages - 1 ) {
				$markup .= '<li class="page-item"><span class="page-link">...</span></li>';
			}
			$markup .= $this->get_paginated_link(
				(object) array(
					'is_current' => false,
					'page'       => $total_pages,
					'url'        => get_pagenum_link( $total_pages ),
				)
			);
		}

		$markup .= $this->get_next_page_link();
		$markup .= $this->get_last_page_link();
		$markup .= '</ul>';
		return $markup;
	}

	/**
	 * Echoes the full pagination `<ul></ul>` markup
	 */
	public function the_pagination(): void {
		echo $this->get_the_pagination();
	}

	/**
	 * Custom pagination function
	 *
	 * @see https://developer.wordpress.org/reference/functions/paginate_links/#comment-3862
	 * @return array the paginated links
	 */
	private function get_paginated_links_array(): array {
		// When we're on page 1, 'paged' is 0, but we're counting from 1,
		// so we're using max() to get 1 instead of 0
		$this->current_index = max( 1, get_query_var( 'paged', 1 ) );

		// This creates an array with all available page numbers, if there
		// is only *one* page, max_num_pages will return 0, so here we also
		// use the max() function to make sure we'll always get 1
		$pages = range( 1, max( 1, $this->query->max_num_pages ) );
		// Now, map over $pages and return the page number, the url to that
		// page and a boolean indicating whether that number is the current page
		return array_map(
			function ( $page ) {
				return (object) array(
					'is_current' => $page === $this->current_index,
					'page'       => $page,
					'url'        => get_pagenum_link( $page ),
				);
			},
			$pages
		);
	}

	/**
	 * Gets the paginated links
	 *
	 * @return string the paginated links
	 */
	public function get_paginated_links(): string {
		$link_array     = array();
		$current_page   = $this->get_current_page();
		$total_pages    = $this->query->max_num_pages;
		$mid_size_links = $this->mid_size_links( $current_page, $total_pages );

		foreach ( $this->get_paginated_links_array() as $link ) {
			if ( in_array( $link->page, $mid_size_links, true ) || $link->is_current ) {
				if ( $link->is_current ) {
					$this->current_index = $link->page;
				}
				$link_array[ $link->page ] = $this->get_paginated_link( $link );
			}
		}
		return implode( '', $link_array );
	}

	/**
	 * Returns an array of page numbers for the middle of the pagination
	 *
	 * @param int $current_page The current page number
	 * @param int $total_pages The total number of pages
	 * @param int $mid_size The number of pages to show on either side of the current page
	 * @return array The array of page numbers
	 */
	public function mid_size_links( $current_page, $total_pages, $mid_size = 2 ): array {
		$start = max( 1, $current_page - $mid_size );
		$end   = min( $total_pages, $current_page + $mid_size );

		$links = array();
		for ( $i = $start; $i <= $end; $i++ ) {
			$links[] = $i;
		}
		return $links;
	}

	/**
	 * Get the paginated link
	 *
	 * @param stdClass $link The link object
	 * @return string the paginated link
	 */
	private function get_paginated_link( stdClass $link ): string {
		$markup     = '';
		$link_class = 'page-item';
		if ( $link->is_current ) {
			$link_class .= ' active';
		}
		$link_el = $link->is_current ? 'span' : 'a';
		$markup .= "<li class='{$link_class}'" . ( $link->is_current ? ' aria-current="page"' : '' ) . '>';
		$markup .= "<{$link_el} " . ( ! $link->is_current ? "href='{$link->url}'" : '' ) . "class='page-link'>{$link->page}</{$link_el}></li>";
		return $markup;
	}

	/**
	 * Echoes the paginated links
	 */
	public function paginated_links(): void {
		echo $this->get_paginated_links();
	}

	/**
	 * Get the next page link
	 *
	 * @return string the next page link
	 */
	public function get_next_page_link(): string {
		$next_page      = $this->get_current_page() + 1;
		$next_page_link = get_pagenum_link( $next_page );
		$is_disabled    = $next_page > $this->query->max_num_pages;
		$markup         = $this->get_page_link_markup( $is_disabled, '>', 'Next', $next_page_link, );
		return $markup;
	}

	/**
	 * Page link markup
	 *
	 * @param bool        $is_disabled Whether the link is disabled
	 * @param string      $inner_text The inner text of the link
	 * @param string      $aria_label The aria label
	 * @param string|null $link The link
	 * @return string the next page link markup
	 */
	private function get_page_link_markup( bool $is_disabled, string $inner_text, string $aria_label, ?string $link = null, ): string {
		$markup = '<li class="page-item">';
		if ( $is_disabled ) {
			$markup .= "<button class='page-link disabled d-none d-lg-block' disabled tabindex='-1'>{$inner_text}</button>";
		} else {
			$markup .= "<a href='{$link}' class='page-link' aria-label='{$aria_label}'>{$inner_text}</a>";
		}
		$markup .= '</li>';
		return $markup;
	}


	/**
	 * Get the previous page link
	 *
	 * @return string the previous page link
	 */
	public function get_previous_page_link(): string {
		$prev_page = $this->get_current_page() - 1;
		if ( $prev_page < 0 ) {
			$prev_page = 0;
		}
		$prev_page_link = get_pagenum_link( $prev_page );
		$is_disabled    = 0 === $prev_page;
		return $this->get_page_link_markup( $is_disabled, '<', 'Previous', $prev_page_link );
	}

	/**
	 * Get the last page link
	 *
	 * @return string the last page link
	 */
	public function get_last_page_link(): string {
		$last_page      = $this->query->max_num_pages;
		$last_page_link = get_pagenum_link( $last_page );
		$is_disabled    = $this->get_current_page() === $this->query->max_num_pages;
		return $this->get_page_link_markup( $is_disabled, '>>', 'Last Page', $last_page_link );
	}

	/**
	 * Get the first page link
	 *
	 * @return string the first page link
	 */
	public function get_first_page_link(): string {
		$first_page      = 1;
		$first_page_link = get_pagenum_link( $first_page );
		$is_disabled     = is_null( $this->get_current_page() );
		return $this->get_page_link_markup( $is_disabled, '<<', 'First page', $first_page_link );
	}
}
