import TalentActionsHandler from './TalentActionsHandler';

window.addEventListener( 'DOMContentLoaded', () => {
	try {
		const actionsHandler = new TalentActionsHandler();
		actionsHandler.init();
	} catch ( err ) {
		// eslint-disable-next-line no-console
		console.error( err );
	}
} );
