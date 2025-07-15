<?php
/**
 * The Filters
 *
 * @package ChoctawNation
 */

$fields = array(
	'Is Choctaw',
	'Experience',
	'Gender',
	'Skin Tone',
	'Hair Color',
	'Eye Color',
	'Weight',
	'Age Range',
	'Features',
	'Last Used',
);

?>
<details class="card border-primary position-sticky top-0 shadow-sm overflow-hidden" id="sidebar-filters">
	<summary class="h5 m-3 d-lg-none">
		Show Filters
	</summary>
	<div class="d-flex flex-column gap-3 mt-lg-3">
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
</details>


<!--   <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#multiCollapseExample2" aria-expanded="false" aria-controls="multiCollapseExample2">Toggle second element</button>
-->
