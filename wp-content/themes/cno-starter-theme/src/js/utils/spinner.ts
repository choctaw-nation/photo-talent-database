/**
 * Creates a Bootstrap spinner
 * @param target Container element to insert the spinner into
 * @returns The spinner element
 */
export function insertSpinner( target: HTMLElement ) {
	const spinner = document.createElement( 'div' );
	spinner.className = 'spinner-border text-primary';
	spinner.setAttribute( 'role', 'status' );
	const srOnly = document.createElement( 'span' );
	srOnly.className = 'visually-hidden';
	srOnly.textContent = 'Loading...';
	spinner.appendChild( srOnly );
	target.insertAdjacentElement( 'afterbegin', spinner );
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
