import dateAsYmd from '../../../utils/dateAsYmd';
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
			'.btn-last-used-custom'
		) as HTMLButtonElement;
	}

	updateLastUsedString( date: string ) {
		const lastUsedContainer = this.cardEl.querySelector(
			'.last-used-value'
		) as HTMLSpanElement;
		if ( date === dateAsYmd() ) {
			lastUsedContainer.textContent = 'Today';
		} else {
			const [ year, month, day ] = date
				.match( /(\d{4})(\d{2})(\d{2})/ )!
				.slice( 1 );
			const dateObj = new Date( year, month - 1, day );
			lastUsedContainer.textContent = dateObj.toLocaleDateString(
				'en-US',
				{
					year: 'numeric',
					month: 'long',
					day: 'numeric',
				}
			);
		}
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
