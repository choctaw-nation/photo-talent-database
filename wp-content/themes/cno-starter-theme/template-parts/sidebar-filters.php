<?php
/**
 * The Filters
 *
 * @package ChoctawNation
 */

$fields = array(
	'Is Choctaw',
	'Gender',
	'Skin Tone',
	'Hair Color',
	'Eye Color',
	'Weight',
	'Age Range',
	'Features',
	'Last Used',
	'Experience',
);

?>
<div class="accordion">
	<div class="accordion-item border-primary overflow-hidden">
		<h2 class="accordion-header">
			<button class="accordion-button fs-5 fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-filters" aria-expanded="true" aria-controls="sidebar-filters">
				Show Filters
			</button>
		</h2>
		<div class="accordion-collapse collapse show" id="sidebar-filters">
			<div class="d-flex flex-column gap-3 mt-3">
				<div class="d-flex flex-wrap justify-content-between align-items-center mx-3">
					<h2 class="fs-5 mb-0">Filters</h2>
					<?php echo do_shortcode( "[searchandfilter field='Reset']" ); ?>
				</div>
				<div class="list-group list-group-flush">
					<?php
					foreach ( $fields as $field ) {
						echo '<div class="list-group-item list-group-item-action rounded-0">';
						echo do_shortcode( "[searchandfilter field='{$field}']" );
						echo '</div>';
					}
					?>
				</div>
			</div>
		</div>
	</div>
</div>