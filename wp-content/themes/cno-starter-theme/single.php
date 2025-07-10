<?php
/**
 * Single Template
 *
 * @package ChoctawNation
 */

get_header();
?>
<main <?php post_class( 'container my-5 d-flex flex-column align-items-stretch row-gap-5' ); ?>>
	<header>
		<?php the_title( '<h1 class="display-1">', '</h1>' ); ?>
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
			</div>
		</div>
		<div class="col">
			<h2>Attributes</h2>
			<div class="d-flex flex-column">
				<?php
				$fields = array(
					'Height'        => '',
					'Weight'        => 'weight',
					'Date of Birth' => 'dob',
				);
				foreach ( $fields as $label => $key ) {
					if ('Height' === $label) {
						$value = get_field('height_ft') . "' " . get_field('height_in') . '"';
					} elseif ('Date of Birth' === $label) {
						$value = cno_get_age(get_the_ID());
					else {
					$value = get_post_meta( get_the_ID(), $key, true );
					}
					echo "<p class='mb-0'><span class='fw-bold'>{$label}:</span> {$value}</p>";
				}
				?>

			</div>
		</div>
	</section>
	<section>
		<?php the_content(); ?>
	</section>
</main>
<?php
get_footer();