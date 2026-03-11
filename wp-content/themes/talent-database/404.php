<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package ChoctawNation
 */

get_header();
?>
<main id="main" class="site-main container py-5 mt-5">
	<section class="error-404 not-found">
		<h1 class="mb-3">404</h1>
		<p class="alert alert-info mb-4">
			Page not found.
		</p>
		<a class="btn btn-outline-primary" href="<?php echo esc_url( home_url() ); ?>">
			Back Home &raquo;
		</a>
	</section>
</main>
<?php
get_footer();
