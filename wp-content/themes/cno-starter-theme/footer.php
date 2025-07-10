<?php
/**
 * Basic Footer Template
 *
 * @package ChoctawNation
 */

?>

<footer class="footer text-bg-black py-3">
	<div class="container d-flex flex-column row-gap-4">
		<div class="row">
			<?php
			if ( has_nav_menu( 'footer_menu' ) ) {
				wp_nav_menu(
					array(
						'theme_location'  => 'footer_menu',
						'menu_class'      => 'footer-nav list-unstyled navbar-nav flex-row',
						'container'       => 'nav',
						'container_class' => 'navbar',
						'depth'           => 1,
					)
				);
			}
			?>
		</div>
		<div class="row">
			<div class="col-auto">
				<a href="<?php echo esc_url( site_url() ); ?>" class="logo">
					<figure class="logo-img d-inline-block">
						<span aria-label="to Home Page">
							<?php echo bloginfo( 'name' ); ?>
						</span>
					</figure>
				</a>
			</div>
		</div>
		<div class="row">
			<div class="col text-center" id="copyright">
				<?php echo '&copy;&nbsp;' . date( 'Y' ) . '&nbsp;Choctaw Nation of Oklahoma'; // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date ?>
			</div>
		</div>
	</div>
</footer>
<?php wp_footer(); ?>
</body>

</html>