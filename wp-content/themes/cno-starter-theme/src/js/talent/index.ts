import sendEmail from '../utils/sendEmail';
import sidebarToggleHandler from './sidebarToggleHandler';
import Controller from './talent-selection-handler/Controller';

window.addEventListener( 'DOMContentLoaded', () => {
	new Controller();
	sidebarToggleHandler();
	sendEmail();
} );
