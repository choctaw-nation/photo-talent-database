<?php
/**
 * Title: TWM Diamond Separator
 * Description: The stylized Hr element with diamonds in the center from the together we're more site
 * Slug: twm/diamond-separator
 * Categories: twm/ui
 * Viewport Width: 1320
 *
 * @package ChoctawNation
 */

$class_list = isset( $args['class'] ) ? $args['class'] : '';
$color      = isset( $args['color'] ) ? $args['color'] : 'white';

$custom_classes  = $class_list && is_array( $class_list ) ? $class_list : array( $class_list );
$default_classes = array(
	'diamond-separator',
	'd-flex',
	'align-items-center',
	'justify-content-center',
	'position-relative',
	'column-gap-2',
);

$classes = array_merge( $default_classes, $custom_classes );

$hr_classes   = array( 'opacity-100 border-1 position-relative flex-shrink-1 flex-grow-1 my-0' );
$hr_classes[] = 'white' !== $color ? 'border-dark' : 'border-white';

?>
<figure class="<?php echo join( ' ', $classes ); ?>" aria-label="a stylized horizontal splitter with diamonds in the center">
	<hr class="<?php echo join( ' ', $hr_classes ); ?>" aria-hidden="true" />
	<div class="diamonds" style="<?php echo "--color:var(--bs-{$color});"; ?>" aria-hidden="true">
		<svg xmlns='http://www.w3.org/2000/svg' height='16' viewBox='0 0 91.515 18.303'>
			<g data-name='Group 15' style='isolation: isolate'>
				<path d='M0,9.151,15.252,0,30.5,9.151,15.252,18.3Z' fill='var(--color)'></path>
				<path data-name='diamond' d='M0,9.151,15.252,0,30.5,9.151,15.252,18.3Z' transform='translate(30.505)' fill='var(--color)'></path>
				<path data-name='diamond' d='M0,9.151,15.252,0,30.5,9.151,15.252,18.3Z' transform='translate(61.01)' fill='var(--color)'></path>
			</g>
		</svg>
	</div>
	<hr class="<?php echo join( ' ', $hr_classes ); ?>" aria-hidden="true" />
</figure>
