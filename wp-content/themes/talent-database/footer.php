<?php
/**
 * Basic Footer Template
 *
 * @package ChoctawNation
 */

$global_parts = array(
	'create-pdf-modal-trigger' => 'ui/button',
	'create-pdf'               => 'modal',
	'container'                => 'toast',
);
if ( is_user_logged_in() ) {
	foreach ( $global_parts as $name => $slug ) {
		get_template_part( 'template-parts/' . $slug, $name );
	}
}
?>

<footer class="footer text-bg-black py-4">
	<div class="container-fluid">
		<div class="row row-cols-auto justify-content-between align-items-center">
			<div class="col-auto">
				<a href="<?php echo esc_url( is_user_logged_in() ? home_url( '/talent' ) : home_url() ); ?>" class="fs-base text-decoration-none">
					<?php echo bloginfo( 'name' ); ?>
				</a>
			</div>
			<div class="col" id="copyright">
				<p class="mb-0">
					<?php
					$current_year = ( new DateTime( 'now', wp_timezone() ) )->format( 'Y' );
					echo '&copy;&nbsp;' . $current_year . '&nbsp;Choctaw Nation of Oklahoma';
					?>
				</p>
			</div>
		</div>
	</div>
</footer>
<?php wp_footer(); ?>
</body>

</html>
