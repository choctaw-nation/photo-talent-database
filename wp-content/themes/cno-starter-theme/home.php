<?php
/**
 * The primary archive page.
 *
 * Included for historical reasons. This file is not used in the theme.
 *
 * @package ChoctawNation
 */

use ChoctawNation\Asset_Loader;
use ChoctawNation\Enqueue_Type;

cno_lock_down_route();
new Asset_Loader( 'talent', Enqueue_Type::script, 'pages' );
get_header();
?>
<main <?php post_class( 'd-flex flex-column align-items-stretch row-gap-5 my-5 container-fluid' ); ?>>
	<header class="text-center">
		<h1 class="display-1 mb-0">All Talent</h1>
	</header>
	<div class="row align-items-stretch gx-3 row-gap-5">
		<section class="col-12 col-lg-3 col-xxl-2 position-relative">
			<?php get_template_part( 'template-parts/sidebar', 'filters' ); ?>
		</section>
		<section class="col d-flex flex-column align-items-stretch row-gap-5" id="talent">
			<?php if ( have_posts() ) : ?>
			<div class="row row-cols-auto row-cols-sm-2 row-cols-md-3 row-cols-xxl-4 gx-2 row-gap-2">
				<?php
				while ( have_posts() ) {
					the_post();
					echo '<div class="col">';
					get_template_part( 'template-parts/card', 'talent-preview' );
					echo '</div>';
				}
				?>
			</div>
				<?php cno_the_pagination(); ?>
			<?php else : ?>
			<p>No posts found!</p>
			<?php endif; ?>
		</section>
	</div>
</main>
<?php
get_footer();
