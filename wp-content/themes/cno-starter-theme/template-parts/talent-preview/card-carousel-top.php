<?php
/**
 * Talent Preview Image Carousel
 *
 * @package ChoctawNation
 */

$slug        = 'carousel-' . sanitize_title( get_the_title() );
$image_names = array( 'front', 'back', 'left', 'right', 'three_quarters' );
$image_args  = array(
	'loading' => 'lazy',
	'class'   => 'h-100 w-100 object-fit-cover d-block carousel-item__image',
);

$images = array();

foreach ( $image_names as $name ) {
	$image = get_field( 'image_' . $name );
	if ( $image ) {
		$images[ $name ] = wp_get_attachment_image( $image, 'full', false, $image_args );
	}
}

?>
<div class="card-img-top">
	<div id="<?php echo esc_attr( $slug ); ?>" class="carousel slide">
		<div class="carousel-indicators mb-0">
			<?php foreach ( $images as $name => $image ) : ?>
				<?php $active_class = 'front' === $name ? 'active' : ''; ?>
			<button type="button" data-bs-target="#<?php echo esc_attr( $slug ); ?>" data-bs-slide-to="<?php echo esc_attr( array_search( $name, array_keys( $images ), true ) ); ?>"
					class="<?php echo esc_attr( $active_class ); ?> rounded-circle" aria-current="<?php echo esc_attr( $active_class ? 'true' : 'false' ); ?>"
					aria-label="<?php echo esc_attr( ucfirst( $name ) ); ?>"></button>
			<?php endforeach; ?>
		</div>
		<div class="carousel-inner">
			<?php foreach ( $images as $name => $image ) : ?>
			<div class="carousel-item <?php echo 'front' === $name ? 'active' : ''; ?>">
				<div class="carousel-item__overlay position-absolute"></div>
				<?php echo $image; ?>
				<div class="carousel-caption p-0">
					<p class="mb-0"><?php echo 'three_quarters' === $name ? 'Three Quarters' : ucfirst( $name ); ?></p>
				</div>
			</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>
