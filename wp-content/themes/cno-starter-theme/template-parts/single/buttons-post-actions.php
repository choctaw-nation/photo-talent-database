<?php
/**
 * Post Actions Template Part
 * Adds Approve / Deny buttons & JS to posts that are pending.
 *
 * @package ChoctawNation
 */

use ChoctawNation\Asset_Loader;
use ChoctawNation\Enqueue_Type;

$post_status      = get_post_status( get_the_ID() );
$allowed_statuses = array( 'pending', 'draft' );

if ( ! in_array( $post_status, $allowed_statuses, true ) ) {
	return;
}
new Asset_Loader( 'talentActions', Enqueue_Type::script, 'pages', array() );
?>
<div class="d-flex flex-wrap gap-2 post-actions-container">
	<button class="btn btn-black rounded-pill" data-action="approve">Approve</button>
	<button class="btn btn-outline-danger rounded-pill" data-action="reject">Reject</button>
</div>
