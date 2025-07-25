<?php
/**
 * Single Template
 *
 * @package ChoctawNation
 */

cno_lock_down_route();
get_header();
?>
<main <?php post_class( 'container my-5 d-flex flex-column align-items-stretch row-gap-5' ); ?>>
	<header class="d-flex flex-column align-items-start row-gap-3">
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item">
					<a href="<?php echo home_url( '/talent' ); ?>" class="link-offset-1 link-offset-2-hover">All Talent</a>
				</li>
				<li class="breadcrumb-item active" aria-current="page">
					<?php the_title(); ?>
				</li>
			</ol>
		</nav>
		<h1 class=" display-1 mb-0">
			<?php the_title(); ?>
		</h1>
		<?php
		if ( 'Choctaw' === cno_get_is_choctaw() ) {
			echo '<p class="badge text-bg-primary fs-6 mb-0">Choctaw</p>';
		}
		get_template_part( 'template-parts/content', 'post-actions' );
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
	<section>
		<h2>Photos</h2>
		<div class="row row-cols-1 row-cols-lg-3 row-gap-4">
			<?php
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
					<div style="background-image:linear-gradient(to bottom,transparent 80%,rgba(0,0,0,.5))" class="position-absolute inset-0 z-1 w-100 h-100">
					</div>
					<?php
						echo wp_get_attachment_image(
							$image_id,
							'full',
							false,
							array(
								'class'   => 'position-absolute z-n1 inset-0 w-100 h-100 object-fit-cover',
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
			<?php endforeach; ?>
		</div>
	</section>
	<?php get_template_part( 'template-parts/content', 'post-actions' ); ?>
</main>
<?php
get_footer();