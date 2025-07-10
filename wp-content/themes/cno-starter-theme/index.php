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
<main <?php post_class( 'd-flex flex-column align-items-stretch row-gap-5' ); ?>>
	<header class="container">
		<h1 class="display-1"><?php the_archive_title(); ?></h1>
	</header>
	<section class="text-bg-primary py-5">
		<div class="container">
			<div class="row">
				<div class="col">Filters</div>
			</div>
		</div>
	</section>
	<section class="container">
		<?php if ( have_posts() ) : ?>
		<div class="row-cols-1 row-cols-lg-3">
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
	</section>
</main>
<?php
get_footer();