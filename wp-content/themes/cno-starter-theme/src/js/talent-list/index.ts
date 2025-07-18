import ToastHandler from '../utils/ToastHandler';

window.addEventListener( 'DOMContentLoaded', () => {
	const toaster = new ToastHandler();
	const selectedTalentList = document.getElementById(
		'selected-talent-list'
	);
	if ( selectedTalentList ) {
		const postId = [ ...document.body.classList ]
			.filter( ( className ) => className.startsWith( 'postid-' ) )
			.map( ( className ) =>
				parseInt( className.replace( 'postid-', '' ), 10 )
			);

		selectedTalentList.addEventListener( 'click', async ( ev ) => {
			const target = ev.target as HTMLElement;
			if ( target.matches( '.btn-close' ) ) {
				const talentId = parseInt( target.dataset.postId || '', 10 );
				if ( postId[ 0 ] ) {
					try {
						const success = await removeTalent(
							postId[ 0 ],
							talentId
						);
						if ( success ) {
							const listElement = target.closest( 'li' );
							if ( listElement ) {
								listElement.remove();
							}
							toaster.showToast(
								'Talent removed successfully.',
								'success'
							);
						}
					} catch ( err ) {
						toaster.showToast( err.message, 'error' );
						console.error( err );
					}
				}
			}
		} );
	}
} );

async function removeTalent(
	postId: number,
	talentId: number
): Promise< boolean > {
	const response = await fetch( `/wp-json/cno/v1/talent-list/${ postId }`, {
		method: 'DELETE',
		headers: {
			'Content-Type': 'application/json',
			'X-WP-Nonce': window.cnoApi.nonce,
		},
		body: JSON.stringify( { talentId } ),
	} );
	const { success, message, data } = await response.json();
	if ( success ) {
		return success;
	} else {
		throw new Error( message );
	}
}
