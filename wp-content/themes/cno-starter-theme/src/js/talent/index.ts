import sidebarToggleHandler from './sidebarToggleHandler';
import Controller from './talent-selection-handler/TalentSelectionHandler';

window.addEventListener( 'DOMContentLoaded', () => {
	const handler = new Controller();
	handler.init();
	sidebarToggleHandler();
} );
