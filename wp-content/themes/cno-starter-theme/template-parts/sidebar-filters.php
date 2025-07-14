<?php
/**
 * The Filters
 *
 * @package ChoctawNation
 */

$fields = array(
	'Last Used',
	'Experience',
	'Gender',
	'Skin Tone',
	'Hair Color',
	'Eye Color',
	'Weight',
	'Age Range',
	'Features',
);

?>
<div class="card border-primary position-sticky top-0 shadow-sm">
	<div class="card-body">
		<div class="d-flex flex-wrap justify-content-between mb-3 align-items-center">
			<h2 class="fs-5 mb-0">Filters</h2>
			<?php echo do_shortcode( "[searchandfilter field='Reset']" ); ?>
		</div>
		<div class="d-flex flex-column gap-3">
			<?php
			foreach ( $fields as $field ) {
				echo do_shortcode( "[searchandfilter field='{$field}']" );
			}
			?>
		</div>
	</div>
</div>