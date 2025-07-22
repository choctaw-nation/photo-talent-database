import TalentActionsHandler from './TalentActionsHandler';

window.addEventListener( 'DOMContentLoaded', () => {
	try {
		const actionsHandler = new TalentActionsHandler();
		actionsHandler.init();
	} catch ( err ) {
		console.error( err );
	}
} );
