<?php
/**
 * Talent Preview Image Carousel
 *
 * @package ChoctawNation
 */

$talent_id   = $args['id'] ?? get_the_ID();
$slug        = 'carousel-' . sanitize_title( get_the_title( $talent_id ) );
$image_names = array( 'front', 'back', 'left', 'right', 'three_quarters' );
$image_args  = array(
	'loading' => 'lazy',
	'class'   => 'h-100 w-100 object-fit-cover d-block carousel-item__image z-1 position-relative',
);

$images = array();

foreach ( $image_names as $name ) {
	$image = get_field( 'image_' . $name, $talent_id );
	if ( $image ) {
		if ( is_array( $image ) ) {
			$images[ $name ] = wp_get_attachment_image( $image['ID'], 'full', false, $image_args );
		} else {
			$images[ $name ] = wp_get_attachment_image( $image, 'full', false, $image_args );
		}
	}
}

?>
<div class="card-img-top overflow-hidden">
	<div id="<?php echo esc_attr( $slug ); ?>" class="carousel slide">
		<?php $controls = array( 'prev', 'next' ); ?>
		<?php foreach ( $controls as $control ) : ?>
		<button class="carousel-control-<?php echo esc_attr( $control ); ?> z-3" type="button" data-bs-target="#<?php echo esc_attr( $slug ); ?>"
				data-bs-slide="<?php echo esc_attr( $control ); ?>">
			<span class="carousel-control-<?php echo esc_attr( $control ); ?>-icon" aria-hidden="true"></span>
			<span class="visually-hidden"><?php echo ucfirst( $control ); ?></span>
		</button>
		<?php endforeach; ?>
		<div class="carousel-indicators z-3 mb-0">
			<?php foreach ( $images as $name => $image ) : ?>
			<?php $active_class = 'front' === $name ? 'active' : ''; ?>
			<button type="button" data-bs-target="#<?php echo esc_attr( $slug ); ?>" data-bs-slide-to="<?php echo esc_attr( array_search( $name, array_keys( $images ), true ) ); ?>"
					class="<?php echo esc_attr( $active_class ); ?> rounded-circle" aria-current="<?php echo esc_attr( $active_class ? 'true' : 'false' ); ?>"
					aria-label="<?php echo esc_attr( ucfirst( $name ) ); ?>"></button>
			<?php endforeach; ?>
		</div>
		<div class="carousel-item__overlay position-absolute z-2"></div>
		<div class="carousel-inner">
			<?php foreach ( $images as $name => $image ) : ?>
			<div class="carousel-item <?php echo 'front' === $name ? 'active' : ''; ?>">
				<?php echo $image; ?>
				<div class="carousel-caption p-0 z-3">
					<p class="mb-0 fs-root"><?php echo 'three_quarters' === $name ? 'Three Quarters' : ucfirst( $name ); ?></p>
				</div>
			</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>