<?php
/**
 * Offcanvas Date Picker
 *
 * @package ChoctawNation
 */

if ( ! is_home() || ! have_posts() ) {
	return;
}
?>
<div class="offcanvas offcanvas-bottom" tabindex="-1" id="custom-date-offcanvas" aria-labelledby="customDateOffCanvasLabel">
	<div class="offcanvas-header">
		<h5 class="offcanvas-title" id="customDateOffCanvasLabel">Custom Date</h5>
	</div>
	<div class="offcanvas-body small">
		<?php $now = new DateTime( 'now', wp_timezone() ); ?>
		<form id="custom-date-picker" aria-label="Select a custom date" class="d-flex flex-column align-items-stretch row-gap-3" autocomplete="off">
			<div class="row row-cols-auto gx-0 gap-3">
				<div class="col flex-grow-1 flex-shrink-1">
					<div class="form-floating">
						<select class="form-select" id="date-month" name="date-month" required aria-required="true" aria-label="Month" placeholder="Month">
							<option value="" disabled>Select month</option>
							<?php
							$months = array(
								'01' => 'January',
								'02' => 'February',
								'03' => 'March',
								'04' => 'April',
								'05' => 'May',
								'06' => 'June',
								'07' => 'July',
								'08' => 'August',
								'09' => 'September',
								'10' => 'October',
								'11' => 'November',
								'12' => 'December',
							);
							foreach ( $months as $num => $name ) {
								$selected = ( $now->format( 'm' ) === $num ) ? 'selected' : '';
								echo "<option value=\"{$num}\" {$selected}>{$name}</option>";
							}
							?>
						</select>
						<label for="date-month">Month</label>
					</div>
				</div>
				<div class="col flex-grow-1 flex-shrink-1">
					<div class="form-floating">
						<input type="number" class="form-control" id="date-day" name="date-day" min="1" max="31" value="<?php echo $now->format( 'd' ); ?>" required aria-required="true"
								aria-label="Day" inputmode="numeric" pattern="[0-9]*" placeholder="Day">
						<label for="date-day">Day</label>
					</div>
				</div>
				<div class="col flex-grow-1 flex-shrink-1">
					<?php
					$year_min = $now->format( 'Y' ) - 1;
					$year_max = $now->format( 'Y' ) + 1;
					?>
					<div class="form-floating">
						<input type="number" class="form-control" id="date-year" name="date-year" min="<?php echo $year_min; ?>" max="<?php echo $year_max; ?>"
								value="<?php echo $now->format( 'Y' ); ?>" required aria-required="true" aria-label="Year" inputmode="numeric" pattern="[0-9]*" placeholder="Year">
						<label for="date-year">Year</label>
					</div>
					<div class="form-text">Must be between <?php echo $year_min; ?> and <?php echo $year_max; ?></div>
				</div>
			</div>
			<div class="row row-cols-auto gx-0 gap-2">
				<button type="button" class="btn btn-secondary flex-grow-1" data-bs-dismiss="offcanvas" aria-label="Cancel and close">Cancel</button>
				<button type="submit" class="btn btn-primary flex-grow-1" aria-label="Submit custom date">Set Custom Date</button>
			</div>
		</form>
	</div>
</div>
