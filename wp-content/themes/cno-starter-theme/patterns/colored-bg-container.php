<?php
/**
 * Title: Colored BG Container
 * Description: A bootstrap container with a colored background.
 * Slug: cno/colored-bg-container
 * Categories: cno/bootstrap-layouts
 * Viewport Width: 1320
 *
 * @package ChoctawNation
 */

?>
<!-- wp:group {"metadata":{"name":"Background"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|xl","bottom":"var:preset|spacing|xl"}},"elements":{"link":{"color":{"text":"var:preset|color|light"}}}},"backgroundColor":"primary","textColor":"light","layout":{"type":"constrained"}} -->
<div class="wp-block-group has-light-color has-primary-background-color has-text-color has-background has-link-color"
	style="padding-top:var(--wp--preset--spacing--xl);padding-bottom:var(--wp--preset--spacing--xl)">
	<!-- wp:group {"metadata":{"name":"Container"},"className":"container","layout":{"type":"constrained"}} -->
	<div class="wp-block-group container">
		<!-- wp:group {"metadata":{"name":"Row"},"className":"row","layout":{"type":"constrained"}} -->
		<div class="wp-block-group row">
			<!-- wp:group {"metadata":{"name":"Col"},"className":"col","layout":{"type":"constrained"}} -->
			<div class="wp-block-group col">
				<!-- wp:heading -->
				<h2 class="wp-block-heading">Heading El 2</h2>
				<!-- /wp:heading -->
				<!-- wp:paragraph -->
				<p>Paragraph</p>
				<!-- /wp:paragraph -->
			</div><!-- /wp:group -->
		</div><!-- /wp:group -->
	</div><!-- /wp:group -->
</div><!-- /wp:group -->
