<?php
/**
 * Template Name: Pending Talent
 * Description: A page template for displaying pending talent.
 *
 * @package ChoctawNation
 */

$pending_talent = new WP_Query(
	array(
		'post_type'   => 'post',
		'post_status' => 'pending,draft',
	)
);
cno_lock_down_route( ! $pending_talent->have_posts() );
get_header();
?>
<main <?php post_class( 'container my-5 d-flex flex-column row-gap-5 align-items-stretch' ); ?>>
	<header>
		<h1 class="display-1 text-center mb-0">Pending Talent</h1>
	</header>
	<section class="row row-cols-1 row-cols-md-auto row-cols-lg-3 row-gap-4">
		<?php while ( $pending_talent->have_posts() ) : ?>
		<?php $pending_talent->the_post(); ?>
		<div class="col">
			<?php get_template_part( 'template-parts/card', 'talent-preview', array( 'is_preview' => true ) ); ?>
		</div>
		<?php endwhile; ?>
		<?php wp_reset_postdata(); ?>
	</section>
	<?php cno_the_pagination( $pending_talent ); ?>
</main>
<?php
get_footer();