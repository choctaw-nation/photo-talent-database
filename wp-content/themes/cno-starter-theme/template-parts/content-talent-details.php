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
					echo "<p class='mb-0'><span class='fw-bold'>{$label}:</span> " . get_post_meta( $talent_id, $key, true ) . '</p>';
				}
				?>
			<?php if ( get_field( 'features' ) ) : ?>
			<div class="features w-auto align-self-start flex-grow-0 mt-3">
				<h3 class="fs-5 mb-0">Distinguishing Features:</h3>
				<p><?php the_field( 'features', $talent_id ); ?></p>
			</div>
			<?php endif; ?>
		</div>
	</div>
	<div class="col">
		<h2>Attributes</h2>
		<div class="d-flex flex-column">
			<?php
				$fields = array(
					'Height' => get_field( 'height_ft', $talent_id ) . "' " . get_field( 'height_in', $talent_id ) . '"',
					'Weight' => get_field( 'weight', $talent_id ) . ' lbs',
					'Age'    => cno_get_age( $talent_id ),
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
					$value = cno_get_attribute( $key, $talent_id );
					if ( ! empty( $value ) ) {
						echo "<p class='mb-0'><span class='fw-bold'>{$label}:</span> {$value}</p>";
					}
				}
				?>
		</div>
	</div>
</section>