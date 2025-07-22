<?php
/**
 * Basic Header Template
 *
 * @package ChoctawNation
 */

$anchor_url = is_user_logged_in() ? home_url( '/talent' ) : home_url();
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
	<header class="text-bg-black" id="site-header">
		<div class="container-fluid py-4">
			<div class="row row-cols-auto justify-content-between align-items-center">
				<div class="col">
					<a class="fs-2 text-white text-decoration-none" href="<?php echo esc_url( $anchor_url ); ?>">
						<?php echo bloginfo( 'title' ); ?>
					</a>

				</div>
				<div class="col">
					<a href="<?php echo is_user_logged_in() ? wp_logout_url( home_url() ) : wp_login_url(); ?>" class="btn btn-outline-white rounded-pill">
						<?php echo is_user_logged_in() ? 'Logout' : 'Login'; ?></a>
				</div>
			</div>
		</div>
	</header>