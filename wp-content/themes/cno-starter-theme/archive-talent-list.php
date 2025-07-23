<?php
/**
 * Talent List Single Template
 *
 * @package ChoctawNation
 */

use ChoctawNation\Asset_Loader;
use ChoctawNation\Bootstrap_Pagination;
use ChoctawNation\Enqueue_Type;

cno_lock_down_route( ! current_user_can( 'edit_talent-lists' ) );
new Asset_Loader( 'talentList', Enqueue_Type::script, 'pages' );
new Asset_Loader( 'pdfGenerator', Enqueue_Type::script, 'pages' );
get_header();
?>
<main <?php post_class( 'container my-5 d-flex flex-column align-items-stretch row-gap-5' ); ?>>
	<header class="text-center">
		<h1 class="display-1 mb-0">
			Talent Lists
		</h1>
	</header>
	<?php if ( have_posts() ) : ?>
	<section class="row row-cols-auto row-cols-lg-3 row-gap-4 align-items-stretch">
		<?php while ( have_posts() ) : ?>
			<?php the_post(); ?>
		<div class="col">
			<div class="card border-black">
				<div class="card-body">
					<h2 class="card-title"><?php echo substr( get_the_title(), 12 ); ?></h2>
					<div class="meta d-flex flex-wrap gap-2">
						<p class="badge text-bg-secondary mb-0">Created on:
							<?php
							$creation_date_string = substr( get_the_title(), 0, 8 );
							$date_object          = DateTime::createFromFormat( 'Ymd', $creation_date_string, wp_timezone() );
							if ( ! $date_object ) {
								$creation_date_string = 'N/A';
							} else {
								$creation_date_string = $date_object->format( 'F j, Y' );
							}
							echo $creation_date_string;
							?>
							</span>
						</p>
						<p class="badge text-bg-danger mb-0">Expires On:
							<?php
							$expiry_date_string = get_field( 'post_expiry' );
							$expiry_date_object = DateTime::createFromFormat( 'Ymd', $expiry_date_string, wp_timezone() );
							if ( ! $expiry_date_object ) {
								$expiry_date_string = 'N/A';
							} else {
								$expiry_date_string = $expiry_date_object->format( 'F j, Y' );
							}
							echo $expiry_date_string;
							?>
						</p>
					</div>
					<?php if ( has_excerpt() ) : ?>
					<p class="card-text"><?php the_excerpt(); ?></p>
					<?php endif; ?>
					<div class="actions-container d-flex flex-wrap justify-content-end align-items-start gap-2 mt-auto">
						<?php get_template_part( 'template-parts/button', 'delete-list' ); ?>
						<a href="<?php the_permalink(); ?>" class="btn btn-black">View List</a>
					</div>
				</div>
			</div>
		</div>
		<?php endwhile; ?>
	</section>
		<?php cno_the_pagination(); ?>
	</div>
	<?php else : ?>
	<p>No talent lists found.</p>
	<?php endif; ?>
</main>
<?php
get_footer();
