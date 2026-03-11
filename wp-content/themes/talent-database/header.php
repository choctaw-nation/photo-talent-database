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
	<header class="text-bg-black container-fluid" id="site-header">
		<nav class="navbar navbar-expand-md justify-content-md-between">
			<div class="col-6 col-md-auto">
				<a class="navbar-brand" href="<?php echo esc_url( $anchor_url ); ?>">
					<?php echo bloginfo( 'title' ); ?>
				</a>
			</div>
			<button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="offcanvas offcanvas-end flex-md-grow-0" tabindex="-1" id="offcanvasNavbar" style="--bs-offcanvas-bg:black">
				<?php get_template_part( 'template-parts/menu', 'main-menu' ); ?>
			</div>
		</nav>
	</header>
