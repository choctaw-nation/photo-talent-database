<?php
/**
 * Front Page Template
 *
 * @package ChoctawNation
 */

if ( is_user_logged_in() ) {
	wp_safe_redirect( home_url( '/talent' ) );
	exit;
}

?>
<!DOCTYPE html>
<html lang="<?php bloginfo( 'language' ); ?>">

<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php wp_head(); ?>
</head>

<body <?php body_class( 'd-flex flex-column align-items-stretch min-vh-100' ); ?>>
	<?php wp_body_open(); ?>
	<header>
		<div class="container d-flex flex-column flex-md-row justify-content-between align-items-center py-3">
			<a href="<?php echo is_user_logged_in() ? esc_url( home_url( '/talent' ) ) : esc_url( home_url( '/' ) ); ?>" class="d-flex align-items-center mb-3 mb-md-0 text-dark text-decoration-none">
				<img src="<?php echo esc_url( get_template_directory_uri() . '/img/the-great-seal-min.svg' ); ?>" alt="Choctaw Nation of Oklahoma" width="200" height="50" />
				<span><?php echo bloginfo( 'site_title' ); ?></span>
			</a>
		</div>
	</header>
	<main <?php post_class( 'container d-flex flex-column justify-content-center align-items-center flex-grow-1' ); ?>>
		<h1 class="text-center mb-0"><div class="display-5">Choctaw Nation of Oklahoma</div><div class="display-1 fw-bold">Talent Database</div></h1>
		<div class="d-flex flex-wrap column-gap-5 row-gap-3 w-100 justify-content-center">
			<?php
			$btn_classes = array(
				'btn',
				'btn-lg',
				'text-uppercase',
				'rounded-pill',
			);
			$btn_color   = 'black';
			if ( is_user_logged_in() ) {
				printf( '<a href="%s" class="%s btn-outline-%s">View Talent</a>', home_url( '/talent' ), implode( ' ', $btn_classes ), $btn_color );
			}
			?>
			<a href="/apply" class="<?php echo implode( ' ', $btn_classes ) . "  btn-{$btn_color}"; ?>">Apply</a>
		</div>
	</main>
	<footer>
		<div class="container d-flex flex-column flex-md-row justify-content-between align-items-center py-3">
			<p class="mb-0">&copy; <?php echo gmdate( 'Y' ); ?> Choctaw Nation of Oklahoma</p>
			<a href="<?php echo esc_url( home_url( '/privacy-policy' ) ); ?>" class="text-decoration-none">Privacy Policy</a>
		</div>
	</footer>
	<?php wp_footer(); ?>
</body>

</html>
