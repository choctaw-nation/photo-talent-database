<?php
/**
 * Talent Preview Card
 *
 * @package ChoctawNation
 */

$talent_id      = $args['id'] ?? get_the_ID();
$last_used_date = cno_get_last_used_string( $talent_id );
$is_preview     = $args['is_preview'] ?? false;
?>
<div class="card border border-2 rounded-3 border-black h-100" data-post-id="<?php echo $talent_id; ?>" data-talent-name="<?php echo get_the_title( $talent_id ); ?>"
	 id="talent-<?php echo $talent_id; ?>">
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
			$props = $is_preview ? array() : array(
				'Last Used' => $last_used_date,
			);
			$props = array_merge(
				$props,
				array(
					'Age'    => cno_get_age( $talent_id ),
					'Height' => get_field( 'height_ft', $talent_id ) . "' " . get_field( 'height_in', $talent_id ) . '"',
					'Gender' => cno_get_attribute( 'gender', $talent_id ),
				),
			)
			?>
			<?php foreach ( $props as $label => $value ) : ?>
			<p class="mb-0">
				<span class="fw-bold"><?php echo esc_html( $label ); ?>:</span>
				<?php
				if ( 'Last Used' === $label ) {
					echo '<span class="last-used-value">' . esc_html( $value ) . '</span>';
				} else {
					echo esc_html( $value );
				}
				?>
			</p>
			<?php endforeach; ?>
		</div>
		<div class="d-flex mt-auto w-auto align-self-end gap-3 flex-wrap card-footer p-0 bg-transparent border-0">
			<?php if ( $is_preview ) : ?>
			<a href="<?php the_permalink(); ?>" class="btn btn-black mt-auto align-self-end">View Talent</a>
			<?php else : ?>
			<button class="btn btn-outline-black btn-last-used">Set As Used</button>
			<button class="btn btn-black btn-select-talent">Select Talent</button>
			<?php endif; ?>
		</div>
	</div>
</div>