<?php
/**
 * Talent Images
 *
 * @package ChoctawNation
 */

$images    = array( 'front', 'back', 'left', 'right', 'three_quarters' );
$image_ids = array_map(
	function ( $image ) {
		$image_arr = get_field( 'image_' . $image );
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
<div class="col flex-grow-1">
	<figure class="position-relative mb-0 d-flex flex-column justify-content-end" style="aspect-ratio: 2 / 3">
		<div style="background-image:linear-gradient(to bottom,transparent 80%,rgba(0,0,0,.5))" class="position-absolute inset-0 z-1 w-100 h-100"></div>
		<?php
			echo wp_get_attachment_image(
				$image_id,
				'full',
				false,
				array(
					'class'   => 'position-absolute z-n1 inset-0 w-100 h-100 object-fit-contain',
					'loading' => 'lazy',
				)
			);
		?>
		<figcaption class="z-2 text-white text-center fs-5 mb-2">
			<p class="mb-0">
				<?php echo 'three_quarters' === $images[ $index ] ? 'Three Quarters' : ucfirst( $images[ $index ] ); ?>
			</p>
		</figcaption>
	</figure>
</div>
	<?php
endforeach;
