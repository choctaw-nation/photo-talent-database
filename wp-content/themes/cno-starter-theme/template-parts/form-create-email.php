<?php
/**
 * Create Email Form
 *
 * @package ChoctawNation
 */

?>
<form action="" id="create-email-form" class="d-flex flex-column row-gap-3 align-items-stretch" method="POST">
	<div class="form-floating">
		<input type="email" class="form-control" id="email" placeholder="name@choctawnation.com" required>
		<label class="fs-root" for="email">Email address</label>
	</div>
	<div class="form-floating">
		<textarea class="form-control" id="message" style="height:clamp(100px,30vh,300px)" placeholder="Enter your message here..."></textarea>
		<label class="fs-root" for="message">Optional Message</label>
	</div>
</form>
