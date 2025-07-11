<?php
/**
 * Create Email Modal Template
 *
 * @package ChoctawNation
 */

?>
<div class="modal fade" tabindex="-1" id="create-email-modal">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h1 class="modal-title fs-5">Create Email</h1>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body bg-light">
				<form action="" id="create-email-form">
					<div class="form-floating mb-3">
						<input type="email" class="form-control" id="email" placeholder="name@choctawnation.com" required>
						<label class="fs-root" for="email">Email address</label>
					</div>
					<div class="form-floating mb-3">
						<textarea class="form-control" id="message" style="height:clamp(100px,30vh,300px)" placeholder="Enter your message here..."></textarea>
						<label class="fs-root" for="message">Optional Message</label>
					</div>
				</form>
			</div>
			<div class="modal-footer gap-2">
				<button type="reset" form="create-email-form" class="btn btn-danger m-0">Clear Selection</button>
				<input type="submit" class="btn btn-primary m-0" value="Send Email" form="create-email-form" />
			</div>
		</div>
	</div>
</div>
