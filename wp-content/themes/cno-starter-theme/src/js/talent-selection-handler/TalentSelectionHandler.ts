import DomHandler from './view/DomHandler';
import LocalStorage from './LocalStorage';

/**
 * Controller. Wires up the db with DOM UI interactions.
 */
export default class TalentSelectionHandler {
	view: DomHandler;
	db: LocalStorage;

	constructor() {
		this.view = new DomHandler();
		this.db = new LocalStorage();
	}

	init() {
		this.view.init( this.db );
	}
}
