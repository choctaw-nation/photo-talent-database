<?php
/**
 * Front Page Template
 *
 * @package ChoctawNation
 */

if ( is_user_logged_in() ) {
	wp_safe_redirect( home_url( '/talent' ) );
	exit;
}
get_header();
?>
<main <?php post_class( 'container d-flex flex-column justify-content-center align-items-center flex-grow-1' ); ?>>
	<h1 class="text-center mb-0">
		<div class="display-6">Choctaw Nation of Oklahoma</div>
		<div class="display-2 fw-bold">Talent Database</div>
	</h1>
	<div class="d-flex flex-wrap column-gap-5 row-gap-3 w-100 justify-content-center">
		<?php
			$btn_classes = array(
				'btn',
				'btn-lg',
				'text-uppercase',
				'rounded-pill',
			);
			$btn_color   = 'black';
			if ( is_user_logged_in() ) {
				printf( '<a href="%s" class="%s btn-outline-%s">View Talent</a>', home_url( '/talent' ), implode( ' ', $btn_classes ), $btn_color );
			}
			?>
		<a href="/apply" class="<?php echo implode( ' ', $btn_classes ) . "  btn-{$btn_color}"; ?>">Apply</a>
	</div>
</main>
<?php
get_footer();