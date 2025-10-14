<?php
/**
 * Main Menu
 *
 * @package ChoctawNation
 */

$lists_count     = wp_count_posts( 'talent-list' )->publish;
$pending_talent  = wp_count_posts();
$requested_url   = ( isset( $_SERVER['REQUEST_URI'] ) ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
$menu_links      = array(
	'Talent Lists'   => esc_url( get_post_type_archive_link( 'talent-list' ) ),
	'Pending Talent' => esc_url( user_trailingslashit( '/pending-talent' ) ),
);
$link_conditions = array( $lists_count > 0, ( (int) $pending_talent->pending + (int) $pending_talent->draft ) > 0 );
?>
<div class="offcanvas-header"><button class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button></div>
<div class="offcanvas-body">
	<ul class="col col-auto d-flex flex-column flex-md-row flex-wrap justify-content-md-end align-items-md-center gap-3 mb-0 ps-0 list-unstyled">
		<?php
		foreach ( $link_conditions as $index => $condition ) {
			if ( ! $condition ) {
				continue;
			}
			$link_classes = array( 'fs-base', 'link-offset-1' );
			$menu_link    = $menu_links[ array_keys( $menu_links )[ $index ] ];
			$label        = array_keys( $menu_links )[ $index ];
			if ( strpos( $menu_link, $requested_url ) === false ) {
				$link_classes[] = 'text-decoration-none';
			}
			echo "<li><a href='{$menu_link}' class='" . esc_attr( implode( ' ', $link_classes ) ) . "'>{$label}</a></li>";
		}
		?>
		<li>
			<?php
			$is_logged_in    = is_user_logged_in();
			$login_out_url   = $is_logged_in ? wp_logout_url( home_url() ) : wp_login_url();
			$login_out_label = $is_logged_in ? 'Logout' : 'Login';
			printf(
				'<a href="%s" class="btn btn-outline-white rounded-pill">%s</a>',
				esc_url( $login_out_url ),
				$login_out_label
			);
			?>
		</li>
	</ul>
</div>
