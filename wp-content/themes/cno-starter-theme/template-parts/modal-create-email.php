<?php
/**
 * Create Email Modal Template
 *
 * @package ChoctawNation
 */

?>
<div class="modal fade" tabindex="-1" id="create-email-modal">
	<div class="modal-dialog modal-dialog-centered modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h1 class="modal-title fs-5">Selected Talent List</h1>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body bg-light">
				<div class="tab-content" id="modal-tab-content">
					<div class="tab-pane fade show active" id="form-and-list" role="tabpanel" aria-labelledby="back-to-form">
						<div class="row row-cols-auto row-cols-sm-2 row-gap-4">
							<div class="col flex-grow-1">
								<?php get_template_part( 'template-parts/form', 'create-email' ); ?>
							</div>
							<div class="col flex-grow-1">
								<div class="row" id="selected-talent-container">
									<div class="col d-flex flex-wrap row-gap-2 column-gap-3 justify-content-between align-items-center mb-3" id="actions-container">
										<h2 class="fs-5 mb-0">Selected Talent</h2>
									</div>
									<div class="col-12 d-flex flex-column" id="selected-talent-list"></div>
								</div>
							</div>
						</div>
					</div>
					<div class="tab-pane fade" id="save-list" aria-labelledby="save-list-tab" role="tabpanel">
						<?php get_template_part( 'template-parts/form', 'save-list' ); ?>
					</div>
				</div>
			</div>
			<div class="modal-footer gap-2">
				<div role="tablist" class="d-flex gap-2">
					<button class="btn btn-outline-black d-none m-0 btn-sm fw-normal" id="back-to-form" data-bs-target="#form-and-list" role="tab" aria-controls="form-and-list"
							aria-selected="true">Back to Form</button>
					<button class="btn btn-outline-black m-0 btn-sm fw-normal" id="save-list-tab" data-bs-toggle="tab" data-bs-target="#save-list" role="tab" aria-controls="save-list"
							aria-selected="false">Save
						List</button>
				</div>
				<input type="submit" class="btn btn-black m-0 btn-sm fw-normal" value="Send Email" form="create-email-form" />
			</div>
		</div>
	</div>
</div>
