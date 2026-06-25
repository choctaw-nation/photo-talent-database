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
<main <?php post_class( 'container col-lg-8 text-center d-flex flex-column justify-content-center align-items-center flex-grow-1' ); ?>>
		<div class="display-6">Choctaw Nation of Oklahoma</div>
	<h1 class="display-2 fw-bold mb-0">Talent Database
	</h1>
	<a href="/apply" class="btn btn-lg btn-black text-uppercase rounded-pill mb-3">Apply</a>
	<p class="mb-0">This website is owned, operated, and authorized by the Choctaw Nation of Oklahoma. Information presented on this site is provided on behalf of the Choctaw Nation of Oklahoma.</p>
</main>
<?php
get_footer();
