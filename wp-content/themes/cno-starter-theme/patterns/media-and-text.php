<?php
/**
 * Title: Media and Text
 * Description: A media and text block with a heading, paragraph, and image. Uses Bootstrap's 2 col layout
 * Slug: cno/media-and-text
 * Categories: cno/bootstrap-layouts
 * Viewport Width: 1320
 *
 * @package ChoctawNation
 */

?>
<!-- wp:group {"tagName":"section","metadata":{"name":"Section"},"className":"container","layout":{"type":"constrained"}} -->
<section class="wp-block-group container">
	<!-- wp:group {"metadata":{"name":"Row"},"className":"row row-cols-1 row-cols-lg-2 flex-row-reverse","layout":{"type":"flex","verticalAlignment":"stretch","orientation":"horizontal"}} -->
	<div class="wp-block-group row row-cols-1 row-cols-lg-2 flex-row-reverse">
		<!-- wp:group {"metadata":{"name":"Col"},"className":"col","layout":{"type":"constrained"}} -->
		<div class="wp-block-group col">
			<!-- wp:image {"id":660,"sizeSlug":"large","linkDestination":"none"} -->
			<figure class="wp-block-image size-large"><img src="https://choctaw-print.local/wp-content/uploads/2024/06/choctaw-print-1024x576.jpg" alt="Choctaw Print Services"
					class="wp-image-660" /></figure>
			<!-- /wp:image -->
		</div>
		<!-- /wp:group -->

		<!-- wp:group {"metadata":{"name":"Col"},"className":"col","layout":{"type":"constrained"}} -->
		<div class="wp-block-group col">
			<!-- wp:heading -->
			<h2 class="wp-block-heading">Our Story</h2>
			<!-- /wp:heading -->

			<!-- wp:paragraph -->
			<p>Lorem ipsum dolor sit amet consectetur. Laoreet donec viverra tempus in viverra lacinia mollis sit sagittis. Et in urna ut gravida etiam sodales purus rhoncus diam. Adipiscing
				amet mauris ultricies maecenas at. In mollis fusce quam integer. Nullam nam in sit consequat urna montes ac non.</p>
			<!-- /wp:paragraph -->

		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->
