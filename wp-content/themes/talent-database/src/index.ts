import './styles/main.scss';
import Controller from './js/talent/talent-selection-handler/Controller';
import { handleGeneratePdfButtonClick } from './js/pdf-generator';

window.addEventListener( 'DOMContentLoaded', () => {
	try {
		new Controller();
		handleGeneratePdfButtonClick();
	} catch ( error ) {
		// eslint-disable-next-line no-console
		console.error( 'Error initializing talent selection:', error );
	}
} );
