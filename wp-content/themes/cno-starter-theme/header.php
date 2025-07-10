<?php
/**
 * Basic Header Template
 *
 * @package ChoctawNation
 */

use ChoctawNation\Navwalker;
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
		<div class="container text-center py-4">
			<a class="fs-2 text-white text-decoration-none" href="<?php echo esc_url( site_url() ); ?>">
				<?php echo bloginfo( 'title' ); ?>
			</a>
		</div>
	</header>