<?php
/**
 * Single Template
 *
 * @package ChoctawNation
 */

cno_lock_down_route();
get_header();
?>
<main <?php post_class( 'container my-5 d-flex flex-column align-items-stretch row-gap-5' ); ?>>
	<header class="d-flex flex-column align-items-start row-gap-3">
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item">
					<a href="<?php echo home_url( '/talent' ); ?>" class="link-offset-1 link-offset-2-hover">All Talent</a>
				</li>
				<li class="breadcrumb-item active" aria-current="page">
					<?php the_title(); ?>
				</li>
			</ol>
		</nav>
		<h1 class=" display-1 mb-0">
			<?php the_title(); ?>
		</h1>
		<?php
		if ( 'Choctaw' === cno_get_is_choctaw() ) {
			echo '<p class="badge text-bg-primary fs-6 mb-0">Choctaw</p>';
		}
		get_template_part( 'template-parts/single/buttons', 'post-actions' );
		?>
	</header>
	<?php get_template_part( 'template-parts/single/content', 'talent-details' ); ?>
	<section class="d-flex flex-column row-gap-4 align-items-stretch">
		<div>
			<h2 class="mb-0">Photos</h2>
			<div class="row row-cols-1 row-cols-lg-3 row-gap-4">
				<?php get_template_part( 'template-parts/single/content', 'talent-images' ); ?>
			</div>
		</div>
		<?php $additional_images = get_field( 'additional_images' ); ?>
	</section>
	<?php if ( $additional_images ) : ?>
	<section>
		<h2 class="mb-0">Additional Images</h2>
		<div class="row row-cols-auto row-cols-lg-3 row-gap-4">
			<?php foreach ( $additional_images as $image ) : ?>
			<div class="col flex-grow-1 flex-lg-grow-0">
				<?php get_template_part( 'template-parts/single/card', 'additional-image', array( 'image' => $image ) ); ?>
			</div>
			<?php endforeach; ?>
		</div>
	</section>
	<?php endif; ?>
	<?php get_template_part( 'template-parts/single/buttons', 'post-actions' ); ?>
</main>
<?php
get_footer();
