<?php
/**
 * Save Talent List Form
 *
 * @package ChoctawNation
 */

?>
<form action="" method="POST" id="save-list-form" class="d-flex flex-column align-items-stretch gap-3">
	<div>
		<label class="visually-hidden" for="listName">List Name</label>
		<input type="text" class="form-control" id="listName" name="listName" placeholder="List Name" required aria-describedby="listNameHelper" />
		<div id="listNameHelper" class="form-text fs-root ms-1 d-none">The post will be named <span id="listNamePreview"></span></div>
	</div>
	<div class="input-group">
		<span class="input-group-text">List Expiration</span>
		<input type="number" min="1" max="12" class="form-control" aria-label="Time" value="2" aria-describedby="listExpirationHelper" id="listExpirationLength" name="listExpirationLength">
		<select class="form-select" aria-label="Post Expiry Time Frame" id="listExpirationUnit" name="listExpirationUnit">
			<option value="days">Day(s)</option>
			<option value="weeks" selected>Week(s)</option>
			<option value="months">Month(s)</option>
		</select>
	</div>
	<div>
		<label class="visually-hidden" for="listDescription">List Description</label>
		<textarea class="form-control" placeholder="List Description" id="listDescription" name="listDescription" style="height: 100px"></textarea>
		<div id="listDescriptionHelper" class="form-text fs-root ms-1">Provide a brief description of the list's purpose.</div>
	</div>
	<div class="d-flex flex-wrap justify-content-end gap-2">
		<input type="submit" class="btn btn-black btn-sm fw-normal mt-auto align-self-end" value="Save List" />

	</div>
</form>
