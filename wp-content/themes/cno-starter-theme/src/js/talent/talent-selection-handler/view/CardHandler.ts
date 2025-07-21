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
		const cardFooter = this.cardEl.querySelector( '.card-footer' )!;
		if ( isLoading ) {
			cardFooter.insertAdjacentHTML(
				'afterbegin',
				`<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>`
			);
		} else {
			cardFooter.querySelector( '.spinner-border' )?.remove();
		}
	}
}
