<?php
/**
 * Talent Preview Card
 *
 * @package ChoctawNation
 */

$talent_id      = $args['id'] ?? get_the_ID();
$last_used_date = cno_get_last_used_string( $talent_id );
?>
<div class="card border border-2 rounded-3 border-black h-100">
	<?php get_template_part( 'template-parts/talent-preview/card', 'carousel-top', array( 'id' => $talent_id ) ); ?>
	<div class="card-body d-flex flex-column align-items-stretch">
		<h3 class="card-title d-flex flex-wrap align-items-center gap-2 mb-3">
			<?php
			echo get_the_title( $talent_id );
			$is_choctaw = cno_get_is_choctaw( $talent_id );
			if ( 'Choctaw' === $is_choctaw ) {
				echo '<span class="badge text-bg-primary fs-6">Choctaw</span>';
			}
			?>
		</h3>
		<div class="d-flex flex-column mb-5">
			<?php
			$props = array(
				'Last Used' => $last_used_date,
				'Age'       => cno_get_age( $talent_id ),
				'Height'    => get_field( 'height_ft', $talent_id ) . "' " . get_field( 'height_in', $talent_id ) . '"',
				'Gender'    => cno_get_attribute( 'gender', $talent_id ),
			);
			?>
			<?php foreach ( $props as $label => $value ) : ?>
			<p class="mb-0">
				<span class="fw-bold"><?php echo esc_html( $label ); ?>:</span> <?php echo esc_html( $value ); ?>
			</p>
			<?php endforeach; ?>
		</div>
		<?php if ( isset( $args['id'] ) ) : ?>
		<div class="d-flex mt-auto w-auto align-self-end gap-3 flex-wrap">
			<button class="btn btn-outline-danger" data-post-id="<?php echo $talent_id; ?>" data-talent-name="<?php echo get_the_title( $talent_id ); ?>">Remove Talent</button>
		</div>
		<?php else : ?>
		<div class="d-flex mt-auto w-auto align-self-end gap-3 flex-wrap">
			<button class="btn btn-black" data-post-id="<?php echo $talent_id; ?>" data-talent-name="<?php echo get_the_title( $talent_id ); ?>">Select Talent</button>
		</div>
		<?php endif; ?>
	</div>
</div>
