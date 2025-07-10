<?php
/**
 * Talent Preview Card
 *
 * @package ChoctawNation
 */

?>
<div class="card border border-2 rounded-3 border-black">
	<div class="card-body d-flex flex-column align-items-stretch">
		<?php the_title( '<h3>', '</h3>' ); ?>
		<p class="card-text"><?php the_excerpt(); ?></p>
		<div class="d-flex mt-auto w-auto align-self-end gap-3 flex-wrap">
			<a href="<?php the_permalink(); ?>" class="btn btn-outline-black">View Details</a>
			<button class="btn btn-black">Add to Cart</button>
		</div>
	</div>
</div>