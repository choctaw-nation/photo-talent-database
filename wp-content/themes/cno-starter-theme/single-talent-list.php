<?php
/**
 * Talent List Single Template
 *
 * @package ChoctawNation
 */

use ChoctawNation\Asset_Loader;
use ChoctawNation\Enqueue_Type;

cno_lock_down_route( ! current_user_can( 'edit_talent-lists' ) );
new Asset_Loader( 'talentList', Enqueue_Type::script, 'pages' );
new Asset_Loader( 'pdfGenerator', Enqueue_Type::script, 'pages' );
get_header();
?>
<main <?php post_class( 'container my-5' ); ?>>
	<header>
		<h1 class="mb-0">
			<?php the_title(); ?>
		</h1>
		<?php if ( has_excerpt() ) : ?>
		<p class="text-muted mb-0">
			<?php echo get_the_excerpt(); ?>
		</p>
		<?php endif; ?>
	</header>
	<div class="row row-cols-1 row-cols-sm-auto flex-row-reverse row-gap-4 mt-4">
		<section class="col flex-grow-1">
			<h2>Selected Talent</h2>
			<?php $selected_talent = get_field( 'selected_talent' ); ?>
			<?php if ( ! empty( $selected_talent ) ) : ?>
			<ul class="list-group rounded-1" id="selected-talent-list">
				<?php foreach ( $selected_talent as $talent_id ) : ?>
				<li class="list-group-item list-group-item-action ps-2 d-flex flex-wrap gap-3 align-items-center justify-content-between">
					<div class="d-flex gap-3 flex-wrap align-items-center">
						<figure class="mb-0 ratio ratio-1x1 overflow-hidden rounded-circle" style="width:75px; height:75px;">
							<?php
							echo wp_get_attachment_image(
								get_field( 'image_front', $talent_id ),
								'medium',
								false,
								array(
									'class'   => 'w-100 h-100 object-fit-cover',
									'loading' => 'lazy',
								)
							);
							?>
						</figure>
						<div class="d-flex flex-column gap-2">
							<h3 class="fs-5 mb-0">
								<?php echo get_the_title( $talent_id ); ?>
							</h3>
							<div class="d-flex flex-wrap gap-2">
								<?php echo 'Choctaw' === cno_get_is_choctaw( $talent_id ) ? '<span class="badge text-bg-primary fw-normal fs-root">Choctaw</span>' : ''; ?>
								<?php $last_used = cno_get_last_used_string( $talent_id ); ?>
								<?php echo $last_used ? '<span class="badge bg-secondary fw-normal w-auto fs-root">Last Used: ' . esc_html( $last_used ) . '</span>' : ''; ?>
							</div>
						</div>
					</div>
					<button class="btn-close stretched-link" data-post-id="<?php echo $talent_id; ?>"><span class="visually-hidden">Close</span></button>
				</li>
				<?php endforeach; ?>
			</ul>
			<div class="mt-4 d-flex gap-2" id="selected-talent-list-footer">
				<?php get_template_part( 'template-parts/button', 'generate-pdf-button' ); ?>
			</div>
			<?php else : ?>
			<p class="text-muted">No talent selected.</p>
			<?php endif; ?>
		</section>
	</div>
	<?php comments_template(); ?>
</main>
<?php
get_template_part( 'template-parts/toast', 'container' );
get_footer();