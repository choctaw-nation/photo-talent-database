import { insertSpinner, removeSpinner } from '../../../utils/spinner';

export default class CardHandler {
	cardEl: HTMLDivElement;
	id: number;

	lastUsedTodayButton: HTMLButtonElement;
	customDateTrigger: HTMLButtonElement;

	constructor( postId: string ) {
		this.id = Number( postId );
		this.cardEl = document.getElementById(
			`talent-${ this.id }`
		) as HTMLDivElement;
		this.lastUsedTodayButton = this.cardEl.querySelector(
			'.btn-last-used'
		) as HTMLButtonElement;
		this.customDateTrigger = this.cardEl.querySelector(
			'button[data-bs-toggle="offcanvas"]'
		) as HTMLButtonElement;
	}

	updateLastUsedString() {
		const lastUsedContainer = this.cardEl.querySelector(
			'.last-used-value'
		) as HTMLSpanElement;
		lastUsedContainer.textContent = 'Today';
	}

	useIsLoading( isLoading: boolean, clickedButton: HTMLButtonElement ) {
		const buttons = [ this.lastUsedTodayButton, this.customDateTrigger ];
		buttons.forEach( ( button ) => {
			button.disabled = isLoading;
		} );
		if ( isLoading ) {
			insertSpinner( clickedButton, 'beforeend', [
				'spinner-border-sm',
				'ms-3',
			] );
		} else {
			removeSpinner( clickedButton.querySelector( '.spinner-border' ) );
		}
	}
}
