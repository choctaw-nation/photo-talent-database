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
<div class="card border border-2 rounded-3 border-black h-100" data-post-id="<?php echo $talent_id; ?>" data-talent-name="<?php echo esc_textarea( get_the_title( $talent_id ) ); ?>"
	id="talent-<?php echo $talent_id; ?>">
	<?php get_template_part( 'template-parts/talent-preview/card', 'carousel-top', array( 'id' => $talent_id ) ); ?>
	<div class="card-body d-flex flex-column align-items-stretch row-gap-3">
		<div class="d-flex flex-wrap align-items-center gap-2">
			<h3 class="card-title mb-0 fs-6">
				<?php echo esc_textarea( get_the_title( $talent_id ) ); ?>
			</h3>
			<?php
			$tribal_status    = cno_get_is_choctaw( $talent_id );
			$is_choctaw       = 'Choctaw Tribal Member' === $tribal_status;
			$is_tribal_member = 'Tribal Member' === $tribal_status;
			$badge_classes    = array( 'badge', 'fs-root' );
			if ( $is_choctaw ) {
				$badge_classes[] = 'text-bg-primary';
			} elseif ( $is_tribal_member ) {
				$badge_classes[] = 'text-bg-warning';
			}
			if ( $is_choctaw ) {
				echo '<span class="' . esc_attr( implode( ' ', $badge_classes ) ) . '">Choctaw</span>';
			} elseif ( $is_tribal_member ) {
				echo '<span class="' . esc_attr( implode( ' ', $badge_classes ) ) . '">Tribal Member</span>';
			}
			?>
		</div>
		<div class="d-flex flex-column">
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
			<?php
				if ( empty( $value ) ) {
					continue;
				}
				if ( 'Height' === $label && "' \"" === $value ) {
					continue;
				}
				?>
			<p class="mb-0 d-flex flex-wrap gap-2 align-items-center">
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
	<div class="btn-group btn-group-sm flex-wrap" role="group" aria-label="Talent Card Actions">
		<?php if ( $is_preview ) : ?>
		<a href="<?php the_permalink(); ?>" class="btn btn-black mt-auto align-self-end">View Talent</a>
		<?php else : ?>
		<button type="button" class="btn rounded-top-0 border-bottom-0 border-start-0 border-end-0 btn-outline-primary" <?php echo get_the_talent_modal_trigger_attributes(); ?>>
			View Talent
		</button>
		<button type="button" class="btn rounded-top-0 border-bottom-0 border-start-0 border-end-0 btn-outline-primary" data-bs-toggle="dropdown" data-bs-auto-close="outside"
			aria-expanded="false">
			Set As Used
		</button>
		<ul class="dropdown-menu">
			<li>
				<button class="dropdown-item btn btn-link btn-last-used-custom" type="button" aria-controls="custom-date-offcanvas">Custom
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