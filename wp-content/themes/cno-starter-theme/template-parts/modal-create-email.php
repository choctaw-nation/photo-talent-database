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
				<form action="" id="create-email-form" class="d-flex flex-column row-gap-3 align-items-stretch">
					<div class="form-floating">
						<input type="email" class="form-control" id="email" placeholder="name@choctawnation.com" required>
						<label class="fs-root" for="email">Email address</label>
					</div>
					<div class="row" id="selected-talent-container">
						<div class="col d-flex flex-wrap row-gap-2 column-gap-3 justify-content-between align-items-center mb-3" id="actions-container">
							<h2 class="fs-5 mb-0">Selected Talent</h2>
						</div>
						<div class="col-12 d-flex flex-column" id="selected-talent-list"></div>
					</div>
					<div class="form-floating">
						<textarea class="form-control" id="message" style="height:clamp(100px,30vh,300px)" placeholder="Enter your message here..."></textarea>
						<label class="fs-root" for="message">Optional Message</label>
					</div>
				</form>
			</div>
			<div class="modal-footer gap-2">
				<button class="btn btn-outline-black m-0 btn-sm fw-normal">Save List</button>
				<input type="submit" class="btn btn-black m-0 btn-sm fw-normal" value="Send Email" form="create-email-form" />
			</div>
		</div>
	</div>
</div>
