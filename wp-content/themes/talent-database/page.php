<?php
/**
 * A blank page.
 *
 * @package ChoctawNation
 */

get_header();
?>
<div <?php post_class( 'alignfull is-layout-constrained has-global-padding' ); ?> style="margin-block:var(--wp--preset--spacing--lg)">
	<?php the_content(); ?>
</div>
<?php
get_footer();
