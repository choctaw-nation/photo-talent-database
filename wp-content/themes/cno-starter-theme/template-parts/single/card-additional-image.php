<?php
/**
 * Additional Image Card
 *
 * @package ChoctawNation
 */

$image_id = $args['image'] ?? null;
if ( ! $image_id ) {
	wp_die( 'No image ID provided to card-additional-image.php template part.' );
}
?>
<figure class="card">
	<?php
	echo wp_get_attachment_image(
		$image_id,
		'full',
		false,
		array(
			'class'   => 'card-img-top w-100 h-100 object-fit-contain',
			'loading' => 'lazy',
		)
	);
	$image_caption     = wp_get_attachment_caption( $image_id );
	$image_description = get_post_field( 'post_content', $image_id );
	$show_card_body    = $image_caption || $image_description;
	?>
	<?php if ( $show_card_body ) : ?>
		<figcaption class="card-body">
		<?php
		if ( $image_caption ) {
			echo '<p class="mb-0"><span class="fw-bold">Caption: </span>' . esc_html( $image_caption ) . '</p>';
		}
		if ( $image_description ) {
			echo '<p class="mb-0 mt-1"><span class="fw-bold">Description: </span>' . esc_html( $image_description ) . '</p>';
		}
		?>
		</figcaption>
	<?php endif; ?>
</figure>
