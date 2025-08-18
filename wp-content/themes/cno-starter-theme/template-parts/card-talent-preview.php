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
		<div class="d-flex flex-column mb-3">
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
	</div>
	<div class="btn-group btn-group-sm" role="group" aria-label="Talent Card Actions">
		<?php if ( $is_preview ) : ?>
		<a href="<?php the_permalink(); ?>" class="btn btn-black mt-auto align-self-end">View Talent</a>
		<?php else : ?>
		<a href="<?php the_permalink(); ?>" class="btn rounded-top-0 border-bottom-0 border-start-0 border-end-0 btn-outline-primary">View Talent</a>
		<button type="button" class="btn rounded-top-0 border-bottom-0 border-start-0 border-end-0 btn-outline-primary" data-bs-toggle="dropdown" aria-expanded="false">
			Set As Used
		</button>
		<ul class="dropdown-menu">
			<li>
				<button class="dropdown-item btn btn-link" type="button" data-bs-toggle="offcanvas" data-bs-target="#custom-date-offcanvas" aria-controls="custom-date-offcanvas">Custom
					Date</button>
			</li>
			<li>
				<button class="dropdown-item btn btn-link btn-last-used">Today</button>
			</li>
		</ul>
		<button class="btn rounded-top-0 border-bottom-0 border-start-0 border-end-0 btn-outline-primary btn-select-talent">Select Talent</button>
		<?php endif; ?>
	</div>
</div>
