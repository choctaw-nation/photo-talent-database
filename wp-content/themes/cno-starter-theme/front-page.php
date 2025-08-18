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
	<main <?php post_class( 'container d-flex flex-column justify-content-center align-items-center' ); ?>>
		<h1 class="display-1 mb-0 fw-bold text-center"><?php echo bloginfo( 'site_title' ); ?></h1>
		<div class="d-flex flex-wrap column-gap-5 row-gap-3 w-100 justify-content-center">
			<?php
			$btn_classes = array(
				'btn',
				'btn-lg',
				'text-uppercase',
				'rounded-pill',
			);
			$btn_color   = 'black';
			?>
			<?php if ( is_user_logged_in() ) : ?>
			<a href="<?php echo home_url( '/talent' ); ?>" class="<?php echo implode( ' ', $btn_classes ) . " btn-outline-{$btn_color}"; ?>">View Talent</a>
			<?php else : ?>
			<a href="<?php echo wp_login_url(); ?>" class="<?php echo implode( ' ', $btn_classes ) . " btn-outline-{$btn_color}"; ?>">Login</a>
			<?php endif; ?>
			<a href="/apply" class="<?php echo implode( ' ', $btn_classes ) . "  btn-{$btn_color}"; ?>">Apply</a>
		</div>
	</main>
	<?php wp_footer(); ?>
</body>

</html>
