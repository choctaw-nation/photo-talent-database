import ToastHandler from '../utils/ToastHandler';
import LocalStorage from '../talent/talent-selection-handler/model/LocalStorage';

window.addEventListener( 'DOMContentLoaded', () => {
	const toaster = new ToastHandler();
	const db = new LocalStorage();
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
						const success = await db.removeTalentFromTalentList(
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
						// eslint-disable-next-line no-console
						console.error( err );
					}
				}
			}
		} );
	}
	const selectedTalentListFooter = document.getElementById(
		'selected-talent-list-footer'
	);
	if ( selectedTalentListFooter ) {
		selectedTalentListFooter.addEventListener( 'click', async ( ev ) => {
			const target = ev.target as HTMLButtonElement;
			if ( ! target.hasAttribute( 'data-post-id' ) ) {
				return;
			}
			if ( 'Delete List' === target.textContent ) {
				const postId = parseInt( target.dataset.postId || '', 10 );
				if ( postId ) {
					try {
						target.disabled = true;
						const { success, message } =
							await db.deleteTalentList( postId );
						if ( success ) {
							toaster.showToast(
								`${ message } Redirecting you to the talent lists page.`,
								'success',
								() => {
									window.location.href = '/talent-lists';
								}
							);
						} else {
							toaster.showToast( message, 'error' );
						}
					} catch ( err ) {
						toaster.showToast( err.message, 'error' );
						// eslint-disable-next-line no-console
						console.error( err );
					} finally {
						target.disabled = false;
					}
				}
			}
		} );
	}
} );
