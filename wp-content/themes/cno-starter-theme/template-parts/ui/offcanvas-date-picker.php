<?php
/**
 * Offcanvas Date Picker
 *
 * @package ChoctawNation
 */

if ( ! is_home() || ! have_posts() ) {
	return;
}
?>
<div class="offcanvas offcanvas-bottom" tabindex="-1" id="custom-date-offcanvas" aria-labelledby="customDateOffCanvasLabel">
	<div class="offcanvas-header">
		<h5 class="offcanvas-title" id="customDateOffCanvasLabel">Custom Date</h5>
		<button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
	</div>
	<div class="offcanvas-body small">
		...
	</div>
</div>
