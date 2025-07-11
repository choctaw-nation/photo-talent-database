<?php
/**
 * The primary archive page.
 *
 * Included for historical reasons. This file is not used in the theme.
 *
 * @package ChoctawNation
 */

get_header();
?>
<main <?php post_class( 'd-flex flex-column align-items-stretch row-gap-5 my-5' ); ?>>
	<header class="container">
		<h1 class="display-1 mb-0">All Talent</h1>
	</header>
	<div class="container-fluid">
		<div class="row align-items-stretch gx-2 row-gap-5">
			<section class="col-12 col-lg-2 position-relative">
				<div class="text-bg-primary py-5 px-2 position-sticky top-0">
					<h2 class="fs-6">Filters</h2>
				</div>
			</section>
			<section class="col" id="talent">
				<?php if ( have_posts() ) : ?>
				<div class="row row-cols-auto row-cols-sm-2 row-cols-md-3 row-cols-xxl-4 row-gap-4">
					<?php
					while ( have_posts() ) {
						the_post();
						echo '<div class="col">';
						get_template_part( 'template-parts/card', 'talent-preview' );
						echo '</div>';
					}
					?>
				</div>
				<?php else : ?>
				<p>No posts found!</p>
				<?php endif; ?>
				<?php
				if ( have_posts() ) {
					// Pagination.
					get_template_part( 'template-parts/button', 'create-email-modal-trigger' );
					get_template_part( 'template-parts/modal', 'create-email' );
				}
				?>
			</section>
		</div>
	</div>
</main>
<?php

get_footer();
