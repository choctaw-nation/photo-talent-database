<?php
/**
 * Comments Template
 *
 * This template is used to display comments and the comment form.
 *
 * @package ChoctawNation
 */

if ( ! comments_open() || 0 === (int) get_comments_number() ) {
	return;
} ?>
<aside class="mt-5">
	<h2 class="fs-4 mb-4">Comments</h2>
	<ul class="list-group">
		<?php
		wp_list_comments(
			array(
				'type'     => 'comment',
				'callback' => function ( $comment ) {
					?>
		<li class="list-group-item py-3">
			<div class="col-auto d-flex flex-column">
				<h3 class="fs-5 mb-0">
					<?php
						$display_name = get_the_author_meta( 'display_name', $comment->user_id );
						echo $display_name ? esc_html( $display_name ) : esc_html( $comment->comment_author );
					?>
				</h3>
				<small class="text-muted"><?php comment_date( 'F j, Y' ); ?> at <?php comment_time( 'g:ia' ); ?></small>
				<p class="mb-0 mt-2">
					<?php echo esc_textarea( $comment->comment_content ); ?>
				</p>
			</div>
		</li>
					<?php
				},

			)
		);
		?>
	</ul>
	<div class="comment-form mt-4">
		<?php
			comment_form(
				array(
					'class_form'    => 'd-flex flex-column gap-3',
					'title_reply'   => '<h3 class="fs-5">Leave a Reply</h3>',
					'submit_button' => '<button type="submit" class="btn btn-black">Post Comment</button>',
					'comment_field' => '<textarea id="comment" name="comment" class="form-control" rows="4" required></textarea>',
				)
			);
			?>
	</div>
</aside>
