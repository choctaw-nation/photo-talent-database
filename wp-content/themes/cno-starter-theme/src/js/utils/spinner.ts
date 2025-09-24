/**
 * Creates a Bootstrap spinner
 * @param target Container element to insert the spinner into
 * @return The spinner element
 */
export function insertSpinner(
	target: HTMLElement,
	location: InsertPosition = 'afterbegin',
	classes: string[] = []
) {
	const spinner = document.createElement( 'div' );
	const spinnerClasses = [ 'spinner-border', 'text-primary', ...classes ];
	spinner.className = spinnerClasses.join( ' ' );
	spinner.setAttribute( 'role', 'status' );
	const srOnly = document.createElement( 'span' );
	srOnly.className = 'visually-hidden';
	srOnly.textContent = 'Loading...';
	spinner.appendChild( srOnly );
	target.insertAdjacentElement( location, spinner );
	return spinner;
}

/**
 * Removes a Bootstrap spinner
 * @param spinner The spinner element to remove
 */
export function removeSpinner( spinner: HTMLElement | null ) {
	if ( spinner ) {
		spinner.remove();
	}
}
