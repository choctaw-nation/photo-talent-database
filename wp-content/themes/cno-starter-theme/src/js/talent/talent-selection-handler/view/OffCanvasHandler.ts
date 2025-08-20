import { insertSpinner, removeSpinner } from '../../../utils/spinner';

export default class OffCanvasHandler {
	offcanvasEl: HTMLDivElement;
	customDateForm: HTMLFormElement;
	id: string;

	constructor( id: string ) {
		this.id = id;
		const offcanvas = document.getElementById(
			'custom-date-offcanvas'
		) as HTMLDivElement;
		if ( ! offcanvas ) {
			throw new Error( "Couldn't find the offcanvas element!" );
		}
		this.offcanvasEl = offcanvas;
		const form = this.offcanvasEl.querySelector< HTMLFormElement >(
			'#custom-date-picker'
		);
		if ( ! form ) {
			throw new Error( "Couldn't find the custom date picker form!" );
		}
		this.customDateForm = form;
	}

	handleFormSubmission( callback: ( date: string ) => Promise< void > ) {
		this.customDateForm.addEventListener( 'submit', ( ev ) => {
			ev.preventDefault();
			const formData = new FormData( this.customDateForm );
			const date = `${ formData.get( 'date-year' ) }${ formData.get(
				'date-month'
			) }${ formData.get( 'date-day' ) }`;
			this.setFormElementsDisability( true );
			const submitButton = this.customDateForm.querySelector(
				'button[type="submit"]'
			) as HTMLButtonElement;
			insertSpinner( submitButton, 'beforeend', [
				'text-white',
				'spinner-border-sm',
				'ms-3',
			] );
			callback( date ).then( () => {
				this.setFormElementsDisability( false );
				removeSpinner(
					submitButton.querySelector( '.spinner-border' )
				);
			} );
		} );
	}

	private setFormElementsDisability( disabled: boolean ) {
		Array.from( this.customDateForm.elements ).forEach( ( el ) => {
			if (
				el instanceof HTMLInputElement ||
				el instanceof HTMLSelectElement ||
				el instanceof HTMLTextAreaElement ||
				el instanceof HTMLButtonElement
			) {
				el.disabled = disabled;
			}
		} );
	}
}
