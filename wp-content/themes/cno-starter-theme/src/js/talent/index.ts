import sidebarToggleHandler from './sidebarToggleHandler';
import Controller from './talent-selection-handler/Controller';

window.addEventListener( 'DOMContentLoaded', () => {
	try {
		new Controller();
	} catch ( error ) {
		console.error( 'Error initializing talent selection:', error );
	}
	sidebarToggleHandler();
} );
