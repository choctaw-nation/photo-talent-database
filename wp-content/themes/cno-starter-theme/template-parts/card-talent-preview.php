<?php
/**
 * Talent Preview Card
 *
 * @package ChoctawNation
 */

$last_used_date = get_field( 'last_used' );
if ( ! $last_used_date ) {
	$last_used_date = 'N/A';
} else {
	$last_used_datetime = DateTime::createFromFormat( 'Ymd', $last_used_date, wp_timezone() );
	$last_used_date     = $last_used_datetime ? $last_used_datetime->format( 'F j, Y' ) : 'N/A';
}
?>
<div class="card border border-2 rounded-3 border-black h-100">
	<?php get_template_part( 'template-parts/talent-preview/card', 'carousel-top' ); ?>
	<div class="card-body d-flex flex-column align-items-stretch">
		<h3 class="card-title d-flex flex-wrap align-items-center gap-2 mb-3">
			<?php
			the_title();
			$is_choctaw = cno_get_is_choctaw();
			if ( 'Choctaw' === cno_get_is_choctaw() ) {
				echo '<span class="badge text-bg-primary fs-6">Choctaw</span>';
			}
			?>
		</h3>
		<div class="d-flex flex-column mb-5">
			<?php
			$props = array(
				'Last Used' => $last_used_date,
				'Age'       => cno_get_age(),
				'Height'    => get_field( 'height_ft' ) . "' " . get_field( 'height_in' ) . '"',
				'Gender'    => cno_get_attribute( 'gender' ),
			);
			?>
			<?php foreach ( $props as $label => $value ) : ?>
			<p class="mb-0">
				<span class="fw-bold"><?php echo esc_html( $label ); ?>:</span> <?php echo esc_html( $value ); ?>
			</p>
			<?php endforeach; ?>
		</div>
		<div class="d-flex mt-auto w-auto align-self-end gap-3 flex-wrap">
			<a href="<?php the_permalink(); ?>" class="btn btn-outline-black">View Details</a>
			<button class="btn btn-black" data-post-id="<?php echo get_the_ID(); ?>">Add to Cart</button>
		</div>
	</div>
</div>
