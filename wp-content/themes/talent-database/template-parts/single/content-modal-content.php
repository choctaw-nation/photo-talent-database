<?php
/**
 * Modal Content
 * Used by REST endpoint to return content for the single talent modal
 *
 * @package ChoctawNation
 */

$args['id'] = isset( $args['id'] ) ? $args['id'] : get_the_ID();
get_template_part( 'template-parts/single/content', 'talent-details', array( 'id' => $args['id'] ) );
?>
<section>
	<h2 class="fs-5 mb-0">Photos</h2>
	<div class="gap-3 gx-0 align-items-stretch" style="display:grid;grid-template-columns: repeat(auto-fit, minmax(30%, 1fr))">
		<?php
		$images    = array( 'front', 'back', 'left', 'right', 'three_quarters' );
		$image_ids = array_map(
			function ( $image ) use ( $args ) {
				$image_arr = get_field( 'image_' . $image, $args['id'] );
				return is_array( $image_arr ) ? $image_arr['ID'] : (int) $image_arr;
			},
			$images
		);
		?>
		<?php foreach ( $image_ids as $index => $image_id ) : ?>
		<?php
			if ( ! $image_id ) {
				continue;
			}
			?>
		<div class="col">
			<figure class="d-flex flex-column h-100 mb-0" style="aspect-ratio: 2 / 3">
				<?php
				echo wp_get_attachment_image(
					$image_id,
					'full',
					false,
					array(
						'class'   => 'w-100 h-auto object-fit-contain',
						'loading' => 'lazy',
					)
				);
				?>
				<figcaption class="text-center fs-5">
					<?php echo 'three_quarters' === $images[ $index ] ? 'Three Quarters' : ucfirst( $images[ $index ] ); ?>
				</figcaption>
			</figure>
		</div>
		<?php endforeach; ?>
	</div>
</section>