<?php
/**
 * Talent Details
 *
 * @package ChoctawNation
 */

$talent_id           = isset( $args['id'] ) ? $args['id'] : get_the_ID();
$contact_fields      = array(
	'Email' => 'email',
	'Phone' => 'phone',
);
$have_contact_fields = array_reduce(
	$contact_fields,
	function ( $carry, $key ) use ( $talent_id ) {
		return $carry || ! empty( get_field( $key, $talent_id ) );
	},
	false
);
?>
<section class="row row-cols-1 row-cols-lg-auto row-gap-4">
	<?php if ( $have_contact_fields ) : ?>
	<div class="col">
		<h2 class="fs-5">About</h2>
		<div class="d-flex flex-column row-gap-2">
			<?php
			foreach ( $contact_fields as $label => $key ) {
				echo "<p class='mb-0'><span class='fw-bold'>{$label}:</span> " . get_field( $key, $talent_id, escape_html: true ) . '</p>';
			}
			?>
			<?php if ( get_field( 'features', $talent_id ) ) : ?>
			<div class="features w-auto align-self-start flex-grow-0 mt-3">
				<h3 class="fs-6 mb-0">Distinguishing Features:</h3>
				<p><?php the_field( 'features', $talent_id ); ?></p>
			</div>
			<?php endif; ?>
		</div>
	</div>
	<?php endif; ?>
	<?php
	$details_fields = array(
		'Weight' => empty( get_field( 'weight', $talent_id ) ) ? '' : get_field( 'weight', $talent_id ) . ' lbs',
		'Age'    => cno_get_age( $talent_id ),
	);
	$height_ft      = get_field( 'height_ft', $talent_id );
	$height_in      = get_field( 'height_in', $talent_id );
	if ( $height_ft || $height_in ) {
		$details_fields['Height'] = trim( $height_ft . "' " . $height_in . '"' );
	} else {
		$details_fields['Height'] = '';
	}
	$have_details     = array_reduce(
		$details_fields,
		function ( $carry, $value ) {
			return $carry || ! empty( $value );
		},
		false
	);
	$attribute_fields = array(
		'Gender'     => 'gender',
		'Experience' => 'experience',
		'Eye Color'  => 'eye-color',
		'Hair Color' => 'hair-color',
	);
	$have_attributes  = array_reduce(
		$attribute_fields,
		function ( $carry, $key ) use ( $talent_id ) {
			$value = cno_get_attribute( $key, $talent_id );
			return $carry || ! empty( $value );
		},
		false
	);
	?>
	<?php if ( ( $have_details || $have_attributes ) ) : ?>
	<div class="col">
		<h2 class="fs-5">Attributes</h2>
		<div class="d-flex flex-column">
			<?php
			if ( $have_details ) {
				foreach ( $details_fields as $label => $value ) {
					if ( empty( $value ) ) {
						continue;
					}
					echo "<p class='mb-0'><span class='fw-bold'>{$label}:</span> {$value}</p>";
				}
			}
			if ( $have_attributes ) {
				foreach ( $attribute_fields as $label => $key ) {
					$value = cno_get_attribute( $key, $talent_id );
					if ( ! empty( $value ) ) {
						echo "<p class='mb-0'><span class='fw-bold'>{$label}:</span> {$value}</p>";
					}
				}
			}
			?>
		</div>
	</div>
	<?php endif; ?>
	<?php
	$clothing_field_keys = array( 'shirt_size', 'shoe_size' );
	$gender              = cno_get_attribute( 'gender', $talent_id );
	if ( 'Male' === $gender ) {
		$clothing_field_keys = array( ...$clothing_field_keys, 'pant_size_length', 'pant_size_width', 'suit_size' );
	} elseif ( 'Female' === $gender ) {
		$clothing_field_keys = array( ...$clothing_field_keys, 'dress_size', 'pant_size_width' );
	}
	$clothing_fields      = array_combine(
		$clothing_field_keys,
		array_map(
			function ( $key ) use ( $talent_id ) {
				return get_field( $key, $talent_id );
			},
			$clothing_field_keys
		)
	);
	$have_clothing_fields = array_reduce(
		$clothing_fields,
		function ( $carry, $value ) {
			return $carry || ! empty( $value );
		},
		false
	);
	?>
	<?php if ( $have_clothing_fields ) : ?>
	<div class="col">
		<h2 class="fs-5">Clothing Sizes</h2>
		<div class="d-flex flex-column">
			<?php
			$pants            = array();
			$pants_did_render = false;
			foreach ( $clothing_fields as $key => $value ) {
				if ( empty( $value ) ) {
					continue;
				}
				if ( 'pant_size_length' === $key ) {
					$pants['length'] = $value;
				}
				if ( 'pant_size_width' === $key ) {
					$pants['width'] = $value;
				}
				if ( in_array( $key, array( 'pant_size_length', 'pant_size_width' ), true ) && 'Male' === $gender ) {
					if ( empty( $pants['length'] ) || empty( $pants['width'] ) ) {
						continue;
					}
					if ( false === $pants_did_render ) {
						echo "<p class='mb-0'><span class='fw-bold'>Pants:</span> {$pants['width']} x {$pants['length']}</p>";
						$pants_did_render = true;
					}
				} else {
					$label = ucwords( str_replace( '_', ' ', $key ) );
					echo "<p class='mb-0'><span class='fw-bold'>{$label}:</span> {$value}</p>";

				}
			}
			?>
		</div>
	</div>
	<?php endif; ?>
</section>
