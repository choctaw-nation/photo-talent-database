import { insertSpinner, removeSpinner } from '../../../utils/spinner';

export default class CardHandler {
	cardEl: HTMLDivElement;
	id: number;

	setLastUsedButton: HTMLButtonElement;

	constructor( postId: string ) {
		this.id = Number( postId );
		this.cardEl = document.getElementById(
			`talent-${ this.id }`
		) as HTMLDivElement;
		this.setLastUsedButton = this.cardEl.querySelector(
			'.btn-last-used'
		) as HTMLButtonElement;
	}

	updateLastUsedString() {
		const lastUsedContainer = this.cardEl.querySelector(
			'.last-used-value'
		) as HTMLSpanElement;
		lastUsedContainer.textContent = 'Today';
	}

	useIsLoading( isLoading: boolean ) {
		this.setLastUsedButton.disabled = isLoading;
		const cardFooter =
			this.cardEl.querySelector< HTMLElement >( '.card-footer' )!;
		if ( isLoading ) {
			insertSpinner( cardFooter );
		} else {
			removeSpinner( cardFooter.querySelector( '.spinner-border' ) );
		}
	}
}
