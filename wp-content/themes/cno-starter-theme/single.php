<?php
/**
 * Single Template
 *
 * @package ChoctawNation
 */

get_header();
?>
<main <?php post_class( 'container my-5 d-flex flex-column align-items-stretch row-gap-5' ); ?>>
	<header class="d-flex flex-column align-items-start">
		<h1 class=" display-1 mb-0">
			<?php the_title(); ?>
		</h1>
		<?php
		if ( 'Choctaw' === cno_get_is_choctaw() ) {
				echo '<p class="badge text-bg-primary fs-6 mb-0">Choctaw</p>';
		}
		?>
	</header>
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
				<p><?php the_field( 'features' ); ?></p>
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
	<section>
		<h2>Photos</h2>
		<div class="row row-cols-1 row-cols-lg-3 row-gap-4">
			<?php
			$images    = array( 'front', 'back', 'left', 'right', 'three_quarters' );
			$image_ids = array_map(
				function ( $image ) {
					$image_id = get_field( 'image_' . $image );
					return $image_id ?: null;
				},
				$images
			);
			foreach ( $image_ids as $image_id ) {
				if ( ! $image_id ) {
					continue;
				}
				echo '<div class="col flex-grow-1">';
				echo '<figure class="ratio ratio-2x3 mb-0">';
				echo wp_get_attachment_image(
					$image_id,
					'full',
					false,
					array(
						'class'   => 'w-100 h-100 object-fit-cover',
						'loading' => 'lazy',
					)
				);
				echo '</figure>';
				echo '</div>';
			}
			?>
		</div>
	</section>
</main>
<?php
get_footer();
