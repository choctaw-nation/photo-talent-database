<?php
/**
 * Talent Details
 *
 * @package ChoctawNation
 */

$talent_id = isset( $args['id'] ) ? $args['id'] : get_the_ID();
?>
<section class="row row-cols-1 row-cols-lg-2 row-gap-4">
	<div class="col">
		<h2>About</h2>
		<div class="d-flex flex-column row-gap-2">
			<?php
				$fields = array(
					'Email' => 'email',
					'Phone' => 'phone',
				);
				foreach ( $fields as $label => $key ) {
					echo "<p class='mb-0'><span class='fw-bold'>{$label}:</span> " . get_post_meta( get_the_ID(), $key, true ) . '</p>';
				}
				?>
			<?php if ( get_field( 'features' ) ) : ?>
			<div class="features w-auto align-self-start flex-grow-0 mt-3">
				<h3 class="fs-5 mb-0">Distinguishing Features:</h3>
				<p><?php the_field( 'features' ); ?></p>
			</div>
			<?php endif; ?>
		</div>
	</div>
	<div class="col">
		<h2>Attributes</h2>
		<div class="d-flex flex-column">
			<?php
				$fields = array(
					'Height' => get_field( 'height_ft' ) . "' " . get_field( 'height_in' ) . '"',
					'Weight' => get_field( 'weight' ) . ' lbs',
					'Age'    => cno_get_age(),
				);
				foreach ( $fields as $label => $value ) {
					echo "<p class='mb-0'><span class='fw-bold'>{$label}:</span> {$value}</p>";
				}
				$attributes = array(
					'Gender'     => 'gender',
					'Experience' => 'experience',
					'Eye Color'  => 'eye-color',
					'Hair Color' => 'hair-color',
				);
				foreach ( $attributes as $label => $key ) {
					$value = cno_get_attribute( $key );
					if ( ! empty( $value ) ) {
						echo "<p class='mb-0'><span class='fw-bold'>{$label}:</span> {$value}</p>";
					}
				}
				?>
		</div>
	</div>
</section>
